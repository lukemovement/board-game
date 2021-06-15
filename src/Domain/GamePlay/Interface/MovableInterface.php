<?php

namespace App\Domain\GamePlay\Interface;

use App\Domain\Common\Type\Position;

interface MovableInterface {

    public function getPosition(): Position;
    public function setPosition(Position $position): self;

}