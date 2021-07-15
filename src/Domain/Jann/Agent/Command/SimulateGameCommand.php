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
use App\Domain\GamePlay\Repository\GameRepository;
use App\Domain\GamePlay\Service\MovePlayerService;
use App\Domain\GamePlay\Service\PlayerAttackZombieService;
use App\Domain\GamePlay\Service\SpawnZombieService;
use App\Domain\GamePlay\Service\TakeZombieTurnService;
use App\Domain\Jann\Agent\Dto\DecisionDto;
use App\Domain\Jann\Agent\Entity\Agent;
use App\Domain\Jann\Agent\Service\DecisionExecutionService;
use App\Domain\Jann\Agent\Service\DecisionPickerService;
use App\Domain\Jann\Agent\Service\RandomExecutionService;
use App\Domain\Jann\Agent\Service\RenderGameService;
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
    public const ARG_GAME = "game";

    public function __construct(
        private SpawnZombieService $spawnZombieService,
        private BehaviourAnalysisService $behaviourAnalysisService,
        private BehaviourPredictionService $behaviourPredictionService,
        private TileStateSetupService $tileStateSetupService,
        private DecisionPickerService $decisionPickerService,
        private DecisionExecutionService $decisionExecutionService,
        private PlayerStateRepository $playerStateRepository,
        private BehaviourRepository $behaviourRepository,
        private RandomExecutionService $randomExecutionService,
        private EntityManagerInterface $entityManager,
        private GameRepository $gameRepository,
        private RenderGameService $renderGameService,
        private TakeZombieTurnService $takeZombieTurnService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::ARG_GAME, InputOption::VALUE_REQUIRED, "The id of the game to use")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $game = $this->gameRepository->find($input->getArgument(self::ARG_GAME));

        $player = $game->getPlayers()->get(0);

        $this->spawnZombieService->execute($game);

        $this->entityManager->persist($game);

        $file = $this->renderGameService->execute($game, $player);
        $io->writeln("Move output: "  . $file->getPath());

        $this->entityManager->persist($game);
        $this->entityManager->flush();
        
        while($player->getPlayerStat(PlayerStatConfig::ENERGY_ID)->getComputedLevel() > 0) {

            $behaviours = $this->behaviourPredictionService->execute($player);
            $decisionsCollections = $this->behaviourAnalysisService->execute($behaviours);
            $decision = $this->decisionPickerService->execute($decisionsCollections);

            if (null === /*$decision*/ null) {
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
            
            $this->entityManager->persist($game);
            $this->entityManager->flush();
            $file = $this->renderGameService->execute($game, $player);
            $io->writeln("Move output: "  . $file->getPath());
        }

        $this->takeZombieTurnService->execute($game);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $game->increaseMoveCount();
        $file = $this->renderGameService->execute($game, $player);
        $io->writeln("Move output: "  . $file->getPath());

        $game->nextRound();

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
