<?php

namespace presentkim\virtualchest\inventory;

use pocketmine\Player;
use pocketmine\block\Block;
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
  types\WindowTypes, UpdateBlockPacket, ContainerOpenPacket, BlockEntityDataPacket
};
use pocketmine\tile\Spawnable;
use presentkim\virtualchest\util\Translation;

class VirtualChestInventory extends CustomInventory{

    /** @var NetworkLittleEndianNBTStream|null */
    private static $nbtWriter = null;

    /** @var self[][] */
    public static $vchests = [];

    /** CompoundTag */
    private $nbt;

    /** Vector3[] */
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

        $this->nbt = new CompoundTag('', [
          new StringTag('id', 'Chest'),
          new IntTag('x', 0),
          new IntTag('y', 0),
          new IntTag('z', 0),
          new StringTag('CustomName', Translation::translate('chest-name', $ownerName, $num)),
        ]);

        if (self::$nbtWriter === null) {
            self::$nbtWriter = new NetworkLittleEndianNBTStream();
        }
    }

    /** @param Player $who */
    public function onOpen(Player $who) : void{
        BaseInventory::onOpen($who);

        $this->vectors[$key = $who->getLowerCaseName()] = $who->subtract(0, 3, 0)->floor();
        if ($this->vectors[$key]->y < 0) {
            $this->vectors[$key]->y = 0;
        }

        $pk = new UpdateBlockPacket();
        $pk->blockId = Block::CHEST;
        $pk->blockData = 0;
        $pk->x = $this->vectors[$key]->x;
        $pk->y = $this->vectors[$key]->y;
        $pk->z = $this->vectors[$key]->z;
        $who->sendDataPacket($pk);


        $this->nbt->setInt('x', $this->vectors[$key]->x);
        $this->nbt->setInt('y', $this->vectors[$key]->y);
        $this->nbt->setInt('z', $this->vectors[$key]->z);
        self::$nbtWriter->setData($this->nbt);

        $pk = new BlockEntityDataPacket();
        $pk->x = $this->vectors[$key]->x;
        $pk->y = $this->vectors[$key]->y;
        $pk->z = $this->vectors[$key]->z;
        $pk->namedtag = self::$nbtWriter->write();
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

    public function onClose(Player $who) : void{
        BaseInventory::onClose($who);

        $block = $who->getLevel()->getBlock($this->vectors[$key = $who->getLowerCaseName()]);

        $pk = new UpdateBlockPacket();
        $pk->x = $this->vectors[$key]->x;
        $pk->y = $this->vectors[$key]->y;
        $pk->z = $this->vectors[$key]->z;
        $pk->blockId = $block->getId();
        $pk->blockData = $block->getDamage();
        $who->sendDataPacket($pk);

        $tile = $who->getLevel()->getTile($this->vectors[$key]);
        if ($tile instanceof Spawnable) {
            $who->sendDataPacket($tile->createSpawnPacket());
        }
        unset($this->vectors[$key]);
    }

    /** @return string */
    public function getName() : string{
        return "VirtualChestInventory";
    }

    /** @return int */
    public function getDefaultSize() : int{
        return 27;
    }

    /** @return int */
    public function getNetworkType() : int{
        return WindowTypes::CONTAINER;
    }

    /**
     * @param string $tagName
     *
     * @return ListTag
     */
    public function nbtSerialize(string $tagName = 'Inventory') : ListTag{
        $tag = new ListTag($tagName, [], NBT::TAG_Compound);
        for ($slot = 0; $slot < 27; ++$slot) {
            $item = $this->getItem($slot);
            if (!$item->isNull()) {
                $tag[$slot] = $item->nbtSerialize($slot);
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
        foreach ($tag as $i => $item) {
            $inventory->setItem($item->getByte("Slot"), Item::nbtDeserialize($item));
        }
        return $inventory;
    }
}