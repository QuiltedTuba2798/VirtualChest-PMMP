<?php

namespace blugin\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use blugin\mathparser\MathParser;
use blugin\virtualchest\VirtualChest;
use blugin\virtualchest\command\{
  PoolCommand, SubCommand
};
use blugin\virtualchest\util\{
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
            if (class_exists(MathParser::class)) {
                try{
                    $price = implode(' ', $args);
                    MathParser::parse($price, [
                      'c' => 1, //count
                      'm' => 1, //money
                    ]);
                } catch (\Exception $exception){
                    $price = null;
                    $this->plugin->getLogger()->critical("{$exception->getMessage()}. Call in price sub command");
                }
            } else {
                $price = Utils::toInt($args[0], null, function (int $i){
                    return $i >= -1;
                });
            }
            if ($price === null) {
                $sender->sendMessage(Translation::translate('command-generic-failure@invalid', $args[0]));
            } else {
                $this->plugin->getConfig()->set('price', $price);
                $sender->sendMessage($this->translate('success', $price));
            }
            return true;
        } else {
            return false;
        }
    }
}