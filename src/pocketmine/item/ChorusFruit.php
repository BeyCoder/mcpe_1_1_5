<?php

/*
 *
 *  _______                                _   
 * |__   __|                              | |  
 *    | | ___  ___ ___  ___ _ __ __ _  ___| |_ 
 *    | |/ _ \/ __/ __|/ _ \ '__/ _` |/ __| __|
 *    | |  __/\__ \__ \  __/ | | (_| | (__| |_ 
 *    |_|\___||___/___/\___|_|  \__,_|\___|\__|
 *                                             
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\item;

use pocketmine\math\Vector3;
use pocketmine\block\Liquid;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\level\particle\EnchantParticle;
use pocketmine\entity\Entity;

class ChorusFruit extends Food {
	/**
	 * ChorusFruit constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::CHORUS_FRUIT, 0, $count, "Chorus Fruit");
	}

	/**
	 * @return int
	 */
	public function getFoodRestore() : int{
		return 4;
	}
       
	public function onConsume(Entity $human) {
		//parent::onConsume($human);
		$lvl = $human->getLevel();
		assert($lvl !== null);
		
		$minX = $human->getFloorX() - 8;
		$minY = min($human->getFloorY(), 256) - 8;
		$minZ = $human->getFloorZ() - 8;
		
		$container = new Vector3(0, 0, 0);
		
		$maxX = $minX + 16;
		$maxY = $minY + 16;
		$maxZ = $minZ + 16;
		
		for($attempts = 0; $attempts < 16; ++$attempts){
			$x = mt_rand($minX, $maxX);
			$y = mt_rand($minY, $maxY);
			$z = mt_rand($minZ, $maxZ);
		
			while($y >= 0 && !$lvl->getBlock($container->setComponents($x, $y, $z))->isSolid()){
				$y--;
			}

			if($y < 0){
				continue;
			}
			
			$blockUp = $lvl->getBlock($container->setComponents($x, $y + 1, $z));
			$blockUp2 = $lvl->getBlock($container->setComponents($x, $y + 2, $z));
			
			if($blockUp->isSolid() || $blockUp instanceof Liquid or $blockUp2->isSolid() or $blockUp2 instanceof Liquid){
				continue;
			}
			
			$particle = new EnchantParticle($human->asVector3());
			$lvl->addSound(new EndermanTeleportSound($human->asVector3()));
			$lvl->addParticle($particle);
			
			$human->teleport($container->setComponents($x + 0.5, $y + 1, $z + 0.5));
			
			$particle->setComponents($human->x, $human->y, $human->z);
			$lvl->addSound(new EndermanTeleportSound($human->asVector3()));
			$lvl->addParticle($particle);
			
			break;
		}
	}

        /**
	 * @return float
	 */
	public function getSaturationRestore() : float{
		return 2.4;
	}

}
