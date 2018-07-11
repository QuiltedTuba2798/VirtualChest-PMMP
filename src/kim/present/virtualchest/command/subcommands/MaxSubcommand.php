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
	PoolCommand, Subcommand
};
use pocketmine\command\CommandSender;

class MaxSubcommand extends Subcommand{
	/**
	 * MaxSubcommand constructor.
	 *
	 * @param PoolCommand $owner
	 */
	public function __construct(PoolCommand $owner){
		parent::__construct($owner, 'max');
	}

	/**
	 * @param CommandSender $sender
	 * @param String[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, array $args) : bool{
		if(isset($args[0])){
			if(!is_numeric($args[0])){
				$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.generic.num.notNumber', [$args[0]]));
			}else{
				$count = (int) $args[0];
				if($count < 0){
					$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.generic.num.tooSmall', [$args[0], "0"]));
				}else{
					$this->plugin->getConfig()->set('max-count', $count);
					$sender->sendMessage($this->translate('success', (string) $count));
				}
			}
			return true;
		}
		return false;
	}
}