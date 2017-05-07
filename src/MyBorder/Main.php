<?php

namespace MyBorder;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;

Class Main extends PluginBase implements Listener
{
    public function onEnable(){
        $this->getServer()->getLogger()->info("MBorder is loaded!");
    }



}