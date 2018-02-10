<?php

namespace presentkim\virtualchest;

use pocketmine\item\ItemFactory;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\plugin\PluginBase;
use onebone\economyapi\EconomyAPI;
use presentkim\virtualchest\command\PoolCommand;
use presentkim\virtualchest\command\subcommands\{
  OpenSubCommand, BuySubCommand, PriceSubCommand, MaxSubCommand, DefaultSubCommand, SetSubCommand, ViewSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
};
use presentkim\virtualchest\container\VirtualChestContainer;
use presentkim\virtualchest\inventory\VirtualChestInventory;
use presentkim\virtualchest\util\Translation;

class VirtualChest extends PluginBase{

    /** @var self */
    private static $instance = null;

    /** @var string */
    public static $prefix = '';

    /** @var PoolCommand */
    private $command;

    /** @return self */
    public static function getInstance() : self{
        return self::$instance;
    }

    public function onLoad() : void{
        if (self::$instance === null) {
            self::$instance = $this;
            Translation::loadFromResource($this->getResource('lang/eng.yml'), true);

            if (file_exists($filename = "{$this->getDataFolder()}config.yml")) {
                $oldConfig = yaml_parse(file_get_contents($filename));
                if (isset($oldConfig['playerData'])) {
                    $this->getLogger()->alert('Convert old player data to nbt (' . count($oldConfig['playerData']) . ' items)');

                    if (!file_exists($dataFolder = $this->getDataFolder())) {
                        mkdir($dataFolder, 0777, true);
                    }
                    if (!file_exists($playerDataFolder = "{$dataFolder}players\\")) {
                        mkdir($playerDataFolder, 0777, true);
                    }

                    foreach ($oldConfig['playerData'] as $playerName => $playerData) {
                        try{
                            $this->getLogger()->debug("Start convert {$playerName}'s data");
                            $count = $playerData[0];
                            $chests = [];
                            foreach ($playerData[1] as $index => $itemDatas) {
                                $items = [];
                                foreach ($itemDatas as $slot => $itemData) {
                                    try{
                                        if (is_array($itemData)) {
                                            $args = explode(':', $itemData[0]);
                                            if (isset($value[1])) {
                                                $args[] = $itemData[1];
                                            }
                                            $items[$slot] = ItemFactory::get(...$args);
                                        } else {
                                            $items[$slot] = ItemFactory::get(...explode(':', $itemData));
                                        }
                                    } catch (\Error $e){
                                        $this->getLogger()->error($e);
                                    }
                                }
                                $chests[$index] = new VirtualChestInventory($playerName, $index + 1, $items);
                            }
                            $container = new VirtualChestContainer($playerName, $count, $chests);
                            $nbtStream = new BigEndianNBTStream();
                            $nbtStream->setData($container->nbtSerialize($playerName));

                            file_put_contents($file = "{$playerDataFolder}{$playerName}.dat", $nbtStream->writeCompressed());
                            $this->getLogger()->debug("Succeed convert {$playerName}'s data");
                        } catch (\Throwable $e){
                            $this->getLogger()->error($e->getMessage());
                            $this->getLogger()->warning("Error occurred saving {$playerName}'s data");
                        }
                    }
                    unset($oldConfig['playerData']);
                    yaml_emit_file($filename, $oldConfig, YAML_UTF8_ENCODING);
                }
            }
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
        if (!file_exists($playerDataFolder = "{$dataFolder}players\\")) {
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

        if (!file_exists($playerDataFolder = "{$dataFolder}players\\")) {
            mkdir($playerDataFolder, 0777, true);
        }
        foreach (VirtualChestContainer::getContainers() as $playerName => $container) {
            $nbtStream = new BigEndianNBTStream();
            $nbtStream->setData($container->nbtSerialize($playerName));

            file_put_contents($file = "{$playerDataFolder}{$playerName}.dat", $nbtStream->writeCompressed());
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
        if (file_exists($file = "{$this->getDataFolder()}players\\{$playerName}.dat")) {
            try{
                $nbtStream = new BigEndianNBTStream();
                $nbtStream->readCompressed(file_get_contents($file));

                $container = VirtualChestContainer::nbtDeserialize($playerName, $nbtStream->getData());
                VirtualChestContainer::setContainer($playerName, $container);

                return $container;
            } catch (\Throwable $e){
                $this->getLogger()->critical($e->getMessage());
            }
        }
        return null;
    }
}
