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
    name: 'CreateGameCommand',
    description: 'Create a game to train Jann',
)]
class CreateGameCommand extends Command
{
    public const ARG_MAP = "map";

    public function __construct(
        private MapRepository $mapRepository,
        private PlayerStatConfigRepository $playerStatConfigRepository,
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

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $io->writeln("Game ID: " . $game->getId());

        return Command::SUCCESS;
    }
}
