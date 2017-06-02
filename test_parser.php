<?php
require('phpQuery/phpQuery/phpQuery.php');
$time_start = microtime(true);
$urls_json = file_get_contents("data_scholar_links_msc.json");
$urls_json = json_decode($urls_json, true);
$data = [];
foreach ($urls_json as $title=>$links) {
    $workers = [];
    $i = 1;
    foreach ($links as $url) {
        $years_array = [];
        $cites_array = [];
        $html = file_get_contents($url);

        $doc = phpQuery::newDocument($html);

        $worker_name = pq('#gsc_prf_in')->text();

        $stat['overall']['cit_num'] = pq("#gsc_rsb_st")->find('tr:eq(1) > td:eq(1)')->text();
        $stat['last_five']['cit_num'] = pq("#gsc_rsb_st")->find('tr:eq(1) > td:eq(2)')->text();
        $stat['overall']['h_index'] = pq("#gsc_rsb_st")->find('tr:eq(2) > td:eq(1)')->text();
        $stat['last_five']['h_index'] = pq("#gsc_rsb_st")->find('tr:eq(2) > td:eq(2)')->text();
        $stat['overall']['i10_index'] = pq("#gsc_rsb_st")->find('tr:eq(3) > td:eq(1)')->text();
        $stat['last_five']['i10_index'] = pq("#gsc_rsb_st")->find('tr:eq(3) > td:eq(2)')->text();
        $workers_data['stat'] = $stat;

        $cites = pq("#gsc_g_bars")->find("a.gsc_g_a");
        $years = pq("#gsc_g_x")->find('span');
        foreach ($years as $year) {
            $years_array[] = pq($year)->text();
        }

        $years_num = count($years_array);
        foreach ($cites as $cite) {
            $z_index = explode(";", pq($cite)->attr('style'))[2];
            $num = explode(":", $z_index)[1];
            if ($years_num == $num) {
                $cites_array[] = pq($cite)->text();
                $years_num--;
            }
            else {
                $cites_array[] = 0;
                $cites_array[] = pq($cite)->text();
                $years_num = $years_num - 2;
            }
        }
        if (count($years_array) == count($cites_array)) {
            $workers_data['cites'] = array_combine($years_array, $cites_array);
        }
        else {
            for ($k = 0; $k < count($years_array) - count($cites_array); $k++) {
                $cites_array[] = 0;
            }
        }
        $workers[$worker_name] = $workers_data;
        echo $i, ".", $worker_name, "\n";
        $i++;
    }
    $data = json_encode($workers, JSON_PRETTY_PRINT);
    $fp = fopen("scholar_$title.json", 'w');
    fwrite($fp, $data);
    fclose($fp);
}

$time_end = microtime(true);
$time = $time_end - $time_start;
echo "Process Time: {$time}";

//Process Time: 555.72933888435 + Process Time: 107.830552101 from python < Process time: 4062.9746
//