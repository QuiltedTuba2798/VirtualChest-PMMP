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

namespace kim\present\virtualchest;

use kim\present\virtualchest\command\{
	BuySubcommand, DefaultSubcommand, MaxSubcommand, OpenSubcommand, PriceSubcommand, SetSubcommand, Subcommand, ViewSubcommand
};
use kim\present\virtualchest\container\VirtualChestContainer;
use kim\present\virtualchest\lang\PluginLang;
use kim\present\virtualchest\listener\PlayerEventListener;
use kim\present\virtualchest\task\CheckUpdateAsyncTask;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\{
	Command, CommandSender, PluginCommand
};
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;

class VirtualChest extends PluginBase{
	public const SUBCOMMAND_OPEN = 0;
	public const SUBCOMMAND_BUY = 1;
	public const SUBCOMMAND_PRICE = 2;
	public const SUBCOMMAND_MAX = 3;
	public const SUBCOMMAND_DEFAULT = 4;
	public const SUBCOMMAND_SET = 5;
	public const SUBCOMMAND_VIEW = 6;

	public const MAX_COUNT_TAG = "MaxCount";
	public const DEFAULT_COUNT_TAG = "DefaultCount";
	public const PRICE_TAG = "Price";

	/** @var VirtualChest */
	private static $instance;

	/**
	 * @return VirtualChest
	 */
	public static function getInstance() : VirtualChest{
		return self::$instance;
	}

	/** @var PluginLang */
	private $language;

	/** @var PluginCommand */
	private $command;

	/** @var Subcommand[] */
	private $subcommands;

	/** @var int */
	private $maxCount, $defaultCount;

	/** @var string */
	private $price;

	/**
	 * Called when the plugin is loaded, before calling onEnable()
	 */
	public function onLoad() : void{
		self::$instance = $this;
	}

	/**
	 * Called when the plugin is enabled
	 */
	public function onEnable() : void{
		//Save default resources
		$this->saveResource("lang/eng/lang.ini", false);
		$this->saveResource("lang/kor/lang.ini", false);
		$this->saveResource("lang/language.list", false);

		//Load config file
		$this->saveDefaultConfig();
		$this->reloadConfig();
		$config = $this->getConfig();

		//Check latest version
		if($config->getNested("settings.update-check", false)){
			$this->getServer()->getAsyncPool()->submitTask(new CheckUpdateAsyncTask());
		}

		//Load language file
		$this->language = new PluginLang($this, $config->getNested("settings.language"));
		$this->getLogger()->info($this->language->translateString("language.selected", [$this->language->getName(), $this->language->getLang()]));

		//Load virtual chest settings data
		if(file_exists($file = "{$this->getDataFolder()}Settings.dat")){
			$namedTag = (new BigEndianNBTStream())->readCompressed(file_get_contents($file));
			if($namedTag instanceof CompoundTag){
				$this->defaultCount = $namedTag->getInt(self::DEFAULT_COUNT_TAG, 0);
				$this->maxCount = $namedTag->getInt(self::MAX_COUNT_TAG, -1);
				$this->price = $namedTag->getString(self::PRICE_TAG, "-1");
			}else{
				$this->getLogger()->error("The file is not in the NBT-CompoundTag format : $file");
			}
		}else{
			$this->defaultCount = 0;
			$this->maxCount = -1;
			$this->price = "-1";
		}

		//Register main command
		$this->command = new PluginCommand($config->getNested("command.name"), $this);
		$this->command->setPermission("virtualchest.cmd");
		$this->command->setAliases($config->getNested("command.aliases"));
		$this->command->setUsage($this->language->translateString("commands.virtualchest.usage"));
		$this->command->setDescription($this->language->translateString("commands.virtualchest.description"));
		$this->getServer()->getCommandMap()->register($this->getName(), $this->command);

		//Register subcommands
		$this->subcommands = [
			self::SUBCOMMAND_OPEN => new OpenSubcommand($this),
			self::SUBCOMMAND_DEFAULT => new DefaultSubcommand($this),
			self::SUBCOMMAND_SET => new SetSubcommand($this),
			self::SUBCOMMAND_VIEW => new ViewSubcommand($this)
		];
		if(class_exists(EconomyAPI::class)){
			$this->subcommands[self::SUBCOMMAND_PRICE] = new PriceSubCommand($this);
			$this->subcommands[self::SUBCOMMAND_BUY] = new BuySubCommand($this);
			$this->subcommands[self::SUBCOMMAND_PRICE] = new PriceSubCommand($this);
			$this->subcommands[self::SUBCOMMAND_MAX] = new MaxSubCommand($this);
		}

		//Load permission's default value from config
		$permissions = $this->getServer()->getPluginManager()->getPermissions();
		$defaultValue = $config->getNested("permission.main");
		if($defaultValue !== null){
			$permissions["virtualchest.cmd"]->setDefault(Permission::getByName($config->getNested("permission.main")));
		}
		foreach($this->subcommands as $key => $subcommand){
			$label = $subcommand->getLabel();
			$defaultValue = $config->getNested("permission.children.{$label}");
			if($defaultValue !== null){
				$permissions["virtualchest.cmd.{$label}"]->setDefault(Permission::getByName($defaultValue));
			}
		}

		//Register event listeners
		$this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
	}

	/**
	 * Called when the plugin is disabled
	 * Use this to free open things and finish actions
	 */
	public function onDisable() : void{
		//Save players data
		foreach(VirtualChestContainer::getContainers() as $playerName => $container){
			$player = $this->getServer()->getPlayerExact($playerName);
			if($player === null){
				$namedTag = $this->getServer()->getOfflinePlayerData($playerName);
				if($namedTag instanceof CompoundTag){
					$namedTag->setTag($container->nbtSerialize("VirtualChest"));
					$this->getServer()->saveOfflinePlayerData($playerName, $namedTag);
				}else{
					$this->getLogger()->critical("Invalid data found in \"{$playerName}.dat\", expected " . CompoundTag::class . ", got " . (is_object($namedTag) ? get_class($namedTag) : gettype($namedTag)));
				}
			}else{
				$player->namedtag->setTag($container->nbtSerialize("VirtualChest"));
			}
		}

		//Save virtual chest settings data
		$namedTag = new CompoundTag("", [
			new IntTag(self::DEFAULT_COUNT_TAG, $this->defaultCount),
			new IntTag(self::MAX_COUNT_TAG, $this->maxCount),
			new StringTag(self::PRICE_TAG, $this->price)
		]);
		file_put_contents("{$this->getDataFolder()}Settings.dat", (new BigEndianNBTStream())->writeCompressed($namedTag));
	}

	/**
	 * @param CommandSender $sender
	 * @param Command       $command
	 * @param string        $label
	 * @param string[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(empty($args[0])){
			$targetSubcommand = null;
			foreach($this->subcommands as $key => $subcommand){
				if($sender->hasPermission($subcommand->getPermission())){
					if($targetSubcommand === null){
						$targetSubcommand = $subcommand;
					}else{
						//Filter out cases where more than two command has permission
						return false;
					}
				}
			}
			$targetSubcommand->handle($sender);
		}else{
			$label = array_shift($args);
			foreach($this->subcommands as $key => $subcommand){
				if($subcommand->checkLabel($label)){
					$subcommand->handle($sender, $args);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @Override for multilingual support of the config file
	 *
	 * @return bool
	 */
	public function saveDefaultConfig() : bool{
		$resource = $this->getResource("lang/{$this->getServer()->getLanguage()->getLang()}/config.yml");
		if($resource === null){
			$resource = $this->getResource("lang/" . PluginLang::FALLBACK_LANGUAGE . "/config.yml");
		}

		if(!file_exists($configFile = $this->getDataFolder() . "config.yml")){
			$ret = stream_copy_to_stream($resource, $fp = fopen($configFile, "wb")) > 0;
			fclose($fp);
			fclose($resource);
			return $ret;
		}
		return false;
	}

	/**
	 * @return PluginLang
	 */
	public function getLanguage() : PluginLang{
		return $this->language;
	}

	/**
	 * @return Subcommand[]
	 */
	public function getSubcommands() : array{
		return $this->subcommands;
	}

	/**
	 * @return int
	 */
	public function getMaxCount() : int{
		return $this->maxCount;
	}

	/**
	 * @param int $maxCount
	 */
	public function setMaxCount(int $maxCount) : void{
		$this->maxCount = $maxCount;
	}

	/**
	 * @return int
	 */
	public function getDefaultCount() : int{
		return $this->defaultCount;
	}

	/**
	 * @param int $defaultCount
	 */
	public function setDefaultCount(int $defaultCount) : void{
		$this->defaultCount = $defaultCount;
	}

	/**
	 * @return string
	 */
	public function getPrice() : string{
		return $this->price;
	}

	/**
	 * @param string $price
	 */
	public function setPrice(string $price) : void{
		$this->price = $price;
	}

	/**
	 * @param string $playerName
	 *
	 * @return null|VirtualChestContainer
	 */
	public function loadPlayerData(string $playerName) : ?VirtualChestContainer{
		if(file_exists($file = "{$this->getServer()->getDataPath()}players/{$playerName}.dat")){
			try{
				$nbt = (new BigEndianNBTStream())->readCompressed(file_get_contents($file));
				if($nbt instanceof CompoundTag){
					$namedTag = $nbt->getCompoundTag("VirtualChest");
					if($nbt instanceof CompoundTag){
						$container = VirtualChestContainer::nbtDeserialize($playerName, $namedTag);
						VirtualChestContainer::setContainer($playerName, $container);
						return $container;
					}
				}else{
					$this->getLogger()->critical("Invalid data found in \"{$playerName}.dat\", expected " . CompoundTag::class . ", got " . (is_object($namedTag) ? get_class($namedTag) : gettype($namedTag)));
				}
			}catch(\Throwable $e){
				$this->getLogger()->critical($e->getMessage());
			}
		}
		return null;
	}
}