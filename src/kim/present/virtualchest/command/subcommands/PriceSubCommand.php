<?php

declare(strict_types=1);

namespace kim\present\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use kim\present\mathparser\MathParser;
use kim\present\virtualchest\command\{
  PoolCommand, SubCommand
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
            $price = null;
            if (class_exists(MathParser::class)) {
                try{
                    $price = implode(' ', $args);
                    MathParser::parse($price, [
                      'c' => 1, //count
                      'm' => 1, //money
                    ]);
                } catch (\Exception $exception){
                    $this->plugin->getLogger()->critical("{$exception->getMessage()}. Call in price sub command");
                }
            } else {
                if (!is_numeric($args[0])) {
                    $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.num.notNumber', [$args[0]]));
                } elseif(((int) $args[0]) < -1) {
                    $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.num.tooSmall', [$args[0], -1]));
                } else {
                    $price = (string) ((int) $args[0]);
                }
            }
            if ($price === null) {
                $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.num.notNumber', [$args[0]]));
            } else {
                $this->plugin->getConfig()->set('price', $price);
                $sender->sendMessage($this->translate('success', (string) $price));
            }
            return true;
        } else {
            return false;
        }
    }
}