<?php
$mit_num = 0;
$mit_overall = [];
for ($i = 1; $i < 5; $i++) {
    $mit = file_get_contents("data_scholar_Massachusetts Institute of Technology$i.json");
    $mit = json_decode($mit, true);
    $mit_overall = array_merge($mit, $mit_overall);
}
$mephi = file_get_contents("scholar_Gwangju Institute of Science and Technology.json");
$mephi = json_decode($mephi, true);


$kfu = file_get_contents("data_scholar_Kazan Federal University.json");
$kfu = json_decode($kfu, true);

$sum = 0;
$arr = [];

check_cites(2008, 2013, $kfu);
check_cites(2012, 2017, $mephi);
check_cites(2012, 2017, $mit_overall);

check_h_index($kfu);
check_h_index($mephi);
check_h_index($mit_overall);

function check_cites($year1, $year2, $uni) {
    $sum = 0;
    $num = 0;
    foreach ($uni as $item) {

        foreach ($item as $value) {
            if (count($value['cites']) > 2) {
                for ($i = $year1; $i < $year2; $i++) {
                    if (array_key_exists($i, $value['cites'])) {
                        $sum += $value['cites'][$i];
                    }
                }
                $num++;
            }
        }
    }
    echo $sum / $num . "\n";
}

function check_h_index($uni) {
    $sum = 0;
    $num = 0;
    foreach ($uni as $item) {
        foreach ($item as $value) {
            $sum += $value['stats']['last_five']['h_index'];
            $num++;
        }
    }
    echo $sum / $num . "\n";
}