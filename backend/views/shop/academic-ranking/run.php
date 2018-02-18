<?php
use backend\entities\WebPage;

/** @var $year string */
/** @var $html string */
/** @var $baseUrl string */
/** @var $url string */
/** @var $field string */
/** @var $result, $result1, $result2 array */
/** $var $count integer */
/** $var $htmlDom DOMXPath */

// get year from query string
$year = Yii::$app->request->queryParams['year'];
$baseUrl = "http://www.shanghairanking.com";

/** ARWU ranking */
// year of ARWU ranking: 2003...2017
// url for ARWU rating
// $url = "$baseUrl/ARWU$year.html";

/** subjects ranking */
// set subject url from config
// $url = Yii::$app->params['subjectsUrls']['finance'];

/** ARWU in FIELD ranking */
// year of 'ARWU in FIELD' ranking: 2007...2016
// fields: 'SCI', 'LIFE', 'MED', 'SOC'
// $field = 'SCI';
// url for ARWU
// $url = "$baseUrl/Field" . $field . "$year.html";

/** Global Ranking of Sport Science Schools and Departments */
// year of ranking: 2016, 2017
$url = "$baseUrl/Special-Focus-Institution-Ranking/Sport-Science-Schools-and-Departments-{$year}.html";

$html = WebPage::getDataFromApi($url);

$result = [];
$result1 = [];
$result2 = [];

$htmlDom = WebPage::dom($html);

$count1 = $htmlDom->query("//tr[@class='bgfd']")->length;
$count2 = $htmlDom->query("//tr[@class='bgf5']")->length;
$count = $count1 + $count2;
for ($i = 1; $i <= $count1; $i++) {
    $ranking = $htmlDom->query("//tr[@class='bgfd'][$i]/td")->item(0)->nodeValue;
    $name = $htmlDom->query("//tr[@class='bgfd'][$i]/td")->item(1)->nodeValue;
    $result1[$name] = [
        'ranking' => $ranking,
        'year' => $year
    ];
}
for ($i = 1; $i <= $count2; $i++) {
    $ranking = $htmlDom->query("//tr[@class='bgf5'][$i]/td")->item(0)->nodeValue;
    $name = $htmlDom->query("//tr[@class='bgf5'][$i]/td")->item(1)->nodeValue;
    $result2[$name] = [
        'ranking' => $ranking,
        'year' => $year
    ];
}
$result = array_merge($result1, $result2);
uasort($result, function($a,$b){
    return ($a[] = $a['ranking'] - $b['ranking']);
});


//////////// VIEW PART ////////
echo '<pre>';
print_r($result);
echo '<pre>';
