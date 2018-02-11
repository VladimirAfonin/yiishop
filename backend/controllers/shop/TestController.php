<?php
namespace backend\controllers\shop;

use backend\entities\WebPageHelper;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
use backend\entities\WebPage;
use app\models\ConflictsList;

class TestController extends Controller
{

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ]
            ]
        ];
    }

    /**
     * get table with conflicts
     * scrap data
     * @return string
     */
    public function actionTest()
    {
        $googleApiKey = 'AIzaSyDdQ2h8pTul-FVW89x4vMN6mL7xn-N7Ms4';
        // get names of items from csv file
        $path = dirname(__FILE__);
        $fh = fopen($path . '/../../entities/universities.csv', 'r');
        $namesOfUniversities = [];
        while ($row = fgetcsv($fh, 0, ',', 'r')) {
            $namesOfUniversities[] = $row[1];
        }

        $namesOfUniversities = array_slice($namesOfUniversities, 1);
        $countOfItems = count($namesOfUniversities);
        $try = true;
        $steps = 500;
        $step  = 0;
        $conflictsCount = 0;
        while($try) {
            $wikiId = 'Q13371'; // harvard for example,  must be from db
            $linkToWiki = 'http://en.wikipedia.org/wiki/Harvard_University'; // // harvard for example,  must be from db
            $propertyWiki = 'P856'; // for website

            $websiteWiki = WebPage::getDataFromApi('https://www.wikidata.org/w/api.php', ['action' => 'wbgetclaims', 'entity' => $wikiId, 'property' => $propertyWiki, 'format' => 'json']);
            $websiteFromGoogleApi = WebPage::getDataFromApi('https://kgsearch.googleapis.com/v1/entities:search', ['query' => $namesOfUniversities[$step], 'key' => $googleApiKey, 'types' => 'CollegeOrUniversity', 'limit' => 1]);

            $arrFromGoogleApi = json_decode($websiteFromGoogleApi);
            $websiteFromGoogleApi = $arrFromGoogleApi->itemListElement[0]->result->url ?? ''; // google
            $arrFromWiki = json_decode($websiteWiki);
            $websiteWiki = $arrFromWiki->claims->$propertyWiki[0]->mainsnak->datavalue->value ?? ''; // wiki
            $websiteFromDB = 'http://www.harvard.edu'; // for harvard // db

            // check for conflicts
            $dataResult = WebPageHelper::isWebsiteInfoEqual($websiteFromGoogleApi, $websiteFromDB, $websiteWiki);
            if($dataResult < 98) {
                $m = new ConflictsList();
                $m->name = $namesOfUniversities[$step];
                $m->link_wiki = $linkToWiki;
                $m->wiki_website = $websiteWiki;
                $m->google_website = $websiteFromGoogleApi;
                $m->db_website = $websiteFromDB;
                $m->save(false);
                $conflictsCount++;
            }
            $step++;
            // get page from cache or new request
            WebPage::get('https://www.google.ru/search', ['q' => $namesOfUniversities[$step], 'gl => US', 'hl' => 'en'], [], false);
            $try = ($conflictsCount < $steps);
        }

        return $this->render('conflicts', [
            'models' => ConflictsList::find()->all(),
           ]);
    }

    /**
     * detail view for one item
     *
     * @return string
     */
    public function actionDetailView($needParser = 0, $id = false)
    {
//        exit('yes'); // todo
        $googleApiKey = 'AIzaSyDdQ2h8pTul-FVW89x4vMN6mL7xn-N7Ms4';
        // get names of items from csv file
        $path = dirname(__FILE__);
        $fh = fopen($path . '/../../entities/universities.csv', 'r');
        $namesOfUniversities = [];
        while ($row = fgetcsv($fh, 0, ',', 'r')) {
            $namesOfUniversities[] = $row[1];
        }
//        print_r($namesOfUniversities);exit(); // todo
        $id = ($id) ?? 1;
        $nameUni = $namesOfUniversities[$id]; // for harvard e.g.

        $wikiId = 'Q13371'; // harvard for example,  must be from db
        $linkToWiki = 'http://en.wikipedia.org/wiki/Harvard_University'; // // harvard for example,  must be from db
        $propertyWiki = 'P856';

        $websiteWiki = WebPage::getDataFromApi('https://www.wikidata.org/w/api.php', ['action' => 'wbgetclaims', 'entity' => $wikiId, 'property' => $propertyWiki, 'format' => 'json']);
        $websiteFromGoogleApi = WebPage::getDataFromApi('https://kgsearch.googleapis.com/v1/entities:search', ['query' => $nameUni, 'key' => $googleApiKey, 'types' => 'CollegeOrUniversity', 'limit' => 1]);

        $arrFromGoogleApi = json_decode($websiteFromGoogleApi);
        $websiteFromGoogleApi = $arrFromGoogleApi->itemListElement[0]->result->url ?? '';
        $arrFromWiki = json_decode($websiteWiki);
        $websiteWiki = $arrFromWiki->claims->$propertyWiki[0]->mainsnak->datavalue->value ?? '';
         $websiteFromDB = 'http://www.harvard.edu'; // for harvard

        if($needParser == 1) {
            $html = WebPage::get('https://www.google.ru/search', ['q' => $nameUni, 'gl => US', 'hl' => 'en'], [], false);
        }


        // old version
        return $this->render('test', compact(
            'html',
            'websiteFromDB',
            'websiteWiki',
            'nameUni',
            'linkToWiki',
            'websiteFromGoogleApi'));
    }
}