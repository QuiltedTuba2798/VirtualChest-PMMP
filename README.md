[![Telegram](https://img.shields.io/badge/Telegram-PresentKim-blue.svg?logo=telegram)](https://t.me/PresentKim)

[![icon/96x96](https://raw.githubusercontent.com/PMMPPlugin/VirtualChest/master/meta/icon/96x96.png)]()

[![License](https://img.shields.io/github/license/PMMPPlugin/VirtualChest.svg?label=License)](LICENSE)
[![Poggit](https://poggit.pmmp.io/ci.shield/PMMPPlugin/VirtualChest/VirtualChest)](https://poggit.pmmp.io/ci/PMMPPlugin/VirtualChest)
[![Release](https://img.shields.io/github/release/PMMPPlugin/VirtualChest.svg?label=Release)](https://github.com/PMMPPlugin/VirtualChest/releases/latest)
[![Download](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/total.svg?label=Download)](https://github.com/PMMPPlugin/VirtualChest/releases/latest)


A plugin give virtual chest to player for PocketMine-MP

## Command
Main command : `/vchest <open | default | set | lang | reload | save>`

| subcommand | arguments                        | description                 |
| ---------- | -------------------------------- | --------------------------- |
| Open       | \[chest number\]                 | Open my virtual chest       |
| Set        | \<player name\> \<chest count\>  | Set player's chest count    |
| Default    | \<chest count\>                  | Set default chest count     |
| View       | \<player name\> \[chest number\] | Open player's virtual chest |
| Lang       | \<language prefix\>              | Load default lang file      |
| Reload     |                                  | Reload all data             |
| Save       |                                  | Save all data               |




## Permission
| permission         | default  | description        |
| ------------------ | -------- | ------------------ |
| vchest.cmd         | USER     | main command       |
|                    |          |                    |
| vchest.cmd.open    | USER     | open subcommand    |
| vchest.cmd.set     | OP       | set  subcommand    |
| vchest.cmd.default | OP       | default subcommand |
| vchest.cmd.view    | OP       | view subcommand    |
| vchest.cmd.lang    | OP       | lang subcommand    |
| vchest.cmd.reload  | OP       | reload subcommand  |
| vchest.cmd.save    | OP       | save subcommand    |