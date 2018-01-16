<?php

namespace presentkim\virtualchest;

use pocketmine\plugin\PluginBase;
use presentkim\virtualchest\util\Translation;
use presentkim\virtualchest\inventory\VirtualChestInventory;
use presentkim\virtualchest\command\PoolCommand;
use presentkim\virtualchest\command\subcommands\{
  OpenSubCommand, DefaultSubCommand, SetSubCommand, ViewSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
};

class VirtualChestMain extends PluginBase{

    /** @var self */
    private static $instance = null;

    /** @var string */
    public static $prefix = '';

    /** @var PoolCommand */
    private $command;

    /** @return self */
    public static function getInstance(){
        return self::$instance;
    }

    public function onLoad(){
        if (self::$instance === null) {
            self::$instance = $this;
            $this->getServer()->getLoader()->loadClass('presentkim\virtualchest\util\Utils');
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

    public function onEnable(){
        $this->load();
    }

    public function onDisable(){
        $this->save();
    }

    public function load(){
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

    public function reloadCommand(){
        if ($this->command == null) {
            $this->command = new PoolCommand($this, 'vchest');
            $this->command->createSubCommand(OpenSubCommand::class);
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

    public function save(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $config = $this->getConfig();
        foreach ($config->getAll()['playerData'] as $playerName => $data) {
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
            $config->set($playerName, [
              $data[0],
              $newData,
            ]);
        }
        $this->saveConfig();
    }
}
