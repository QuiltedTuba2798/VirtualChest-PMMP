<?php

namespace blugin\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use blugin\virtualchest\VirtualChest;
use blugin\virtualchest\command\{
  PoolCommand, SubCommand
};
use blugin\virtualchest\util\{
  Translation, Utils
};

class MaxSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'max');
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
                $sender->sendMessage(Translation::translate('command-generic-failure@invalid', $args[0]));
            } else {
                $this->plugin->getConfig()->set('max-count', $count);
                $sender->sendMessage($this->translate('success', $count));
            }
            return true;
        }
        return false;
    }
}