<?php

namespace presentkim\virtualchest\command;


use pocketmine\command\{
  Command, CommandExecutor, CommandSender, PluginCommand
};
use presentkim\virtualchest\util\Translation;
use presentkim\virtualchest\VirtualChestMain as Plugin;

class PoolCommand extends PluginCommand implements CommandExecutor{

    /** @var SubCommand[] */
    protected $subCommands = [];

    /** @var string */
    private $name;

    /** @var string */
    public $uname;

    /**
     * @param Plugin       $owner
     * @param string       $name
     * @param SubCommand[] $subCommands
     */
    public function __construct(Plugin $owner, string $name, SubCommand ...$subCommands){
        parent::__construct($name, $owner);
        $this->setExecutor($this);

        $this->uname = $name;
        $this->setPermission("{$name}.cmd");
        $this->updateTranslation();

        $this->subCommands = $subCommands;
    }

    /**
     * @param CommandSender $sender
     * @param Command       $command
     * @param string        $label
     * @param string[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if (!isset($args[0])) {
            return false;
        } else {
            $label = array_shift($args);
            foreach ($this->subCommands as $key => $value) {
                if ($value->checkLabel($label)) {
                    $value->execute($sender, $args);
                    return true;
                }
            }
            return false;
        }
    }

    /** @return SubCommand[] */
    public function getSubCommands() : array{
        return $this->subCommands;
    }

    /** @param SubCommand[] $subCommands */
    public function setSubCommands(SubCommand ...$subCommands){
        $this->subCommands = $subCommands;
    }

    /** @param SubCommand::class $subCommandClass */
    public function createSubCommand($subCommandClass){
        $this->subCommands[] = new $subCommandClass($this);
    }

    public function updateTranslation(){
        $this->name = Translation::translate("command-{$this->uname}");
        $this->description = Translation::translate("command-{$this->uname}@description");
        $this->usageMessage = Translation::translate("command-{$this->uname}@usage");
        $aliases = Translation::getArray("command-{$this->uname}@aliases");
        if (is_array($aliases)) {
            $this->setAliases($aliases);
        }
    }

    public function updateSudCommandTranslation(){
        foreach ($this->subCommands as $key => $value) {
            $value->updateTranslation();
        }
    }

    /** @return string */
    public function getName() : string{
        return $this->name;
    }
}