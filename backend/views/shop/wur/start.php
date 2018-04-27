<?php
use backend\entities\WebPage;

/** $var $result array */
/** $var $univIndicators array */
/** $var $numberOfPages integer */
/** $var $detailPageInfo string */

// page to display in pagination: [0...10]
$numberOfPages = 10;
for ($i = 0; $i <= $numberOfPages; $i++ ){
    if(!file_exists("start/$i.json")) {
        $url = "http://colleges.startclass.com/ajax_search_sponsored?_len=100&page=$i&app_id=5307&_sortfld=_GC_rank&_sortdir=DESC&_fil[0][field]=is_closed&_fil[0][operator]==&_fil[0][value]=0&_fil[0][permanent]=true&_tpl=srp&head[]=instnm&head[]=_GC_main_srp&head[]=_GC_rank&head[]=_GC_money_sponsorship&head[]=_GC_acceptance_rate_srp&head[]=_GC_combined_tuition_srp&head[]=_GC_test_scores_srp&head[]=grad150&head[]=md_earn_wne_p10&head[]=stufacr&head[]=anyaidp&head[]=np_avg&head[]=_GC_mobile_srp&head[]=auto_ranks&head[]=acceptance_rate&head[]=sat_avg&head[]=location&head[]=chg2ay3&head[]=enrlt_total&head[]=enrlt_ug&head[]=id&head[]=_encoded_title&head[]=enrlt_total&head[]=enrlt_ug";
        $response = WebPage::getRequestToUrl($url);
        $fp = fopen("start/$i.json", "w");
        fwrite($fp, $response);
        fclose($fp);
        sleep(3);
    }
}

// detail page info
$detailPageInfo = 'http://colleges.startclass.com/l';

// get result data from all json files
$result = [];
for ($i = 0; $i <= $numberOfPages; $i++) {
    $univIndicators = json_decode(file_get_contents("start/$i.json"), true);
    foreach ($univIndicators['data']['data'] as $item) {
        $result[$item[0]] = [
            'rank_display' => $item[13]['formatted'] ?? $item[13],
            'main_info' => $item[1],
            'tuition' => $item[5],
            'exam_score' => $item[6],
            'graduation_rate' => $item[7]['formatted'] ?? $item[7],
            'median_salary_after_graduation' => $item[8]['formatted'] ?? $item[8],
            'student_faculty_ratio' => $item[9]['formatted'],
            'percent_freshman_receive_grants' => $item[10]['formatted'] ?? $item[10],
            'acceptance_rate' => (trim( ($item[14]['formatted'] ?? $item[14]) . ' ' . trim($item[4]) ) ),
            'average_SAT' => $item[15]['formatted'] ?? $item[15],
            'total_enrolled_students' => $item[18]['formatted'] ?? $item[18],
            'undergrad_population' => $item[19]['formatted'] ?? $item[19],
            'detail_page_info' => "$detailPageInfo/$item[20]"
        ];
    }
}

echo '<pre>';
print_r($result);
echo '</pre>';