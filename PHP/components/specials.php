<?php
debug('<!--Loading Specials-->');

#Load components and boxes with labels (Mined, Crafted, Kills & Deaths)
$specials = $db->GetMinedAndCrafted();
echo "
<div class='container'>
    <div class='row'>
      <div class='col-sm-6'>
        <div class='card'>
          <div class='card-body'>
            <h5 class='card-title'>Mined</h5>
            <p class='card-text'><span class='big'>".number_format($specials['mined'])."</span></p>
          </div>
        </div>
      </div>
      <div class='col-sm-6'>
        <div class='card'>
          <div class='card-body'>
            <h5 class='card-title'>Crafted</h5>
            <p class='card-text'><span class='big'>".number_format($specials['crafted'])."</span></p>
          </div>
        </div>
      </div>
    </div>
    <br>
    <div class='row'>
      <div class='col-sm-6'>
        <div class='card'>
          <div class='card-body'>
            <h5 class='card-title'>Player Kills</h5>
            <p class='card-text'><span class='big'>".number_format($specials['playerkills'])."</span></p>
          </div>
        </div>
      </div>
      <div class='col-sm-6'>
        <div class='card'>
          <div class='card-body'>
            <h5 class='card-title'>Deaths</h5>
            <p class='card-text'><span class='big'>".number_format($specials['deaths'])."</span></p>
          </div>
        </div>
      </div>
    </div>
</div><br>
";

# Load the section of the the top items
$ranks = $db->GetRanks();
$i = count($ranks);
$j = 0;
while ($j <= $i) {

    if(stripos($ranks[$j]['key'],'time'))
    {
        $value = ToReadableTime($ranks[$j]['value']);
    }
    else
    {
        $value = number_format($ranks[$j]['value']);
    }

    echo "<div class='container'><div class='row'>";
    if(isset($ranks[$j])) {
        echo "    
          <div class='col-sm-6'>
            <div class='card'>
              <div class='card-body'>
                <h5 class='card-title'>" . $ranks[$j]['descr'] . "</h5>
                <p class='card-text'><span class='big'>".  $ranks[$j]['player'] ."</span><br><span class='small'>". $value . "</span></p>
                <a href='#' id='".$ranks[$j]['key']."' class='btn btn-primary specials'>See all</a>
              </div>
            </div>
          </div>
            ";
    }
    $j++;
    if(stripos($ranks[$j]['key'],'time'))
    {
        $value = ToReadableTime($ranks[$j]['value']);
    }
    else
    {
        $value = number_format($ranks[$j]['value']);
    }
    if(isset($ranks[$j])) {
        echo "    
          <div class='col-sm-6'>
            <div class='card'>
              <div class='card-body'>
                <h5 class='card-title'>".$ranks[$j]['descr']."</h5>
                <p class='card-text'><span class='big'>".  $ranks[$j]['player'] ."</span><br><span class='small'>". $value. "</span></p>
                <a href='#' id='".$ranks[$j]['key']."' class='btn btn-primary specials'>See all</a>
              </div>
            </div>
          </div>     
            ";
    }
    $j++;

    echo "</div></div><br>";
}
