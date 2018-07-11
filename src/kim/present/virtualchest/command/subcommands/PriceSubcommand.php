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

use kim\present\mathparser\MathParser;
use kim\present\virtualchest\command\{
	PoolCommand, Subcommand
};
use pocketmine\command\CommandSender;

class PriceSubcommand extends Subcommand{
	/**
	 * PriceSubcommand constructor.
	 *
	 * @param PoolCommand $owner
	 */
	public function __construct(PoolCommand $owner){
		parent::__construct($owner, 'price');
	}

	/**
	 * @param CommandSender $sender
	 * @param String[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, array $args) : bool{
		if(isset($args[0])){
			$price = null;
			if(class_exists(MathParser::class)){
				try{
					$price = implode(' ', $args);
					MathParser::parse($price, [
						'c' => 1, //count
						'm' => 1  //money
					]);
				}catch(\Exception $exception){
					$this->plugin->getLogger()->critical("{$exception->getMessage()}. Call in price sub command");
				}
			}else{
				if(!is_numeric($args[0])){
					$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.generic.num.notNumber', [$args[0]]));
				}elseif(((int) $args[0]) < -1){
					$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.generic.num.tooSmall', [$args[0], "-1"]));
				}else{
					$price = (string) ((int) $args[0]);
				}
			}
			if($price === null){
				$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.generic.num.notNumber', [$args[0]]));
			}else{
				$this->plugin->getConfig()->set('price', $price);
				$sender->sendMessage($this->translate('success', (string) $price));
			}
			return true;
		}else{
			return false;
		}
	}
}