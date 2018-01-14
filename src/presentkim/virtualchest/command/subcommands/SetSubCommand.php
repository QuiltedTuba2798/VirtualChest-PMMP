<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use presentkim\virtualchest\{
  command\PoolCommand, VirtualChestMain as Plugin, util\Translation, command\SubCommand
};
use function presentkim\virtualchest\util\toInt;

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
    public function onCommand(CommandSender $sender, array $args){
        if (isset($args[1])) {
            $playerName = strtolower($args[0]);

            $config = $this->plugin->getConfig();

            $player = Server::getInstance()->getPlayerExact($playerName);
            $exists = $config->exists($playerName);
            if ($player === null && !$exists) {
                $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid-player', $args[0]));
            } else {
                $count = toInt($args[1], null, function (int $i){
                    return $i > 0;
                });
                $config->set($playerName, [
                  $count,
                  $config->get($playerName)[1] ?? [],
                ]);
                $sender->sendMessage(Plugin::$prefix . $this->translate('success', $playerName, $count));
            }
            return true;
        }
        return false;
    }
}