<?php

declare(strict_types=1);

namespace kim\present\virtualchest\command\subcommands;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use kim\present\virtualchest\command\{
  PoolCommand, SubCommand
};
use kim\present\virtualchest\container\VirtualChestContainer;

class SetSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'set');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (isset($args[1])) {
            $container = VirtualChestContainer::getContainer($playerName = strtolower($args[0]), true);
            if ($container === null) {
                $player = Server::getInstance()->getPlayer($playerName);
                if ($player !== null) {
                    $container = VirtualChestContainer::getContainer($playerName = $player->getLowerCaseName(), true);
                }
            }
            if ($container === null) {
                $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.player.notFound', [$args[0]]));
            } elseif (!is_numeric($args[1])) {
                $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.num.notNumber', [$args[1]]));
            } else {
                $count = (int) $args[1];
                if($count < 0){
                    $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.num.tooSmall', [$args[1], 0]));
                } else {
                    $container->setCount($count);
                    $sender->sendMessage($this->translate('success', $playerName, (string) $count));
                }
            }
            return true;
        }
        return false;
    }
}