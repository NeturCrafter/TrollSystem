<?php
/*
 * Copyright (c) NeturCrafter.
 * All rights reserved.
 */
namespace NeturCrafter\TrollSystem\command;
use Jibix\Forms\element\type\Input;
use Jibix\Forms\form\response\CustomFormResponse;
use Jibix\Forms\form\type\CustomForm;
use Jibix\Forms\form\type\MenuForm;
use Jibix\Forms\menu\Button;
use Jibix\Forms\menu\type\CloseButton;
use NeturCrafter\TrollSystem\util\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class TrollCommand
 * @package NeturCrafter\TrollSystem\command
 * @author NeturCrafter
 * @date 11.07.2023 - 13:18
 * @project TrollSystem
 */
class TrollCommand extends Command {

    public function __construct(string $name){
        parent::__construct($name, "Command to troll other players", "§cUsage: §7/{$name} <playerName>");
        parent::setPermission("troll.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if (!$sender instanceof Player){
            $sender->sendMessage("§cPlease use this command in-game");
            return;
        }
        if (!$this->testPermission($sender)) return;
        if (!isset($args[0])){
            $sender->sendMessage($this->getUsage());
            return;
        }
        $trolledPlayer = Server::getInstance()->getPlayerExact($args[0]);
        if (!$trolledPlayer instanceof Player){
            $sender->sendMessage("§cThe player $args[0] was not found!");
            return;
        }
        $sender->sendForm(new MenuForm(
            "§aTrolling {$trolledPlayer->getName()}",
            "Select an option to troll {$trolledPlayer->getName()}",
            [
                new CloseButton(),
                new Button("Burn", function (Player $player, Button $button) use ($trolledPlayer): void{
                    $trolledPlayer->setOnFire(200);
                    $player->sendMessage("§3{$trolledPlayer->getName()}§a is now on fire for 10seconds!");
                }),
                new Button("Void", function (Player $player, Button $button) use ($trolledPlayer): void{
                    $trolledPlayer->teleport(new Vector3(0,0,0));
                    $player->sendMessage("§3{$trolledPlayer->getName()}§a is now falling into the void!");
                }),
                new Button("Pumpkin-Head", function (Player $player, Button $button) use ($trolledPlayer): void{
                    $trolledPlayer->getArmorInventory()->setHelmet(VanillaBlocks::PUMPKIN()->asItem());
                    $player->sendMessage("§3{$trolledPlayer->getName()}§a now has a pumpkin head!");
                }),
                new Button("Inventory-Shuffle", function (Player $player, Button $button) use ($trolledPlayer): void{
                    Utils::shuffleInventory($trolledPlayer);
                    $player->sendMessage("§3{$trolledPlayer->getName()}§a now has a shuffled inventory!");
                }),
                new Button("Inventory-Drop", function (Player $player, Button $button) use ($trolledPlayer): void{
                    Utils::dropInventory($trolledPlayer);
                    $player->sendMessage("§3{$trolledPlayer->getName()}§a now has a dropped inventory!");
                }),
                new Button("Fake-OP", function (Player $player, Button $button) use ($trolledPlayer): void{
                    $trolledPlayer->sendMessage("§7You are now op!");
                    $player->sendMessage("§3{$trolledPlayer->getName()}§a was sent a Fake-OP message!");
                }),
                new Button("Fake-Ban", function (Player $player, Button $button) use ($trolledPlayer): void{
                    $trolledPlayer->kick("§fBanned by admin.", false);
                    $player->sendMessage("§3{$trolledPlayer->getName()}§a was Fake-Banned!");
                }),
                new Button("Fake-Join", function (Player $player, Button $button) use ($trolledPlayer): void{
                    Server::getInstance()->broadcastMessage(KnownTranslationFactory::multiplayer_player_joined($trolledPlayer->getName())->prefix(TextFormat::YELLOW));
                }),
                new Button("Fake-Quit", function (Player $player, Button $button) use ($trolledPlayer): void{
                    Server::getInstance()->broadcastMessage(KnownTranslationFactory::multiplayer_player_left($trolledPlayer->getName())->prefix(TextFormat::YELLOW));
                }),
                new Button("Sudo", function (Player $player, Button $button) use ($trolledPlayer): void{
                    $player->sendForm(new CustomForm(
                        "§aTrolling {$trolledPlayer->getName()}",
                        [
                            new Input("Sudo","Write a command or text")
                        ],
                        function (Player $player, CustomFormResponse $response): void{
                            $input = $response->getInput();
                            $player->chat($input->getValue());
                        }
                    ));
                }),
                new Button("Crash game\n§oLet the players game crash", function (Player $player, Button $button) use ($trolledPlayer): void{
                    $blob = [];
                    for ($i=0; $i<100; $i++) $blob[] = mt_rand(0, 10000);
                    $trolledPlayer->getNetworkSession()->sendDataPacket(LevelChunkPacket::create(new ChunkPosition(0, 0), 0, 0, $blob, "adsogfsdg"));
                }),
            ]
        ));
    }
}