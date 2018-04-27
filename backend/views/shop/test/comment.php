<?php
use backend\entities\WebPage;

/** $var $namesOfUniversities array */
/** $var $arrFromFile array */
/** $var $endpointPlaceId string */
/** $var $endpoint string */
/** $var $flag string */
/** $var $resultMsg string */

// get names of items from csv file or from DB
$path = dirname(__FILE__);
$fh = fopen($path . '/../files/universities.csv', 'r');
$namesOfUniversities = [];
while ($row = fgetcsv($fh, 0, ',', 'r')) {
    $namesOfUniversities[] = $row[1];
}
$namesOfUniversities = array_slice($namesOfUniversities, 1);
$processedUniversities = file($path . '/../files/processed_universities.txt');
$processedUniversities = array_map('trim', $processedUniversities);

// a few API keys
$googleApiKey = 'AIzaSyDdQ2h8pTul-FVW89x4vMN6mL7xn-N7Ms4';
$googleApiKey2 = 'AIzaSyAmyuAN8Y0H_AovnCGMsNDm73xQ5vM-T3I';
$googleApiKey3 = 'AIzaSyC0kcx94MXTG3rWblgpqOJHBxBe2QebskY';
$googleApiKey4 = ' AIzaSyBSEpYcJJih4Wmox2IGe9LTWolv3gWF2JU ';

$endpointPlaceId = "https://maps.googleapis.com/maps/api/place/textsearch/json";
$endpoint = "https://maps.googleapis.com/maps/api/place/details/json";

$commentsData = [];
foreach ($namesOfUniversities as $k => $value) {
    if (!in_array($value, $processedUniversities) && !empty($namesOfUniversities)) {
        // get place id
        $resultPlaceId = WebPage::getDataFromApi($endpointPlaceId, ['query' => $value, 'key' => $googleApiKey4]);
        $resultPlaceId = json_decode($resultPlaceId, true);

        if(($resultPlaceId['status']) == 'OK') {
            // get delay, otherwise we get quota limit from google API
            sleep(5);
            $placeId = $resultPlaceId['results'][0]['place_id'];
            // get final result data
            $result = WebPage::getDataFromApi($endpoint, ['place_id' => $placeId, 'key' => $googleApiKey4, 'type' => 'university']);
            $result = json_decode($result, true);

            $commentsData[$value] = $result['result']['reviews'] ?? '';

            if (isset($result['result']['reviews']) && !empty($result['result']['reviews'])) {
                file_put_contents($path . '/../files/processed_universities.txt', PHP_EOL . $value, FILE_APPEND);
                // get data from file
                $dataFromFile = file_get_contents($path . '/../files/data.txt');
                $arrFromFile = unserialize($dataFromFile);
                $arrFromFile = !empty($arrFromFile) ? $arrFromFile : [];
                $arrFromRequest[$value] = $commentsData[$value];
                $finalArr = array_merge($arrFromFile, $arrFromRequest);
                // save summary data to file
                file_put_contents($path . '/../files/data.txt', serialize($finalArr));
            }
            $flag = 'Success';
        } else if ($resultPlaceId['status'] == 'OVER_QUERY_LIMIT') {
            $flag = $resultPlaceId['error_message'];
            break;
        }
    }
}

// get data for view
$dataFromFile = file_get_contents($path . '/../files/data.txt');
$arrFromFile = unserialize($dataFromFile);
$resultMsg = $flag ?? 'success';

echo "<h3>Result of scraping: <span class='label label-default'>$resultMsg</span></h3>";
echo '<pre>';
print_r($arrFromFile);
echo '</pre>';
