<?php
use backend\entities\WebPageHelper;
use backend\entities\WebPage;

$htmlDom = WebPage::dom($html);
$elem = $htmlDom->query('//div[@class="mod"]/following-sibling::div[1]');

// get 'website' info from Knowledge Panel of google
$websiteFromKP = $htmlDom->query('//a[2][@class="fl"]/@href')->item(0)->nodeValue ?? null;
preg_match('#http(s?):\/\/(w{3}\.{1})?(.+)\.(.+)\/#i', $websiteFromKP, $matchesKP);


// elements sometime can change with google
if ( ($elem->length) < 1) {
    $elem = $htmlDom->query('//div[@class="_o0d"]/following-sibling::div[1]');
}

// get short desc of search item
$shortDesc = ($htmlDom->query('//div[contains(@class, "kno-rdesc")]')->item(0)->nodeValue ?? null);
// the second attempt
if(is_null($shortDesc)) $shortDesc = $htmlDom->query('//div[@class="_o0d"]/following-sibling::div[3]')->item(0)->nodeValue ?? null;

/* prepare final data for search item */
$itemsArr = [];
for($i = 0; $i < $elem->length; $i++) {
    $itemsArr[$i] = $elem->item($i)->nodeValue;
}

// remove null items
$itemsArr = array_diff($itemsArr, ['']);
// find 'item: value', that we need
$resultItems = preg_grep('/^[a-zA-ZА-ЯЁа-яё].+(.+?):\s/iu', $itemsArr);

// create final dataArray, for example: [ ['Address'] => 'some value', ['Acceptance rate'] => '17%(2013)' e.g. ]
$data = [];
foreach ($resultItems as $items) {
    $arr = explode(':', $items);

    // create $data['detailed_score_sat_items']
    if ((strpos($arr[0], 'scor') != false) && strpos($arr[0], 'SAT') != false) {
        $data['detailed_score_sat_items'] = explode(',', $arr[1]);
        for ($i = 0; $i < count($data['detailed_score_sat_items']); $i++) {
            $data['detailed_score_sat_items'][$i] = array_diff( explode(' ', $data['detailed_score_sat_items'][$i]), [''] );
        }
    }

    // create $data['detailed_tuition_fees']
    if (strpos($arr[0], 'tuition') != false || strpos($arr[0], 'fees' != false)) {
        if (count($arr) > 3) {
            $arr = array_slice($arr, 1);
            $arr = trim(implode(' ', $arr));
            preg_match_all('~(\w+\s\w+){1}\s+([0-9]{1,}\,[0-9]{1,}\.*[0-9]{1,})\s+([A-Z]{2,})\s+(\([0-9]{4}\))~', $arr, $output_array, PREG_PATTERN_ORDER);
            $key1 = strtolower(str_replace(' ', '_', $output_array[1][0]));
            $data['detailed_tuition_fees'][$key1] = [
                $output_array[2][0],
                $output_array[3][0],
                $output_array[4][0],
            ];
            if(isset($output_array[1][1])) {
                $key2 = strtolower(str_replace(' ', '_', ($output_array[1][1])));
                $data['detailed_tuition_fees'][$key2] = [
                    $output_array[2][1],
                    $output_array[3][1],
                    $output_array[4][1],
                ];
            }
        } else {
            if ((strripos('ternational', ($arr[1] ?? null)) != false) || (strripos('omestic', ($arr[1] ?? null))) != false) {
                $data['detailed_tuition_fees'] = explode(':', $arr[1]);
            }
            $data['detailed_tuition_fees'][0] = array_diff(explode(' ', ($arr[2] ?? $arr[1])), ['']);
            foreach ($data['detailed_tuition_fees'][0] as $k => $item) {
                if(preg_match('~more~i', $item)) {
                    unset($data['detailed_tuition_fees'][0][$k]);
                }
            }
        }
    }

    // create $data['detailed_acceptance_rate']
    if (stripos($arr[0], 'ceptance') != false) {
        $data['detailed_acceptance_rate'] = explode(' ', $arr[1]);
    }

    // create $data['detailed_endowment']
    if (strpos($arr[0], 'ndowment') != false) {
        $data['detailed_endowment'] = explode(' ', $arr[1]);
        if (count($data['detailed_endowment']) > 3) {
            $str = implode(' ', $data['detailed_endowment']);
            preg_match('~\s((\S)*[0-9]{1,})\s+([a-zA-Z])+~', $str, $matches);
            $data['detailed_endowment'] = explode(' ', $matches[0]);
        }
    }

    // create $data['detailed_budget']
    if (stripos($arr[0], 'udget') != false) {
        $data['detailed_budget'] = explode(' ', $arr[1]);

        if (count($data['detailed_budget']) == 5) {
            $dataStr = implode(' ', $data['detailed_budget']);
            preg_match('#([0-9]+[,.]?[0-9]+)+\s(illion)?([a-zA-Z])+([0-9]{4})?#', $dataStr, $matches);
            $data['detailed_budget'][0] = $matches[0];
            $data['detailed_budget'][1] = trim(str_replace( [$matches[0]], '', $dataStr));
            $data['detailed_budget'][2] = '-';
            $data['detailed_budget'] = array_slice($data['detailed_budget'], 0, 3);
        }
        $countDetailBudgetItems = count($data['detailed_budget']);
    }


    // create $data['detailed_doctoral_students']
    if ((stripos($arr[0], 'octoral')) != false && (stripos($arr[0], 'students')) != false) {
        $data['detailed_doctoral_students'] = explode(' ', $arr[1]);
        $countDoctoralStudentsItems = count($data['detailed_doctoral_students']);
    }

    // create $data['detailed_postgraduates']
    if ((stripos($arr[0], 'ostgraduates')) != false) {
        $data['detailed_postgraduates'] = explode(' ', $arr[1]);
        $countPostgraduatesItems = count($data['detailed_postgraduates']);
    }

    // create $data['detailed_graduation_rate']
    if ((stripos($arr[0], 'rate')) != false && (stripos($arr[0], 'aduat')) != false) {
        $data['detailed_graduation_rate'] = explode(' ', $arr[1]);
    }

    // create $data['detailed_score_act_items']
    if ((stripos($arr[0], 'scor') != false) && stripos($arr[0], 'ACT') != false) {
        $data['detailed_score_act_items'] = explode(' ', $arr[1]);
    }

    // create $data['detailed_salary_after']
    if ((stripos($arr[0], 'salary') != false) && stripos($arr[0], 'after') != false) {
        $data['detailed_salary_after_attending'] = explode(' ', $arr[1]);
    }

    // create $data['detailed_total_enrollment']
    if ((strpos($arr[0], 'enrollment') != false) ) {
        $data['detailed_total_enrollment'] = explode(' ', $arr[1]);
        if (count($data['detailed_total_enrollment']) > 2) {
            $data['detailed_total_enrollment'] = array_diff($data['detailed_total_enrollment'], ['']);
            $firstElem = array_shift($data['detailed_total_enrollment']);
            $lastElem = array_pop($data['detailed_total_enrollment']);
            $data['detailed_total_enrollment'] = [];
            $data['detailed_total_enrollment'] = [$firstElem, $lastElem];
        }
    }

    $key = strtolower( str_replace(' ', '_', $arr[0]) );
    $value = trim($arr[1]);
    $data[$key] = $value;
}

// add additional 'info'
$data['detailed_score_sat_items'] = WebPageHelper::addDetailInfo($data, 'detailed_score_sat_items', 'detailItemScoreSAT' );
$data['detailed_score_act_items'] = WebPageHelper::addDetailInfo($data, 'detailed_score_act_items', 'detailItemScoreACT' );
$data['detailed_total_enrollment'] = WebPageHelper::addDetailInfo($data, 'detailed_total_enrollment','detailResultItemInfo', ['value', 'year']);
$data['detailed_salary_after_attending'] = WebPageHelper::addDetailInfo($data, 'detailed_salary_after_attending', 'detailResultItemInfo', ['value', 'currency', 'year']);
$data['detailed_acceptance_rate'] = WebPageHelper::addDetailInfo($data, 'detailed_acceptance_rate', 'detailResultItemInfo', ['rate', 'year']);
$data['detailed_graduation_rate'] = WebPageHelper::addDetailInfo($data, 'detailed_graduation_rate', 'detailResultItemInfo', ['rate', 'year']);
$data['detailed_endowment'] = WebPageHelper::addDetailInfo($data, 'detailed_endowment', 'detailResultItemInfo', ['value', 'currency']);

if (isset($data['detailed_budget'])) {
    $data['detailed_budget'] = (WebPageHelper::addDetailInfo($data, 'detailed_budget', 'detailBudget', ($countDetailBudgetItems >= 3) ? ['value', 'currency', 'year'] : ['value', 'year']));
}
if (isset($data['detailed_doctoral_students'])) {
    $data['detailed_doctoral_students'] = (WebPageHelper::addDetailInfo($data, 'detailed_doctoral_students', 'detailResultItemInfo', ($countDoctoralStudentsItems == 2) ? ['value'] : ['value', 'year']));
}
if (isset($data['detailed_postgraduates'])) {
    $data['detailed_postgraduates'] = (WebPageHelper::addDetailInfo($data, 'detailed_postgraduates', 'detailResultItemInfo', ($countPostgraduatesItems == 2) ? ['value'] : ['value', 'year']));
}
if (isset($data['detailed_tuition_fees'])) {
    $data['detailed_tuition_fees'] = (count($data['detailed_tuition_fees']) > 1) ? WebPageHelper::detailTuitionFees($data['detailed_tuition_fees']) : WebPageHelper::detailOneTuitionFees( $data['detailed_tuition_fees'][0] ?? $data['detailed_tuition_fees'], ['value', 'currency', 'year']);
}
// get website info of item
$datgit a['detailed_website_info']['from_db'] = $website;
$data['detailed_website_info']['from_parsing'] = $matchesKP[0] ?? 'not_found';
$data['detailed_website_info']['is_equal'] = WebPageHelper::isWebsiteInfoEqual($matchesKP[0] ?? null, $website);

$data['short_desc'] = $shortDesc;
?>

<!-- VIEW PART -->
<?php
echo '<hr>';
var_dump($html);
echo '<hr>';

// draft array
echo '<pre>';
print_r($itemsArr);
echo '</pre>';

// summary data
echo '<pre>';
print_r($data);
echo '</pre>';
?>
<!--short desc of item -->
<p><?= $shortDesc ?></p>



