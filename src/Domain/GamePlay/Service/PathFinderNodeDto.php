<?php

namespace App\Domain\GamePlay\Service;

use App\Domain\GameData\Entity\MapTile;
use Doctrine\Common\Collections\ArrayCollection;

class PathFinderNodeDto {

    public MapTile $destination;

    public ArrayCollection $route;
}