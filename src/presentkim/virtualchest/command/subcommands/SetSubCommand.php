<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\{
  Server, command\CommandSender
};
use presentkim\virtualchest\{
  command\PoolCommand, util\Utils, VirtualChestMain as Plugin, util\Translation, command\SubCommand
};

class SetSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'set');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (isset($args[1])) {
            $playerName = strtolower($args[0]);

            $config = $this->plugin->getConfig();

            $player = Server::getInstance()->getPlayerExact($playerName);
            $datas = $config->get('playerData');
            if ($player === null && !isset($datas[$playerName])) {
                $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid-player', $args[0]));
            } else {
                $count = Utils::toInt($args[1], null, function (int $i){
                    return $i > 0;
                });
                if ($count === null) {
                    $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[1]));
                } else {
                    $datas[$playerName] = [
                      $count,
                      $datas[$playerName][1] ?? [],
                    ];
                    $config->set('playerData', $datas);
                    $sender->sendMessage(Plugin::$prefix . $this->translate('success', $player === null ? $playerName : $player->getName(), $count));
                }
            }
            return true;
        }
        return false;
    }
}