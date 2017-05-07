<?php

namespace MyBorder;

use pocketmine\math\Vector2;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerMoveEvent, PlayerQuitEvent, PlayerJoinEvent};
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as F;
//use pocketmine\math\Vector3;

Class Main extends PluginBase implements Listener
{
    public $config;

    public function onEnable(){
        $this->getServer()->getLogger()->info(F::GREEN."MBorder is loaded!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, array(
            "world" => 100
        ));
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $Plevel = $player->getLevel();
        $config = $this->config->getAll();
        foreach($config as $level => $radius){
            $lvl = $this->getServer()->getLevelByName($level);
            if($Plevel === $lvl){
                $v = new Vector2($lvl->getSpawnLocation()->getX(),$lvl->getSpawnLocation()->getZ());
                $p = new Vector2($player->getX(), $player->getZ());
                if($p->distance($v) >= $radius){
                    $player->teleport($lvl->getSpawnLocation());
                }
            }
        }
    }

    public function Move(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $Plevel = $player->getLevel();
        $config = $this->config->getAll();
        $moveTo = $event->getTo();
        foreach($config as $level => $radius){
            $lvl = $this->getServer()->getLevelByName($level);
            if($Plevel === $lvl){
                $v = new Vector2($lvl->getSpawnLocation()->getX(),$lvl->getSpawnLocation()->getZ());
                $p = new Vector2($moveTo->x, $moveTo->z);
                if($p->distance($v) >= $radius){
                    //$event->setCancelled();
                    $event->setTo($event->getFrom());
                    $player->sendPopup(F::YELLOW. "[MyBorder]" .F::GOLD. " Вы далеко зашли!");
                    return;
                }
            }
        }
    }

    public function onTeleport(EntityTeleportEvent $event){
        $player = $event->getEntity();
        if($player instanceof Player){
            $Plevel = $player->getLevel();
            $config = $this->config->getAll();
            $moveTo = $event->getTo();
            foreach($config as $level => $radius){
                $lvl = $this->getServer()->getLevelByName($level);
                if($Plevel === $lvl){
                    $v = new Vector2($lvl->getSpawnLocation()->getX(),$lvl->getSpawnLocation()->getZ());
                    $p = new Vector2($moveTo->x, $moveTo->z);
                    if($p->distance($v) >= $radius){
                        $event->setCancelled();
                        //$event->setTo($event->getFrom());
                        $player->sendMessage(F::YELLOW. "[MyBorder]" .F::GOLD. " Вам нельзя телепортироваться за пределы границы");
                        return;
                    }
                }
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if(isset($this->positions[$player->getName()])){
            unset($this->positions[$player->getName()]);
        }
    }
}