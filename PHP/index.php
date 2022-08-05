<?php
include 'autoload.php';


include './components/header.php';

$db = new DB();

echo "<div class='container-sm wrapper'>";
echo "  <div class='container'>
            <a href='index.php'><img class='header-img' src='img/stonks.png'></a>
        </div>";
include './components/search.php';
include './components/resultbox.php';
include './components/charts.php';
include './components/specials.php';
echo "</div>";

include './components/footer.php';
