<?php
require('phpQuery/phpQuery/phpQuery.php');

$url = 'http://www.universityrankings.ch/';

$rate[] = 'QS';
$rate[] = 'Shanghai';
$rate[] = 'Times';

//$rating = 'Times';
foreach ($rate as $rating) {
    $html = file_get_contents("http://www.universityrankings.ch/results/$rating/2016?ranking=$rating&year=2016&region=&q=&s=");
    $doc = phpQuery::newDocument($html);
    $pages = pq('.container.right:first > a:nth-child(6)')->text();

    $links_ar = [];

    for ($i = 0; $i < $pages * 50; $i += 50 ) {
        $html = file_get_contents("http://www.universityrankings.ch/results/$rating/2016?ranking=$rating&year=2016&region=&q=&s=$i");
        $doc = phpQuery::newDocument($html);
        $links = pq('.institution > a');
        foreach ($links as $link) {
            $links_ar[] = pq($link)->attr('href');
        }
    }

    $data = json_encode($links_ar);
    $fp = fopen("links_$rating.json", 'w');
    fwrite($fp, $data);
    fclose($fp);
}


