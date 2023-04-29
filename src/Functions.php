<?php

declare(strict_types=1);

namespace creeperplayer20\tpall;

use creeperplayer20\tpall\{Tpall, Main};

class Functions {
    public static function getDataFromConfig($type) : string {
        return (string)Main::getInstance()->getConfig()->get("$type");
    }

    static function getColor($secondsLeft) : string {
        $color = "";
        switch ($secondsLeft) {
            case 3:
                $color = Self::getDataFromConfig("no-three-color");
            break;
            case 2:
                $color = Self::getDataFromConfig("no-two-color");
            break;
            case 1:
                $color = Self::getDataFromConfig("no-one-color");
            break;
            case 0:
                $color = Self::getDataFromConfig("no-zero-color");
            break;
            default:
                $color = Self::getDataFromConfig("no-default-color");
            break;
        }
        return $color;
    }

    public static function changeTextColor($secondsLeft, $prefix, $player) {
        $timeLeft = Self::getSeconds($secondsLeft);
        $timeLeft = str_replace("{seconds}", Functions::getColor($secondsLeft) . $timeLeft, Functions::getDataFromConfig("teleport-before-message"));
        if ($secondsLeft <= 3)
            $player->sendTip($timeLeft);
        else
            $player->sendTip($timeLeft);
    }

    public static function getReason($reason) : string {
        $replacedReason = str_replace("{prefix}", Self::getDataFromConfig("prefix"), Self::getDataFromConfig("reason-text"));
        if($reason == null || $reason == "")          
            return $replacedReason . Self::getDataFromConfig("reason-default");
        else
            return $replacedReason . $reason;
    }

    static function getSeconds($secondsLeft) : string {
        if ($secondsLeft >= 60) {
            $minutes = floor($secondsLeft / 60);
            $seconds = $secondsLeft % 60;
            return $minutes . 'm ' . $seconds . 's';
        } else
            return $secondsLeft . 's';
    }


    static function getSound($sound) : string {
        $sounds = [
            0 => "Nothing",
            1 => "random.levelup",
            2 => "note.bass",
            3 => "note.hat",
            4 => "note.pling",
            5 => "note.snare",
        ];
        return $sounds[$sound];
    }

    public static function playTeleportSound($player, $dataCS, $dataTS, $secondsLeft) {
        if(Functions::getSound($dataCS) != "Nothing" && !($secondsLeft <= 0))
            Sound::PlaySound($player, Functions::getSound($dataCS));
        else if(Functions::getSound($dataTS) != "Nothing" && $secondsLeft <= 0)
            Sound::PlaySound($player, Functions::getSound($dataTS));
        return;
    }
}

?>