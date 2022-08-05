<?php

class DB
{
    private $conn;

    function __construct() {
        $this->conn = new mysqli(DATABASE['host'], DATABASE['user'], DATABASE['password'], DATABASE['database'], DATABASE['port']);
        return $this->conn->host_info;
    }

    function GetMinedAndCrafted() {
        $result = $this->conn->query("SELECT *  FROM stat_types WHERE descr = 'mined' OR descr = 'crafted' OR descr = 'minecraft:custom:player_kills' OR descr = 'minecraft:custom:deaths'");
        $rows = $result->fetch_all();
        foreach ($rows as $row) {
            switch ($row[1]) {
                case 'mined':
                    $mined = $row[3];
                    break;
                case 'crafted':
                    $crafted = $row[3];
                    break;
                case 'minecraft:custom:deaths':
                    $deaths = $row[3];
                    break;
                case 'minecraft:custom:player_kills':
                    $kills = $row[3];
                    break;

            }
        }
        $result->free_result();
        return array('mined' => $mined, 'crafted' => $crafted, 'playerkills' => $kills, 'deaths' => $deaths);
    }

    function GetRanks() {
        $rows = array();

        foreach(RANKS as $key => $value) {
            $result = $this->conn->query("SELECT stats.descr, stats.value, users.name FROM stats LEFT JOIN users on stats.UUID = users.UUID WHERE stats.descr = '".$key."' order by value desc limit 1");
            $rows[] = $result->fetch_all();
        }
        $result_array = array();
        foreach($rows as $row) {
            $temp['key'] = $row[0][0];
            $temp['descr'] = RANKS[$row[0][0]];
            $temp['player'] = $row[0][2];
            $temp['value'] = $row[0][1];
            $result_array[] = $temp;
        }
        return $result_array;
    }

    function GetIndexes($param) {
     $param = "%".$param."%"; //prepare for sql ilike

        $qry = $this->conn->prepare("SELECT descr AS value FROM stat_types WHERE friendly LIKE ?");
        $qry->bind_param("s",$param);
        $qry->execute();
        $result = $qry->get_result();
        $actions = $result->fetch_all();

        $qry = $this->conn->prepare("SELECT name as value FROM users WHERE name LIKE ?");
        $qry->bind_param("s",$param);
        $qry->execute();
        $result = $qry->get_result();
        $users = $result->fetch_all();

        $rows = array_merge($actions,$users);

        $array = array();
        foreach ($rows as $row) {

            if(substr($row[0],0,4)=='mine')
            {
                $tmp = explode(":",$row[0]);
                switch ($tmp[1]) {
                    case 'used':
                        $action = "Used ";
                        break;
                    case 'dropped':
                        $action = "Dropped ";
                        break;
                    case 'picked_up':
                        $action = "Picked Up ";
                        break;
                    case 'mined':
                        $action = "Mined ";
                        break;
                    case 'crafted':
                        $action = "Crafted ";
                        break;
                    case 'killed':
                        $action = "Killed ";
                        break;
                    case 'broken':
                        $action = "Broken ";
                        break;
                    case 'killed_by':
                        $action = "Killed by ";
                        break;
                    default:
                        $action = "";
                        break;
                    }
                $item  = ucwords(str_replace("_"," ",$tmp[2]));

                $array[$row[0]] = $action.$item;

            }
            else
            {
                $array[$row[0]] = ucwords($row[0]);
            }

        }
        return $array;
    }

    function GetItem($param) {
        $qry = $this->conn->prepare("SELECT 
                                            RANK() OVER (PARTITION BY stats.descr ORDER BY value DESC) as rank,
                                            users.name, stats.value  
                                            FROM stats 
                                            LEFT JOIN users ON users.UUID = stats.UUID 
                                            WHERE descr = ?
                                            ORDER by value DESC ");
        $qry->bind_param("s",$param);
        $qry->execute();
        $result = $qry->get_result();

        return $result->fetch_all();

    }

    function GetPlayer($param) {
        $qry = $this->conn->prepare("select UUID from users where name = ?");
        $qry->bind_param("s",$param);
        $qry->execute();
        $result = $qry->get_result();
        $user = $result->fetch_all();

        $qry = $this->conn->prepare("SELECT 
                                            ranks.descr,
                                            ranks.rank,
                                            ranks.value
                                        FROM users
                                        LEFT JOIN (SELECT 
                                                    RANK() OVER (PARTITION BY stats.descr ORDER BY value DESC) as rank, 
                                                    stats.UUID,
                                                    stats.descr,
                                                    stats.value  
                                                FROM stats 
                                        ORDER BY value DESC) as ranks on users.UUID = ranks.uuid
                                        WHERE users.name = ?
                                        ORDER BY ranks.rank ASC");
        $qry->bind_param("s",$param);
        $qry->execute();
        $result = $qry->get_result();

        $return[$user[0][0]] = $result->fetch_all();

        return $return;
    }

    function GetCharts($param) {
        switch ($param) {
            case 'killed':
                $result = $this->conn->query("SELECT descr, total FROM stat_types WHERE descr LIKE '%:killed:%' ORDER BY total DESC");
                $rows = $result->fetch_all();
                return $rows;
        }
    }


}
