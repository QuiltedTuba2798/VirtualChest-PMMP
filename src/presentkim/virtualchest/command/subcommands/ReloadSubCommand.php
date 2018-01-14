<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\virtualchest\{
  command\PoolCommand, VirtualChestMain as Plugin, command\SubCommand
};

class ReloadSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'reload');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        $this->plugin->load();
        $sender->sendMessage(Plugin::$prefix . $this->translate('success'));

        return true;
    }
}