<?php

declare(strict_types=1);

namespace kim\present\virtualchest;

use kim\present\virtualchest\command\PoolCommand;
use kim\present\virtualchest\command\subcommands\{
	BuySubCommand, DefaultSubCommand, MaxSubCommand, OpenSubCommand, PriceSubCommand, SetSubCommand, ViewSubCommand
};
use kim\present\virtualchest\container\VirtualChestContainer;
use kim\present\virtualchest\lang\PluginLang;
use onebone\economyapi\EconomyAPI;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;

class VirtualChest extends PluginBase{
	/** @var VirtualChest */
	private static $instance;

	/** @var PoolCommand */
	private $command;

	/** @var PluginLang */
	private $language;

	/**
	 * @return VirtualChest
	 */
	public static function getInstance() : VirtualChest{
		return self::$instance;
	}

	public function onLoad() : void{
		self::$instance = $this;
	}

	public function onEnable() : void{
		$dataFolder = $this->getDataFolder();
		if(!file_exists($dataFolder)){
			mkdir($dataFolder, 0777, true);
		}
		if(!file_exists($playerDataFolder = "{$dataFolder}players/")){
			mkdir($playerDataFolder, 0777, true);
		}
		$this->language = new PluginLang($this);
		$this->reloadConfig();

		if($this->command == null){
			$this->command = new PoolCommand($this, 'vchest');
			$this->command->createSubCommand(OpenSubCommand::class);
			if(class_exists(EconomyAPI::class)){
				$this->command->createSubCommand(BuySubCommand::class);
				$this->command->createSubCommand(PriceSubCommand::class);
				$this->command->createSubCommand(MaxSubCommand::class);
			}
			$this->command->createSubCommand(DefaultSubCommand::class);
			$this->command->createSubCommand(SetSubCommand::class);
			$this->command->createSubCommand(ViewSubCommand::class);
		}
		if($this->command->isRegistered()){
			$this->getServer()->getCommandMap()->unregister($this->command);
		}
		$this->getServer()->getCommandMap()->register(strtolower($this->getName()), $this->command);
	}

	public function onDisable() : void{
		$dataFolder = $this->getDataFolder();
		if(!file_exists($dataFolder)){
			mkdir($dataFolder, 0777, true);
		}
		$this->saveConfig();

		if(!file_exists($playerDataFolder = "{$dataFolder}players/")){
			mkdir($playerDataFolder, 0777, true);
		}
		foreach(VirtualChestContainer::getContainers() as $playerName => $container){
			file_put_contents($file = "{$playerDataFolder}{$playerName}.dat", (new BigEndianNBTStream())->writeCompressed($container->nbtSerialize($playerName)));
		}
	}

	/**
	 * @param string $playerName
	 *
	 * @return null|VirtualChestContainer
	 */
	public function loadPlayerData(string $playerName) : ?VirtualChestContainer{
		if(file_exists($file = "{$this->getDataFolder()}players/{$playerName}.dat")){
			try{
				$namedTag = (new BigEndianNBTStream())->readCompressed(file_get_contents($file));
				if($namedTag instanceof CompoundTag){
					$container = VirtualChestContainer::nbtDeserialize($playerName, $namedTag);
					VirtualChestContainer::setContainer($playerName, $container);
					return $container;
				}else{
					$this->getLogger()->critical("Invalid data found in \"{$playerName}.dat\", expected " . CompoundTag::class . ", got " . (is_object($namedTag) ? get_class($namedTag) : gettype($namedTag)));
				}
			}catch(\Throwable $e){
				$this->getLogger()->critical($e->getMessage());
			}
		}
		return null;
	}

	/**
	 * @param string $name = ''
	 *
	 * @return PoolCommand
	 */
	public function getCommand(string $name = '') : PoolCommand{
		return $this->command;
	}

	/**
	 * @return PluginLang
	 */
	public function getLanguage() : PluginLang{
		return $this->language;
	}

	/**
	 * @return string
	 */
	public function getSourceFolder() : string{
		$pharPath = \Phar::running();
		if(empty($pharPath)){
			return dirname(__FILE__, 5) . DIRECTORY_SEPARATOR;
		}else{
			return $pharPath . DIRECTORY_SEPARATOR;
		}
	}
}