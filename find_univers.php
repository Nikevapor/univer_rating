<?php

$rating_type = 'Shanghai';

$data_json = file_get_contents("data_$rating_type.json");
$data = json_decode($data_json, true);

for ($i = 2011; $i < 2016; $i++) {
    for ($j = $i + 1; $j < 2017; $j++) {
        print "$i-$j";
        $array_top_from_nothing = get_ranks($data, $rating_type, $i, $j)[0];
        $array_top_progress = get_ranks($data, $rating_type, $i, $j)[1];

//arsort($array_top);
        uasort($array_top_from_nothing, function($a, $b){
            return ($a['rank'] - $b['rank']);
        });
        uasort($array_top_progress, function($a, $b){
            return ($b['progress'] - $a['progress']);
        });
        $data_new = json_encode($array_top_from_nothing, JSON_PRETTY_PRINT);
        $fp = fopen("data/$rating_type/20/" . $rating_type . "_from_nothing_$j-$i.json", 'w');
        fwrite($fp, $data_new);
        fclose($fp);

        $data_progress = json_encode($array_top_progress, JSON_PRETTY_PRINT);
        $fp_progress = fopen("data/$rating_type/20/" . $rating_type ."_progress_$j-$i.json", 'w');
        fwrite($fp_progress, $data_progress);
        fclose($fp_progress);
    }
}


function get_ranks($universities, $rating_type, $year1, $year2)
{
    $top_from_nothing = [];
    $top_progress = [];
    foreach ($universities as $title=>$university) {
        foreach ($university['ranks'] as $title_rating=>$rating) {
            $r1 = $rating[$year1];
            $r2 = $rating[$year2];
            if ($title_rating == $rating_type) {
                if ((int)$r2 != 0) {
                    if ($r1 == 0) {
                        print $title . ": ";
                        print "it was out rating $title_rating $year1 , but now in $year2 it is $r2 in $title_rating $year2 ";
                        print "\n";
                        $top_from_nothing[$title]['rank'] = (int)$r2;
                        $top_from_nothing[$title]['rank_str'] = $r2;
                    }
                    elseif ((((int)$r2 - (int)$r1) < 0) && ((int)$r1 - (int)$r2 >= (int)$r1 / 4)) {
                        print $title . ": ";
                        print "it was $r1 in $title_rating $year1, but now in $year2 it is $r2 in $title_rating $year2";
                        print "\n";
                        $top_progress[$title]['old'] = (int)$r1;
                        $top_progress[$title]['old_str'] = $r1;
                        $top_progress[$title]['current'] = (int)$r2;
                        $top_progress[$title]['current_str'] = $r2;
                        $top_progress[$title]['progress'] = (int)$r1 - (int)$r2;
                    }
                }

            }

        }
    }
    $top[0] = $top_from_nothing;
    $top[1] = $top_progress;

    return $top;
}