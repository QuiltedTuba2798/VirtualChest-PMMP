<?php

namespace blugin\virtualchest\command\subcommands;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use onebone\economyapi\EconomyAPI;
use blugin\mathparser\MathParser;
use blugin\virtualchest\VirtualChest;
use blugin\virtualchest\command\{
  PoolCommand, SubCommand
};
use blugin\virtualchest\container\VirtualChestContainer;
use blugin\virtualchest\util\Translation;

class BuySubCommand extends SubCommand{

    /** @var int[] */
    private $checked = [];

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'buy');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if ($sender instanceof Player) {
            $config = $this->plugin->getConfig();
            $container = VirtualChestContainer::getContainer($playerName = $sender->getLowerCaseName(), true);
            $count = $container === null ? $config->get('default-count') : $container->getCount();
            if ($count >= (int) $config->get('max-count')) {
                $sender->sendMessage($this->translate('failure-max'));
                return true;
            } else {
                $economyAPI = EconomyAPI::getInstance();
                $myMoney = $economyAPI->myMoney($playerName);
                $price = $this->getPrice($count, $myMoney);
                if ($price === null) {
                    $sender->sendMessage($this->translate('failure-prevent'));
                    return true;
                } elseif (!isset($this->checked[$playerName]) || (time() - $this->checked[$playerName]) > 10) {
                    $this->checked[$playerName] = time();
                    $sender->sendMessage($this->translate('check', $price));
                } else {
                    unset($this->checked[$playerName]);
                    if ($myMoney < $price) {
                        $sender->sendMessage($this->translate('failure-money', $myMoney));
                    } else {
                        $economyAPI->reduceMoney($playerName, $price);
                        if ($container === null) {
                            $container = new VirtualChestContainer($playerName, $count + 1);
                            VirtualChestContainer::setContainer($playerName, $container);
                        } else {
                            $container->setCount($count + 1);
                        }
                        $sender->sendMessage($this->translate('success', $myMoney - $price));
                    }
                }
            }
        } else {
            $sender->sendMessage(Translation::translate('command-generic-failure@in-game'));
        }
        return true;
    }

    /**
     * @param int $count
     * @param int $money
     *
     * @return int|null
     */
    public function getPrice(int $count, int $money) : ?int{
        $price = $this->plugin->getConfig()->get('price');
        if (class_exists(MathParser::class)) {
            try{
                $price = MathParser::parse($price, [
                  'c' => $count,
                  'm' => $money,
                ]);
            } catch (\Exception $exception){
                $this->plugin->getLogger()->critical("{$exception->getMessage()}. Call in buy sub command");
                return null;
            }
        } elseif (!is_numeric($price)) {
            $this->plugin->getLogger()->critical("Syntax error: '{$price}' is not number. Call in buy sub command");
            return null;
        } elseif ($price < 0) {
            return null;
        }
        return (int) $price;
    }
}