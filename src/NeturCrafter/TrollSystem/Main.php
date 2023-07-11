<?php
/*
 * Copyright (c) NeturCrafter.
 * All rights reserved.
 */
namespace NeturCrafter\TrollSystem;
use NeturCrafter\TrollSystem\command\TrollCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

/**
 * Class Main
 * @package NeturCrafter\TrollSystem
 * @author NeturCrafter
 * @date 11.07.2023 - 13:14
 * @project TrollSystem
 */
final class Main extends PluginBase {
    use SingletonTrait{
        setInstance as private;
        reset as private;
    }

    protected function onLoad(): void{
        self::setInstance($this);
    }

    protected function onEnable(): void{
        $this->getServer()->getCommandMap()->register($this->getName(), new TrollCommand("troll"));
    }
}
