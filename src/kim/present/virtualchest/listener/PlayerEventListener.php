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

namespace kim\present\virtualchest\listener;

use kim\present\virtualchest\container\VirtualChestContainer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerEventListener implements Listener{
	/**
	 * @priority LOWEST
	 *
	 * @param PlayerQuitEvent $event
	 */
	public function onPlayerQuitEvent(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		$container = VirtualChestContainer::getContainer($playerName = $player->getLowerCaseName());
		if($container !== null){
			$player->namedtag->setTag($container->nbtSerialize("VirtualChest"));
		}
	}
}