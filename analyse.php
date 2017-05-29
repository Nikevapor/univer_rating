<?php

$mit = file_get_contents("data_scholar_Massachusetts Institute of Technology.json");
$mephi = file_get_contents("data_scholar_National Research Nuclear University MEPhI.json");

$mephi = json_decode($mephi, true);
$sum = 0;
$arr = [];

foreach ($mephi as $item) {
    $num = count($item);
    foreach ($item as $value) {
        for ($i = 2008; $i < 2013; $i++) {
            if (array_key_exists($i, $value['cites'])) {
                $sum += $value['cites'][$i];
            }
        }
    }
}
echo $sum / $num;