<?php

declare(strict_types=1);

namespace creeperplayer20\tpall;

use creeperplayer20\tpall\{TpAll, Sound, Functions};

use pocketmine\command\{CommandSender, Command};
use pocketmine\player\Player;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    private TpAll $tpAll;
    private static self $instance;

    protected function onLoad(): void
    {
        self::$instance = $this;
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->tpAll = new TpAll();
    }

    public function getPrefix(): string
    {
        return "§f[§6TpAll§r§f] ";
    }

    public function onCommand(CommandSender $player, Command $cmd, string $label, array $args): bool
    {
        if (!($player instanceof Player)) {
            $player->sendMessage($this->getPrefix() . " §cPlease use this command in-game§f!");
            return false;
        }

        if ($cmd->getName() == "tpall") {
            if (isset($args[0]) && $args[0] === 'true') {
                $this->tpAll->TpAllUI($player);
                return true;
            } else if (isset($args[0]) && $args[0] === "false") {
                if (!Functions::areEnoughPlayersOnline($player, true) || $this->tpAll->isTpAllRunning($player))
                    return true;
                $this->tpAll->TpAllLegacy($player);
                return true;
            } else {
                Sound::PlaySound($player, "note.bass");
                $player->sendMessage($this->getPrefix() . "§cPlease use /tpall <ui=true|false>");
                return true;
            }
        }
        return true;
    }
}