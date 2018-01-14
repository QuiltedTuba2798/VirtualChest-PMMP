<?php

namespace presentkim\virtualchest;

use pocketmine\plugin\PluginBase;
use presentkim\virtualchest\util\Translation;
use presentkim\virtualchest\inventory\VirtualChestInventory;
use presentkim\virtualchest\command\PoolCommand;
use presentkim\virtualchest\command\subcommands\{
  SetSubCommand, OpenSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
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
            // register instance
            self::$instance = $this;

            // create vchest PoolCommand
            $this->command = new PoolCommand($this, 'vchest');
            $this->command->createSubCommand(SetSubCommand::class);
            $this->command->createSubCommand(OpenSubCommand::class);
            $this->command->createSubCommand(LangSubCommand::class);
            $this->command->createSubCommand(ReloadSubCommand::class);
            $this->command->createSubCommand(SaveSubCommand::class);

            // load utils
            $this->getServer()->getLoader()->loadClass('presentkim\virtualchest\util\Utils');
        }
    }

    /**
     *
     */
    public function onEnable(){
        $this->load();
    }

    public function onDisable(){
        $this->save();
    }

    public function load(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        // load config
        $this->reloadConfig();

        // load lang
        $langfilename = $dataFolder . 'lang.yml';
        if (!file_exists($langfilename)) {
            Translation::loadFromResource($this->getResource('lang/eng.yml'));
            Translation::save($langfilename);
        } else {
            Translation::load($langfilename);
        }

        // reset virtual chest inventories
        VirtualChestInventory::$vchests = [];

        // register prefix
        self::$prefix = Translation::translate('prefix');

        // update translation and register command
        $this->reloadCommand();
    }

    public function reloadCommand(){
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

        // save config
        $config = $this->getConfig();
        foreach ($config->getAll() as $playerName => $data) {
            $newData = [];
            for ($index = 0; $index < $data[0]; $index++) {
                if (isset(VirtualChestInventory::$vchests[$playerName][$index])) {
                    $newData[$index] = [];
                    /** @var VirtualChestInventory $inventory */
                    $inventory = VirtualChestInventory::$vchests[$playerName][$index];
                    for ($i = 0; $i < 27; $i++) {
                        $item = $inventory->getItem($i);
                        $newData[$index][$i] = [
                          $item->getId(),
                          $item->getDamage(),
                          $item->getCount(),
                          $item->getCompoundTag(),
                        ];
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

        // save lang
        Translation::save("{$dataFolder}lang.yml");
    }
}
