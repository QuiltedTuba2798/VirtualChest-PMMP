<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\{
  Player, item\ItemFactory, command\CommandSender
};
use presentkim\virtualchest\{
  command\PoolCommand, inventory\VirtualChestInventory, VirtualChestMain as Plugin, util\Translation, command\SubCommand
};

class OpenSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'open');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        if ($sender instanceof Player) {
            $playerName = strtolower($sender->getName());

            $config = $this->plugin->getConfig();

            $datas = $config->get('playerData');
            if (!isset($datas[$playerName]) && ($default = $config->get('default-count')) > 0) {
                $datas[$playerName][0] = $default;
                $config->set('playerData', $datas);
            }
            if ($datas[$playerName][0] <= 0) {
                $sender->sendMessage(Plugin::$prefix . $this->translate('failure-none'));
            } else {
                $number = isset($args[0]) ? strtolower($args[0]) : 1;
                if (!is_numeric($number) || ($index = (int) $number - 1) >= $datas[$playerName][0]) {
                    $sender->sendMessage(Plugin::$prefix . $this->translate('failure-invalid', $number));
                    $sender->sendMessage(Plugin::$prefix . $this->translate('count', $datas[$playerName][0]));
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
                        VirtualChestInventory::$vchests[$playerName][$index] = new VirtualChestInventory($sender->getName(), $number, $items);
                    }
                    $sender->addWindow(VirtualChestInventory::$vchests[$playerName][$index]);
                }
            }
        } else {
            $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@in-game'));
        }
        return true;
    }
}