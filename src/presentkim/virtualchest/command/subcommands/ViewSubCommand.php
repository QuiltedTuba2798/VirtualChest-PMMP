<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\{
  Player, item\ItemFactory, command\CommandSender, Server
};
use presentkim\virtualchest\{
  command\PoolCommand, inventory\VirtualChestInventory, VirtualChest as Plugin, util\Translation, command\SubCommand
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
    public function onCommand(CommandSender $sender, array $args) : bool{
        if ($sender instanceof Player) {
            if (isset($args[0])) {
                $playerName = strtolower($args[0]);

                $player = Server::getInstance()->getPlayerExact($playerName);
                $datas = $this->plugin->getConfig()->get('playerData');
                if ($player === null && !isset($datas[$playerName])) {
                    $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid-player', $args[0]));
                } else {
                    if (!isset($datas[$playerName]) || $datas[$playerName][0] <= 0) {
                        $sender->sendMessage(Plugin::$prefix . $this->translate('failure-none'));
                    } else {
                        $number = isset($args[1]) ? strtolower($args[1]) : 1;
                        if (!is_numeric($number) || ($index = (int) $number - 1) >= $datas[$playerName][0]) {
                            $sender->sendMessage(Plugin::$prefix . $this->translate('failure-invalid', $number));
                            $sender->sendMessage(Plugin::$prefix . $this->translate('count', $player === null ? $playerName : $player->getName(), $datas[$playerName][0]));
                        } else {
                            if (!isset(VirtualChestInventory::$vchests[$playerName][$index])) {
                                if (!isset(VirtualChestInventory::$vchests[$playerName])) {
                                    VirtualChestInventory::$vchests[$playerName] = [];
                                }
                                $items = [];
                                if (isset($datas[$playerName][1][$index]) && is_array($datas[$playerName][1][$index])) {
                                    try{
                                        foreach ($datas[$playerName][1][$index] as $key => $value) {
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
            } else {
                return false;
            }
        } else {
            $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@in-game'));
        }
        return true;
    }
}