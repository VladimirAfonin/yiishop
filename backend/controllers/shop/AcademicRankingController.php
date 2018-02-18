<?php
namespace backend\controllers\shop;

use backend\entities\WebRanking;
use yii\web\Controller;
use Yii;
use backend\entities\WebPage;
use app\models\{MainRankings, UniversitySubjectRankings, QsRankings};

ini_set('max_execution_time', 70);
ini_set('memory_limit', '256M');
class AcademicRankingController extends Controller
{
    /**
     * get year from url
     * @param $url
     * @return null
     */
    public function getYear($url)
    {
        preg_match('#[0-9]{4}?#ui', $url, $matches);
        if ( ! empty($matches[0])) {
            $year = $matches[0];
        }
        return $year ?? null;
    }

    /**
     * create json file with all data
     * @param $name
     * @param $url
     * @param string $baseUrl
     * @return mixed
     */
    public function createDataFile($name, $url, $baseUrl = '')
    {
        $year = $this->getYear($baseUrl);
        if(!file_exists("rankings/$name.json")) {
            $univIndicators = WebRanking::getRequestToUrl($url);
            $fp = fopen("rankings/$name.json", "w");
            fwrite($fp, $univIndicators);
            fclose($fp);
        }
        $univIndicators = json_decode(file_get_contents("rankings/$name.json"), true);
        $univIndicators['year'] = $year;
        return $univIndicators;
    }

    //////////////////
    public function actionRun()
    {
//        $baseUrl = "http://www.shanghairanking.com";
//
//        /** ARWU ranking */
//        // set year of ARWU ranking: 2003...2017
//        // $year = 2016;
//        // url for ARWU rating
//        // $url = "$baseUrl/ARWU$year.html";
//
//        /** subjects ranking */
//        // set subject url from config
//        // $year = 2017;
//        // $url = Yii::$app->params['subjectsUrls']['finance'];
//
//        /** ARWU in FIELD ranking */
//        // set year of ARWU in FIELD ranking: 2007...2016
//        // $year = 2016;
//        // fields: 'SCI', 'LIFE', 'MED', 'SOC'
//        // $field = 'SCI';
//        // url for ARWU
//        // $url = "$baseUrl/Field" . $field . "$year.html";
//
//        /** Global Ranking of Sport Science Schools and Departments */
//        // set year of ranking: 2016, 2017
//        $year = 2016;
//        $url = "$baseUrl/Special-Focus-Institution-Ranking/Sport-Science-Schools-and-Departments-{$year}.html";
//
//
//        $html = WebPage::getRequestToUrl($url);
        return $this->render('run' /*, compact('html', 'year') */ );
    }
}