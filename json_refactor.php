<?php
$title = "National Research Nuclear University MEPhI.json";
$data = file_get_contents('data_scholar_' .$title);
$data = json_decode($data, true);

$data_formatted = json_encode($data, JSON_PRETTY_PRINT);
$fp = fopen("scholar_$title", 'w');
fwrite($fp, $data_formatted);
fclose($fp);
