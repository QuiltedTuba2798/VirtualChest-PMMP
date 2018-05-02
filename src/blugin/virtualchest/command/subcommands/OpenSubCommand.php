<?php

declare(strict_types=1);

namespace blugin\virtualchest\command\subcommands;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use blugin\virtualchest\VirtualChest;
use blugin\virtualchest\command\{
  PoolCommand, SubCommand
};
use blugin\virtualchest\container\VirtualChestContainer;
use blugin\virtualchest\util\Translation;

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
    public function onCommand(CommandSender $sender, array $args) : bool{
        if ($sender instanceof Player) {
            $container = VirtualChestContainer::getContainer($playerName = $sender->getLowerCaseName(), true);
            if ($container === null) {
                $defaultCount = $this->plugin->getConfig()->get('default-count');
                if ($defaultCount < 1) {
                    $sender->sendMessage($this->translate('failure-none'));
                    return true;
                } else {
                    $container = new VirtualChestContainer($playerName, $defaultCount);
                    VirtualChestContainer::setContainer($playerName, $container);
                }
            }
            $number = isset($args[0]) ? strtolower($args[0]) : 1;
            $count = $container->getCount();
            if (!is_numeric($number) || $number > $count) {
                $sender->sendMessage($this->translate('failure-invalid', $number));
                $sender->sendMessage($this->translate('count', $count));
            } else {
                $sender->addWindow($container->getChest($number - 1));
            }
        } else {
            $sender->sendMessage(Translation::translate('command-generic-failure@in-game'));
        }
        return true;
    }
}