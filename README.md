# VirtualChest [![license](https://img.shields.io/github/license/Blugin/VirtualChest-PMMP.svg?label=License)](LICENSE)
<img src="./assets/icon/index.svg" height="256" width="256">  

[![release](https://img.shields.io/github/release/Blugin/VirtualChest-PMMP.svg?label=Release)](https://github.com/Blugin/VirtualChest-PMMP/releases/latest) [![download](https://img.shields.io/github/downloads/Blugin/VirtualChest-PMMP/total.svg?label=Download)](https://github.com/Blugin/VirtualChest-PMMP/releases/latest)


A plugin give virtual chest to player for PocketMine-MP
  
<br/><br/>
  
## Softdepend
- [EconomyAPI](https://github.com/onebone/EconomyS) : For buy chest
- [MathParserLib](https://github.com/PMMPPlugin/MathParserLib) : For calculate chest price
  
<br/><br/>
  
## Command
Main command : `/vchest <open | buy | price | max | default | set | view | lang | reload | save>`

| subcommand | arguments                        | description                 |
| :--------- | :------------------------------- | :-------------------------- |
| Open       | \[chest number\]                 | Open my virtual chest       |
| *Buy       |                                  | Buy chest                   |
| *Price     | \<chest price\>                  | Set chest's price           |
| *Max       | \<chest count\>                  | Set max chest count         |
| Default    | \<chest count\>                  | Set default chest count     |
| Set        | \<player name\> \<chest count\>  | Set player's chest count    |
| View       | \<player name\> \[chest number\] | Open player's virtual chest |  
* buy,price,max sub command require [EconomyAPI](https://github.com/onebone/EconomyS) plugin. 
* When price bigget than zero, player can buy chest (default = -1)
* When you have [MathParserLib](https://github.com/PMMPPlugin/MathParserLib) plugin, You can use formula on price. 
    * For example:
   
| You want                       | command                 |
| :----------------------------- | :---------------------- |
| `Price : ChestCount * 10000`   | `/vhest price c*1000`   |
| `Price : ChestCount^2 * 10000` | `/vhest price c^2*1000` |
  
<br/><br/>
  
## Permission
| permission         | default  | description        |
| :----------------- | :------: | :----------------- |
| vchest.cmd         | USER     | main command       |
|                    |          |                    |
| vchest.cmd.open    | USER     | open subcommand    |
| vchest.cmd.buy     | USER     | buy subcommand     |
| vchest.cmd.price   | OP       | price subcommand   |
| vchest.cmd.max     | OP       | max subcommand     |
| vchest.cmd.default | OP       | default subcommand |
| vchest.cmd.set     | OP       | set subcommand     |
| vchest.cmd.view    | OP       | view subcommand    |
  
<br/><br/>
  
## Required API
- PocketMine-MP : higher than [Build #937](https://jenkins.pmmp.io/job/PocketMine-MP/937)
