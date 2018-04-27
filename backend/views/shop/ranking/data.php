<?php
use backend\entities\WebPage;

/** @var $html string */
/** @var $result array */
/** @var $allItems array */

$path = dirname(__FILE__);
$fh = fopen($path . '/../../../entities/allItems.csv', 'r');
$allItems = [];
while ($row = fgetcsv($fh, 0, ';', 'r')) {
    $allItems[$row[0]] = $row[1];
}

$result = [];
foreach ($allItems as $k => $item) {
    $node = $k;
    $url = "https://www.topuniversities.com/node/$node";
    $html = WebPage::getRequestToUrl($url);
    $htmlDom = WebPage::dom($html);

    $about = $htmlDom->query('//div[@class="field-profile-advanced-overview"]')->item(0)->nodeValue
                   ?? $htmlDom->query('//div[@class="field-profile-overview"]')->item(0)->nodeValue
                   ?? '-';
    $students = $htmlDom->query('//div[@class="student line"]')->item(0)->nodeValue ?? '-';

    $result[$k] = [
        'name' => $item,
        'data' => [
            'about' => $about,
            'students_info' => $students
        ],
    ];
}

echo '<pre>';
print_r($result);
echo '</pre>';