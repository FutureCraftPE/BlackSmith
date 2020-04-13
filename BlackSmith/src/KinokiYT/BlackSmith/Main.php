<?php

namespace KinokiYT\BlackSmith;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\event\Listener;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\Armor;

use jojoe77777\FormAPI\{SimpleForm, CustomForm};
use onebone\economyapi\EconomyAPI;

use pocketmine\utils\TextFormat as TF;


class Main extends PluginBase implements Listener {
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        if($sender instanceof Player) {
        switch($cmd->getName()) {
            case "blacksmith":
                $this->uiform($sender);
            }
            return true;
        }
        return false;
    }
    public function uiform(Player $sender) {
        $form = new SimpleForm(function(Player $sender, ?int $data) {
            if(!isset($data)) return;
                switch($data) {
                case 0:
                    $this->repair($sender);
                    break;
                case 1:
                    $this->rename($sender);
                    break;
                case 2:
                    break;
            }
        });
        $form->setTitle("» " . TF::GOLD . "BlackSmith" . TF::DARK_GRAY . " - " . TF::GRAY . "Repair/Rename". TF::DARK_GRAY . " «");
        $form->setContent(TF::DARK_AQUA . "Be sure that you are hold the item that you would like to Repair/Rename!");
        $form->addButton("Repair");
        $form->addButton("Rename Item");
        $form->addButton(TF::RED . "Exit");
        $form->sendToPlayer($sender);
    }
    public function repair(Player $sender) {
        $f = new CustomForm(function(Player $sender, ?array $data){
            if(!isset($data)) return;
        $xp = $this->getConfig()->get("repair");
        $pxp = $sender->getXpLevel();
        $dg = $sender->getInventory()->getItemInHand()->getDamage();
        if($pxp >= $xp * $dg){
        $sender->subtractXpLevels($xp * $dg);
        $index = $sender->getPlayer()->getInventory()->getHeldItemIndex();
        $item = $sender->getInventory()->getItem($index);
	    $id = $item->getId();
            if($item instanceof Armor or $item instanceof Tool){
                if($item->getDamage() > 0){
                    $sender->getInventory()->setItem($index, $item->setDamage(0));
                        $sender->sendMessage(TF::DARK_GRAY . "[" . TF::GOLD . "BlackSmith" . TF::DARK_GRAY . "] " . TF::DARK_AQUA . "Your item has been repaired.");
                    return true;
                        }else{
                            $sender->sendMessage(TF::RED . "[Error] " . TF::GRAY . "This item can't be repaired. It's not a repairable item.");
                                return false;
                            }
							return true;
							}else{
							    $sender->sendMessage(TF::RED . "[Error] " . TF::GRAY . "This item can't be repaired. It's not a repairable item.");
								return false;
						}
						return true;
						}else{
                            $sender->sendMessage(TF::RED . "[Error] " . TF::GRAY . "You don't have enough xp to repair this item.");
                            return true;
					}
					});
    $xp = $this->getConfig()->get("repair");
        $dg = $sender->getInventory()->getItemInHand()->getDamage();
        $pc = $xp * $dg;
        $xps = $sender->getXpLevel();
        $f->setTitle("» " . TF::GOLD . "BlackSmith" . TF::DARK_GRAY . " - " . TF::GRAY . "Repair" . TF::DARK_GRAY . " «");
        $f->addLabel("§7Your XP§8: §a$xps\n§7XP per damage§8: §a$xp\n§7Item damage§8: §a$dg\n\n§7Total XP needed§8: §a$pc");
        $f->sendToPlayer($sender);
	}
    public function rename(Player $sender) {
        $f = new CustomForm(function(Player $sender, ?array $data) {
            if(!isset($data)) return;
                $item = $sender->getInventory()->getItemInHand();
                    if($item->getId() == 0) {
                        $sender->sendMessage(TF::RED . "[Error] " . TF::GRAY . "Hold the item in your hand!");
                        return;
                    }
                $xp = $this->getConfig()->get("rename");
                $pxp = $sender->getXpLevel();
                if($pxp >= $xp) {
                    $sender->subtractXpLevels($xp);
                        $item->setCustomName(TF::colorize($data[1]));
                        $sender->getInventory()->setItemInHand($item);
                        $sender->sendMessage(TF::DARK_GRAY . "[" . TF::GOLD . "BlackSmith" . TF::DARK_GRAY . "] " . TF::DARK_AQUA . "Successfully changed item name to §6$data[1]");
                        } else {
                    $sender->sendMessage(TF::RED . "[Error] " . TF::GRAY . "You don't have enough xp to rename this item!");
                }
            });
        $xp = $this->getConfig()->get("rename");
        $pxp = $sender->getXpLevel();
        $f->setTitle("» " . TF::GOLD . "BlackSmith" . TF::DARK_GRAY . " - " . TF::GRAY . "Rename" . TF::DARK_GRAY . " «");
        $f->addLabel("§7Rename Cost§8: §a$xp\n§7Your XP§8: §a$pxp");
        $f->addInput(TF::RED . "Rename Item:", "Overlord");
        $f->sendToPlayer($sender);
    }
}