<?php

namespace GlobalMessage\Utils;

use GlobalMessage\Main;
use GlobalMessage\Task\GlobalMessageTask;
use pocketmine\network\mcpe\protocol\ToastRequestPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class GlobalMessageManager {

    /** @var Config */
    public Config $config;

    /** @var array */
    public static array $configSettings = [];

    public function __construct() {

        $this->config = new Config(Main::getInstance()->getDataFolder() . "config-globalmessage.yml", Config::YAML);

        self::$configSettings = $this->config->get("config-settings");
    }

    /**
     * @return void
     */
    public static function initConfig(): void {

        if(!file_exists(Main::getInstance()->getDataFolder() . "config-globalmessage.yml"))
            Main::getInstance()->saveResource("config-globalmessage.yml");

        Main::$config = new Config(Main::getInstance()->getDataFolder() . "config-globalmessage.yml", Config::YAML);
    }

    /**
     * @param array $configMessage
     * @param array $configSettings
     * @param int $baseNumber
     * @return void
     */
    public static function sendGlobalMessage(array $configMessage, array $configSettings, int $baseNumber): void {

        if($configSettings["type"] === "message")
            Server::getInstance()->broadcastMessage($configMessage["global-messages"][$baseNumber]);
        elseif($configSettings["type"] === "popup")
            Server::getInstance()->broadcastPopup($configMessage["global-messages"][$baseNumber]);
        elseif($configSettings["type"] === "toast")
            self::sendToast("§6§lGlobal Message", $configMessage["global-messages"][$baseNumber]);
    }

    /**
     * @return void
     */
    public static function initTask(): void {
        Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new GlobalMessageTask(), (Main::$config->get("config-settings")["interval-message"] * 20), (Main::$config->get("config-settings")["interval-message"] * 20));
    }

    /**
     * @param string $title
     * @param string $body
     * @return void
     */
    public static function sendToast(string $title, string $body): void {

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {

            $player->getNetworkSession()->sendDataPacket(ToastRequestPacket::create($title, $body));
        }
    }
}
