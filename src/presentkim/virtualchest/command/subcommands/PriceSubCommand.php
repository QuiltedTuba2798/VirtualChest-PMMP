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

class PriceSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'price');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (isset($args[0])) {
            $price = Utils::toInt($args[0], null, function (int $i){
                return $i >= -1;
            });
            if ($price === null) {
                $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[0]));
            } else {
                $this->plugin->getConfig()->set('price', $price);
                $sender->sendMessage(Plugin::$prefix . $this->translate('success', $price));
            }
            return true;
        } else {
            return false;
        }
    }
}