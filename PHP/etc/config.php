<?php

// 1: Debug messages
// 2: Debug messages + Session info
// 3: Debug messages + Session Info + Post Info
const DEBUG = 1;
const MAGIC = 'bleh';

const APP_NAME = 'Stonks';
const SPECIALS = array('mined', 'crafted');
const DATABASE = array(
    'host' => '192.168.0.6',
    'port' => '3306',
    'database' => 'Stinks',
    'user' => 'stinks',
    'password' => 'bleh'
);
const RANKS = array(
    'minecraft:custom:jump' => 'Most Jumpy Player',
    'minecraft:custom:time_since_death' => 'The one that just won\'t die',
    'minecraft:custom:traded_with_villager' => 'Master Merchant',
    'minecraft:custom:play_time' => 'Most hours played',
    'minecraft:custom:time_since_rest' => 'Biggest insomnia'
);
