<?php

namespace blugin\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use blugin\virtualchest\VirtualChest;
use blugin\virtualchest\command\{
  PoolCommand, SubCommand
};

class SaveSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'save');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        $this->plugin->save();
        $sender->sendMessage($this->translate('success'));

        return true;
    }
}