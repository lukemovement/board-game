<?php

namespace App\Domain\GameData\Command;

use App\Domain\GameData\Entity\Map;
use App\Domain\GameData\Service\MapGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'MapGeneratorCommand',
    description: 'Generator a new map and persist it to the database',
)]
class MapGeneratorCommand extends Command
{
    public const ARG_NAME = "name";

    public const OPT_ROWS = "rows";
    public const OPT_COLUMNS = "columns";
    public const OPT_ZOMBIE_VISIBILITY = "zombie-visibility";
    public const OPT_ITEM_LIMIT = "backpack-limit";

    public function __construct(
        private MapGeneratorService $mapGeneratorService,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::ARG_NAME, InputOption::VALUE_REQUIRED, "The name of the map")
            ->addOption(self::OPT_ROWS, 'r', InputOption::VALUE_OPTIONAL, 'Number or rows on the map', "10")
            ->addOption(self::OPT_COLUMNS, 'c', InputOption::VALUE_OPTIONAL, 'Number or columns on the map', "10")
            ->addOption(self::OPT_ZOMBIE_VISIBILITY, 'zv', InputOption::VALUE_OPTIONAL, 'The number of moves required for a zombie to chaise a player', "5")
            ->addOption(self::OPT_ITEM_LIMIT, 'il', InputOption::VALUE_OPTIONAL, 'The maximum number of items a player is allowed to carray, excluding slots', "10")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $name = $input->getArgument(self::ARG_NAME);

        $rows = (int) $input->getOption(self::OPT_ROWS);
        $columns = (int) $input->getOption(self::OPT_COLUMNS);
        $zombieVisibility = (int) $input->getOption(self::OPT_ZOMBIE_VISIBILITY);
        $itemLimit = (int) $input->getOption(self::OPT_ITEM_LIMIT);

        $map = new Map(
            $name,
            $rows,
            $columns,
            $zombieVisibility,
            $itemLimit
        );
        
        $this->mapGeneratorService->execute(
            $map
        );  

        $this->entityManager->persist($map);

        $this->entityManager->flush();

        $output->writeln("Map ID: " . $map->getId());

        return Command::SUCCESS;
    }
}
