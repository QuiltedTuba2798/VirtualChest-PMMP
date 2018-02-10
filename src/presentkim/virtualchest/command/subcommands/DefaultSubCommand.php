<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\virtualchest\VirtualChest as Plugin;
use presentkim\virtualchest\command\{
  PoolCommand, SubCommand
};
use presentkim\virtualchest\util\{
  Translation, Utils
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
                $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[0]));
            } else {
                $this->plugin->getConfig()->set('default-count', $count);
                $sender->sendMessage(Plugin::$prefix . $this->translate('success', $count));
            }
            return true;
        }
        return false;
    }
}