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

use kim\present\virtualchest\util\Utils;
use kim\present\virtualchest\VirtualChest;
use pocketmine\command\CommandSender;
use pocketmine\Server;

abstract class SubCommand{
	/** @var PoolCommand */
	protected $owner;

	/** @var VirtualChest */
	protected $plugin;

	/** @var string */
	protected $strId;

	/** @var string */
	protected $permission;

	/** @var string */
	protected $label;

	/** @var string[] */
	protected $aliases;

	/** @var string */
	protected $usage;

	/**
	 * SubCommand constructor.
	 *
	 * @param PoolCommand $owner
	 * @param string      $label
	 */
	public function __construct(PoolCommand $owner, string $label){
		$this->owner = $owner;
		$this->plugin = $owner->getPlugin();

		$this->strId = "commands.{$owner->uname}.{$label}";
		$this->permission = "{$owner->uname}.cmd.{$label}";

		$this->label = $this->plugin->getLanguage()->translate($this->strId);
		$this->aliases = $this->plugin->getLanguage()->getArray("{$this->strId}.aliases");
		$this->usage = $this->translate('usage');
	}

	/**
	 * @param string   $tag
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function translate(string $tag, string ...$params) : string{
		return $this->plugin->getLanguage()->translate("{$this->strId}.{$tag}", $params);
	}

	/**
	 * @param CommandSender $sender
	 * @param String[]      $args
	 */
	public function execute(CommandSender $sender, array $args) : void{
		if(!$this->checkPermission($sender)){
			$sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.permission'));
		}elseif(!$this->onCommand($sender, $args)){
			$sender->sendMessage(Server::getInstance()->getLanguage()->translateString("commands.generic.usage", [$this->usage]));
		}
	}

	/**
	 * @param CommandSender $target
	 *
	 * @return bool
	 */
	public function checkPermission(CommandSender $target) : bool{
		if($this->permission === null){
			return true;
		}else{
			return $target->hasPermission($this->permission);
		}
	}

	/**
	 * @param CommandSender $sender
	 * @param String[]      $args
	 *
	 * @return bool
	 */
	abstract public function onCommand(CommandSender $sender, array $args) : bool;

	/**
	 * @param string $label
	 *
	 * @return bool
	 */
	public function checkLabel(string $label) : bool{
		return strcasecmp($label, $this->label) === 0 || $this->aliases && Utils::in_arrayi($label, $this->aliases);
	}

	/**
	 * @return string
	 */
	public function getLabel() : string{
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel(string $label) : void{
		$this->label = $label;
	}

	/**
	 * @return string[]
	 */
	public function getAliases() : array{
		return $this->aliases;
	}

	/**
	 * @param string[] $aliases
	 */
	public function setAliases(array $aliases) : void{
		$this->aliases = $aliases;
	}

	/**
	 * @return string
	 */
	public function getUsage() : string{
		return $this->usage;
	}

	/**
	 * @param string $usage
	 */
	public function setUsage(string $usage) : void{
		$this->usage = $usage;
	}
}