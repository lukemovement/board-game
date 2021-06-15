<?php

declare(strict_types=1);

namespace App\Domain\GamePlay\Command;

use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GameData\Repository\MapRepository;
use App\Domain\GameData\Repository\PlayerStatConfigRepository;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\PlayerStat;
use App\Domain\Profile\Entity\Profile;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'MapGeneratorCommand',
    description: 'Generator a new map and persist it to the database',
)]
class SimulateGameCommand extends Command
{
    public const ARG_MAP = "map";

    public const OPT_PLAYERS = "players";

    public function __construct(
        private MapRepository $mapRepository,
        private PlayerStatConfigRepository $playerStatConfigRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::ARG_MAP, InputOption::VALUE_REQUIRED, "The id of the map to use")
            ->addOption(self::OPT_PLAYERS, 'p', InputOption::VALUE_OPTIONAL, 'Number of players', 3)
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

        $profile = new Profile(
            "Bot"
        );

        $playerCount = (int) $input->getOption(self::OPT_PLAYERS);

        for ($i = 0; $i < $playerCount;$i++) {
            $player = new Player(
                $profile
            );

            foreach($playerStatConfigs as $playerStatConfig) {
                $player->addPlayerStat(new PlayerStat(
                    $playerStatConfig
                ));
            }

            $game->addPlayer($player);
        }

        while($game->getLivingPlayers()->count() < 0) {

        }
        
        return Command::SUCCESS;
    }
}
