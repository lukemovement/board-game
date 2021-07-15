<?php

namespace App\Domain\GameData\Service;

use App\Domain\GameData\Entity\Map;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\GameData\Entity\Tile;
use App\Domain\GameData\Repository\TileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Throwable;

class MapGeneratorService {

    /** @var MapTile[]|ArrayCollection */
    private ArrayCollection $mapTiles;

    /** @var Tile[]|ArrayCollection */
    private ArrayCollection $availableTiles;

    public function __construct(
        private TileRepository $tileRepository
    ) {
        $this->availableTiles = new ArrayCollection($tileRepository->findAll());
    }

    private int $totalColumns;
    private int $totalRows;

    /**
     * @var Map
     */
    private $map;

    public function execute(
        Map $map
    )
    {
        $this->map = $map;
        $this->totalColumns = $map->getColumns();
        $this->totalRows = $map->getRows();
        $this->mapTiles = new ArrayCollection();

        for($i = 0; $i < $this->totalRows; $i++) {
            $this->generateRow($i);
        }

        $map->setMapTiles($this->mapTiles);
    }

    private function generateRow(int $rowIndex)
    {
        for($columnIndex = 0; $columnIndex < $this->totalColumns; $columnIndex++) { 
            $matchingTiles = $this->availableTiles->filter(function(Tile $tile) use (
                $columnIndex,
                $rowIndex
            ) {
                if (0 === $columnIndex && true === $tile->getCanExitLeft()) return false;
                if (0 === $rowIndex && true === $tile->getCanExitTop()) return false;
                if ($rowIndex + 1 === $this->totalRows && true === $tile->getCanExitBottom()) return false;
                if ($columnIndex + 1 === $this->totalColumns && true === $tile->getCanExitRight()) return false;

                if (0 !== $columnIndex) {
                    /** @var MapTile $previousTile */
                    $previousTile = $this->mapTiles->last();
                    if (
                        $previousTile->getTile()->getCanExitRight() !== $tile->getCanExitLeft()
                    ) {
                        return false;
                    }
                }

                if (0 !== $rowIndex) {
                    /** @var MapTile */
                    $previousTile = $this->mapTiles->get($this->mapTiles->count() - $this->totalColumns);
                    if (
                        $previousTile->getTile()->getCanExitBottom() !== $tile->getCanExitTop()
                    ) {
                        return false;
                    }
                }

                return true;
            });

            $nonDeadedEndMatchingTiles = $matchingTiles->filter(function(Tile $mapTile)
            {
                return (new ArrayCollection([
                    $mapTile->getCanExitTop(),
                    $mapTile->getCanExitBottom(),
                    $mapTile->getCanExitLeft(),
                    $mapTile->getCanExitRight(),
                ]))->filter(fn(bool $result) => $result)->count() > 1;
            });
            
            if (!$nonDeadedEndMatchingTiles->isEmpty()) {
                $matchingTiles = $nonDeadedEndMatchingTiles;
            }

            $tile = $matchingTiles->toArray()[array_rand($matchingTiles->toArray())];
            
            $this->mapTiles->add(new MapTile(
                $this->map,
                $tile,
                $columnIndex,
                $rowIndex
            )); 
        }
    }
}