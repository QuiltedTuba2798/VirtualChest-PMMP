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

namespace kim\present\virtualchest\container;

use kim\present\virtualchest\inventory\VirtualChestInventory;
use kim\present\virtualchest\VirtualChest;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\{
	CompoundTag, IntTag, ListTag
};

class VirtualChestContainer{
	/** @var VirtualChestContainer[] */
	private static $containers = [];

	/**
	 * @return VirtualChestContainer[]
	 */
	public static function getContainers() : array{
		return self::$containers;
	}

	/**
	 * @param VirtualChestContainer[] $container
	 */
	public static function setContainers(array $container) : void{
		self::$containers = $container;
	}

	/**
	 * @param string $playerName
	 * @param bool   $load
	 *
	 * @return null|VirtualChestContainer
	 */
	public static function getContainer(string $playerName, bool $load = false) : ?VirtualChestContainer{
		if(isset(self::$containers[$playerName])){
			return self::$containers[$playerName];
		}elseif($load){
			return VirtualChest::getInstance()->loadPlayerData($playerName);
		}else{
			return null;
		}
	}

	/**
	 * @param string $playerName
	 * @param VirtualChestContainer
	 */
	public static function setContainer(string $playerName, VirtualChestContainer $container) : void{
		self::$containers[$playerName] = $container;
	}

	/** @var string */
	private $playerName;

	/** @var int */
	private $count;

	/** @var VirtualChestInventory[] */
	private $chests = [];

	/**
	 * VirtualChestContainer constructor.
	 *
	 * @param string                  $playerName
	 * @param int                     $count
	 * @param VirtualChestInventory[] $chests
	 */
	public function __construct(string $playerName, int $count, array $chests = []){
		$this->playerName = $playerName;
		$this->count = $count;
		$this->chests = $chests;
	}

	/**
	 * @return string
	 */
	public function getPlayerName() : string{
		return $this->playerName;
	}

	/**
	 * @param string $playerName
	 */
	public function setPlayerName(string $playerName) : void{
		$this->playerName = $playerName;
	}

	/**
	 * @return int
	 */
	public function getCount() : int{
		return $this->count;
	}

	/**
	 * @param int $count
	 */
	public function setCount(int $count) : void{
		if($this->count > $count){
			for($i = $count; $i < $this->count; ++$i){
				if(isset($this->chests[$i])){
					foreach($this->chests[$i]->getViewers() as $key => $who){
						$this->chests[$i]->close($who);
					}
					unset($this->chests[$i]);
				}
			}
		}
		$this->count = $count;
	}

	/**
	 * @return VirtualChestInventory[]
	 */
	public function getChests() : array{
		return $this->chests;
	}

	/**
	 * @param VirtualChestInventory[] $chests
	 */
	public function setChests(array $chests) : void{
		$this->chests = $chests;
	}

	/**
	 * @param int $index
	 *
	 * @return null|VirtualChestInventory
	 */
	public function getChest(int $index) : ?VirtualChestInventory{
		if(isset($this->chests[$index])){
			return $this->chests[$index];
		}elseif($this->count > $index){
			$this->chests[$index] = new VirtualChestInventory($this->playerName, $index + 1);
			return $this->chests[$index];
		}else{
			return null;
		}
	}

	/**
	 * @param int                   $index
	 * @param VirtualChestInventory $chest
	 */
	public function setChest(int $index, VirtualChestInventory $chest) : void{
		if(isset($this->chests[$index])){
			foreach($this->chests[$index]->getViewers() as $key => $who){
				$this->chests[$index]->close($who);
			}
		}
		$this->chests[$index] = $chest;
	}

	/**
	 * @param string $tagName
	 *
	 * @return CompoundTag
	 */
	public function nbtSerialize(string $tagName = "Container") : CompoundTag{
		$chestsTag = new ListTag("Chests", [], NBT::TAG_List);
		foreach($this->chests as $index => $chest){
			$chestsTag->push($chest->nbtSerialize((string) $index));
		}
		return new CompoundTag($tagName, [
			new IntTag("Count", $this->count),
			$chestsTag,
		]);
	}

	/**
	 * @param string      $playerName
	 * @param CompoundTag $tag
	 *
	 * @return VirtualChestContainer
	 */
	public static function nbtDeserialize(string $playerName, CompoundTag $tag) : VirtualChestContainer{
		$container = new VirtualChestContainer($playerName, $tag->getInt("Count"));
		/** @var ListTag $chestTag */
		foreach($tag->getListTag("Chests") as $i => $chestTag){
			$container->setChest($i, VirtualChestInventory::nbtDeserialize($playerName, $i, $chestTag));
		}
		return $container;
	}
}