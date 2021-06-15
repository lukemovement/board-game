<?php

namespace App\Domain\GamePlay\Service;

use App\Domain\GameData\Entity\MapTile;
use Doctrine\Common\Collections\ArrayCollection;

class PathFinderService {

    private ArrayCollection $moveTreeNodes;
    private int $maxDepth;

    /**
     * @return ArrayCollection|MapTile[]
     */
    public function execute(
        MapTile $tile,
        int $maxDepth
    ): ArrayCollection
    {
        $this->moveTreeNodes = new ArrayCollection();
        $this->maxDepth = $maxDepth;

        $this->walk($tile, new ArrayCollection());

        return $this->moveTreeNodes;
    }

    private function walk(MapTile $mapTile, ArrayCollection $route, $depth = 0): void
    {
        $depth++;
        
        if ($this->maxDepth === $depth) {
            return;
        }

        $mapTile->getAdjacentTiles()->forAll(
            function(int $i, MapTile $mapTile) use ($route, $depth) {
                if (false === $route->isEmpty()) {
                    /** @var PathFinderNodeDto $previousNode */
                    $previousNode = $route->last();
                    if ($previousNode->destination->getId() === $mapTile->getId()) {
                        return true;
                    }
                }

                $node = new PathFinderNodeDto();

                $node->destination = $mapTile;
                $node->route = $route;

                $this->moveTreeNodes->add($node);

                $newRoute = clone $route;
                $newRoute->add($node);

                $this->walk($mapTile, $newRoute, $depth);

                return true;
            } 
        );
    }
}