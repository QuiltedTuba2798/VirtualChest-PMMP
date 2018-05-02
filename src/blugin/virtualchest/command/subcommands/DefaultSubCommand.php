<?php

declare(strict_types=1);

namespace blugin\virtualchest\command\subcommands;

use pocketmine\command\CommandSender;
use blugin\virtualchest\command\{
  PoolCommand, SubCommand
};
use blugin\virtualchest\util\Utils;

class DefaultSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'default');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (isset($args[0])) {
            $count = Utils::toInt($args[0], null, function (int $i){
                return $i > 0;
            });
            if ($count === null) {
                $sender->sendMessage($this->plugin->getLanguage()->translate('commands.generic.player.notFound', [$args[0]]));
            } else {
                $this->plugin->getConfig()->set('default-count', $count);
                $sender->sendMessage($this->translate('success', (string) $count));
            }
            return true;
        }
        return false;
    }
}