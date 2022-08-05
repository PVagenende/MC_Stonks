<?php

function ToReadableTime($seconds) {
    $days = floor($seconds / 86400);
    $hours = floor(($seconds - ($days * 86400)) / 3600);
    $min = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
    $sec = $seconds - ($days * 86400) - ($hours * 3600) - ($min * 60);

    $output = "";
    if($days)
        $output = (string)$days."days ";
    if($hours)
        $output .= (string)$hours."h:";
    if($min)
        $output .= (string)$min."m:";
    if($sec)
        $output .= (string)$sec."s";

    return $output;


}
