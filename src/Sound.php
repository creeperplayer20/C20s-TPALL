<?php

declare(strict_types=1);

namespace creeperplayer20\tpall;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\math\Vector3;

class Sound {
    public static function PlaySound(Player $player, $sound){
        $packet = new PlaySoundPacket();

        $packet->x = $player->getPosition()->getX();
        $packet->y = $player->getPosition()->getY();
        $packet->z = $player->getPosition()->getZ();
        
        $packet->soundName = "$sound";
        $packet->volume = 100;
        $packet->pitch = 1.2;

        $player->getNetworkSession()->sendDataPacket($packet);
    }
}