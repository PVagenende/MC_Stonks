<?php
header('Content-Type: application/json');
//include_once '../autoload.php';

include_once '../etc/config.php';
include_once '../etc/tools.php';
include_once '../class/MariaDB.php';

if(isset($_POST['key']) AND $_POST['key'] == MAGIC) {
    $db = new DB();
    switch ($_POST['type']) {
        case 'index':
            echo json_encode($db->GetIndexes($_POST['p']));
            exit;
        case 'chart':
            echo json_encode($db->GetCharts($_POST['p']));
            break;
    }
}

