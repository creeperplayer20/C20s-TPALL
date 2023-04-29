<?php

declare(strict_types=1);

namespace creeperplayer20\tpall;

use creeperplayer20\tpall\jojoe77777\FormAPI\CustomForm;
use creeperplayer20\tpall\TpAll;

use pocketmine\command\{CommandSender, Command};
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {
    private static self $instance;

    protected function onLoad() : void {
        self::$instance = $this;
    }
    
    public static function getInstance() : self {
        return self::$instance;
    }
    
    public function onEnable() : void{
        $this->saveDefaultConfig();  
    }

    public function getPrefix() : string {
        return (string)$this->getConfig()->get("prefix");
    }

    public function onCommand(CommandSender $player, Command $cmd, string $label, array $args) : bool {
        if(!($player instanceof Player)) {
            $player->sendMessage($this->getPrefix() . " §cPlease use this command in-game§f!");
            return false;
        }

        if($cmd->getName() == "tpall"){
            if(!($args == null) && $args[0] == "true"){
                $tpall = new TpAll();
                $tpall->TpAll($player); 
            } else if(!($args == null) && $args[0] == "false") {
                foreach ($this->getServer()->getOnlinePlayers() as $tpPlayer) {
                    $message = $keyFromConfig = $this->getConfig()->get("after-teleport-message");
                    
                    $tpPlayer->sendMessage($message);
                    $tpPlayer->teleport($player->getPosition());
                }
                return true;
            } else {
                $player->sendMessage($this->getPrefix() . "§cPlease use /tpall <ui=true|false>");
                Sound::PlaySound($player, "note.bass");
            }
        } return true;
    }
}

?>