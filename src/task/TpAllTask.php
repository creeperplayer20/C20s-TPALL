<?php

declare(strict_types=1);

namespace creeperplayer20\tpall\task;

use creeperplayer20\tpall\{Main, Functions, TpAll, Sound};

use pocketmine\scheduler\{ClosureTask, CancelTaskException, Task};

class TpAllTask extends Task
{
    private $prefix = "";
    public function __construct()
    {
        $this->prefix = Main::getInstance()->getPrefix();
    }

    public function startTpAllTask($secondsLeft, $playerPick, $countdownSound, $teleportSound, $callback): void
    {
        $task = new ClosureTask(function () use (&$secondsLeft, $playerPick, $countdownSound, $teleportSound, $callback): void {
            foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $tpPlayer) {
                Functions::changeTextColor($secondsLeft, $tpPlayer);
                Functions::playTeleportSound($tpPlayer, $countdownSound, $teleportSound, $secondsLeft);
            }
            if ($secondsLeft <= 0) {
                call_user_func($callback);
                if (!$playerPick->isOnline()) {
                    foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $tpPlayer) {
                        Sound::PlaySound($tpPlayer, "note.bell");
                        $tpPlayer->sendMessage($this->prefix . Functions::getConfigValue("teleport-error-message"));
                    }
                    throw new CancelTaskException;
                }
                if (!Functions::areEnoughPlayersOnline($playerPick, true))
                    throw new CancelTaskException;
                foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $tpPlayer) {
                    if ($tpPlayer == $playerPick) {
                        $tpPlayer->sendMessage($this->prefix . Functions::getConfigValue("teleport-after-issuer-message"));
                        continue;
                    }
                    $tpPlayer->sendMessage($this->prefix . Functions::getConfigValue("teleport-after-message"));
                    $tpPlayer->teleport($playerPick->getPosition());
                }
                throw new CancelTaskException;
            }
            $secondsLeft--;
        });
        Main::getInstance()->getScheduler()->scheduleRepeatingTask($task, 20);
    }

    public function onRun(): void {}
}