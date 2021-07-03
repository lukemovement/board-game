<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Command;

use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GameData\Repository\MapRepository;
use App\Domain\GameData\Repository\PlayerStatConfigRepository;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\PlayerStat;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\GamePlay\Service\MovePlayerService;
use App\Domain\GamePlay\Service\PlayerAttackZombieService;
use App\Domain\GamePlay\Service\SpawnZombieService;
use App\Domain\Jann\Agent\Dto\DecisionDto;
use App\Domain\Jann\Agent\Entity\Agent;
use App\Domain\Jann\Agent\Service\DecisionExecutionService;
use App\Domain\Jann\Agent\Service\DecisionPickerService;
use App\Domain\Jann\Agent\Service\RandomExecutionService;
use App\Domain\Jann\Behaviour\Entity\Behaviour;
use App\Domain\Jann\Behaviour\Repository\BehaviourRepository;
use App\Domain\Jann\Behaviour\Service\BehaviourAnalysisService;
use App\Domain\Jann\Behaviour\Service\BehaviourPredictionService;
use App\Domain\Jann\Environment\Repository\PlayerStateRepository;
use App\Domain\Jann\Environment\Repository\ZombieStateRepository;
use App\Domain\Jann\Environment\Service\TileStateSetupService;
use App\Domain\Profile\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'SimulateGameCommand',
    description: 'Simulate a game to train Jann',
)]
class SimulateGameCommand extends Command
{
    public const ARG_MAP = "map";

    public function __construct(
        private MapRepository $mapRepository,
        private PlayerStatConfigRepository $playerStatConfigRepository,
        private SpawnZombieService $spawnZombieService,
        private BehaviourAnalysisService $behaviourAnalysisService,
        private BehaviourPredictionService $behaviourPredictionService,
        private TileStateSetupService $tileStateSetupService,
        private DecisionPickerService $decisionPickerService,
        private MovePlayerService $movePlayerService,
        private PlayerAttackZombieService $playerAttackZombieService,
        private ZombieStateRepository $zombieStateRepository,
        private DecisionExecutionService $decisionExecutionService,
        private PlayerStateRepository $playerStateRepository,
        private BehaviourRepository $behaviourRepository,
        private RandomExecutionService $randomExecutionService,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::ARG_MAP, InputOption::VALUE_REQUIRED, "The id of the map to use")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $map = $this->mapRepository->find($input->getArgument(self::ARG_MAP));

        $playerStatConfigs = $this->playerStatConfigRepository->findAll();

        $game = new Game(
            $map
        );

        $this->entityManager->persist($game);

        $profile = new Profile(
            "Jann"
        );

        $this->entityManager->persist($profile);

        $player = new Player(
            $profile
        );

        $this->entityManager->persist($player);

        foreach($playerStatConfigs as $playerStatConfig) {
            $player->addPlayerStat(new PlayerStat(
                $playerStatConfig
            ));
        }

        $game->addPlayer($player);

        $jann = new Agent($game);

        $nightStarted = false;

        while($game->getLivingPlayers()->count() > 0) {
            if (
                false === $game->isDay() &&
                false === $nightStarted
            ) {
                $nightStarted = true;

                $this->spawnZombieService->execute($game);
            } else {
                $nightStarted = false;
            }

            $behaviours = $this->behaviourPredictionService->execute($player);
            $decisionsCollections = $this->behaviourAnalysisService->execute($behaviours);
            $decision = $this->decisionPickerService->execute($decisionsCollections);

            if (null === $decision) {
                $this->randomExecutionService->execute($player);
            } else {
                $previousTileState = $this->tileStateSetupService->execute($game, $player->getPosition());
                $previousPlayerState = $this->playerStateRepository->findOrCreate($player);
    
                $this->decisionExecutionService->execute($decision, $player);
    
                $nextTileState = $this->tileStateSetupService->execute($game, $player->getPosition());
                $nextPlayerState = $this->playerStateRepository->findOrCreate($player);
    
                $this->behaviourRepository->createOrIncreaseLinkCount(
                    $previousTileState,
                    $decision->behaviour->isTypeMove() ? $nextTileState : null,
                    $previousPlayerState,
                    $nextPlayerState,
                    $decision->behaviour->isTypeAttack() ? $decision->behaviour->getAttackedZombieStateBefore() : null,
                    $decision->behaviour->isTypeAttack() ? $decision->behaviour->getAttackedZombieStateAfter() : null,
                );            
            }
            
            $game->increaseRound();

            $this->entityManager->persist($game);
        }
        
        return Command::SUCCESS;
    }
}
