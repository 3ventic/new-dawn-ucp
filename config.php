<?php
require("common.php");

define('SQLHOST', 'localhost');
define('SQLUSER', 'newdawn');
define('SQLPASS', 'Qjs8wvfeqNftYHhrQjs8wvfeqNftYHhr');
define('SQLDB', 'newdawn');

define('FORUMHOST', 'localhost');
define('FORUMUSER', 'invision');
define('FORUMPASS', 't*=haba3r@!asw6=uqE2!8ke6wes7wus');
define('FORUMDB', 'ipb');

define('DEVELOPER', 100);
define('MODERATOR', 75);
define('DONATOR', 25);
define('USER', 10);
define('GUEST', 0);

define('UPLOAD_DIR', '/home/sites/uploads/maps/');

define('FORUMURL', 'http://www.dawn-tdm.com/');
if (strpos(__DIR__, "dev.dawn-tdm.com") !== false) {
	define('BASEURL', 'http://dev.dawn-tdm.com/');
	define('BRANCH', 'dev');
	error_reporting(E_ALL);
    ini_set("display_errors", "On");
}
else {
	define('BASEURL', 'http://ucp.dawn-tdm.com/');
	define('BRANCH', 'normal');
	error_reporting(0);
    ini_set("display_errors", "Off");
}

// Include all classes
$classlist = scandir("class");
foreach($classlist as $class)
{
    if(in_array($class, ['.', '..', '.svn']))
    {
        continue;
    }
    include("class/$class");
}

// Include error handler
//include("errorhandling.php");

/* TABLE STRUCTURE

CREATE TABLE IF NOT EXISTS `PlayerInfo` (
  `ID` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(24) NOT NULL,
  `Score` mediumint(9) NOT NULL DEFAULT '0',
  `Skin` smallint(3) unsigned NOT NULL DEFAULT '0',
  `Wins` smallint(6) unsigned NOT NULL DEFAULT '0',
  `Runups` smallint(6) unsigned NOT NULL DEFAULT '0',
  `C1` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `C2` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `IRC` smallint(4) unsigned NOT NULL DEFAULT '0',
  `Locked` tinyint(4) NOT NULL DEFAULT '0',
  `Muted` smallint(3) unsigned NOT NULL DEFAULT '0',
  `Channel` smallint(3) NOT NULL DEFAULT '0',
  `ChannelID` int(11) NOT NULL DEFAULT '0',
  `Donor` int(11) NOT NULL DEFAULT '0',
  `color` varchar(100) NOT NULL DEFAULT '0',
  `Cash` int(11) NOT NULL DEFAULT '0',
  `Hat` int(11) NOT NULL DEFAULT '0',
  `Ticket` int(11) NOT NULL DEFAULT '0',
  `Admin` int(11) NOT NULL DEFAULT '0',
  `hashPW` varchar(50) NOT NULL DEFAULT '0',
  `PrimeID` int(11) NOT NULL DEFAULT '0',
  `Primepoint` int(11) NOT NULL DEFAULT '0',
  `Mapsubmitter` int(11) NOT NULL DEFAULT '0',
  `IP` varchar(50) NOT NULL DEFAULT '0',
  `LastLogged` varchar(50) NOT NULL,
  `IsLogged` int(11) NOT NULL,
  `email` varchar(250) NOT NULL DEFAULT '',
  `Invited` varchar(100) NOT NULL,
  `Leader` varchar(100) NOT NULL,
  `Clanman` int(10) NOT NULL DEFAULT '0',
  `Warn` int(11) NOT NULL DEFAULT '0',
  `password` varchar(128) NOT NULL,
  `Vic` int(11) NOT NULL,
  `World` int(11) NOT NULL,
  `Training` int(11) NOT NULL,
  `DDAdmin` int(11) NOT NULL,
  `Armed` int(11) NOT NULL,
  `TogSound` int(11) NOT NULL,
  `oldname` varchar(20) NOT NULL,
  `namesLeft` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `Serial` varchar(128) NOT NULL,
  `HasCarText` int(11) NOT NULL,
  `CarString` varchar(120) NOT NULL,
  `CarFont` varchar(20) NOT NULL DEFAULT 'Arial',
  `CarSize` int(11) NOT NULL,
  `CarString2` varchar(50) NOT NULL,
  `CarFont2` varchar(20) NOT NULL,
  `CarSize2` int(11) NOT NULL,
  `VIPDate` varchar(10) NOT NULL,
  `FreeCamper` tinyint(4) NOT NULL DEFAULT '0',
  `FreeMapTicket` tinyint(4) NOT NULL DEFAULT '0',
  `Hunters` int(11) NOT NULL,
  `Clan` int(11) NOT NULL,
  `ForumName` varchar(30) NOT NULL,
  `tosDone` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ucpBan` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Wheel` int(11) NOT NULL DEFAULT '0',
  `Wanted` int(11) NOT NULL,
  `CopSkin` int(11) NOT NULL,
  `CivSkin` int(11) NOT NULL,
  `CMoney` int(11) NOT NULL DEFAULT '0',
  `Equip` int(11) NOT NULL,
  `STD` int(11) NOT NULL,
  `Condom` int(11) NOT NULL,
  `Wallet` int(11) NOT NULL,
  `House` int(11) NOT NULL,
  `Jail` int(11) NOT NULL,
  `Bank` int(11) NOT NULL,
  `Seed` int(11) NOT NULL,
  `Weed` int(11) NOT NULL,
  `Spawn` int(11) NOT NULL,
  `BJWorld` int(11) NOT NULL,
  `GGWin` int(11) NOT NULL,
  `DDWin` int(11) NOT NULL,
  `DDRunup` int(11) NOT NULL,
  `BJWin` int(11) NOT NULL,
  `BJMG` int(11) NOT NULL,
  `Points` float NOT NULL,
  `Ranka` int(11) NOT NULL,
  `Ranked` int(11) NOT NULL,
  `UZISkill` int(11) NOT NULL,
  `Diff` int(11) NOT NULL,
  `Kills` int(11) NOT NULL,
  `Deaths` int(11) NOT NULL,
  `SHKills` int(11) NOT NULL,
  `SHWins` int(11) NOT NULL,
  `SHDeaths` int(11) NOT NULL,
  `WeaponSH` int(11) NOT NULL,
  `SHLevel` int(11) NOT NULL DEFAULT '1',
  `SHExp` int(11) NOT NULL,
  `SHExtra` int(11) NOT NULL,
  `SHMiss` int(11) NOT NULL,
  `Radar` int(11) NOT NULL,
  `CLevel` int(11) NOT NULL DEFAULT '1',
  `CExp` int(11) NOT NULL,
  `CLeader` int(11) NOT NULL DEFAULT '-1',
  `CMember` int(11) NOT NULL DEFAULT '-1',
  `Job` int(11) NOT NULL,
  `MusicTog` int(11) NOT NULL DEFAULT '0',
  `pHat` int(11) NOT NULL,
  `pHat2` int(11) NOT NULL,
  `pHat3` int(11) NOT NULL,
  `ZCash` int(11) NOT NULL DEFAULT '0',
  `ZLevel` int(11) NOT NULL DEFAULT '1',
  `ZExp` int(11) NOT NULL DEFAULT '0',
  `ZKills` int(11) NOT NULL DEFAULT '0',
  `ZDeaths` int(11) NOT NULL DEFAULT '0',
  `ZInfected` int(11) NOT NULL DEFAULT '0',
  `ZExtra` int(11) NOT NULL DEFAULT '0',
  `ZClass` int(11) NOT NULL DEFAULT '0',
  `STKills` int(11) NOT NULL,
  `STDeaths` int(11) NOT NULL,
  `drRuKi` int(11) NOT NULL,
  `drAcKi` int(11) NOT NULL,
  `drDeaths` int(11) NOT NULL,
  `drMap` int(11) NOT NULL,
  `HGKills` int(11) NOT NULL,
  `HGDeaths` int(11) NOT NULL,
  `HGExp` int(11) NOT NULL,
  `HGLevel` int(11) NOT NULL,
  `Sharp` int(11) NOT NULL,
  `Strenght` int(11) NOT NULL,
  `Endu` int(11) NOT NULL,
  `HGDual` int(11) NOT NULL,
  `HGPoint` int(11) NOT NULL,
  `Ammos` int(11) NOT NULL,
  `HGHS` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `usernameidx` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=289142 ;

*/