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
