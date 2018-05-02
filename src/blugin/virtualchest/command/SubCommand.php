<?php

declare(strict_types=1);

namespace blugin\virtualchest\command;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use blugin\virtualchest\VirtualChest;
use blugin\virtualchest\util\Utils;

abstract class SubCommand{

    /** @var PoolCommand */
    protected $owner;

    /** @var VirtualChest */
    protected $plugin;

    /** @var string */
    protected $strId;

    /** @var string */
    protected $permission;

    /** @var string */
    protected $label;

    /** @var string[] */
    protected $aliases;

    /** @var string */
    protected $usage;

    /**
     * SubCommand constructor.
     *
     * @param PoolCommand $owner
     * @param string      $label
     */
    public function __construct(PoolCommand $owner, string $label){
        $this->owner = $owner;
        $this->plugin = $owner->getPlugin();

        $this->strId = "commands.{$owner->uname}.{$label}";
        $this->permission = "{$owner->uname}.cmd.{$label}";

        $this->updateTranslation();
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     */
    public function execute(CommandSender $sender, array $args) : void{
        if (!$this->checkPermission($sender)) {
            $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.permission'));
        } elseif (!$this->onCommand($sender, $args)) {
            $sender->sendMessage(Server::getInstance()->getLanguage()->translateString("commands.generic.usage", [$this->usage]));
        }
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    abstract public function onCommand(CommandSender $sender, array $args) : bool;

    /**
     * @param CommandSender $target
     *
     * @return bool
     */
    public function checkPermission(CommandSender $target) : bool{
        if ($this->permission === null) {
            return true;
        } else {
            return $target->hasPermission($this->permission);
        }
    }

    /**
     * @param string   $tag
     * @param string[] $params
     *
     * @return string
     */
    public function translate(string $tag, string ...$params) : string{
        return $this->plugin->getLanguage()->translate("{$this->strId}.{$tag}", $params);
    }

    /**
     * @param string $label
     *
     * @return bool
     */
    public function checkLabel(string $label) : bool{
        return strcasecmp($label, $this->label) === 0 || $this->aliases && Utils::in_arrayi($label, $this->aliases);
    }

    public function updateTranslation() : void{
        $this->label = $this->plugin->getLanguage()->translate($this->strId);
        $this->aliases = $this->plugin->getLanguage()->getArray("{$this->strId}.aliases");
        $this->usage = $this->translate('usage');
    }

    /** @return string */
    public function getLabel() : string{
        return $this->label;
    }

    /** @param string $label */
    public function setLabel(string $label) : void{
        $this->label = $label;
    }

    /**  @return string[] */
    public function getAliases() : array{
        return $this->aliases;
    }

    /** @param string[] $aliases */
    public function setAliases(array $aliases) : void{
        $this->aliases = $aliases;
    }

    /** @return string */
    public function getUsage() : string{
        return $this->usage;
    }

    /** @param string $usage */
    public function setUsage(string $usage) : void{
        $this->usage = $usage;
    }
}