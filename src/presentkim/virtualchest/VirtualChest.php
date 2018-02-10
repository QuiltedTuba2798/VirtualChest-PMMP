<?php

namespace presentkim\virtualchest;

use pocketmine\plugin\PluginBase;
use onebone\economyapi\EconomyAPI;
use presentkim\virtualchest\command\PoolCommand;
use presentkim\virtualchest\command\subcommands\{
  OpenSubCommand, PriceSubCommand, MaxSubCommand, DefaultSubCommand, SetSubCommand, ViewSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
};
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
                if (!isset($oldConfig['playerData'])) {
                    $newConfig = yaml_parse(stream_get_contents($this->getResource("config.yml")));
                    $newConfig['playerData'] = $oldConfig;
                    yaml_emit_file($filename, $newConfig, YAML_UTF8_ENCODING);
                    $this->getLogger()->info('Converted old data');
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
        VirtualChestInventory::$vchests = [];

        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
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

        $config = $this->getConfig();
        $datas = $config->get('playerData');
        foreach ($datas as $playerName => $data) {
            $newData = [];
            for ($index = 0; $index < $data[0]; $index++) {
                if (isset(VirtualChestInventory::$vchests[$playerName][$index])) {
                    $newData[$index] = [];
                    /** @var VirtualChestInventory $inventory */
                    $inventory = VirtualChestInventory::$vchests[$playerName][$index];
                    for ($i = 0; $i < 27; $i++) {
                        $item = $inventory->getItem($i);
                        if (!$item->isNull()) {
                            $newData[$index][$i] = implode(':', [
                              $item->getId(),
                              $item->getDamage(),
                              $item->getCount(),
                            ]);
                            if (!empty($compountTag = $item->getCompoundTag())) {
                                $newData[$index][$i] = [
                                  $newData[$index][$i],
                                  $compountTag,
                                ];
                            }
                        }
                    }
                } elseif (isset($data[1][$index])) {
                    $newData[$index] = $data[1][$index];
                }
            }
            $datas[$playerName][1] = $newData;
        }
        $config->set('playerData', $datas);
        $this->saveConfig();
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
}
