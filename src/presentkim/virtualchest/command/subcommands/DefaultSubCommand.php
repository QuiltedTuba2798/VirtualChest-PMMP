<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\{
  Server, command\CommandSender
};
use presentkim\virtualchest\{
  command\PoolCommand, util\Utils, VirtualChestMain as Plugin, util\Translation, command\SubCommand
};

class DefaultSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'default');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (isset($args[0])) {
            $count = Utils::toInt($args[0], null, function (int $i){
                return $i > 0;
            });
            if ($count === null) {
                $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[1]));
            } else {
                $this->plugin->getConfig()->set('default-count', $count);
                $sender->sendMessage(Plugin::$prefix . $this->translate('success', $count));
            }
            return true;
        }
        return false;
    }
}