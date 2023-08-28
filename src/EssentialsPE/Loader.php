<?php
namespace EssentialsPE;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Commands\AFK;
use EssentialsPE\Commands\Antioch;
use EssentialsPE\Commands\Back;
use EssentialsPE\Commands\BreakCommand;
use EssentialsPE\Commands\Broadcast;
use EssentialsPE\Commands\Burn;
use EssentialsPE\Commands\ClearInventory;
use EssentialsPE\Commands\Compass;
use EssentialsPE\Commands\Condense;
use EssentialsPE\Commands\Depth;
use EssentialsPE\Commands\Economy\Balance;
use EssentialsPE\Commands\Economy\Eco;
use EssentialsPE\Commands\Economy\Pay;
use EssentialsPE\Commands\Economy\Sell;
use EssentialsPE\Commands\Economy\SetWorth;
use EssentialsPE\Commands\Economy\Worth;
use EssentialsPE\Commands\Economy\BalanceTop;
use EssentialsPE\Commands\EssentialsPE;
use EssentialsPE\Commands\Feed;
use EssentialsPE\Commands\Extinguish;
use EssentialsPE\Commands\Fly;
use EssentialsPE\Commands\GetPos;
use EssentialsPE\Commands\Heal;
use EssentialsPE\Commands\Home\DelHome;
use EssentialsPE\Commands\Home\Home;
use EssentialsPE\Commands\Home\SetHome;
use EssentialsPE\Commands\ItemCommand;
use EssentialsPE\Commands\ItemDB;
use EssentialsPE\Commands\Jump;
use EssentialsPE\Commands\KickAll;
use EssentialsPE\Commands\Kit;
use EssentialsPE\Commands\Lightning;
use EssentialsPE\Commands\More;
use EssentialsPE\Commands\Mute;
use EssentialsPE\Commands\Near;
use EssentialsPE\Commands\Nick;
use EssentialsPE\Commands\Nuke;
use EssentialsPE\Commands\Override\Gamemode;
use EssentialsPE\Commands\Override\Kill;
use EssentialsPE\Commands\Override\Msg;
use EssentialsPE\Commands\Ping;
use EssentialsPE\Commands\PowerTool\PowerTool;
use EssentialsPE\Commands\PowerTool\PowerToolToggle;
use EssentialsPE\Commands\PTime;
use EssentialsPE\Commands\RealName;
use EssentialsPE\Commands\Repair;
use EssentialsPE\Commands\Reply;
use EssentialsPE\Commands\Seen;
use EssentialsPE\Commands\SetSpawn;
use EssentialsPE\Commands\Spawn;
use EssentialsPE\Commands\Speed;
use EssentialsPE\Commands\Sudo;
use EssentialsPE\Commands\Suicide;
use EssentialsPE\Commands\Teleport\TPA;
use EssentialsPE\Commands\Teleport\TPAccept;
use EssentialsPE\Commands\Teleport\TPAHere;
use EssentialsPE\Commands\Teleport\TPAll;
use EssentialsPE\Commands\Teleport\TPDeny;
use EssentialsPE\Commands\Teleport\TPHere;
use EssentialsPE\Commands\TempBan;
use EssentialsPE\Commands\Top;
use EssentialsPE\Commands\Unlimited;
use EssentialsPE\Commands\Vanish;
use EssentialsPE\Commands\Warp\DelWarp;
use EssentialsPE\Commands\Warp\Setwarp;
use EssentialsPE\Commands\Warp\Warp;
use EssentialsPE\Commands\Whois;
use EssentialsPE\Commands\World;
use EssentialsPE\EventHandlers\OtherEvents;
use EssentialsPE\EventHandlers\PlayerEvents;
use EssentialsPE\EventHandlers\SignEvents;
use EssentialsPE\Events\CreateAPIEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{
    /** @var BaseAPI */
    private $api;

    public function onEnable(): void{
        if($this->getConfig()->get("enable") === false) {
           $this->setEnabled(false);
        }
        // Before anything else...
        $this->checkConfig();

        // Custom API Setup :3
        $this->getServer()->getPluginManager()->callEvent($ev = new CreateAPIEvent($this, BaseAPI::class));
        $class = $ev->getClass();
        $this->api = new $class($this);

        // Other startup code...
        if(!is_dir($this->getDataFolder())){
            mkdir($this->getDataFolder());
        }
        
	$this->getLogger()->info(TextFormat::YELLOW . "Loading...");
        $this->registerEvents();
        $this->registerCommands();
        if(count($p = $this->getServer()->getOnlinePlayers()) > 0){
            $this->getAPI()->createSession($p);
        }
        if($this->getAPI()->isUpdaterEnabled()){
            $this->getAPI()->fetchEssentialsPEUpdate(false);
        }
        $this->getAPI()->scheduleAutoAFKSetter();
    }

    public function onDisable(): void{
        if(count($l = $this->getServer()->getOnlinePlayers()) > 0){
            $this->getAPI()->removeSession($l);
        }
        $this->getAPI()->close();
    }

    /**
     * Function to register all the Event Handlers that EssentialsPE provide
     */
    public function registerEvents(){
        $this->getServer()->getPluginManager()->registerEvents(new OtherEvents($this->getAPI()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEvents($this->getAPI()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new SignEvents($this->getAPI()), $this);
    }

    /**
     * Function to register all EssentialsPE's commands...
     * And to override some default ones
     */
    private function registerCommands(){
        $commands = [
            new AFK($this->getAPI()),
            new Antioch($this->getAPI()),
            new Back($this->getAPI()),
            //new BigTreeCommand($this->getAPI()), TODO
            new BreakCommand($this->getAPI()),
            new Broadcast($this->getAPI()),
            new Burn($this->getAPI()),
            new ClearInventory($this->getAPI()),
            new Compass($this->getAPI()),
            new Condense($this->getAPI()),
            new Depth($this->getAPI()),
            new EssentialsPE($this->getAPI()),
            new Extinguish($this->getAPI()),
            new Fly($this->getAPI()),
            new GetPos($this->getAPI()),
            //new Hat($this->getAPI()), TODO: Implement when MCPE implements "Block-Hat rendering"
            new Heal($this->getAPI()),
            new ItemCommand($this->getAPI()),
            new ItemDB($this->getAPI()),
            new Jump($this->getAPI()),
            new KickAll($this->getAPI()),
            new Kit($this->getAPI()),
            new Lightning($this->getAPI()),
            new More($this->getAPI()),
            new Mute($this->getAPI()),
            new Near($this->getAPI()),
            new Nick($this->getAPI()),
            new Nuke($this->getAPI()),
            new Ping($this->getAPI()),
            new Feed($this->getAPI()),
            new PTime($this->getAPI()),
            new RealName($this->getAPI()),
            new Repair($this->getAPI()),
            new Seen($this->getAPI()),
            new SetSpawn($this->getAPI()),
            new Spawn($this->getAPI()),
            new Speed($this->getAPI()),
            new Sudo($this->getAPI()),
            new Suicide($this->getAPI()),
            new TempBan($this->getAPI()),
            new Top($this->getAPI()),
            //new TreeCommand($this->getAPI()), TODO
            new Unlimited($this->getAPI()),
            new Vanish($this->getAPI()),
            new Whois($this->getAPI()),
            new World($this->getAPI()),
		
            // Messages
            new Msg($this->getAPI()),
            new Reply($this->getAPI()),
		
            // Override
            new Gamemode($this->getAPI()),
            new Kill($this->getAPI())		
		];
	    
		$economyCommands = [
	        new Balance($this->getAPI()),
	        new Eco($this->getAPI()),
	        new Pay($this->getAPI()),
	        new Sell($this->getAPI()),
	        new SetWorth($this->getAPI()),
	        new Worth($this->getAPI()),
	        new BalanceTop($this->getAPI())
		];

		$homeCommands = [
	        new DelHome($this->getAPI()),
	        new Home($this->getAPI()),
	        new SetHome($this->getAPI())
		];

		$powertoolCommands = [
	        new PowerTool($this->getAPI()),
			new PowerToolToggle($this->getAPI())
		];

		$teleportCommands = [
	        new TPA($this->getAPI()),
	        new TPAccept($this->getAPI()),
	        new TPAHere($this->getAPI()),
	        new TPAll($this->getAPI()),
	        new TPDeny($this->getAPI()),
	        new TPHere($this->getAPI())
		];

		$warpCommands = [
	        new DelWarp($this->getAPI()),
	        new Setwarp($this->getAPI()),
	        new Warp($this->getAPI())
		];


		if($this->getServer()->getPluginManager()->getPlugin("SimpleWarp") === null) {
	            foreach($warpCommands as $warpCommand) {
		        if($this->getConfig()->get("warps") === true) {
			    $commands[] = $warpCommand;
		        }
		    }
	    } else {
	        $this->getLogger()->info(TextFormat::YELLOW . "SimpleWarp installed, disabling EssentialsPE warps...");
	    }

		foreach($teleportCommands as $teleportCommand) {
		    if($this->getConfig()->get("teleporting") === true) {
			 $commands[] = $teleportCommand;
		    }
		}

		foreach($powertoolCommands as $powertoolCommand) {
		    if($this->getConfig()->get("powertool") === true) {
			 $commands[] = $powertoolCommand;
		    }
		}

		foreach($homeCommands as $homeCommand) {
		    if($this->getConfig()->get("homes") === true) {
			 $commands[] = $homeCommand;
		    }
		}
		foreach($economyCommands as $economyCommand) {
		    if($this->getConfig()->get("economy") === true) {
			 $commands[] = $economyCommand;
		    }
		}
	    
        $aliased = [];
        foreach($commands as $cmd){
            /** @var BaseCommand $cmd */
            $commands[$cmd->getName()] = $cmd;
            $aliased[$cmd->getName()] = $cmd->getName();
            foreach($cmd->getAliases() as $alias){
                $aliased[$alias] = $cmd->getName();
            }
        }
        $cfg = $this->getConfig()->get("commands", []);
        foreach($cfg as $del){
            if(isset($aliased[$del])){
                unset($commands[$aliased[$del]]);
            }else{
                $this->getLogger()->debug("\"$del\" command not found inside EssentialsPE, skipping...");
            }
        }
        $this->getServer()->getCommandMap()->registerAll("EssentialsPE", $commands);
    }

    public function checkConfig(){
        if(!is_dir($this->getDataFolder())){
            mkdir($this->getDataFolder());
        }
        if(!file_exists($this->getDataFolder() . "config.yml")){
            $this->saveDefaultConfig();
        }
        $this->saveResource("Economy.yml");
        $this->saveResource("Kits.yml");
        $this->saveResource("Warps.yml");
        $cfg = $this->getConfig();

        if(!$cfg->exists("version") || $cfg->get("version") !== "0.0.3"){
            $this->getLogger()->debug(TextFormat::RED . "An invalid config file was found, generating a new one...");
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config.yml.old");
            $this->saveDefaultConfig();
            $cfg = $this->getConfig();
        }

        $booleans = ["enable-custom-colors"];
        foreach($booleans as $key){
            $value = null;
            if(!$cfg->exists($key) || !is_bool($cfg->get($key))){
                switch($key){
                    // Properties to auto set true
                    case "safe-afk":
                        $value = true;
                        break;
                    // Properties to auto set false
                    case "enable-custom-colors":
                        $value = false;
                        break;
                }
            }
            if($value !== null){
                $cfg->set($key, $value);
            }
        }

        $integers = ["oversized-stacks", "near-radius-limit", "near-default-radius"];
        foreach($integers as $key){
            $value = null;
            if(!is_numeric($cfg->get($key))){
                switch($key){
                    case "auto-afk-kick":
                        $value = 300;
                        break;
                    case "oversized-stacks":
                        $value = 64;
                        break;
                    case "near-radius-limit":
                        $value = 200;
                        break;
                    case "near-default-radius":
                        $value = 100;
                        break;
                }
            }
            if($value !== null){
                $cfg->set($key, $value);
            }
        }

        $afk = ["safe", "auto-set", "auto-broadcast", "auto-kick", "broadcast"];
        foreach($afk as $key){
            $value = null;
            $k = $this->getConfig()->getNested("afk." . $key);
            switch($key){
                case "safe":
                case "auto-broadcast":
                case "broadcast":
                    if(!is_bool($k)){
                        $value = true;
                    }
                    break;
                case "auto-set":
                case "auto-kick":
                    if(!is_int($k)){
                        $value = 300;
                    }
                    break;
            }
            if($value !== null){
                $this->getConfig()->setNested("afk." . $key, $value);
            }
        }

        $updater = ["enabled", "time-interval", "warn-console", "warn-players", "channel"];
        foreach($updater as $key){
            $value = null;
            $k = $this->getConfig()->getNested("updater." . $key);
            switch($key){
                case "time-interval":
                    if(!is_int($k)){
                        $value = 1800;
                    }
                    break;
                case "enabled":
                case "warn-console":
                case "warn-players":
                    if(!is_bool($k)){
                        $value = true;
                    }
                    break;
                case "channel":
                    if(!is_string($k) || ($k !== "stable" && $k !== "beta" && $k !== "development")){
                        $value = "stable";
                    }
            }
            if($value !== null){
                $this->getConfig()->setNested("updater." . $key, $value);
            }
        }
    }

    /**
     * @return BaseAPI
     */
    public function getAPI(): BaseAPI{
        return $this->api;
    }
}
