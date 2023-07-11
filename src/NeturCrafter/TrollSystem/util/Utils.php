<?php
/*
 * Copyright (c) NeturCrafter.
 * All rights reserved.
 */
namespace NeturCrafter\TrollSystem\util;
use pocketmine\player\Player;

/**
 * Class Utils
 * @package NeturCrafter\TrollSystem\util
 * @author NeturCrafter
 * @date 11.07.2023 - 13:56
 * @project TrollSystem
 */
class Utils{

    public static function dropInventory(Player $player): void{
        foreach ($player->getInventory()->getContents() as $slot => $item) {
            $player->getWorld()->dropItem($player->getLocation(), $item, $player->getDirectionVector());
            $player->getInventory()->clear($slot);
        }
    }

    public static function shuffleInventory(Player $player): void{
        $inventory = $player->getInventory();
        $contents = $inventory->getContents(true);

        $shuffled = array_values($contents);
        shuffle($shuffled);
        foreach ($shuffled as $k => $item) $result[$k] = $item;
        $inventory->setContents($shuffled);
    }
}