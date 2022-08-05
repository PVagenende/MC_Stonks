<?php

$mobs_killed = $db->GetCharts('killed');
//debug($mobs_killed);
echo "<div id='chart_killed'></div>";

/*
echo "<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart('chart_killed', {
	animationEnabled: true,
	exportEnabled: true,
	theme: 'light1', // 'light1', 'light2', 'dark1', 'dark2'
	title:{
		text: 'Mobs Killed'
	},
	data: [{
		type: 'stackedBar100', //change type to bar, line, area, pie, etc  
		dataPoints: ".  json_encode($mobs_killed, JSON_NUMERIC_CHECK) ."
	}]
});
chart.render();
 
}
</script>";*/
