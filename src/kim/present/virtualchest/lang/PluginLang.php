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

namespace kim\present\virtualchest\lang;

use kim\present\virtualchest\VirtualChest;

class PluginLang{
	public const FALLBACK_LANGUAGE = "eng";

	/** @var VirtualChest */
	protected $plugin;

	/** @var string[] */
	protected $lang = [];

	/** @var string[] */
	protected $fallbackLang = [];

	/**
	 * PluginLang constructor.
	 *
	 * @param VirtualChest $plugin
	 */
	public function __construct(VirtualChest $plugin){
		$this->plugin = $plugin;

		$fallbackLangResource = "{$plugin->getSourceFolder()}resources/lang/lang.ini";
		$dataFolder = $plugin->getDataFolder();
		$langFile = "{$dataFolder}lang.ini";
		$langResource = "{$plugin->getSourceFolder()}resources/lang/{$plugin->getServer()->getLanguage()->getLang()}.ini";
		if(!file_exists($langFile)){
			if(!file_exists($dataFolder)){
				mkdir($dataFolder, 0777, true);
			}
			copy(file_exists($langResource) ? $langResource : $fallbackLangResource, $langFile);
		}
		$this->lang = $this->loadLang($langFile);
		$this->fallbackLang = $this->loadLang($fallbackLangResource);
	}

	/**
	 * @param string $file
	 *
	 * @return null|array
	 */
	public function loadLang(string $file) : ?array{
		if(file_exists($file)){
			$result = [];
			foreach(parse_ini_file($file, false, INI_SCANNER_RAW) as $key => $value){
				if(is_string($value)){
					$result[$key] = stripcslashes($value);
				}elseif(is_array($value)){
					$result[$key] = [];
					foreach($value as $index => $str){
						$result[$key][] = stripcslashes($str);
					}
				}
			}
			return $result;
		}else{
			return null;
		}
	}

	/**
	 * @return VirtualChest
	 */
	public function getPlugin() : VirtualChest{
		return $this->plugin;
	}

	/**
	 * @return string[]
	 */
	public function getLang() : array{
		return $this->lang;
	}

	/**
	 * @param string[] $lang
	 */
	public function setLang(array $lang) : void{
		$this->lang = $lang;
	}

	/**
	 * @param string $id
	 *
	 * @return null|string[]
	 */
	public function getArray(string $id) : ?array{
		if(isset($this->lang[$id])){
			$result = $this->lang[$id];
		}elseif(isset($this->fallbackLang[$id])){
			$result = $this->fallbackLang[$id];
		}else{
			return null;
		}
		if(is_array($result)){
			return $result;
		}else{
			return [$result];
		}
	}

	/**
	 * @param string   $id
	 * @param string[] $params = []
	 *
	 * @return null|string
	 */
	public function translate(string $id, array $params = []) : ?string{
		$text = $this->get($id);
		if($text === null){
			return $id;
		}else{
			foreach($params as $i => $param){
				$text = str_replace("{%$i}", $param, $text);
			}
			return $text;
		}
	}

	/**
	 * @param string $id
	 *
	 * @return null|string
	 */
	public function get(string $id) : ?string{
		if(isset($this->lang[$id])){
			$result = $this->lang[$id];
		}elseif(isset($this->fallbackLang[$id])){
			$result = $this->fallbackLang[$id];
		}else{
			return null;
		}
		if(is_array($result)){
			return $result[array_rand($result)];
		}else{
			return $result;
		}
	}

	/**
	 * @return string[]
	 */
	public function getLanguageList() : array{
		$result = [];
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->plugin->getSourceFolder() . 'resources/lang/')) as $filePath => $fileInfo){
			if(substr($filePath, -4) == '.ini'){
				$lang = $this->loadLang($filePath);
				if(isset($lang['language.name'])){
					$result[substr($fileInfo->getFilename(), 0, -4)] = $lang['language.name'];
				}
			}
		}
		return $result;
	}
}