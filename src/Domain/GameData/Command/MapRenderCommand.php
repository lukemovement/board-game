<?php

namespace App\Domain\GameData\Command;

use App\Application\Service\FileSystem\FileSystem;
use App\Domain\GameData\Entity\Map;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\GameData\Repository\MapRepository;
use App\Domain\GameData\Service\MapGeneratorService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'MapRenderCommand',
    description: 'Render an existing map from the database into a HTML file',
)]
class MapRenderCommand extends Command
{
    public const ARG_MAP_ID = "map_id";

    public function __construct(
        private MapRepository $mapRepository,
        private FileSystem $fileSystem
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::ARG_MAP_ID, InputOption::VALUE_REQUIRED, "The id of the map you want to render");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $mapId = $input->getArgument(self::ARG_MAP_ID);
        $map = $this->mapRepository->find($mapId);

        if (null === $map) {
            $io->error("Map with ID of $mapId not found");
            return Command::INVALID;
        }

        $htmlMap = "
        <style>
            .row {
                display: grid;
                grid-template-columns: auto auto auto auto auto auto auto auto auto auto;
            }

            .tile {
                display: grid;
                grid-template-columns: auto auto auto;
                border: 1px solid red;
            } 

            .tile .road {
                background: url(https://t3.ftcdn.net/jpg/00/67/32/42/360_F_67324202_9eSsx7EgHkDEnK0AGRkCvliSrd3DCL0C.jpg);
                background-size: contain;
            }

            .tile .grass {
                background: url(https://naldzgraphics.net/wp-content/uploads/2014/07/3-lawn-seamless-grass-texture.jpg);
                background-size: contain;
            }

            .tile .grass, .tile .road {
                height: 17;
            }
        </style>
        ";

        $mapTileRows = array_chunk($map->getMapTiles()->toArray(), $map->getColumns());
        $mapTileRows = new ArrayCollection($mapTileRows);
        
        $mapTileRows->forAll(function(int $i, array $mapTiles) use (&$htmlMap) {
            $mapTiles = new ArrayCollection($mapTiles);

            $htmlMap .= "<div class='row row-$i'>";

            $mapTiles->forAll(function(int $i, MapTile $mapTile) use (&$htmlMap)
            {
                $mapTileId = $mapTile->getTile()->getId();

                $htmlMap .= "
                <div class=\"tile tile-$mapTileId\">
                    <div class=\"grass\"></div>
                    <div class=\"".($mapTile->getTile()->getCanExitTop() ? 'road' : 'grass')."\"></div>
                    <div class=\"grass\"></div>
                    
                    <div class=\"".($mapTile->getTile()->getCanExitLeft() ? 'road' : 'grass')."\"></div>
                    <div class=\"road\"></div>
                    <div class=\"".($mapTile->getTile()->getCanExitRight() ? 'road' : 'grass')."\"></div>

                    <div class=\"grass\"></div>
                    <div class=\"".($mapTile->getTile()->getCanExitBottom() ? 'road' : 'grass')."\"></div>
                    <div class=\"grass\"></div>
                </div>
                ";

                return true;
            });

            $htmlMap .= "</div>";

            return true;
        });

        $this->fileSystem->getMapsDirectory()->getFile(
            $map->getName() . ".html"
        )->write($htmlMap);

        return Command::SUCCESS;
    }
}
