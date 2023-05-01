<?php

declare(strict_types=1);

namespace creeperplayer20\tpall;

use creeperplayer20\tpall\jojoe77777\FormAPI\CustomForm;
use creeperplayer20\tpall\{Sound, Main, Functions};
use creeperplayer20\tpall\task\TpAllTask;

use pocketmine\player\Player;

class TpAll
{
    public $isTpAllRunning = false;
    private $playersList = [];

    function __construct()
    {
        $this->prefix = Main::getInstance()->getPrefix();
        $this->tpAllTask = new TpAllTask();
    }

    public function TpAllUI(Player $player)
    {
        if($this->isTpAllRunning($player))
            return;

        $playerList = [];
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $playerPick) {
            $playerList[] = $playerPick->getName();
        }
        $this->playersList[$player->getName()] = $playerList;

        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null)
                return true;

            $secondsLeft = $data[0];
            $countdownSound = $data[1];
            $teleportSound = $data[2];
            $playerPick = Main::getInstance()->getServer()->getPlayerByPrefix($this->playersList[$player->getName()][$data[3]]);

            if (!Functions::isPlayerValid($player, $playerPick) || $this->isTpAllRunning($player))
                return true;

            $this->isTpAllRunning = true;

            $tpAll = $this;
            $this->tpAllTask->startTpAllTask($secondsLeft, $playerPick, $countdownSound, $teleportSound, function () use ($tpAll) {
                $tpAll->isTpAllRunning = false;
            });
            Sound::PlaySound($playerPick, "note.bell");
            Sound::PlaySound($player, "note.bell");

            Main::getInstance()->getServer()->broadcastMessage(Functions::getTeleportReason($data[4]));
        });
        $form->setTitle(Functions::getConfigValue("ui-title"));
        $form->addSlider("Seconds", 10, 300);
        $form->addDropdown("Countdown sound:", ["Nothing", "random.levelup", "note.bass", "note.hat", "note.pling", "note.snare", "note.bd", "note.harp"]);
        $form->addDropdown("Teleport sound:", ["Nothing", "random.levelup", "note.bass", "note.hat", "note.pling", "note.snare", "note.bd", "note.harp", "portal.travel"]);
        $form->addDropdown("Teleport to:", $this->playersList[$player->getName()]);
        $form->addInput("Reason:", "§6It's event time§f!");

        $player->sendForm($form);
    }

    public function TpAllLegacy(Player $player)
    {
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $tpPlayer) {
            Sound::PlaySound($tpPlayer, Functions::getTeleportSound(Functions::getConfigValue("legacy-teleport-sound")));
            if ($tpPlayer == $player) {
                $tpPlayer->sendMessage($this->prefix . Functions::getConfigValue("teleport-after-issuer-message"));
                continue;
            }
            $tpPlayer->sendMessage($this->prefix . Functions::getConfigValue("teleport-after-message"));
            $tpPlayer->teleport($player->getPosition());
        }
    }

    public function isTpAllRunning($player): bool
    {
        if ($this->isTpAllRunning) {
            Sound::PlaySound($player, "note.bass");
            $player->sendMessage($this->prefix . Functions::getConfigValue("tpall-task-is-running-message"));
            return true;
        }
        return false;
    }
}