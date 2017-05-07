<?php

namespace MyBorder;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerMoveEvent, PlayerQuitEvent};
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as F;
use pocketmine\math\Vector3;

Class Main extends PluginBase implements Listener
{
    public $positions = array();
    public $config;

    public function onEnable(){
        $this->getServer()->getLogger()->info(F::GREEN."MBorder is loaded!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, array(
            "world" => 100
        ));
    }

    public function Move(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $Plevel = $player->getLevel();
        $config = $this->config->getAll();
        foreach($config as $level => $radius){
            $lvl = $this->getServer()->getLevelByName($level);
            if($Plevel === $lvl){
                $v = new Vector3($lvl->getSpawnLocation()->getX(),$lvl->getSpawnLocation()->getY(),$lvl->getSpawnLocation()->getZ());
                if($player->getPosition()->distance($v) >= $radius){
                    $event->setCancelled();
                    $player->sendPopup(F::YELLOW. "[MyBorder]" .F::GOLD. " Вы далеко зашли!");
                }
            }
        }
        if($event->isCancelled()){
            if(isset($this->positions[$player->getName()])){
                $player->setMotion($this->positions[$player->getName()]);
            }
        }else{
            $this->positions[$player->getName()] = $player->getLocation();
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if(isset($this->positions[$player->getName()])){
            unset($this->positions[$player->getName()]);
        }
    }
}