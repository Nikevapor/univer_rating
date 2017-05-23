<?php

$data = file_get_contents('data_scholar_google.json');
$data = json_decode($data, true);

$data_formatted = json_encode($data, JSON_PRETTY_PRINT);
$fp = fopen("scholar_University_of_Lisbon.json", 'w');
fwrite($fp, $data_formatted);
fclose($fp);
