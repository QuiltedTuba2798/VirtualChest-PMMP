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

namespace kim\present\virtualchest\inventory;

use kim\present\virtualchest\VirtualChest;
use pocketmine\block\{
	Block, BlockFactory
};
use pocketmine\inventory\{
	BaseInventory, CustomInventory
};
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\{
	NBT, NetworkLittleEndianNBTStream
};
use pocketmine\nbt\tag\{
	CompoundTag, IntTag, ListTag, StringTag
};
use pocketmine\network\mcpe\protocol\{
	BlockEntityDataPacket, ContainerOpenPacket, types\WindowTypes, UpdateBlockPacket
};
use pocketmine\Player;
use pocketmine\tile\Spawnable;

class VirtualChestInventory extends CustomInventory{
	/** @var CompoundTag */
	private $nbt;

	/** @var Vector3[] */
	private $vectors = [];

	/**
	 * VirtualChestInventory constructor.
	 *
	 * @param string      $ownerName
	 * @param int         $num
	 * @param array       $items
	 * @param string|null $title
	 */
	public function __construct(string $ownerName, int $num = 0, $items = [], string $title = null){
		parent::__construct(new Vector3(0, 0, 0), $items, 27, $title);

		$this->nbt = new CompoundTag("", [
			new StringTag("id", "Chest"),
			new IntTag("x", 0),
			new IntTag("y", 0),
			new IntTag("z", 0),
			new StringTag("CustomName", VirtualChest::getInstance()->getLanguage()->translateString("virtualchest.name", [
				$ownerName,
				$num,
			])),
		]);
	}

	/**
	 * @param Player $who
	 */
	public function onOpen(Player $who) : void{
		BaseInventory::onOpen($who);

		$this->vectors[$key = $who->getLowerCaseName()] = $who->subtract(0, 3, 0)->floor();
		if($this->vectors[$key]->y < 0){
			$this->vectors[$key]->y = 0;
		}

		$pk = new UpdateBlockPacket();
		$pk->x = $this->vectors[$key]->x;
		$pk->y = $this->vectors[$key]->y;
		$pk->z = $this->vectors[$key]->z;
		$pk->blockRuntimeId = BlockFactory::toStaticRuntimeId(Block::CHEST);
		$pk->flags = UpdateBlockPacket::FLAG_NONE;
		$who->sendDataPacket($pk);


		$this->nbt->setInt("x", $this->vectors[$key]->x);
		$this->nbt->setInt("y", $this->vectors[$key]->y);
		$this->nbt->setInt("z", $this->vectors[$key]->z);

		$pk = new BlockEntityDataPacket();
		$pk->x = $this->vectors[$key]->x;
		$pk->y = $this->vectors[$key]->y;
		$pk->z = $this->vectors[$key]->z;
		$pk->namedtag = (new NetworkLittleEndianNBTStream())->write($this->nbt);
		$who->sendDataPacket($pk);


		$pk = new ContainerOpenPacket();
		$pk->type = WindowTypes::CONTAINER;
		$pk->entityUniqueId = -1;
		$pk->x = $this->vectors[$key]->x;
		$pk->y = $this->vectors[$key]->y;
		$pk->z = $this->vectors[$key]->z;
		$pk->windowId = $who->getWindowId($this);
		$who->sendDataPacket($pk);

		$this->sendContents($who);
	}

	/**
	 * @param Player $who
	 */
	public function onClose(Player $who) : void{
		BaseInventory::onClose($who);

		$block = $who->getLevel()->getBlock($this->vectors[$key = $who->getLowerCaseName()]);

		$pk = new UpdateBlockPacket();
		$pk->x = $this->vectors[$key]->x;
		$pk->y = $this->vectors[$key]->y;
		$pk->z = $this->vectors[$key]->z;
		$pk->blockRuntimeId = BlockFactory::toStaticRuntimeId($block->getId(), $block->getDamage());
		$pk->flags = UpdateBlockPacket::FLAG_NONE;
		$who->sendDataPacket($pk);

		$tile = $who->getLevel()->getTile($this->vectors[$key]);
		if($tile instanceof Spawnable){
			$who->sendDataPacket($tile->createSpawnPacket());
		}
		unset($this->vectors[$key]);
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "VirtualChestInventory";
	}

	/**
	 * @return int
	 */
	public function getDefaultSize() : int{
		return 27;
	}

	/**
	 * @return int
	 */
	public function getNetworkType() : int{
		return WindowTypes::CONTAINER;
	}

	/**
	 * @param string $tagName
	 *
	 * @return ListTag
	 */
	public function nbtSerialize(string $tagName = "Inventory") : ListTag{
		$tag = new ListTag($tagName, [], NBT::TAG_Compound);
		for($slot = 0; $slot < 27; ++$slot){
			$item = $this->getItem($slot);
			if(!$item->isNull()){
				$tag->push($item->nbtSerialize($slot));
			}
		}
		return $tag;
	}

	/**
	 * @param string  $playerName
	 * @param int     $num
	 * @param ListTag $tag
	 *
	 * @return VirtualChestInventory
	 */
	public static function nbtDeserialize(string $playerName, int $num, ListTag $tag) : VirtualChestInventory{
		$inventory = new VirtualChestInventory($playerName, $num);
		/** @var CompoundTag $itemTag */
		foreach($tag as $i => $itemTag){
			$inventory->setItem($itemTag->getByte("Slot"), Item::nbtDeserialize($itemTag));
		}
		return $inventory;
	}
}