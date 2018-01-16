<?php

namespace presentkim\virtualchest\inventory;

use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\tile\Spawnable;
use pocketmine\inventory\{
  BaseInventory, CustomInventory, Inventory, InventoryHolder
};
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\{
  CompoundTag, IntTag, StringTag
};
use pocketmine\network\mcpe\protocol\{
  types\WindowTypes, UpdateBlockPacket, ContainerOpenPacket, BlockEntityDataPacket
};
use presentkim\virtualchest\util\Translation;

class VirtualChestInventory extends CustomInventory implements InventoryHolder{

    /** @var NetworkLittleEndianNBTStream|null */
    private static $nbtWriter = null;

    /** @var  self[][] */
    public static $vchests = [];

    /** Vector3 */
    private $vec;

    /** CompoundTag */
    private $nbt;

    /**
     * VirtualChestInventory constructor.
     *
     * @param string      $ownerName
     * @param int         $num
     * @param array       $items
     * @param string|null $title
     */
    public function __construct(string $ownerName, int $num = 0, $items = [], string $title = null){
        parent::__construct($this, $items, 27, $title);

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

        $this->vec = $who->floor();
        $this->vec->y = 0;

        $pk = new UpdateBlockPacket();
        $pk->blockId = Block::CHEST;
        $pk->blockData = 0;
        $pk->x = $this->vec->x;
        $pk->y = $this->vec->y;
        $pk->z = $this->vec->z;
        $who->sendDataPacket($pk);


        $this->nbt->setInt('x', $this->vec->x);
        $this->nbt->setInt('y', $this->vec->y);
        $this->nbt->setInt('z', $this->vec->z);
        self::$nbtWriter->setData($this->nbt);

        $pk = new BlockEntityDataPacket();
        $pk->x = $this->vec->x;
        $pk->y = $this->vec->y;
        $pk->z = $this->vec->z;
        $pk->namedtag = self::$nbtWriter->write();
        $who->sendDataPacket($pk);


        $pk = new ContainerOpenPacket();
        $pk->type = WindowTypes::CONTAINER;
        $pk->entityUniqueId = -1;
        $pk->x = $this->vec->x;
        $pk->y = $this->vec->y;
        $pk->z = $this->vec->z;
        $pk->windowId = $who->getWindowId($this);
        $who->sendDataPacket($pk);

        $this->sendContents($who);
    }

    public function onClose(Player $who) : void{
        BaseInventory::onClose($who);

        $block = $who->getLevel()->getBlock($this->vec);

        $pk = new UpdateBlockPacket();
        $pk->x = $this->vec->x;
        $pk->y = $this->vec->y;
        $pk->z = $this->vec->z;
        $pk->blockId = $block->getId();
        $pk->blockData = $block->getDamage();
        $who->sendDataPacket($pk);

        $tile = $who->getLevel()->getTile($this->vec);
        if ($tile instanceof Spawnable) {
            $who->sendDataPacket($tile->createSpawnPacket());
        }
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

    /** @return Inventory */
    public function getInventory(){
        return $this;
    }
}