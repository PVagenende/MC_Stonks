# Updated Stonks

#Usage of the python part
###Main.py  
This is the main worker of the script that collects all stats and uploads them to the database.
It has been build to preform minimal operations and ignore any unneeded actions (f.e. updating records of a player that hasn't played)  
This can be cronn'ed as often as desired (f.e. every 15 or 30 minutes depending on server resources)

###Housekeeping.py
This is a helper file that will 
- poll the minecraft API for any changes in usernames. This has been put to a seperate file as to lower the polling count towards the minecraft API.  
This scripts sleeps for 5 seconds between two pollings of users. So the script execution time will be very long (Roughly `(Amount of players * 5) + Amount of players` in seconds) 
- Set calulated fields  
This file should not be cronn'ed more than twice a day (once a day seems more than sufficient).

Parameters  
-NoNames: Run the script without polling the Minecraft API for updates on player names  
-NoNewStats: Run the script without reading any new information from the stat json files  
-NoCalculation: Run the script without calculating specials like 'mined' or 'crafted'

###Possible features to be added
- Flag alt accounts so that they don't show up in the stats

#Requirements
- mariadb: pip3 install mariadb
- requests: pip3 install requests

#Database preperation
CREATE DATABASE IF NOT EXISTS `[database_name]` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `[database_name]`;

CREATE TABLE `stats` (
  `ID` int(11) NOT NULL,
  `UUID` text NOT NULL,
  `descr` text NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `UUID` text NOT NULL,
  `name` text NOT NULL,
  `lastchange` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `stats` ADD PRIMARY KEY (`ID`);
ALTER TABLE `stats` MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `users` ADD PRIMARY KEY (`UUID`(36));

INSERT INTO `stat_types` (`ID`, `descr`, `friendly`, `total`, `top`) VALUES (NULL, 'mined', 'Mined', '0', ''), (NULL, 'crafted', 'Crafted', '0', '');

#Configuration options for /Python/config.ini
###[LOG]  
**log_level**  
Possible values are 50 (Critical), 40 (Error), 30 (Warning), 20 (Info), 10 (Debug).  
Advised log levels are either 30 for minimal logging or 20 for normal logging. Log level 10 should only be used in case of error for debugging purposes as this generates a lot of logging.
Default value = 30

**log_file**
The desired name of the log file to write to. The log file will be placed in the root directory.  
Default value = log_file.log

###[WORLDS]
***world*** *(MANDATORY !!)*  
This parameter defines all the worlds to collect stats from.  
At least one value is mandatory (world), the path can be absolute or relative to the script.  
Multiple worlds can be entered, each one below the previous in the format `name = path_to_world_directory`

###[MARIADB]
***host***  
Host of the MariaDB Server

***port***
Port of the MariaDB Server

***database***
Database for the application

***user***  
User with permissions on the database (select/insert/update)  

***password***  
Password for the set user

#Usage of the PHP part
All tests have been done running php version 7.3.7

###About debug.php
The debug.php file is not required to run the PHP part of stonks, it is there just to include debug functions. As long as the `DEBUG` value in config.php is set to `0`, this file can not pose any security concern. 


#Configuration options for /PHP/config.php
***DEBUG*** enable or disable debugging. Should be left on 0  
Default Value = 0

***MAGIC*** leave default, this is something to prevent url requests  
Default Value = 'bleh'

***APP_NAME*** the name of the web page title  
Default Value = 'Stonks'  

***SPECIALS*** values to check in the database for pre-calculated values, this is to speed up script execution time.  
Default Value = array('mined', 'crafted')

***DATABASE*** The connection parameters to the database. These must be entered in a `KEY` => `VALUE` format.  
Default Value = array(
    'host' => '[hostname]',
    'port' => '[host port]',
    'database' => '[database name]',
    'user' => '[database user]',
    'password' => '[database password]')

***RANKS*** A list of minecraft stats to list on the front page of the stonks website. These must be entered in a `ID` => `Description` format.  
Default Valie = array(
    'minecraft:custom:jump' => 'Most Jumpy Player',
    'minecraft:custom:time_since_death' => 'The one that just won\'t die',
    'minecraft:custom:traded_with_villager' => 'Master Merchant',
    'minecraft:custom:play_time' => 'Most hours played',
    'minecraft:custom:time_since_rest' => 'Biggest insomnia')
