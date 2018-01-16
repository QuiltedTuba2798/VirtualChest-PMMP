<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\{
  Player, item\ItemFactory, command\CommandSender, Server
};
use presentkim\virtualchest\{
  command\PoolCommand, inventory\VirtualChestInventory, VirtualChestMain as Plugin, util\Translation, command\SubCommand
};

class ViewSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'view');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        if ($sender instanceof Player) {
            if (isset($args[0])) {
                $playerName = strtolower($args[0]);

                $config = $this->plugin->getConfig();

                $player = Server::getInstance()->getPlayerExact($playerName);
                $exists = $config->exists($playerName);
                if ($player === null && !$exists) {
                    $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid-player', $args[0]));
                } else {
                    $data = $this->plugin->getConfig()->get($playerName);
                    if ($data === false) {
                        $sender->sendMessage(Plugin::$prefix . $this->translate('failure-none'));
                    } else {
                        $number = isset($args[1]) ? strtolower($args[1]) : 1;
                        if (!is_numeric($number) || ($index = (int) $number - 1) >= $data[0]) {
                            $sender->sendMessage(Plugin::$prefix . $this->translate('failure-invalid', $number));
                            $sender->sendMessage(Plugin::$prefix . $this->translate('count', $player === null ? $playerName : $player->getName(),$data[0]));
                        } else {
                            if (!isset(VirtualChestInventory::$vchests[$playerName][$index])) {
                                if (!isset(VirtualChestInventory::$vchests[$playerName])) {
                                    VirtualChestInventory::$vchests[$playerName] = [];
                                }
                                $items = [];
                                if (isset($data[1][$index]) && is_array($data[1][$index])) {
                                    try{
                                        foreach ($data[1][$index] as $key => $value) {
                                            if (is_array($value)) {
                                                $args = explode(':', $value[0]);
                                                if (isset($value[1])) {
                                                    $args[] = $value[1];
                                                }
                                                $items[$key] = ItemFactory::get(...$args);
                                            } else {
                                                $items[$key] = ItemFactory::get(...explode(':', $value));
                                            }
                                        }
                                    } catch (\Error $e){
                                        $this->plugin->getLogger()->error($e);
                                    }
                                }
                                VirtualChestInventory::$vchests[$playerName][$index] = new VirtualChestInventory($player === null ? $playerName : $player->getName(), $number, $items);
                            }
                            $sender->addWindow(VirtualChestInventory::$vchests[$playerName][$index]);
                        }
                    }
                }
            }else{
                return false;
            }
        } else {
            $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@in-game'));
        }
        return true;
    }
}