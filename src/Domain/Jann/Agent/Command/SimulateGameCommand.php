<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Command;

use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GameData\Repository\MapRepository;
use App\Domain\GameData\Repository\PlayerStatConfigRepository;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\PlayerStat;
use App\Domain\Jann\Agent\Entity\Agent;
use App\Domain\Profile\Entity\Profile;
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
        private PlayerStatConfigRepository $playerStatConfigRepository
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

        $profile = new Profile(
            "Jann"
        );

        $player = new Player(
            $profile
        );

        foreach($playerStatConfigs as $playerStatConfig) {
            $player->addPlayerStat(new PlayerStat(
                $playerStatConfig
            ));
        }

        $game->addPlayer($player);

        $jann = new Agent();

        while($game->getLivingPlayers()->count() < 0) {

        }
        
        return Command::SUCCESS;
    }
}
