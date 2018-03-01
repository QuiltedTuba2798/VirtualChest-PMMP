[![Telegram](https://img.shields.io/badge/Telegram-PresentKim-blue.svg?logo=telegram)](https://t.me/PresentKim)

[![icon/192x192](meta/icon/192x192.png?raw=true)]()

[![License](https://img.shields.io/github/license/PMMPPlugin/VirtualChest.svg?label=License)](LICENSE)
[![Poggit](https://poggit.pmmp.io/ci.shield/PMMPPlugin/VirtualChest/VirtualChest)](https://poggit.pmmp.io/ci/PMMPPlugin/VirtualChest)
[![Release](https://img.shields.io/github/release/PMMPPlugin/VirtualChest.svg?label=Release)](https://github.com/PMMPPlugin/VirtualChest/releases/latest)
[![Download](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/total.svg?label=Download)](https://github.com/PMMPPlugin/VirtualChest/releases/latest)


A plugin give virtual chest to player for PocketMine-MP
 
When price bigget than zero, player can buy chest (default = -1)

When you have [MathParserLib](https://github.com/PMMPPlugin/MathParserLib) plugin, You can use formula on price.  
For example:  
`Price : ChestCount * 10000` : `/vhest price c*1000`  
`Price : ChestCount^2 * 10000` : `/vhest price c^2*1000`  
  
<br/><br/>
  
## Softdepend
- [EconomyAPI](https://github.com/onebone/EconomyS) : For buy chest
- [MathParserLib](https://github.com/PMMPPlugin/MathParserLib) : For calculate chest price
  
<br/><br/>
  
## Command
Main command : `/vchest <open | buy | price | max | default | set | view | lang | reload | save>`

| subcommand | arguments                        | description                 |
| ---------- | -------------------------------- | --------------------------- |
| Open       | \[chest number\]                 | Open my virtual chest       |
| *Buy       |                                  | Buy chest                   |
| *Price     | \<chest price\>                  | Set chest's price           |
| *Max       | \<chest count\>                  | Set max chest count         |
| Default    | \<chest count\>                  | Set default chest count     |
| Set        | \<player name\> \<chest count\>  | Set player's chest count    |
| View       | \<player name\> \[chest number\] | Open player's virtual chest |
| Lang       | \<language prefix\>              | Load default lang file      |
| Reload     |                                  | Reload all data             |
| Save       |                                  | Save all data               |  

* buy,price,max sub command require [EconomyAPI](https://github.com/onebone/EconomyS) plugin. 
  
<br/><br/>
  
## Permission
| permission         | default  | description        |
| ------------------ | -------- | ------------------ |
| vchest.cmd         | USER     | main command       |
|                    |          |                    |
| vchest.cmd.open    | USER     | open subcommand    |
| vchest.cmd.buy     | USER     | buy subcommand     |
| vchest.cmd.price   | OP       | price subcommand   |
| vchest.cmd.max     | OP       | max subcommand     |
| vchest.cmd.default | OP       | default subcommand |
| vchest.cmd.set     | OP       | set subcommand     |
| vchest.cmd.view    | OP       | view subcommand    |
| vchest.cmd.lang    | OP       | lang subcommand    |
| vchest.cmd.reload  | OP       | reload subcommand  |
| vchest.cmd.save    | OP       | save subcommand    |
  
<br/><br/>
  
## Required API
- PocketMine-MP : higher than [Build #745](https://jenkins.pmmp.io/job/PocketMine-MP/745)
  
<br/><br/>
  
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

  
---
### v1.1.6 [![Source](https://img.shields.io/badge/source-v1.1.6-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.6) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.6/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.6)
- \[Changed\] Add return type hint
- \[Fixed\] Violation of PSR-0
- \[Changed\] Rename main class to VirtualChest
  
  
---
### v1.1.7 [![Source](https://img.shields.io/badge/source-v1.1.7-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.7) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.7/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.7)
- \[Added\] Add PluginCommand getter and setter
- \[Added\] Add getters and setters to SubCommand
- \[Fixed\] Add api 3.0.0-ALPHA11
- \[Changed\] Show only subcommands that sender have permission to use
  
  
---
### v1.1.8 [![Source](https://img.shields.io/badge/source-v1.1.8-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.8) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.8/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.8)
- \[Changed\] Change player data structure (save nbt file)
- \[Added\] Add max sub command
- \[Added\] Add price sub command
- \[Added\] Add buy sub command
- \[Fixed\] Can't set player's chest count to zero
  
  
---
### v1.1.9 [![Source](https://img.shields.io/badge/source-v1.1.9-blue.png?label=source)](https://github.com/PMMPPlugin/VirtualChest/tree/v1.1.9) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/VirtualChest/v1.1.9/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/VirtualChest/releases/v1.1.9)
- \[Fixed\] DIRECTORY_SEPARATOR error
