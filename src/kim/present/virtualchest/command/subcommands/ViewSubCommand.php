<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0.0
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\virtualchest\command\subcommands;

use kim\present\virtualchest\command\{
	PoolCommand, SubCommand
};
use kim\present\virtualchest\container\VirtualChestContainer;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

class ViewSubCommand extends SubCommand{
	/**
	 * ViewSubCommand constructor.
	 *
	 * @param PoolCommand $owner
	 */
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
		if($sender instanceof Player){
			if(isset($args[0])){
				$container = VirtualChestContainer::getContainer($playerName = strtolower($args[0]), true);
				if($container === null){
					$player = Server::getInstance()->getPlayer($playerName);
					if($player !== null){
						$container = VirtualChestContainer::getContainer($playerName = $player->getLowerCaseName(), true);
					}
				}
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
				if($container === null){
					$sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.player.notFound', [$args[0]]));
				}else{
					$number = isset($args[1]) ? strtolower($args[1]) : 1;
					$count = $container->getCount();
					if(!is_numeric($number) || $number > $count){
						$sender->sendMessage($this->translate('failure.invalid', $number));
						$sender->sendMessage($this->translate('count', $playerName, (string) $count));
					}else{
						$sender->addWindow($container->getChest($number - 1));
					}
				}
			}else{
				return false;
			}
		}else{
			$sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.onlyPlayer'));
		}
		return true;
	}
}