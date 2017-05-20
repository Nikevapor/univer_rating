<?php
require('phpQuery/phpQuery/phpQuery.php');
echo 's';
$time_start = microtime(true);

$url = 'http://www.universityrankings.ch/';

$rating_type = 'Times';

$links = file_get_contents("links_$rating_type.json");

$links = json_decode($links, true);

$data = [];

$ratings = ['Shanghai', 'QS', 'Times'];

foreach ($links as $link) {

    $ranks = [];
    $qs = [];
    $shanghai = [];
    $times = [];
    $item_data = [];
    $temp_titles = [];

    $html = file_get_contents($url.$link);
    $doc = phpQuery::newDocument($html);
    $title = pq('.heading1 > h1')->text();
    $country = pq('.column1.wp70 > a:first')->text();
    $web = pq('.column1.wp70 > a:last')->text();
    $rows = pq('table:nth-child(1) table tr')->not(':first')->not(':last');
    $first_row = pq('table:nth-child(1) table tr:first');
    $temp_titles[] = pq($first_row)->find('td:nth-child(2)')->text();
    $temp_titles[] = pq($first_row)->find('td:nth-child(3)')->text();
    $temp_titles[] = pq($first_row)->find('td:nth-child(4)')->text();
    foreach ($rows as $row) {
        $year = pq($row)->find('td:first')->text();
        $i = 2;
        foreach ($temp_titles as $temp_title) {
            switch ($temp_title) {
                case "Shanghai":
                    $shanghai[$year] = strpos(pq($row)->find("td:nth-child($i) > a")->text(), "-") !== false ? //if-else ternary operator
                        pq($row)->find("td:nth-child($i) > a")->text() :
                        (int)pq($row)->find("td:nth-child($i) > a")->text();
                    break;
                case "QS":
                    $qs[$year] = strpos(pq($row)->find("td:nth-child($i) > a")->text(), "-") !== false ? //if-else ternary operator
                        pq($row)->find("td:nth-child($i) > a")->text() :
                        (int)pq($row)->find("td:nth-child($i) > a")->text();
                    break;
                case "Times":
                    $times[$year] = strpos(pq($row)->find("td:nth-child($i) > a")->text(), "-") !== false ? //if-else ternary operator
                        pq($row)->find("td:nth-child($i) > a")->text() :
                        (int)pq($row)->find("td:nth-child($i) > a")->text();
                    break;
            }
            $i++;

        }
    }

    foreach ($temp_titles as $temp_title) {
        $temp_title == 'Shanghai' ? $ranks['Shanghai'] = $shanghai : null;
        $temp_title == 'QS' ? $ranks['QS'] = $qs : null;
        $temp_title == 'Times' ?  $ranks['Times'] = $times : null;
    }

    $item_data['country'] = $country;
    $item_data['web'] = $web;
    $item_data['ranks'] = $ranks;
    $data[$title] = $item_data;
//    break;
    //$data[$title] =
    //var_dump($link);
}

$data = json_encode($data, JSON_PRETTY_PRINT);
$fp = fopen("data_$rating_type.json", 'w');
fwrite($fp, $data);
fclose($fp);

$time_end = microtime(true);
$time = $time_end - $time_start;
echo "Process Time: {$time}";
// Process time : 235.9746389389 QS
// Process Time: 452.9172501564 Times