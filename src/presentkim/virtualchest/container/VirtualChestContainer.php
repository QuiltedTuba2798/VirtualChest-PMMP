<?php

namespace presentkim\virtualchest\container;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\{
  CompoundTag, ListTag, IntTag
};
use presentkim\virtualchest\VirtualChest as Plugin;
use presentkim\virtualchest\inventory\VirtualChestInventory;

class VirtualChestContainer{

    /** @var self[] */
    private static $containers = [];

    /** @return VirtualChestContainer[] */
    public static function getContainers() : array{
        return self::$containers;
    }

    /** @param VirtualChestContainer[] $container */
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
        if (isset(self::$containers[$playerName])) {
            return self::$containers[$playerName];
        } elseif ($load) {
            return Plugin::getInstance()->loadPlayerData($playerName);
        } else {
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

    /** @return string */
    public function getPlayerName() : string{
        return $this->playerName;
    }

    /** @param string $playerName */
    public function setPlayerName(string $playerName) : void{
        $this->playerName = $playerName;
    }

    /** @return int */
    public function getCount() : int{
        return $this->count;
    }

    /** @param int $count */
    public function setCount(int $count) : void{
        if ($this->count > $count) {
            for ($i = $count; $i < $this->count; ++$i) {
                if (isset($this->chests[$i])) {
                    foreach ($this->chests[$i]->getViewers() as $key => $who) {
                        $this->chests[$i]->close($who);
                    }
                    unset($this->chests[$i]);
                }
            }
        }
        $this->count = $count;
    }

    /** @return VirtualChestInventory[] */
    public function getChests() : array{
        return $this->chests;
    }

    /** @param VirtualChestInventory[] $chests */
    public function setChests(array $chests) : void{
        $this->chests = $chests;
    }

    /**
     * @param int $index
     *
     * @return null|VirtualChestInventory
     */
    public function getChest(int $index) : ?VirtualChestInventory{
        if (isset($this->chests[$index])) {
            return $this->chests[$index];
        } elseif ($this->count > $index) {
            $this->chests[$index] = new VirtualChestInventory($this->playerName, $index + 1);
            return $this->chests[$index];
        } else {
            return null;
        }
    }

    /**
     * @param int                   $index
     * @param VirtualChestInventory $chest
     */
    public function setChest(int $index, VirtualChestInventory $chest) : void{
        if (isset($this->chests[$index])) {
            foreach ($this->chests[$index]->getViewers() as $key => $who) {
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
    public function nbtSerialize(string $tagName = 'Container') : CompoundTag{
        $chestsTag = new ListTag('Chests', [], NBT::TAG_List);
        foreach ($this->chests as $index => $chest) {
            $chestsTag->push($chest->nbtSerialize($index));
        }
        return new CompoundTag($tagName, [
          new IntTag('Count', $this->count),
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
        $container = new VirtualChestContainer($playerName, $tag->getInt('Count'));
        /** @var ListTag $chestTag */
        foreach ($tag->getListTag('Chests') as $i => $chestTag) {
            $container->setChest($i, VirtualChestInventory::nbtDeserialize($playerName, $i, $chestTag));
        }
        return $container;
    }
}