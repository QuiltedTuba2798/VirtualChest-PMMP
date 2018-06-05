<?php

declare(strict_types=1);

namespace kim\present\virtualchest\command\subcommands;

use kim\present\virtualchest\command\{
	PoolCommand, SubCommand
};
use kim\present\virtualchest\container\VirtualChestContainer;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class OpenSubCommand extends SubCommand{
	/**
	 * OpenSubCommand constructor.
	 *
	 * @param PoolCommand $owner
	 */
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
		if($sender instanceof Player){
			$container = VirtualChestContainer::getContainer($playerName = $sender->getLowerCaseName(), true);
			if($container === null){
				$defaultCount = (int) $this->plugin->getConfig()->get('default-count');
				if($defaultCount < 1){
					$sender->sendMessage($this->translate('failure.none'));
					return true;
				}else{
					$container = new VirtualChestContainer($playerName, $defaultCount);
					VirtualChestContainer::setContainer($playerName, $container);
				}
			}
			$number = isset($args[0]) ? strtolower($args[0]) : 1;
			$count = $container->getCount();
			if(!is_numeric($number) || $number > $count){
				$sender->sendMessage($this->translate('failure.invalid', $number));
				$sender->sendMessage($this->translate('count', (string) $count));
			}else{
				$sender->addWindow($container->getChest($number - 1));
			}
		}else{
			$sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.onlyPlayer'));
		}
		return true;
	}
}