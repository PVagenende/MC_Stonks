<?php
include 'autoload.php';


include './components/header.php';


echo "<div class='container-sm wrapper'>";
echo "  <div class='container'>
            <a href='index.php'><img class='header-img' src='img/stonks.png'></a>
        </div>";
echo "<br><br>";
echo "<div class='container-sm' style='display: inline-block'>";
if(isset($_POST['key']) AND $_POST['key'] == MAGIC) {

    $db = new DB();
    switch ($_POST['type']) {
        case 'detail':
            $p = $_POST['p'];

            if(substr($p,0,4)=='mine'){
                $rows = $db->GetItem($p);
                echo "<div class='container'><h1>".cleanname($p)."</h1></div>";
                echo "<table class='table table-striped' id='item_details'>";
                echo "
                    <thead>
                        <tr>
                            <th scope='col'>Rank</th>
                            <th scope='col'>Player</th>
                            <th scope='col'>Amount</th>
                        </tr>
                    </thead>
                    <tbody>";
                foreach ($rows as $row)
                {
                    echo "
                    <tr class='usersearch' id='".$row[1]."'>
                      <td id='".$row[1]."'>$row[0]</td>
                      <td id='".$row[1]."'>$row[1]</td>
                      <td id='".$row[1]."'>".CleanValue($p, $row[2])."</td>
                    </tr>
                    ";
                }
                echo "</tbody></table>";
            }
            else {
                $result = $db->GetPlayer($p);
                $keys = array_keys($result);
                $uuid = $keys[0];
                $rows = $result[$uuid];

                echo "<div class='container'><h1>".$p."</h1></div>";
                echo "<table class='table table-striped' id='item_details'>";
                echo "
                    <thead>
                        <tr>
                            <th scope='col'>Statistic</th>
                            <th scope='col'>Rank</th>
                            <th scope='col'>Amount</th>
                        </tr>
                    </thead>
                    <tbody>";
                foreach ($rows as $row)
                {
                    echo "
                    <tr class='itemsearch' id='".$row[0]."'>
                      <td id='".$row[0]."'>".cleanname($row[0])."</td>
                      <td id='".$row[0]."'>$row[1]</td>
                      <td id='".$row[0]."'>".CleanValue($row[0], $row[2])."</td>
                    </tr>
                    ";
                }
                echo "</tbody></table>";
            }





            break;
    }
}
echo "</div>"; //close div for table
if(!($uuid == null)) {
    echo "<div class='skin'><img src='https://mc-heads.net/body/$uuid/left'></div>";
}
echo "</div>"; // close div for wrapper

function CleanName($param) {
    $tmp = explode(":",$param);
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
    return ucwords($action." ".str_replace("_"," ",$tmp[2]));
}

function CleanValue($key, $value) {
    if(stripos($key,'time'))
    {
        $value = ToReadableTime($value);
    }
    else
    {
        $value = number_format($value);
    }
    return $value;
}

include './components/footer.php';
