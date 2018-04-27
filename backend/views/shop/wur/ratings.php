<?php
use backend\entities\WebPage;

/** @var $year integer */
/** @var $urls array */
/** @var $finalData array */
/** @var $resultData array */

// set year of ranking: 2017...2018
$year = 2017;
$urls = Yii::$app->params['urlsOfWur'][$year];

$finalData = [];
foreach ($urls as $k => $item) {
    if ( ! file_exists("wur_rankings/$year/$k.json")) {
        $html = WebPage::getRequestToUrl($item);
        $html = json_decode($html, true);
        $fp = fopen("wur_rankings/$year/$k.json", "w");
        fwrite($fp, json_encode($html['data']));
        fclose($fp);
    }
    $resultData = json_decode(file_get_contents("wur_rankings/$year/$k.json"), true);
    $finalData[$k] = $resultData;
}

echo "<h3>Year of ranking: $year</h3>";
echo '<pre>';
print_r($finalData);
echo '</pre>';