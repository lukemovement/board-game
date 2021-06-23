<?php

namespace App\Domain\GamePlay\Dto;

use App\Domain\GameData\Entity\MapTile;
use Doctrine\Common\Collections\ArrayCollection;

class PathFinderNodeDto {

    public MapTile $destination;

    /**
     * @var MapTile[]|ArrayCollection
     */
    public ArrayCollection $route;
}