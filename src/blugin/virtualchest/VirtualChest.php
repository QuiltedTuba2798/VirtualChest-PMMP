<?php

namespace blugin\virtualchest;

use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use onebone\economyapi\EconomyAPI;
use blugin\virtualchest\command\PoolCommand;
use blugin\virtualchest\command\subcommands\{
  OpenSubCommand, BuySubCommand, PriceSubCommand, MaxSubCommand, DefaultSubCommand, SetSubCommand, ViewSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
};
use blugin\virtualchest\container\VirtualChestContainer;
use blugin\virtualchest\util\Translation;

class VirtualChest extends PluginBase{

    /** @var VirtualChest */
    private static $instance = null;

    /** @var string */
    public static $prefix = '';

    /** @var PoolCommand */
    private $command;

    /** @return VirtualChest */
    public static function getInstance() : VirtualChest{
        return self::$instance;
    }

    public function onLoad() : void{
        if (self::$instance === null) {
            self::$instance = $this;
            Translation::loadFromResource($this->getResource('lang/eng.yml'), true);
        }
    }

    public function onEnable() : void{
        $this->load();
    }

    public function onDisable() : void{
        $this->save();
    }

    public function load() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }
        if (!file_exists($playerDataFolder = "{$dataFolder}players/")) {
            mkdir($playerDataFolder, 0777, true);
        }

        $this->reloadConfig();

        $langfilename = $dataFolder . 'lang.yml';
        if (!file_exists($langfilename)) {
            $resource = $this->getResource('lang/eng.yml');
            fwrite($fp = fopen("{$dataFolder}lang.yml", "wb"), $contents = stream_get_contents($resource));
            fclose($fp);
            Translation::loadFromContents($contents);
        } else {
            Translation::load($langfilename);
        }

        self::$prefix = Translation::translate('prefix');
        $this->reloadCommand();
    }

    public function reloadCommand() : void{
        if ($this->command == null) {
            $this->command = new PoolCommand($this, 'vchest');
            $this->command->createSubCommand(OpenSubCommand::class);
            if (class_exists(EconomyAPI::class)) {
                $this->command->createSubCommand(BuySubCommand::class);
                $this->command->createSubCommand(PriceSubCommand::class);
                $this->command->createSubCommand(MaxSubCommand::class);
            }
            $this->command->createSubCommand(DefaultSubCommand::class);
            $this->command->createSubCommand(SetSubCommand::class);
            $this->command->createSubCommand(ViewSubCommand::class);
            $this->command->createSubCommand(LangSubCommand::class);
            $this->command->createSubCommand(ReloadSubCommand::class);
            $this->command->createSubCommand(SaveSubCommand::class);
        }
        $this->command->updateTranslation();
        $this->command->updateSudCommandTranslation();
        if ($this->command->isRegistered()) {
            $this->getServer()->getCommandMap()->unregister($this->command);
        }
        $this->getServer()->getCommandMap()->register(strtolower($this->getName()), $this->command);
    }

    public function save() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $this->saveConfig();

        if (!file_exists($playerDataFolder = "{$dataFolder}players/")) {
            mkdir($playerDataFolder, 0777, true);
        }
        foreach (VirtualChestContainer::getContainers() as $playerName => $container) {
            file_put_contents($file = "{$playerDataFolder}{$playerName}.dat", (new BigEndianNBTStream())->writeCompressed($container->nbtSerialize($playerName)));
        }
    }

    /**
     * @param string $name = ''
     *
     * @return PoolCommand
     */
    public function getCommand(string $name = '') : PoolCommand{
        return $this->command;
    }

    /** @param PoolCommand $command */
    public function setCommand(PoolCommand $command) : void{
        $this->command = $command;
    }

    /**
     * @param string $playerName
     *
     * @return null|VirtualChestContainer
     */
    public function loadPlayerData(string $playerName) : ?VirtualChestContainer{
        if (file_exists($file = "{$this->getDataFolder()}players/{$playerName}.dat")) {
            try{
                $namedTag = (new BigEndianNBTStream())->readCompressed(file_get_contents($file));
                if ($namedTag instanceof CompoundTag) {
                    $container = VirtualChestContainer::nbtDeserialize($playerName, $namedTag);
                    VirtualChestContainer::setContainer($playerName, $container);
                    return $container;
                } else {
                    $this->getLogger()->critical("Invalid data found in \"{$playerName}.dat\", expected " . CompoundTag::class . ", got " . (is_object($namedTag) ? get_class($namedTag) : gettype($namedTag)));
                }
            } catch (\Throwable $e){
                $this->getLogger()->critical($e->getMessage());
            }
        }
        return null;
    }
}
