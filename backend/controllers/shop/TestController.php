<?php
namespace backend\controllers\shop;

use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
use backend\entities\WebPage;

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
     * get raw html data,
     * scrap data
     *
     * @return string
     */
    public function actionTest()
    {
        // get names of univ from csv file
        $path = dirname(__FILE__);
        $fh = fopen($path . '/../../entities/universities.csv', 'r');
        $namesOfUniversities = [];
        while ($row = fgetcsv($fh, 10000, ',', 'r')) {
            $namesOfUniversities[] = $row[1];
        }

        $nameUni = $namesOfUniversities[313];
//        $website = 'http://www.uniroma1.it'; // 81
//        $website = 'http://www.kaznu.kz'; // 77
//        $website = 'http://www.raj.ru'; // 72
//        $website = 'http://www.unimi.it'; // 78
        $website = 'http://www.unikin.cd'; // 85

        $html = WebPage::get('https://www.google.ru/search', ['q' => $nameUni, 'gl => US', 'hl' => 'en'], [], false);

//        $html = WebPage::get('https://www.google.ru/search', ['q' => $summaryArr[1], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => $queryArr[8], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => $queryArr[7], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => $queryArr[6], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => $queryArr[5], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => $queryArr[4], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => $queryArr[3], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => $queryArr[2], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => $queryArr[1], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => $queryArr[0], 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Western Australia', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Zurich', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Tohoku University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Rice University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ivan Franko National University of Lviv', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Illinois Institute of Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of California, Office of The President', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Tehran', 'gl => US', 'hl' => 'en'], [], true);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Gothenburg', 'gl => US', 'hl' => 'en'], [], true);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Russian Academy of Justice', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Tokyo', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Swiss Federal Institute of Technology Zurich', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Canterbury', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ecole Normale Superieure  Paris', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Heidelberg University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Universidade de Sorocaba', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Al-Farabi University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Seoul National University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Bangkok University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ryerson University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ryerson University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Pepperdine University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Heilongjiang University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ankara University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Andrews University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Technical University Munich', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Porto', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Saurashtra University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Weizmann Institute of Science', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Liaoning Medical University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Indian Institute of Science', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Hanyang University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Osaka University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ohio State University - Columbus', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Oregon Health and Science University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Kiel', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Medical College of Wisconsin', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Lanzhou University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Kyung Hee University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Technical University of Lisbon', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Calgary', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Copenhagen Business School', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Universidad de Palermo Argentina', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Escola Bahiana de Medicina e Saúde Publica', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Universidade Estadual do Rio Grande do Sul UERGS', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Okayama University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'King Fahd University of Petroleum & Minerals', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'ESPCI ParisTech', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'China Agricultural University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Arkansas State University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Technion-Israel Institute of Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Mordovia State University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Hamburg', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Amity University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Texas at Dallas', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Bauman Moscow State Technical University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Tsukuba', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Kyoto University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Emory University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Tehran', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Saint Petersburg State University of Architecture and Civil Engineering', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Carleton University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'United Arab Emirates University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Royal Melbourne Institute of Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Hong Kong University of Science and Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Curtin University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'City University of Hong Kong', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Australian National University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'National University of Singapore', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Florida International University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Massachusetts Institute of Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Cambridge', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'ASA College', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Moscow State University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Lingnan University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Sydney'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'harvard university'], [], true);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Lomonosov University'], [], true);

        return $this->render('test', compact('html', 'website'));
    }
}