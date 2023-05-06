<?php

declare(strict_types=1);

namespace creeperplayer20\tpall;

use creeperplayer20\tpall\Main;

use pocketmine\player\Player;

class Functions
{
    public static function getPrefix(): string
    {
        return Main::getInstance()->getPrefix();
    }

    public static function getConfigValue($type): string
    {
        return (string) Main::getInstance()->getConfig()->get("$type");
    }

    static function getTeleportColor($secondsLeft): string
    {
        $color = "";
        switch ($secondsLeft) {
            case 3:
                $color = Self::getConfigValue("no-three-color");
                break;
            case 2:
                $color = Self::getConfigValue("no-two-color");
                break;
            case 1:
                $color = Self::getConfigValue("no-one-color");
                break;
            case 0:
                $color = Self::getConfigValue("no-zero-color");
                break;
            default:
                $color = Self::getConfigValue("no-default-color");
                break;
        }
        return $color;
    }

    public static function changeTextColor($secondsLeft, $player): void
    {
        $timeLeft = str_replace("{seconds}", Self::getTeleportColor($secondsLeft) . Self::formatSeconds($secondsLeft), Self::getConfigValue("teleport-before-message"));
        if ($secondsLeft <= 3)
            $player->sendTip($timeLeft);
        else
            $player->sendTip($timeLeft);
    }

    public static function getTeleportReason($reason): string
    {
        $reasonText = Self::getConfigValue("reason-text");
        if ($reason == null || $reason == "")
            return $reasonText . Self::getConfigValue("reason-default");
        else
            return $reasonText . $reason;
    }

    static function formatSeconds($secondsLeft): string
    {
        if ($secondsLeft >= 60) {
            $minutes = floor($secondsLeft / 60);
            $seconds = $secondsLeft % 60;
            return $minutes . 'm ' . $seconds . 's';
        } else
            return $secondsLeft . 's';
    }

    public static function getTeleportSound($sound): string
    {
        $sounds = [
            0 => "Nothing",
            1 => "random.levelup",
            2 => "note.bass",
            3 => "note.hat",
            4 => "note.pling",
            5 => "note.snare",
            6 => "note.bd",
            7 => "note.harp",
            8 => "random.pop2",
            9 => "portal.travel",
        ];

        if (is_int($sound))
            return $sounds[$sound] ?? "Nothing";
        else
            return in_array($sound, $sounds) ? $sound : "Nothing";
    }

    public static function playTeleportSound($player, $countdownSound, $teleportSound, $secondsLeft): void
    {
        if (Self::getTeleportSound($countdownSound) != "Nothing" && !($secondsLeft <= 0))
            Sound::PlaySound($player, Self::getTeleportSound($countdownSound));
        else if (Self::getTeleportSound($teleportSound) != "Nothing" && $secondsLeft <= 0 && Functions::areEnoughPlayersOnline($player, false))
            Sound::PlaySound($player, Self::getTeleportSound($teleportSound));
    }

    public static function areEnoughPlayersOnline($player, $sendMessage): bool
    {
        if (Count(Main::getInstance()->getServer()->getOnlinePlayers()) <= 1) {
            Sound::PlaySound($player, "note.bass");
            if ($sendMessage)
                $player->sendMessage(Self::getPrefix() . Self::getConfigValue("no-players-online-message"));
            return false;
        }
        return true;
    }

    public static function isPlayerValid($player, $target): bool
    {
        if ($target instanceof Player)
            return true;
        else {
            Sound::PlaySound($player, "note.bass");
            $player->sendMessage(Self::getPrefix() . Self::getConfigValue("no-player-found-message"));
            return false;
        }
    }
}