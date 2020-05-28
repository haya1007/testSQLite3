<?php

namespace sqlite3;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class Main extends PluginBase implements Listener{
	public function onEnable(){
		Server::getInstance()->getPluginManager()->registerEvents($this, $this);
		Server::getInstance()->getLogger()->info("SQLite3を読み込みました");

		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder(), 0744, true);
		}

		$dbfile = $this->getDataFolder()."test.db";

		if(!file_exists($dbfile)){
			$this->db = new \SQLite3($dbfile, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
		}else{
			$this->db = new \SQLite3($dbfile, SQLITE3_OPEN_READWRITE);
		}

		$this->db->query("CREATE TABLE IF NOT EXISTS  player (name TEXT PRIMARY KEY,ip TEXT, level TEXT)");
	}

	public function onDisable(){
		$this->db->close();
	}

	public function onJoin(PlayerJoinEvent $event){
		$this->regi($event);
	}

	public function regi($event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$ip = $player->getAddress();
		$level = $player->getLevel()->getName();
		$this->db->query("INSERT OR REPLACE INTO player VALUES(\"$name\", \"$ip\", \"$level\")");
	}

	public function chk($player, $name){
		$result = $this->db->query("SELECT ip, level FROM player WHERE name = \"$name\"");
		$rows = $result->fetchArray(SQLITE3_ASSOC);

/*		if($rows['ip'] == null){
			$player->sendMessage("データがありません");
		}elseif($result){*/
			$player->sendMessage("[Name] ".$name);
			$player->sendMessage("[IP] ". $rows['ip']);
			$player->sendMessage("[Level] ". $rows['level']);
		#}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if($sender instanceof Player){
			if($label == "player"){
				var_dump($args);
				$this->chk($sender, $args[0]);
			}
		}
		return true;
	}
}