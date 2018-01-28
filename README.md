[![Telegram](https://img.shields.io/badge/Telegram-PresentKim-blue.svg?logo=telegram)](https://t.me/PresentKim)

[![icon/96x96](https://raw.githubusercontent.com/PMMPPlugin/VirtualChest/master/meta/icon/192x192.png)]()

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




## ChangeLog
### v1.0.0 [![Source](https://img.shields.io/badge/source-v1.0.0-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.0.0) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.0.0/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.0.0)
- First release
  
  
---
### v1.0.1 [![Source](https://img.shields.io/badge/source-v1.0.1-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.0.1) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.0.1/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.0.1)
- \[Fixed\] main command config not work
  
  
---
### v1.1.0 [![Source](https://img.shields.io/badge/source-v1.1.0-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.0) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.0/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.0)
- \[Changed\] translation method
  
  
---
### v1.1.1 [![Source](https://img.shields.io/badge/source-v1.1.1-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.1) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.1/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.1)
- \[Added\] view sub command
  
  
---
### v1.1.2 [![Source](https://img.shields.io/badge/source-v1.1.2-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.2) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.2/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.2)
- \[Fixed\] box closes as soon as it opens
  
  
---
### v1.1.3 [![Source](https://img.shields.io/badge/source-v1.1.3-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.3) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.3/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.3)
- \[Changed\] config data structure
- \[Added\] default sub command
  
  
---
### v1.1.4 [![Source](https://img.shields.io/badge/source-v1.1.4-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.4) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.4/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.4)
- \[Fixed\] error occurs when opening the box when the default value is 0
  
  
---
### v1.1.5 [![Source](https://img.shields.io/badge/source-v1.1.5-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.5) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.5/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.5)
- \[Changed\] inventory holder (according to https://github.com/pmmp/PocketMine-MP/commit/2fb580db26cb9335d38d38cba99864f54793cbf8)