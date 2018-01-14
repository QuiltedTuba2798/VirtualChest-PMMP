<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\virtualchest\{
  command\PoolCommand, VirtualChestMain as Plugin, command\SubCommand
};

class SaveSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'save');
    }

    /**
     * @param CommandSender $sender
     * @param array         $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        $this->plugin->save();
        $sender->sendMessage(Plugin::$prefix . $this->translate('success'));

        return true;
    }
}