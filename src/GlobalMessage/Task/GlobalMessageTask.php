<?php

namespace GlobalMessage\Task;

use GlobalMessage\Main;
use GlobalMessage\Utils\GlobalMessageManager;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class GlobalMessageTask extends Task {

    /** @var Config */
    public Config $config;

    /** @var int */
    public int $baseNumber = 0;

    /** @var array */
    public array $configMessage = [];

    /** @var array */
    public array $configSettings = [];

    public function __construct() {

        $this->config = new Config(Main::getInstance()->getDataFolder() . "config-globalmessage.yml", Config::YAML);
        $this->baseNumber = 0;

        $this->configMessage = $this->config->get("config-message");
        $this->configSettings = $this->config->get("config-settings");
    }

    public function onRun(): void {

        GlobalMessageManager::sendGlobalMessage($this->configMessage, $this->configSettings, $this->baseNumber);

        if((count($this->configMessage["global-messages"]) - 1) === $this->baseNumber) {
            $this->baseNumber = 0;
            return;
        }

        $this->baseNumber++;
    }
}
