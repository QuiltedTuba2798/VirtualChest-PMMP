<?php

namespace presentkim\virtualchest\command\subcommands;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\CommandSender;
use presentkim\virtualchest\VirtualChest as Plugin;
use presentkim\virtualchest\command\{
  PoolCommand, SubCommand
};
use presentkim\virtualchest\container\VirtualChestContainer;
use presentkim\virtualchest\util\Translation;

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
                $container = VirtualChestContainer::getContainer($playerName = strtolower($args[0]), true);
                if ($container === null) {
                    $player = Server::getInstance()->getPlayer($playerName);
                    if ($player !== null) {
                        $container = VirtualChestContainer::getContainer($playerName = $player->getLowerCaseName(), true);
                    }
                }
                if ($container === null) {
                    $defaultCount = $this->plugin->getConfig()->get('default-count');
                    if ($defaultCount < 1) {
                        $sender->sendMessage(Plugin::$prefix . $this->translate('failure-none'));
                        return true;
                    } else {
                        $container = new VirtualChestContainer($playerName, $defaultCount);
                        VirtualChestContainer::setContainer($playerName, $container);
                    }
                }
                if ($container === null) {
                    $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid-player', $args[0]));
                } else {
                    $number = isset($args[0]) ? strtolower($args[0]) : 1;
                    $count = $container->getCount();
                    if (!is_numeric($number) || $number > $count) {
                        $sender->sendMessage(Plugin::$prefix . $this->translate('failure-invalid', $number));
                        $sender->sendMessage(Plugin::$prefix . $this->translate('count', $number));
                    } else {
                        $sender->addWindow($container->getChest($number - 1));
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