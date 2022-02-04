<?php

declare(strict_types=1);

namespace creeperplayer20\tpall;

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\command\Command;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Main extends PluginBase{

public function onDisable() : void{
    
    $this->getLogger()->info("[§aC20§r - §6TPALL§r] §4Plugin is disabled!");
        
}

public function onCommand(CommandSender $sender,Command $cmd,string $label,array $args) : bool{

    if($cmd->getName() == "tpall"){

    foreach ($this->getServer()->getOnlinePlayers() as $tpPlayer) {

        $tpPlayer->sendMessage("[§6TpAll§r§f] " . $keyFromConfig = $this->getConfig()->get("message"));
        $tpPlayer->teleport($sender->getPosition());

} return true;

}}}
