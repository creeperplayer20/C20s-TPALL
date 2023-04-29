<?php

declare(strict_types=1);

namespace creeperplayer20\tpall;

use creeperplayer20\tpall\jojoe77777\FormAPI\CustomForm;
use creeperplayer20\tpall\{Sound, Main};

use pocketmine\scheduler\{ClosureTask, CancelTaskException};

use pocketmine\command\{CommandSender, Command};
use pocketmine\player\Player;

use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\event\Event;

class TpAll {
    private $isRunning = false;
    private $prefix;

    public function __construct() {
        $this->prefix = Main::getInstance()->getPrefix();
    }

    public function TpAll(Player $player) {
        if($this->isRunning) {
            $player->sendMessage($this->prefix . "§cAnother TpAll task is already running!");
            return;
        }
        
        $form = new CustomForm(function(Player $player, $data) {
            if($data === null)
                return true;
            
            Sound::PlaySound($player, "note.bass");
            $prefix = Main::getInstance()->getPrefix();

            $secondsLeft = $data[0];

            $dataCS = $data[1]; // Countdown Sound
            $dataTS = $data[2]; // Teleport Sound

            Main::getInstance()->getServer()->broadcastMessage(Functions::getReason($data[3]));

            $task = new ClosureTask(function() use(&$secondsLeft, $player, $prefix, $dataCS, $dataTS) : void {
                foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $tpPlayer) {  
                    Functions::changeTextColor($secondsLeft, $prefix, $tpPlayer);
                    Functions::playTeleportSound($tpPlayer, $dataCS, $dataTS, $secondsLeft);
                }

                if($secondsLeft <= 0) {
                    foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $tpPlayer) {                                
                        if($tpPlayer == $player) {
                            $tpPlayer->sendMessage($prefix . Functions::getDataFromConfig("teleport-after-issuer-message"));
                            continue;
                        }
                        $tpPlayer->sendMessage($prefix . Functions::getDataFromConfig("teleport-after-message"));
                        $tpPlayer->teleport($player->getPosition());
                        $this->isRunning = false;
                    }
                    throw new CancelTaskException;                     
                }
                $secondsLeft--;
            });
            $this->isRunning = true;
            Main::getInstance()->getScheduler()->scheduleRepeatingTask($task, 20);
        });
        $form->setTitle(Functions::getDataFromConfig("ui-title"));
        $form->addSlider("Seconds", 10, 300);
        $form->addDropdown("Countdown sound", ["Nothing", "random.levelup", "note.bass", "note.hat", "note.pling", "note.snare"]);
        $form->addDropdown("Teleport sound", ["Nothing", "random.levelup", "note.bass", "note.hat", "note.pling", "note.snare"]);
        $form->addInput("Reason", "§6It's event time§f!");
        
        $player->sendForm($form);
    }
}

?>