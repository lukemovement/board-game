<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Service;

use App\Application\Service\FileSystem\File;
use App\Application\Service\FileSystem\FileSystem;
use App\Application\Service\Twig\Twig;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Player;
use Doctrine\Common\Collections\ArrayCollection;

class RenderGameService {

    public function __construct(
        private Twig $twig,
        private FileSystem $fileSystem
    ) {}
    
    private Game $game;

    public function execute(
        Game $game,
        Player $player
    ): File
    {
        $this->game = $game;

        $mapTileRows = array_chunk($game->getMap()->getMapTiles()->toArray(), $game->getMap()->getColumns());

        $playerData = $game->getPlayers()->map(fn(Player $player) => [
            "name" => $player->getProfile()->getNickname(),
            "health" => $player->getPlayerStat(PlayerStatConfig::HEALTH_ID)->getLevel(),
            "energy" => $player->getPlayerStat(PlayerStatConfig::ENERGY_ID)->getLevel(),
            "attack" => $player->getPlayerStat(PlayerStatConfig::ATTACK_ID)->getLevel(),
        ])->toArray();

        $mapData = (new ArrayCollection($mapTileRows))->map(function(array $mapTiles) {
            return (new ArrayCollection($mapTiles))->map(function(MapTile $mapTile) {
                return [
                    "exits" => [
                        "top" => $mapTile->getTile()->getCanExitTop(),
                        "bottom" => $mapTile->getTile()->getCanExitBottom(),
                        "left" => $mapTile->getTile()->getCanExitLeft(),
                        "right" => $mapTile->getTile()->getCanExitRight(),
                    ],
                    "tileId" => $mapTile->getTile()->getId(),
                    "mapTileId" => $mapTile->getId(),
                    "zombies" => $this->game->getZombiesAtPosition($mapTile)->count(),
                    "players" => $this->game->getPlayersAtPosition($mapTile)->count(),
                    "hasZombies" => $this->game->getZombiesAtPosition($mapTile)->count() > 0,
                    "hasPlayer" => $this->game->getPlayersAtPosition($mapTile)->count() > 0
                ];
            });
        })->toArray();

        $content = $this->twig->render(
            "Jann/Agent",
            "RenderGameTemplate",
            [
                "map" => $mapData,
                "players" => $playerData,
                "currentMove" => $player->getMoveCount(),
                "nextMove" => $player->getGame()->getMoveCount() + 1,
                "previousMove" => $player->getGame()->getMoveCount() - 1
            ]
        );

        $file = $this->fileSystem->getJannsTrainingDirectory($game)
            ->getFile($player->getGame()->getMoveCount() . ".html");

        $file->write($content);

        return $file;
    }
}