<?php

declare(strict_types=1);

namespace blugin\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use blugin\virtualchest\command\{
  PoolCommand, SubCommand
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
            if (!is_numeric($args[0])) {
                $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.num.notNumber', [$args[0]]));
            } else {
                $count = (int) $args[0];
                if($count < 0){
                    $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.num.tooSmall', [$args[0], 0]));
                } else {
                    $this->plugin->getConfig()->set('default-count', $count);
                    $sender->sendMessage($this->translate('success', (string) $count));
                }
            }
            return true;
        }
        return false;
    }
}