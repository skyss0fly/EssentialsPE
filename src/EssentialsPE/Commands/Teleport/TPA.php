<?php
namespace EssentialsPE\Commands\Teleport;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TPA extends BaseCommand{
    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api){
        parent::__construct($api, "tpa", "Asks the player if you can teleport to them", "<player>", false, ["call", "tpask"]);
        $this->setPermission("essentials.tpa");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $alias, array $args) {
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!$sender instanceof Player || count($args) !== 1){
            $this->sendUsage($sender, $alias);
            return false;
        }
        if(!($player = $this->getAPI()->getPlayer($args[0]))){
            $sender->sendMessage(TextFormat::RED . "[Error] §6This Player cannot be found");
            return false;
        }
        if($player->getName() === $sender->getName()){
            $sender->sendMessage(TextFormat::RED . "[Error] §6You can't teleport to yourself. Try adding a player's name, but not your name.");
            return false;
        }
        $this->getAPI()->requestTPTo($sender, $player);
        $player->sendMessage(TextFormat::AQUA . $sender->getName() . TextFormat::GREEN . " §bwants to teleport to you, please use:\n§3/tpaccept | /tpyes - §bto accept the request.\n§3/tpdeny | /tpno - §bto deny the invitation");
        $sender->sendMessage(TextFormat::GREEN . "§bTeleport request sent to " . $player->getDisplayName() . "Succesfully!");
        return true;
    }
} 
