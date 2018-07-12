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

namespace kim\present\virtualchest\command;

use kim\present\mathparser\MathParser;
use kim\present\virtualchest\container\VirtualChestContainer;
use kim\present\virtualchest\VirtualChest;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class BuySubcommand extends Subcommand{
	/** @var int[] */
	private $checked = [];

	/**
	 * BuySubcommand constructor.
	 *
	 * @param VirtualChest $plugin
	 */
	public function __construct(VirtualChest $plugin){
		parent::__construct($plugin, 'buy');
	}

	/**
	 * @param CommandSender $sender
	 * @param String[]      $args = []
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args = []) : bool{
		if($sender instanceof Player){
			$config = $this->plugin->getConfig();
			$container = VirtualChestContainer::getContainer($playerName = $sender->getLowerCaseName(), true);
			$count = $container === null ? $config->get('default-count') : $container->getCount();
			if($count >= (int) $config->get('max-count')){
				$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.virtualchest.buy.failure.max'));
				return true;
			}else{
				$economyAPI = EconomyAPI::getInstance();
				$myMoney = (int) $economyAPI->myMoney($playerName);
				$price = $this->getPrice($count, $myMoney);
				if($price === null){
					$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.virtualchest.buy.failure.prevent'));
					return true;
				}elseif(!isset($this->checked[$playerName]) || (time() - $this->checked[$playerName]) > 10){
					$this->checked[$playerName] = time();
					$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.virtualchest.buy.check', [(string) $price]));
				}else{
					unset($this->checked[$playerName]);
					if($myMoney < $price){
						$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.virtualchest.buy.failure.money', [(string) $myMoney]));
					}else{
						$economyAPI->reduceMoney($playerName, $price);
						if($container === null){
							$container = new VirtualChestContainer($playerName, $count + 1);
							VirtualChestContainer::setContainer($playerName, $container);
						}else{
							$container->setCount($count + 1);
						}
						$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.virtualchest.buy.success', [(string) ($myMoney - $price)]));
					}
				}
			}
		}else{
			$sender->sendMessage($this->plugin->getLanguage()->translateString('commands.generic.onlyPlayer'));
		}
		return true;
	}

	/**
	 * @param int $count
	 * @param int $money
	 *
	 * @return int|null
	 */
	public function getPrice(int $count, int $money) : ?int{
		$price = $this->plugin->getConfig()->get('price');
		if(class_exists(MathParser::class)){
			try{
				$price = MathParser::parse($price, [
					'c' => $count,
					'm' => $money,
				]);
			}catch(\Exception $exception){
				$this->plugin->getLogger()->critical("{$exception->getMessage()}. Call in buy sub command");
				return null;
			}
		}elseif(!is_numeric($price)){
			$this->plugin->getLogger()->critical("Syntax error: '{$price}' is not number. Call in buy sub command");
			return null;
		}elseif($price < 0){
			return null;
		}
		return (int) $price;
	}
}