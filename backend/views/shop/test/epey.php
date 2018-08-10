<?php
ini_set('max_execution_time', 800);
ini_set('memory_limit', '256M');

use backend\entities\Render;
use backend\entities\WebPage; // todo exadd +
use \backend\views\shop\test\Sheet;
use yii\helpers\Json;
use yii\helpers\Url; // todo exadd +
use yii\helpers\Html; // todo exadd +

const PHONE = 1; // todo exadd +
const WEARABLES = 2; // todo exadd +

$code = Sheet::rf('@backend/views/shop/test/specs.csv', ['indexFrom'=>'code']);
$whatNeed = PHONE; // set what need todo exadd +

if($whatNeed == PHONE) { // todo exadd +
    $category = 1;
} else if($whatNeed == WEARABLES) {
    $category = 16;
}

// todo uncomment
//$needLinksQuery = true; // links query
$needLinksQuery = needLinkQuery(1); // todo exadd +
$result_links = getAllLinks($needLinksQuery, $category); // todo exadd +

//Render::pretty_print($result_links);

$targets = [
    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-note-5-64gb.html',
//    'https://www.epey.com/akilli-telefonlar/apple-iphone-8.html',

	'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus-duos.html',
//	'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus-128gb.html',
//	'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus.html',
//    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus-256gb.html',
//	'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus-256gb-duos.html',
//
    'https://www.epey.com/akilli-telefonlar/apple-iphone-7.html',
//    'https://www.epey.com/akilli-telefonlar/apple-iphone-7-128gb.html',
//    'https://www.epey.com/akilli-telefonlar/apple-iphone-7-256gb.html',
//	'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9.html',
//
//    'https://www.epey.com/akilli-telefonlar/xiaomi-mi-max.html',
//    'https://www.epey.com/akilli-telefonlar/xiaomi-mi-max-16gb.html',
//    'https://www.epey.com/akilli-telefonlar/xiaomi-mi-max-128gb.html',
//    'https://www.epey.com/akilli-telefonlar/xiaomi-mi-max-64gb.html',
//
    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s7-edge.html',
//    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s7-edge-duos.html',
//    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s7-edge-injustice-edition.html',
//
//    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-note-8.html',
//    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-note-8-dual-sim.html',
//    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-note-8-128gb.html',
//
    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-j7.html',
//    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-j7-sm-j700h.html',
//	  'https://www.epey.com/akilli-telefonlar/apple-iphone-x.html',
//
//

    'https://www.epey.com/akilli-telefonlar/apple-iphone-7.html',
    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-a5-2017-dual-sim.html',
    'https://www.epey.com/akilli-telefonlar/htc-u-ultra.html',
    'https://www.epey.com/akilli-telefonlar/razer-phone.html',
//    'https://www.epey.com/akilli-telefonlar/apple-iphone-7-128gb.html',
//    'https://www.epey.com/akilli-telefonlar/apple-iphone-7-256gb.html',
//    'https://www.epey.com/akilli-telefonlar/apple-iphone-x.html',
//    'https://www.epey.com/akilli-telefonlar/apple-iphone-x-256gb.html',
//    'https://www.epey.com/akilli-telefonlar/xiaomi-redmi-4x-32gb.html',
    'https://www.epey.com/akilli-telefonlar/huawei-p20-pro.html',
//    'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9.html',

    'https://www.epey.com/akilli-telefonlar/lg-g6-dual.html',

//	'https://www.epey.com/akilli-saat/samsung-gear-s3-classic.html',
//	'https://www.epey.com/akilli-saat/apple-watch-38-mm-paslanmaz-celik-kasa-ve-milano-loop.html',


//      'https://www.epey.com/akilli-saat/samsung-smart-charm.html'
];

//$result_links = $targets;

$result_summary_info_turkish = [];

////////
//$result_links_wearable = [
//    0 => 'https://www.epey.com/akilli-saat/apple-watch-38-mm-paslanmaz-celik-kasa-ve-modern-tokali-kahverengi-kayis.html',
//    1 => 'https://www.epey.com/akilli-saat/garmin-forerunner-645.html',
//    2 => 'https://www.epey.com/akilli-saat/sony-smartband-talk.html',
//]; //
////////
function needLinkQuery(int $category) // todo exadd +
{
//    $path = dirname(__FILE__) .'/../tag/cron_time.txt';
//    $path = dirname(__FILE__) .'/../tag/cron_time.txt'; // todo exadd +
    if($category == 16) {
        $path = dirname(__FILE__) .'/../tag/map_16.json';
    } else if($category == 1){
        $path = dirname(__FILE__) .'/../tag/map_1.json';
    }

    $period = 60; // 86400 - 1 day
    $cron_time = filemtime($path);
    if (time() - $cron_time >= $period) {
        // get all links // todo exadd +
        $links = getLinksFromEpey($category);
        file_put_contents($path,Json::encode($links));
        $needXmlQuery = true;
    } else {
        $needXmlQuery = false;
    }

    return $needXmlQuery;
}
function getAllLinks(bool $needQuery, $category) // todo exadd + all
{
    $item_links = [];
    if($needQuery) {
        // get xml query for all urls phones
//        $item_links_epey = getLinksFromEpey($category); // todo exadd +
        $item_links_epey = getLinksFromJsonFile($category); // todo exadd +
        foreach($item_links_epey as $link) {
            $item_links[] = saveLinks($link);
        }
    } else {
        $links = WebPage::find()->where(['source' => 'epey'])->asArray()->select('url')->all();
        foreach($links as $k => $item) {
            $item_links[] = $item['url'];
        }
    }
    return $item_links;
}
function getLinksFromJsonFile($category) // todo exadd +
{
    if($category == 16) {
        $path = dirname(__FILE__) .'/../tag/map_16.json';
    } else if($category == 1){
        $path = dirname(__FILE__) .'/../tag/map_1.json';
    }
    return Json::decode(file_get_contents($path));
}
function saveLinks($needed_link) /// todo exadd + all
{
    $path_hash = hash('sha256', $needed_link);
    $m = WebPage::find()->filterWhere(['path_hash' => $path_hash])->one();
    if ($m !== null) {
        return $m->url;
    }
    $m = new WebPage();
    $m->path_hash = $path_hash;
    $m->source = 'epey';
    $m->url = $needed_link;
    $m->save(false);
    return $m->url;
}


// todo delete
//$result_links = array_filter($result_links, function($v,$k){
//    return $k % 100 === 10;
//}, ARRAY_FILTER_USE_BOTH);


foreach($result_links as $k => $url) {

    if(array_search($url,$targets) === false){
        continue;
    }

    ///  todo exadd +
//    $path_hash = hash('sha256', $url);
//    $obj = WebPage::findOne(['path_hash' => $path_hash]);
//    if ($obj) {
//        if (!empty($obj->desc)) { // todo exadd +
//        	if($obj->nid == Webpage::$version) {
//				$result_data = Json::decode($obj->desc);
//				$result_summary_info_turkish[$url] = $result_data;
//				continue;
//			}
//        }
//    }

    ////
	$path_hash = hash('sha256', $url);
	$obj = WebPage::findOne(['path_hash' => $path_hash]);
    if($obj && !empty($obj->desc) && $obj->nid == WebPage::$version) {
		$result_data = Json::decode($obj->desc);
		$result_summary_info_turkish[$url] = $result_data;
		continue;
	}
    ///
    $result_data_turkish = []; // todo exadd +
    $html_turkish = getDataFromApiWithCategory($url);
    $htmlDom2 = dom($html_turkish);

    /* common part */
    // get brand name
    $item_info = $htmlDom2->query('//div[@class="baslik"]/h1/a/text()')->item(0)->nodeValue ?? null;
    if($item_info) {
        $model_info = explode(' ', $item_info);
        $result_data_turkish['w81a9u0'] = $model_info[0]; // brand
        $model_name = substr($item_info, strlen($model_info[0])); // todo exadd + del
        $model_name = preg_replace('/\(+\d+\)+|2\d{3}/ui', "", trim($model_name)); // todo exadd + del
        /* // todo exadd +
		$model_value = $htmlDom2->query('//div[@id="fiyatlar"]/h2/text()')->item(0)->nodeValue ?? null; // todo exadd + all
        if($model_value) {
            preg_match('/\(+\d+\s*GB\)+/ui', $model_value, $out_memory);
            if(isset($out_memory[0]) && !empty($out_memory[0])) {
                $model_name .= ' '.$out_memory[0];
            }
        }
        */
        $model_value = $htmlDom2->query('//div[@class="baslik"]/h1/a/span[@class="aile"]/text()')->item(0)->nodeValue ?? null; // todo exadd +
		if(isset($model_value)) $model_name .= $model_value;
        if(isModelExists($result_summary_info_turkish, $model_name)) continue; // todo exadd +
        $result_data_turkish['33fksng'] = getEngNameFromTurkish($model_name); // todo exadd +
        $result_data_turkish['url'] = Html::a($url, Url::to($url, true)); // todo exadd +
        $result_data_turkish['model_name'] = getEngNameFromTurkish($model_name); // todo exadd +


		// get product version	// todo exadd +
		$product_version = $htmlDom2->query('//div[@class="baslik"]/h1/a/span[@class="kod"]/text()')->item(0)->nodeValue ?? null;
		if($product_version) {
			$result_data_turkish['product_version'] = $product_version;
		};
    }


    // todo exadd +
    for($i=0; $i <= 16; $i++) {
        $price = $htmlDom2->query('//div[@class="fiyatlar"]/div[@class="fiyat fiyat-'.$i.'"]/a/span[@class="urun_fiyat"]/text()')->item(0)->nodeValue ?? null;
        if($price) { // todo exadd + all
            preg_match('/(\d*\.*\d*),*\d*/mui', trim($price), $out_price);
            if(isset($out_price[1]) && !empty($out_price[1])) {
                $result_data_turkish['3n68sce'] = ['TRY' => str_replace('.', '', ($out_price[1]))];
            }
            break;
        }
    }

    // get family products // todo exadd +

    // todo exadd ???
    $z=0;
    do{
		$family_link = $htmlDom2->query('//div[@id="fiyatlar"]/div[@id="varyant"]/div/a[@class="varyant varyant1 cell"]/@href')->item($z)->nodeValue ?? null;
		$family_title = $htmlDom2->query('//div[@id="fiyatlar"]/div[@id="varyant"]/div/a[@class="varyant varyant1 cell"]/@title')->item($z)->nodeValue ?? null; // todo exadd +
		if($family_link && $family_title) $result_data_turkish['family'][trim(str_ireplace(['Tek Hat','Çift Hat','Cep Telefonu'],['one line','double line','mobile phone'],$family_title))] = trim($family_link); // todo exadd +
        $z++;
    } while ($family_link);

    for($z=0;$z<=6;$z++) {
        $family_link = $htmlDom2->query('//div[@id="fiyatlar"]/div[@id="varyant"]/div/a[@class="varyant varyant1 cell"]/@href')->item($z)->nodeValue ?? null;
        $family_title = $htmlDom2->query('//div[@id="fiyatlar"]/div[@id="varyant"]/div/a[@class="varyant varyant1 cell"]/@title')->item($z)->nodeValue ?? null; // todo exadd +
        if($family_link && $family_title) $result_data_turkish['family'][trim(str_ireplace(['Tek Hat','Çift Hat','Cep Telefonu'],['one line','double line','mobile phone'],$family_title))] = trim($family_link); // todo exadd +
    }

    // get main product // todo exadd + all
	if(isset($result_data_turkish['family'])) {
        // version #1
        /*
    	foreach(array_values($result_data_turkish['family']) as $path) {
			$html_family = getDataFromApiWithCategory($path);
			$htmlDom2 = dom($html_family);
			$family_aile = $htmlDom2->query('//div[@class="baslik"]/h1/a/span[@class="aile"]/text()')->item(0)->nodeValue ?? null; // todo exadd + // todo exadd +
			$family_kod = $htmlDom2->query('//div[@class="baslik"]/h1/a/span[@class="kod"]/text()')->item(0)->nodeValue ?? null; // todo exadd + // todo exadd +
			$score = 0;
			if($family_aile) $score += 2;
			if($family_kod) $score += 1;
			$result_data_turkish['family_scores'][$path] = $score;
		}
		// get own scores
		$family_aile = $htmlDom2->query('//div[@class="baslik"]/h1/a/span[@class="aile"]/text()')->item(0)->nodeValue ?? null; // todo exadd + // todo exadd +
		$family_kod = $htmlDom2->query('//div[@class="baslik"]/h1/a/span[@class="kod"]/text()')->item(0)->nodeValue ?? null; // todo exadd + // todo +
        $own_score = 0;
		if($family_aile) $own_score += 2;
		if($family_kod) $own_score += 1;
//		var_dump($family_aile);var_dump($family_kod);exit();
		$result_data_turkish['family_scores'][$url] = $own_score;
        */

        // version #2
        $main_products = array_merge(array_values($result_data_turkish['family']), (array)$url);
        usort($main_products, function($item1, $item2){
           if(strlen($item1) == strlen($item2)) {return 0;}
           return (strlen($item1) < strlen($item2)) ? -1 : 1;
        });
        $main_product = $main_products[0];
		$result_data_turkish['main_product'] = $main_product;
		if($url === $main_product) { // todo exadd +
			$result_data_turkish['is_main_prod'] = 1;
        } else {
			$result_data_turkish['is_main_prod'] = 0;
        }
	} else {
		$result_data_turkish['main_product'] = $url;
		$result_data_turkish['is_main_prod'] = 1;
	}

    // get related products // todo exadd +
    for($b=0;$b<=12;$b++) {
        $related_link = $htmlDom2->query('//div[@id="benzerler"]/div[@id="kiyas"]/div[@id="varyant"]/div[@class="row"]/a[@class="varyant varyant1 cell"]/@href')->item($b)->nodeValue ?? null;
        $related_title = $htmlDom2->query('//div[@id="benzerler"]/div[@id="kiyas"]/div[@id="varyant"]/div[@class="row"]/a/span[@class="vurunfiyat cell"]/span[@class="vurun row"]/text()')->item($b)->nodeValue ?? null;
//        if($related_link && $related_title) $result_data_turkish['related'][] = [trim($related_title) => trim($related_link)]; // todo exadd +
        if($related_link && $related_title) $result_data_turkish['related'][trim(str_ireplace(['Tek Hat','Çift Hat'],['One Line','Double Line'],$related_title))] = trim($related_link); // todo exadd +
    }

	// get image from product // todo +
	$image = $htmlDom2->query('//ul[@class="galerik"]/li/a/img/@src')->item(0)->nodeValue ?? null;
	$result_data_turkish['photos'] = [];
	if($image) {
		preg_match('/\/s.+-(\d+)\.[png|jpg|jpeg]+/uim', $image, $out_url);
		if(isset($out_url[0]) && ! empty($out_url[0])) {
			$main_url = substr($image, 0, strlen($image) - strlen($out_url[0]));
			$fin_url = str_replace('/s_', '/b_', $out_url[0]);
			$fin_url = preg_replace('/-\d+\.[png|jpg|jpeg]+/uim', "", $fin_url);
			for($i = 1; $i <= 15; $i++) {
				$image_url = $main_url . $fin_url . '-' . $i . '.png';
				$picture_png = getImage($image_url);
				if($picture_png) {
					$result_data_turkish['photos'][] = $picture_png;
				}
			}
			for($i = 1; $i <= 25; $i++) {
				$image_url = $main_url . $fin_url . '-' . $i . '.jpg';
				$picture_jpg = getImage($image_url);
				if($picture_jpg) {
					$result_data_turkish['photos'][] = $picture_jpg;
				}
			}
		}
	}


    if(strpos($url, 'akilli-saat') === false) {
        /** get phones data */
        // get screen size
        $screen_size = $htmlDom2->query('//strong[@class="ozellik1 tip"]/following::span[@class="cell cs1"]/span/a/text()')->item(0)->nodeValue ?? null; // screen_size
        if($screen_size) {
            $result_data_turkish['1n820fz'] = explode(' ', $screen_size)[0];
        }

        // get display resolution
        $display_resolution = $htmlDom2->query('//strong[@class="ozellik3 tip"]/following::span[@class="cell cs1"]/span/a/text()')->item(0)->nodeValue ?? null;
        if($display_resolution) {
            $result_data_turkish['nggks18'] = explode('x', $display_resolution)[0]; // display width
            preg_match("/[0-9]+/", explode('x', $display_resolution)[1], $output_array);
            $result_data_turkish['j2p7bju'] = $output_array[0]; // display height
        }

        // get pixel intensity
        $pixel_intensity = ($htmlDom2->query('//strong[@class="ozellik2 tip"]/following::span[1]')->item(0)->nodeValue) ?? null; // pixel_intensity
        if($pixel_intensity) { $result_data_turkish['7x8x76o'] = str_replace(['PPI'],[''],trim($pixel_intensity)); }

        // get screen protection todo exadd +
        $screen_protection = ($htmlDom2->query('//strong[@class="ozellik47"]/following::span[1]')->item(0)->nodeValue) ?? null;
        if($screen_protection) { $result_data_turkish['59e6c9r'] = ($screen_protection); }

        // get image from product // todo exadd +
        /*
        $result_data_turkish['photos'] = [];
        $image = $htmlDom2->query('//ul[@class="galerik"]/li/a/img/@src')->item(0)->nodeValue ?? null;
        if($image) {
            preg_match('/\/s.+-(\d+)\.[png|jpg|jpeg]+/uim', $image, $out_url);
            if(isset($out_url[0]) && !empty($out_url[0])) {
                $main_url = substr($image, 0, strlen($image) - strlen($out_url[0]));
                $fin_url = str_replace('/s_', '/b_', $out_url[0]);
                $fin_url = preg_replace('/-\d+\.[png|jpg|jpeg]+/uim', "", $fin_url);
                for($i = 1; $i <= 15; $i ++) {
                    $image_url = $main_url . $fin_url . '-' . $i . '.png';
                    $picture_png = getImage($image_url);
                    if($picture_png) {
                        $result_data_turkish['photos'][] = $picture_png; // todo exadd +
                    }
                }
                for($i = 1; $i <= 25; $i++) {
                    $image_url =  $main_url . $fin_url . '-' . $i . '.jpg';
                    $picture_jpg = getImage($image_url);
                    if($picture_jpg) {
                        $result_data_turkish['photos'][] = $picture_jpg; // todo exadd +
                    }
                }
            }
        }
        */


        // get display technology
        $display_technology = ($htmlDom2->query('//strong[@class="ozellik4"]/following::span[1]')->item(0)->nodeValue) ?? null; // display_technology
        if($display_technology) { $result_data_turkish['xxyv5nx'] = trim($display_technology); }


        // get display features
        $display_info = $htmlDom2->query('//strong[@class="ozellik5"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_info) {
            $result_data_turkish['alrhep0'] = isValueExists($display_info, 'Multi Touch'); // multitouch
            $result_data_turkish['djrp53w'] = isValueExists($display_info, 'Always-on Display'); // always on display todo exadd +
            $result_data_turkish['yq2jcrl1'] = isValueExists($display_info, 'IGZO'); //  todo exadd +
            $result_data_turkish['y20jcrlz'] = isValueExists($display_info, '120Hz'); // todo exadd +
        }

        // get display touch
        $display_touch = $htmlDom2->query('//strong[@class="ozellik46 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_touch) {
            $arr_display = ['Kapasitif Ekran' => 'Capacitive Screen']; // todo exadd +
            $result_data_turkish['yq2jcrla'] = str_replace(array_keys($arr_display),array_values($arr_display),trim($display_touch));  // touch_type
        }

        // get display number of colors
        $display_colors = $htmlDom2->query('//strong[@class="ozellik45 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_colors) {
            $result_data_turkish['8vzzca7'] = str_replace(['Milyon',' '],['M',''], $display_colors); // number of colors // todo exadd +
        }

		// get display rate
		$display_rate = $htmlDom2->query('//strong[@class="ozellik886"]/following::span[1]')->item(0)->nodeValue ?? null; // body_rate
		if($display_rate) {
			$display_ratio = preg_match('/\d+/ui', $display_rate, $output_array);
			if(isset($output_array[0]) && ( ! empty($output_array[0]))) {
				$result_data_turkish['zq2ektp'] = trim($output_array[0]);
			}
		}

        // get battery capacity
        $battery_capacity = $htmlDom2->query('//strong[@class="ozellik7 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($battery_capacity) {
            $result_data_turkish['wbswcml'] = str_replace('mAh','',trim($battery_capacity)); // todo exadd +
        }

        // get speech time (3g)
        $speech_time = $htmlDom2->query('//strong[@class="ozellik85"]/following::span[1]')->item(0)->nodeValue ?? null;// speech_time
        if($speech_time) {
            preg_match('/\d+/ui', $speech_time, $output_array);
            if(isset($output_array[0]) && (!empty($output_array[0]))) {
                $result_data_turkish['zuqqmwi3'] = $output_array[0];
            }
        }

        // todo exadd +
        $music_time = $htmlDom2->query('//strong[@class="ozellik89"]/following::span[1]')->item(0)->nodeValue ?? null;// music time
        if($music_time) {
            preg_match('/\d+/ui', $music_time, $out_speech_time);
            if(isset($out_speech_time[0]) && !empty($out_speech_time[0])) $result_data_turkish['6ojsm29w'] = $out_speech_time[0];
        }

        // todo exadd +
        $video_time = $htmlDom2->query('//strong[@class="ozellik90"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($video_time) {
            preg_match('/\d+/ui', $video_time, $out_video_time);
            if(isset($out_video_time[0]) && !empty($out_video_time[0])) $result_data_turkish['6ojsm29z'] = $out_video_time[0];
        }

        // todo exadd +
        $net_time_usage = $htmlDom2->query('//strong[@class="ozellik212"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($net_time_usage) {
            preg_match('/\d+/ui', $net_time_usage, $out_net_time);
            if(isset($out_net_time[0]) && !empty($out_net_time[0])) $result_data_turkish['6ojsm290'] = $out_net_time[0];
        }

        // todo exadd +
        $net_time_usage_4g = $htmlDom2->query('//strong[@class="ozellik314"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($net_time_usage_4g) {
            preg_match('/\d+/ui', $net_time_usage_4g, $out_net_time_4g);
            if(isset($out_net_time_4g[0]) && !empty($out_net_time_4g[0])) $result_data_turkish['6ojs1290'] = $out_net_time_4g[0];
        }

        // HSPA  todo exadd +
        $hspa = $htmlDom2->query('//strong[@class="ozellik322 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($hspa) $result_data_turkish['uointeq4'] = isCodecExists($hspa, 'HSPA');

        // get sec camera resol // todo exadd +
        $sec_camera_resol = $htmlDom2->query('//strong[@class="ozellik877"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($sec_camera_resol) $result_data_turkish['lggn117'] = $sec_camera_resol; // todo exadd +

        // get front camera size sensor // todo exadd +
        $front_cam_sensor_size = $htmlDom2->query('//strong[@class="ozellik890"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($front_cam_sensor_size) $result_data_turkish['lggnzaa'] = str_ireplace('İnç','Inc', $front_cam_sensor_size);

        // get internal storage format // todo exadd +
        $storage_info = $htmlDom2->query('//strong[@class="ozellik1768"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($storage_info) $result_data_turkish['c8xo611'] = $storage_info;

        //optical stabilizer  todo exadd +
        $optical_stabilizer = $htmlDom2->query('//strong[@class="ozellik2592"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($optical_stabilizer) $result_data_turkish['lggn066'] = isCodecExists($optical_stabilizer, 'Var');

        // get antutu score v7 todo exadd +
        $antutu_score_7 = $htmlDom2->query('//strong[@class="ozellik4591"]/following::span[1]')->item(0)->nodeValue ?? null; // antutu_score
        if($antutu_score_7) {
            $result_data_turkish['q85w6qm7'] = trim(explode(' ', $antutu_score_7)[0]); // todo exadd +
        }

        // get internet usage(wi-fi)
        $internet_usage = $htmlDom2->query('//strong[@class="ozellik213"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($internet_usage) {
            preg_match('/\d+/ui', $internet_usage, $output_array);
            if(isset($output_array[0]) && (!empty($output_array[0]))) {
                $result_data_turkish['qwkph15b'] = $output_array[0];
            }
        }

        // get charging info
        $charging_info =  $htmlDom2->query('//strong[@class="ozellik81"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($charging_info) {
            $result_data_turkish['xc2onhy'] = isValueExists($charging_info, 'ablosuz'); // wireless charging
        }

        // get battery tech
        $battery_tech = $htmlDom2->query('//strong[@class="ozellik331"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_tech
        if($battery_tech) {
            $battery_type = preg_replace('/\(.+\)/uim', "", $battery_tech); // todo exadd +
            $result_data_turkish['63r9r99'] = trim($battery_type);
        }

        // get replacement battery
        $battery_replace = $htmlDom2->query('//strong[@class="ozellik102 tip"]/following::span[2]')->item(0)->nodeValue ?? null; // replacement_battery
        if($battery_replace) {
            $result_data_turkish['c220c9j'] = getAnswerTurkish($battery_replace);
        }

        // get battery fast charging // todo exadd +
        $fast_charging = $htmlDom2->query('//strong[@class="ozellik880"]/following::span[1]')->item(0)->nodeValue ?? null;
        if ($fast_charging) {
            $result_data_turkish['27s8wl4'] = isValueExists($fast_charging, 'Hızlı'); // fast charging
            // get version of quick charge // todo exadd +
            $version_info = isValueExists($fast_charging, 'Qualcomm Quick Charge');
            if ($version_info == '+') {
                if (preg_match('/\d+\.*\d*/mui', $version_info, $out_arr_version_charge)) $result_data_turkish['27s8wl5'] = trim($out_arr_version_charge[0]);
            }
        }


        // get battery time fast charging
        $time_fast_charging = $htmlDom2->query('//strong[@class="ozellik880"]/following::span[3]')->item(0)->nodeValue ?? null; // time fast charging
        if($time_fast_charging) {
            preg_match('/([0-9]+).+(%[0-9]+|[0-9]+\%)/', $time_fast_charging, $out_charging);
            if(isset($out_charging) && !empty($out_charging)) {
                $percent = preg_replace('/(%)(\d+)/uim', "$2", $out_charging[2]); // todo exadd +
                $minutes = $out_charging[1];
                $other_minutes = ($minutes * (100 - $percent)) / $percent;
                $full_battery_charging = $minutes + $other_minutes;// todo exadd +
                $result_data_turkish['le00i0c'] = str_replace('1.00', '1', number_format($full_battery_charging / 60, 1)); // time fast charging // todo exadd +
            }
        } else {
            $result_data_turkish['le00i0c'] = null; // todo exadd +
        }

        // get camera resolution
        $camera_resol = $htmlDom2->query('//strong[@class="ozellik19 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // camera_resolution
        if($camera_resol) {
            preg_match('/\d+/ui', $camera_resol, $out_camera);
            if(isset($out_camera[0]) && !empty($out_camera[0])) {
                $result_data_turkish['lggn0m2'] = trim($out_camera[0]); // todo exadd +
            }
        }

        // get camera features
//        for ($i = 0; $i <= 15; $i++) { // todo exadd + all
//            $cam_features = $htmlDom2->query('//strong[@class="ozellik69"]/following::span[@class="cell cs1"]/span/a/text()')->item($i)->nodeValue ?? null;;
            $cam_features = $htmlDom2->query('//strong[@class="ozellik69"]/following::span[@class="cell cs1"]')->item(0)->nodeValue ?? null;
            if($cam_features) {
                $result_data_turkish['lggn001'] = isValueExists($cam_features, 'Live Photos') ;
                $result_data_turkish['lggn002'] = isValueExists($cam_features, 'HDR') ;
                $result_data_turkish['gn4gn6xk'] = isValueExists($cam_features, 'Otomatik'); // autofocus
                $result_data_turkish['lggn003'] = isValueExists($cam_features, 'Karma Kızılötesi (Hybrid IR) Filtresi'); // hybrid IR filter
                $result_data_turkish['lggn004'] = isValueExists($cam_features, 'Sesli komut'); // voice command for camera
                $result_data_turkish['c4awfagk'] = isValueExists($cam_features, 'Yüz Algılama'); // face id
                $result_data_turkish['lggn005'] = isValueExists($cam_features, 'Elle Odaklama'); // manual focus
                $result_data_turkish['lggn006'] = isValueExists($cam_features, 'Coğrafi konum etiketleme'); // geo tag
                $result_data_turkish['lggn007'] = isValueExists($cam_features, 'BSI'); // bsi
                $result_data_turkish['lggn008'] = isValueExists($cam_features, 'Depth of Field (DOF)'); // DOF
//                $result_data_turkish['gn4gn6xk'] = isValueExists($cam_features, 'Otomatik Odaklama'); // autofocus // todo exadd +
                $result_data_turkish['lggn009'] = isValueExists($cam_features, 'Safir Kristal Objektif Kapağı'); // crystal cap lens
                $result_data_turkish['lggn010'] = isValueExists($cam_features, 'Seri Çekim (Burst) Modu'); // burst mode
                $result_data_turkish['lggn011'] = isValueExists($cam_features, 'Zamanlayıcı'); // camera timer
            }


//        }

        // get flash alarm
        $flash_alarm = $htmlDom2->query('//strong[@class="ozellik72"]/following::span[2]')->item(0)->nodeValue ?? null; // flash_alarm_1
        if($flash_alarm) { // todo exadd + all
            // led flash
            $arr_led_flash = [
                'Tek Tonlu Flaş' => 'Single-Tone Flash',
                'Yok' => 'No',
                'Çift Tonlu' => 'Dual Tone',
                'Halka' => 'Ring'
            ];
            $led_flash_info = str_replace(array_keys($arr_led_flash), array_values($arr_led_flash),trim($flash_alarm));
            $result_data_turkish['jefetfa2'] = isValueExists($led_flash_info, 'LED');
            if($result_data_turkish['jefetfa2'] == '+') { $result_data_turkish['zrru3eek'] = '+';} // todo exadd +

        }

        // get aperture clear
        $aperture_clear = $htmlDom2->query('//strong[@class="ozellik73 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($aperture_clear) { $result_data_turkish['lggn0m3'] = $aperture_clear;} // aperture

        // get optical zoom
        $optical_zoom = $htmlDom2->query('//strong[@class="ozellik107 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($optical_zoom) { // todo exadd +
            $result_data_turkish['lggn0m4'] = str_replace(' ', '', trim($optical_zoom));
        }

        // get video recording resoluton 4k
        $video_rec_resol = $htmlDom2->query('//strong[@class="ozellik71 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // video_resolution
        if($video_rec_resol) { // todo exadd +
            preg_match('/\d+p/mui', $video_rec_resol, $out_video_rec);
            if(isset($out_video_rec[0]) && !empty($out_video_rec[0])) {
                $result_data_turkish['t9q0h7hd'] = trim($out_video_rec[0]);
            }
        }

        // get video fps value
        $fps_value = $htmlDom2->query('//strong[@class="ozellik70 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($fps_value) { $result_data_turkish['lggn0m5'] = str_replace('fps','',trim($fps_value)); } // todo exadd +

        // get // todo exadd +
        $camera_sensor_size = $htmlDom2->query('//strong[@class="ozellik74 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($camera_sensor_size) { $result_data_turkish['lggnaaa'] = str_ireplace('İnç','Inc', $camera_sensor_size); }

        // get video recording features // todo exadd +
//        for($i = 0; $i <= 8; $i++) {
            $video_rec_features = $htmlDom2->query('//strong[@class="ozellik216"]/following::span[@class="cell cs1"]')->item(0)->nodeValue ?? null;
            if($video_rec_resol) {
                $result_data_turkish['lggn012'] = isCodecExists($video_rec_resol, 'OIS'); // optical image stabilizer // todo exadd +
                $result_data_turkish['lggn013'] = isValueExists($video_rec_resol, 'Time-lapse Video Kayıt'); // time lapse video rec.
                $result_data_turkish['lggn014'] = isCodecExists($video_rec_resol, 'Video Yakınlaştırma'); // video zoom // todo exadd +
                $result_data_turkish['lggn015'] = isCodecExists($video_rec_resol, 'Slow motion video'); // slow motion video // todo exadd +
            }
//        }

        // get video recording options // todo exadd +
//        for($i = 2; $i <= 9; $i++) {
            $video_rec_options = $htmlDom2->query('//strong[@class="ozellik793"]/following::span')->item(0)->nodeValue ?? null;
            if($video_rec_options) { // todo exadd + all
                $video_rec_info = trim($video_rec_options);
                preg_match_all('/\d+p\s+@\s+\d+fps/ui', $video_rec_info, $out_video);
                if(isset($out_video[0]) && !empty($out_video[0])) { // todo exadd + ?
                    $result_data_turkish['lggn016'] = array_map(function($item) {
                       return str_replace(' ', '', $item);
                    }, $out_video[0]);
                }
            }
//        }

        // get second rear camera
        $second_rear_camera = ($htmlDom2->query('//strong[@class="ozellik876"]/following::span[1]')->item(0)->nodeValue) ?? null;
        if($second_rear_camera) {
            $result_data_turkish['lggn017'] = getAnswerTurkish($second_rear_camera);
        }

        // get second rear camera diaphragm
        $second_rear_camera_dia = ($htmlDom2->query('//strong[@class="ozellik878"]/following::span[1]')->item(0)->nodeValue) ?? null;
        if($second_rear_camera_dia) {
            $result_data_turkish['lggn018'] = trim($second_rear_camera_dia);
        }

        // get second rear camera features
        $second_rear_camera_ois = ($htmlDom2->query('//strong[@class="ozellik879"]/following::span[2]')->item(0)->nodeValue) ?? null;
        if($second_rear_camera_ois) {
            $result_data_turkish['lggn019'] = isValueExists($second_rear_camera_ois, 'OIS');
        }

        // get second rear camera zoom
        $second_rear_camera_zoom = ($htmlDom2->query('//strong[@class="ozellik879"]/following::span[3]')->item(0)->nodeValue) ?? null;
        if($second_rear_camera_zoom) {
            if(isValueExists($second_rear_camera_zoom, 'Optik Zoom') === '+') {
                preg_match('/\d+/ui', $second_rear_camera_zoom, $output_array);
                if(isset($output_array[0]) && (!empty($output_array[0]))) {
                    $result_data_turkish['lggn020'] = $output_array[0];
                }
            }
        }

        // get front camera resolution
        $cam_resolution = $htmlDom2->query('//strong[@class="ozellik18 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // front_camera_resolution
        if($cam_resolution) {
            preg_match('/\d+/ui', $cam_resolution, $output_array);
            if(isset($output_array[0]) && !empty($output_array[0])) {
                $result_data_turkish['06wzu4yz'] = trim($output_array[0]); // todo exadd +
            }
        }

        // get front camera video resolution
        $cam_video_res = $htmlDom2->query('//strong[@class="ozellik27 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // front_cam_video_resolution;
        if($cam_video_res) { $result_data_turkish['8041luk6'] = trim($cam_video_res); }


        // get front camera fps value
        $cam_fps_value = $htmlDom2->query('//strong[@class="ozellik32 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cam_fps_value) {$result_data_turkish['lggn0m6'] = str_replace('fps','',trim($cam_fps_value)); } // todo exadd +

        // get front camera aperture
        $cam_aperture = $htmlDom2->query('//strong[@class="ozellik337 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cam_aperture) {$result_data_turkish['lggn0m7'] = trim($cam_aperture); }

        // get from camera capabilities
//        for($i=2;$i<=10;$i++) { // todo exadd + all
            $cam_capabilities_1 = $htmlDom2->query('//strong[@class="ozellik31"]/following::span[@class="cell cs1"]')->item(0)->nodeValue ?? null;
            if($cam_capabilities_1) {
                $result_data_turkish['lggn021'] = isValueExists($cam_capabilities_1, 'Animoji');
                $result_data_turkish['lggn022'] = isValueExists($cam_capabilities_1, 'HDR');
                $result_data_turkish['lggn023'] = isValueExists($cam_capabilities_1, 'Arka Arkaya Çekim Modu');
                $result_data_turkish['lggn024'] = isValueExists($cam_capabilities_1, 'BSI');
                $result_data_turkish['lggn025'] = isValueExists($cam_capabilities_1, 'Live Photos');
                $result_data_turkish['lggn026'] = isValueExists($cam_capabilities_1, 'Portre Modu');
                $result_data_turkish['lggn027'] = isValueExists($cam_capabilities_1, 'Pozlama Kontrolü');
                $result_data_turkish['lggn028'] = isValueExists($cam_capabilities_1, 'Zamanlayıcı');
            }
//            if($cam_capabilities_1) { $result_data_turkish['lggn022'] = isValueExists($cam_capabilities_1, 'HDR'); }
//            if($cam_capabilities_1) { $result_data_turkish['lggn023'] = isValueExists($cam_capabilities_1, 'Arka Arkaya Çekim Modu'); }
//            if($cam_capabilities_1) { $result_data_turkish['lggn024'] = isValueExists($cam_capabilities_1, 'BSI'); }
//            if($cam_capabilities_1) { $result_data_turkish['lggn025'] = isValueExists($cam_capabilities_1, 'Live Photos'); }
//            if($cam_capabilities_1) { $result_data_turkish['lggn026'] = isValueExists($cam_capabilities_1, 'Portre Modu'); }
//            if($cam_capabilities_1) { $result_data_turkish['lggn027'] = isValueExists($cam_capabilities_1, 'Pozlama Kontrolü'); }
//            if($cam_capabilities_1) { $result_data_turkish['lggn028'] = isValueExists($cam_capabilities_1, 'Zamanlayıcı'); }
//        }

        // get 2g frequencies
        $freg_2g = $htmlDom2->query('//strong[@class="ozellik41"]/following::span[1]')->item(0)->nodeValue ?? null;// network_2g_freq
        if($freg_2g) {
            $freg_2g = str_replace(' MHz',',',trim(strip_tags($freg_2g))); // todo exadd +
            $freq_info = explode(',', trim($freg_2g,',')); // todo exadd +
            $result_data_turkish['es77mka'] = array_map(function($item) {
                return trim(strip_tags($item)); // todo exadd +
            }, $freq_info);
        }

        // get 2g technology
        $technology_2g = $htmlDom2->query('//strong[@class="ozellik56 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // gsm
        if($technology_2g) {
            $result_data_turkish['o3kmrtz'] = isValueExists($technology_2g, 'EDGE');
            $result_data_turkish['6me3pwq'] = isValueExists($technology_2g, 'GSM');
            $result_data_turkish['de60w8u'] = isValueExists($technology_2g, 'GPRS');
        }

        // get 3g freq
        $freg_3g = $htmlDom2->query('//strong[@class="ozellik42"]/following::span[1]')->item(0)->nodeValue ?? null;// network_3g_freq;
        if($freg_3g) {
            $freg_3g = str_replace('MHz','MHz,',trim($freg_3g)); // todo exadd +
            preg_match_all('/\d{3,}/uim', $freg_3g, $out_3g);
            if(isset($out_3g[0]) && !empty($out_3g[0])) {
                $final_3g = '';
                for($i = 0;$i <= 6;$i++) {
                    if(isset($out_3g[0][$i])) $final_3g .= ', ' . $out_3g[0][$i];
                }
            }
            $final_3g = explode(',',trim($final_3g,','));
            $final_3g = array_map(function($item) { return strip_tags(trim($item)); }, $final_3g);
            $result_data_turkish['lfy3yhr'] = $final_3g; // todo exadd +
        }

        // get 3g download
        $speed_3g_download = $htmlDom2->query('//strong[@class="ozellik39"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($speed_3g_download) { $result_data_turkish['p4zld7l'] = str_replace('Mbps','', trim($speed_3g_download)); } // todo exadd +

        // get 3g upload
        $upload_3g = $htmlDom2->query('//strong[@class="ozellik40"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($upload_3g) { $result_data_turkish['p4zld8l'] = str_replace('Mbps','',trim($upload_3g)); } // todo exadd +

        // get 4g
//        $four_g = $htmlDom2->query('//strong[@class="ozellik51 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // 4g
//        if($four_g) { $result_data_turkish['w77yz4j'] = getAnswerTurkish($four_g); } // todo exadd + delete

        // get 4g freq // todo exadd + all
        $four_g = $htmlDom2->query('//strong[@class="ozellik43 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // 4g
        if($four_g) {
            $freq_arr = preg_grep('/\d+\s+\(band\s+\d+\)\s+MHz/mui', explode("\n", $four_g));
            if(!empty($freq_arr)) {
                $result_data_turkish['w77yz4j'] = '';
                foreach($freq_arr as $item) {
                    preg_match('/(\d+)\s+\(band\s+(\d+)\)/mui', $item, $out_item);
                    if(isset($out_item[1]) && !empty($out_item[1])) {
                        $result_data_turkish['w77yz4j'] .= $out_item[2] . "($out_item[1]),";
                    }
                }
                $result_data_turkish['w77yz4j'] = explode(',',trim($result_data_turkish['w77yz4j'], ',')); // todo exadd +
            }
        }

        // get 4g download
        $download_4g = $htmlDom2->query('//strong[@class="ozellik52"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($download_4g) { $result_data_turkish['p4zld9l'] = str_replace('Mbps','',trim($download_4g)); } // todo exadd +

        // get 4g upload
        $upload_4g = $htmlDom2->query('//strong[@class="ozellik53"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($upload_4g) {$result_data_turkish['p4zld10l'] = str_replace('Mbps','',trim($upload_4g)); } // todo exadd +

        // get 4g technology
        $lte_info = $htmlDom2->query('//strong[@class="ozellik55 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($lte_info) { $result_data_turkish['k6ddojx'] = isValueExists($lte_info, 'LTE'); } // LTE

        // get 4g features
        $network_4g_feat = $htmlDom2->query('//strong[@class="ozellik1055 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($network_4g_feat) {
            $result_data_turkish['p4zld10lw'] = isValueExists($network_4g_feat, 'VoLTE (Voice over LTE)'); // voLTE
        }

        // get support 4.5g
        $support_45g = $htmlDom2->query('//strong[@class="ozellik1737"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($support_45g) { $result_data_turkish['p4zld1l1'] = getAnswerTurkish(trim($support_45g)); }

        // get chipset
        $chipset = $htmlDom2->query('//strong[@class="ozellik15 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // chipset
        if($chipset) {$result_data_turkish['dkg7n4e'] = trim($chipset); }

        // get main CPU
        $main_cpu = $htmlDom2->query('//strong[@class="ozellik28 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // main_cpu
        if($main_cpu) { // todo exadd +
            preg_match('/GHz\s+(.+)/mui', $main_cpu, $out_cpu);
            if(isset($out_cpu[1]) && !empty($out_cpu[1])) {
                $result_data_turkish['y5xo6x4'] = str_replace('ARM','', trim($out_cpu[1]));
            }
        }

        // get cpu frequency
        $cpu_freq = $htmlDom2->query('//strong[@class="ozellik11 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_freq) { $result_data_turkish['y5xo6x6'] = str_replace(['GHz'],[''], trim($cpu_freq)); } // todo exadd +

        // get cpu core
        $cpu_core = $htmlDom2->query('//strong[@class="ozellik12 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_core) {
            $arr_cpu_core = ['Çekirdek' => '']; // todo exadd +
            $result_data_turkish['y5xo6x5'] = str_replace(array_keys($arr_cpu_core),array_values($arr_cpu_core), trim($cpu_core));
        }

        // get processor architecture
        $cpu_archit = $htmlDom2->query('//strong[@class="ozellik347"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_archit) { // todo exadd + all
            preg_match('/\(*\d{2}-bit\)*/', $cpu_archit, $out_proc);
            if(isset($out_proc[0]) && !empty($out_proc[0])) {
                $result_data_turkish['y4xo655'] = str_replace(['(',')'],'', $out_proc[0]);
                $result_data_turkish['y4xo6x6'] = str_replace($out_proc[0], '', $cpu_archit);
            } else {
                $result_data_turkish['y4xo6x6'] = trim($cpu_archit);
            }
        }

        // get first auxiliary processor
        $first_aux_proc = $htmlDom2->query('//strong[@class="ozellik29"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($first_aux_proc) {
            $result_data_turkish['y5xo6x7'] = str_replace('Hareket İşlemcisi', '', trim($first_aux_proc));
        }

        // get cpu production technology
        $cpu_prod_tech = $htmlDom2->query('//strong[@class="ozellik2033 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_prod_tech) { $result_data_turkish['y3xo6x6'] = trim($cpu_prod_tech); }

        // get gpu info
        $gpu_info = ($htmlDom2->query('//strong[@class="ozellik17 tip"]/following::span[1]')->item(0)->nodeValue) ?? null;
        if($gpu_info) { $result_data_turkish['4kzmswo'] = trim($gpu_info); } // todo exadd +

        // get antutu score
        $antutu_score = $htmlDom2->query('//strong[@class="ozellik1672"]/following::span[1]')->item(0)->nodeValue ?? null; // antutu_score
        if($antutu_score) {
            $result_data_turkish['q85w6qmq'] = trim(explode(' ', $antutu_score)[0]);
        }

        // get memory RAM
        $memory_ram = $htmlDom2->query('//strong[@class="ozellik14 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($memory_ram) { $result_data_turkish['ej4wq1y'] = str_replace(' ', '', trim($memory_ram)); } // todo exadd +

        // get max card memory // todo exadd +
        $max_memory_card = $htmlDom2->query('//strong[@class="ozellik22 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($max_memory_card) $result_data_turkish['yz90cwq'] = trim($max_memory_card);

        // get memory ram type
        $ram_type = $htmlDom2->query('//strong[@class="ozellik332"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($ram_type) {$result_data_turkish['z3xo6x6'] = str_replace('x','',trim($ram_type)); } // todo exadd +

        // get memory ram freq // todo exadd +
        $ram_freq = $htmlDom2->query('//strong[@class="ozellik334"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($ram_freq) $result_data_turkish['z3xo6x7'] = trim($ram_freq);

        // get internal storage
        $internal_storage = $htmlDom2->query('//strong[@class="ozellik21 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($internal_storage) { $result_data_turkish['c8xo6x6'] = trim($internal_storage); } // todo exadd +

        // get memory card support
        $card_support = $htmlDom2->query('//strong[@class="ozellik1557 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // memory_card_support
        if($card_support) {$result_data_turkish['yz90cwl'] = getAnswerTurkish($card_support);}

        // get other memory options
        $memory_options = $htmlDom2->query('//strong[@class="ozellik105 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($memory_options) { $result_data_turkish['a3xo6x6'] = str_replace(['Depolama seçeneği var'],[''],trim($memory_options));} // todo exadd +

        // get length
        $length = $htmlDom2->query('//strong[@class="ozellik26 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // length
        if($length) {$result_data_turkish['qorav98'] = str_replace('mm', '', trim($length));}

        // get width
        $width = $htmlDom2->query('//strong[@class="ozellik8 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // width
        if($width) {$result_data_turkish['65ihv16'] = str_replace('mm', '', trim($width));}

        // get also known name todo exadd + (replace)
		$aliases_parent_node = $htmlDom2->query('//strong[@class="ozellik116 tip"]/following::span[1]')->item(0)->childNodes ?? null;
        if($aliases_parent_node) {
			foreach($aliases_parent_node as $node) {
				$alias = $node->textContent ?? null;
				if(trim($alias)) $result_data_turkish['ywkpha1b'][] = getFeatures($alias);
			}
			$result_data_turkish['ywkpha1b'] = array_map('trim', $result_data_turkish['ywkpha1b']);
		}

		///
//        $b = 0; // todo exadd +
//		do {
//			$alias = $aliases_parent_node->item($b)->nodeValue ?? null;
//			if(trim($alias)) $result_data_turkish['ywkpha1b'][] = getFeatures($alias);
//			$b++;
//		} while($alias);
//		$result_data_turkish['ywkpha1b'] = array_map('trim', $result_data_turkish['ywkpha1b']);
//        Render::pretty_print($result_data_turkish['ywkpha1b']);exit();
        ///////
//
//		$i = 0;
//		do{
//			$aliases = $htmlDom2->query('//strong[@class="ozellik116 tip"]/following::span[1]/span')->item($i)->nodeValue ?? null;
//			if($aliases) $result_data_turkish['ywkpha1b'][] = trim(strip_tags($aliases)); // todo exadd +
//        	$i++;
//		} while($aliases);

//        for($i=0;$i<=3;$i++) { // todo + del
//            $aliases = $htmlDom2->query('//strong[@class="ozellik116 tip"]/following::span[1]/span')->item($i)->nodeValue ?? null;
//            if($aliases) $result_data_turkish['ywkpha1b'][] = trim(strip_tags($aliases)); // todo +
//        }

        // get third camera todo exadd +
        $camera_3 = $htmlDom2->query('//strong[@class="ozellik2318"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($camera_3) {
            $result_data_turkish['06wzu5yz'] = getAnswerTurkish($camera_3);
        }

        // get third camera res todo exadd +
        $camera_3_res = $htmlDom2->query('//strong[@class="ozellik4659"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($camera_3_res) {
            $result_data_turkish['06wzu1yz'] = $camera_3_res;
        }

        // get third camera features todo exadd + all
        for($i=0;$i<=5;$i++) {
            $camera_3_feat = $htmlDom2->query('//strong[@class="ozellik2319"]/following::span[1]/span')->item($i)->nodeValue ?? null;
            if ($camera_3_feat) {
                $result_data_turkish['06wz11yz'][] = str_replace(['Optik'], ['Optic'], trim($camera_3_feat));
            }
        }

        // get second auxiliary processor todo exadd +
        $sec_aux_proc = $htmlDom2->query('//strong[@class="ozellik1607"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($sec_aux_proc) {
            $result_data_turkish['y5xo6x8'] = str_replace('Hareket İşlemcisi', 'Motion Processor', trim($sec_aux_proc));
        }

        $thickness = $htmlDom2->query('//strong[@class="ozellik10 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // thick
        if($thickness) {$result_data_turkish['vbryix7'] = str_replace('mm', '', trim($thickness));}

        // get weight
        $weight = $htmlDom2->query('//strong[@class="ozellik9"]/following::span[1]')->item(0)->nodeValue ?? null;// weight
        if($weight) {$result_data_turkish['uanzwi8'] = str_ireplace('Gram', '',trim($weight));}

        // get color's
        $colors = $htmlDom2->query('//strong[@class="ozellik80 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // colors
        if($colors) { // todo exadd + all
            $color_info = trim(getEngNameFromTurkish(($colors))); // todo ???
            $color_info = preg_replace('/\s/ui', ".", $color_info);
            $result_data_turkish['ywkph10b'] = explode('...',$color_info);
        }

        // get cover materials
        $cover_materials = $htmlDom2->query('//strong[@class="ozellik1320"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cover_materials) { $result_data_turkish['3bjbzry'] = getEngNameMaterial(trim($cover_materials));} // todo  ??

        // get frame materials
        $frame_materials = $htmlDom2->query('//strong[@class="ozellik1321"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($frame_materials) { // todo exadd + all
            $result_data_turkish['3bjbzra'] = str_ireplace([' )','+'],[')',';'], getEngNameMaterial(trim($frame_materials)));
        }

        // get OS
        $platform_os = $htmlDom2->query('//strong[@class="ozellik24 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($platform_os) {
//            $result_data_turkish['ui65qcn'] = trim($platform_os); // get os // todo exadd + delete
            $result_data_turkish['0v8w2sz'] = getOS($platform_os, 'iOS'); // iOS
            $result_data_turkish['a5sj3l2'] = getOS($platform_os, 'indow'); // windows
            $result_data_turkish['vxq3g1f'] = getOS($platform_os, 'lackBerry'); // blackberry
            $result_data_turkish['llulwif'] = getOS($platform_os, 'ndroid'); // android todo exadd +
        }

        // get os version // todo exadd + all
        $version_os = $htmlDom2->query('//strong[@class="ozellik25"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($version_os) {
            $result_data_turkish['ui65qcn'] = trim($version_os);
        }
        // get available ver. // todo exadd +
        $available_os = $htmlDom2->query('//strong[@class="ozellik34 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($available_os) {
            $result_data_turkish['ui71qcn'] = trim($available_os);
        }

        // get radio
        $radio_info = $htmlDom2->query('//strong[@class="ozellik76"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($radio_info) {$result_data_turkish['tix99ot'] = getAnswerTurkish($radio_info); } // radio

        // get speaker features
        $speaker_info = ($htmlDom2->query('//strong[@class="ozellik318"]/following::span[1]')->item(0)->nodeValue) ?? null;
        if($speaker_info) {
            $result_data_turkish['yq2jcrl2'] = isCodecExists($speaker_info, 'Çift Hoparlör'); // dual speaker // todo exadd +
        }

        // get audio input
        $audio_out = $htmlDom2->query('//strong[@class="ozellik324 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($audio_out) {
            $result_data_turkish['yh7xh36'] = trim($audio_out);
            $result_data_turkish['yh7xh3q'] = isValueExists($audio_out,'3.5'); // todo exadd +
        }

        // get wi-fi channels
        $wifi_channels = $htmlDom2->query('//strong[@class="ozellik36 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // wifi_channels
        if($wifi_channels) {$result_data_turkish['2pinrcv'] = trim($wifi_channels);}

        // get wi-fi features
        $wifi_features = $htmlDom2->query('//strong[@class="ozellik59"]/following::span')->item(0)->nodeValue ?? null;
        if($wifi_features) {
            $result_data_turkish['p4zld1l5'] = isValueExists($wifi_features, 'MIMO');
            $result_data_turkish['p4zld1l2'] = isValueExists($wifi_features, 'Dual-Band');
            $result_data_turkish['p4zld1l3'] = isValueExists($wifi_features, 'Hotspot'); // todo exadd + all
            $result_data_turkish['p4zld1l4'] = isValueExists($wifi_features, 'MiraCast'); // todo exadd + all
            $result_data_turkish['p4zld1l6'] = isValueExists($wifi_features, 'Wi-Fi Direct'); // todo exadd + all
            $result_data_turkish['p4zld1l8'] = isValueExists($wifi_features, 'VoWiFi'); // todo exadd + all
            $result_data_turkish['p4zld1l9'] = isValueExists($wifi_features, 'HT80');// todo exadd + all
            $result_data_turkish['p4zld111'] = isValueExists($wifi_features, 'VHT80');// todo exadd + all
            $result_data_turkish['p4zld120'] = isValueExists($wifi_features, '1024QAM');// todo exadd + all
        }

        // get nfc
        $nfc_info = $htmlDom2->query('//strong[@class="ozellik61 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // nfc
        if($nfc_info) {$result_data_turkish['9ee4viy'] = getAnswerTurkish($nfc_info);}

        // get nfc features  todo exadd + all
        $nfc_feat = $htmlDom2->query('//strong[@class="ozellik325 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // nfc
        if($nfc_feat) {
            $result_data['9ee4viz'] = isCodecExists($nfc_feat, 'eSE');
            $result_data['9ee4vid'] = isCodecExists($nfc_feat, 'UICC');
        }

        // get bluetooth ver.
        $bluetooth_version = $htmlDom2->query('//strong[@class="ozellik48 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // bluetooth_version
        if($bluetooth_version) {  // todo exadd +
            $result_data_turkish['p4zld5l'] = trim($bluetooth_version);
        } else {
            $result_data_turkish['p4zld5l'] = '-';
        }

        // get bluetooth features // todo exadd +
        $bluetooth_hid = $htmlDom2->query('//strong[@class="ozellik49 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($bluetooth_version) $result_data_turkish['p4zld51'] = isValueExists($bluetooth_hid, 'HID');



        // get infrared
        $is_infrared = $htmlDom2->query('//strong[@class="ozellik62 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($is_infrared) {$result_data_turkish['hwst1n7'] =  getAnswerTurkish($is_infrared); } // infrared

        // get navigation features
        $glonass = $htmlDom2->query('//strong[@class="ozellik79 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($glonass) {
            $result_data_turkish['39ji8mm'] = isCodecExists($glonass, 'GLONASS'); // glonass // todo exadd +
            $result_data_turkish['yfvshn2'] = isCodecExists($glonass, 'GPS'); // gps // todo exadd +
            $result_data_turkish['yfvshn2'] = isCodecExists($glonass, 'Galileo'); // gps // todo exadd +
            $result_data_turkish['x1xgsb1'] = isCodecExists($glonass, 'BDS'); // gps // todo exadd +
            $result_data_turkish['x1xgsbl'] = isCodecExists($glonass, 'A-GPS'); // gps // todo exadd +
        }

        // get water resistance
        $water_resistance = $htmlDom2->query('//strong[@class="ozellik329 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($water_resistance) {$result_data_turkish['cxeplx1'] = getAnswerTurkish(($water_resistance));} // todo exadd +

        // get video formats // todo exadd + all
        $video_formats = ($htmlDom2->query('//strong[@class="ozellik82 tip"]/following::span[@class="cell cs3"]')->item(0)->nodeValue) ?? null;
        if($video_formats) {
            $result_data_turkish['x055z520'] = isCodecExists($video_formats, 'MP4');
            $result_data_turkish['8j6be1ko'] = isCodecExists($video_formats, 'DivX');
            $result_data_turkish['xc4bb9kc'] = isCodecExists($video_formats, 'XviD');
            $result_data_turkish['crrbpcar'] = isCodecExists($video_formats, 'H.265');
            $result_data_turkish['crrbpca1'] = isCodecExists($video_formats, 'H.264');
            $result_data_turkish['xd942mit'] = isCodecExists($video_formats, 'WMV');
            $result_data_turkish['f3n8nq17'] = isCodecExists($video_formats, 'ASF'); // todo exadd +
            $result_data_turkish['f3n8nq18'] = isCodecExists($video_formats, 'FLV'); // todo exadd +
            $result_data_turkish['f3n8nq19'] = isCodecExists($video_formats, 'M4V'); // todo exadd +
            $result_data_turkish['f3n8nq20'] = isCodecExists($video_formats, 'WEBM'); // todo exadd +
            $result_data_turkish['f3n8nq21'] = isCodecExists($video_formats, '3G2'); // todo exadd +
            $result_data_turkish['f3n8nq22'] = isCodecExists($video_formats, '3GP'); // todo exadd +
            $result_data_turkish['f3n8nq24'] = isCodecExists($video_formats, 'VP8'); // todo exadd +
            $result_data_turkish['f3n8nq25'] = isCodecExists($video_formats, 'VP9'); // todo exadd +
        }


        // get audio formats // todo exadd + all
        $audio_formats = ($htmlDom2->query('//strong[@class="ozellik83 tip"]/following::span[@class="cell cs3"]')->item(0)->nodeValue) ?? null;
        if($audio_formats) {
            $result_data_turkish['f7lsmmw9'] = isCodecExists($audio_formats, 'MP3');
            $result_data_turkish['am1zgml8'] = isCodecExists($audio_formats, 'WAV');
            $result_data_turkish['t1inmosa'] = isCodecExists($audio_formats, 'FLAC');
            $result_data_turkish['lnk8dr8h'] = isCodecExists($audio_formats, 'eAAC');
            $result_data_turkish['7zq7neoh'] = isCodecExists($audio_formats, 'WMA');
            $result_data_turkish['tfuq45ng'] = isCodecExists($audio_formats, 'AAX');
            $result_data_turkish['f3n8nqp4'] = isCodecExists($audio_formats, 'AIFF');
            $result_data_turkish['f3n8nqp5'] = isCodecExists($audio_formats, 'AWB'); // todo exadd +
            $result_data_turkish['f3n8nqp6'] = isCodecExists($audio_formats, 'DFF'); // todo exadd +
            $result_data_turkish['f3n8nqp7'] = isCodecExists($audio_formats, 'IMY'); // todo exadd +
            $result_data_turkish['f3n8nqp8'] = isCodecExists($audio_formats, 'RTX'); // todo exadd +
            $result_data_turkish['f3n8nqp9'] = isCodecExists($audio_formats, 'OGA'); // todo exadd +
            $result_data_turkish['f3n8nq11'] = isCodecExists($audio_formats, 'OTA'); // todo exadd +
            $result_data_turkish['f3n8nq10'] = isCodecExists($audio_formats, 'MXMF'); // todo exadd +
            $result_data_turkish['f3n8nq13'] = isCodecExists($audio_formats, 'AMR'); // todo exadd +
            $result_data_turkish['f3n8nq14'] = isCodecExists($audio_formats, 'APE'); // todo exadd +
            $result_data_turkish['f3n8nq15'] = isCodecExists($audio_formats, 'DSF'); // todo exadd +
            $result_data_turkish['f3n8nq16'] = isCodecExists($audio_formats, 'OGG'); // todo exadd +
            $result_data_turkish['f3n8nq23'] = isCodecExists($audio_formats, 'PCM'); // todo exadd +
            $result_data_turkish['f3n8nq26'] = isCodecExists($audio_formats, 'OPUS'); // todo exadd +
            $result_data_turkish['f3n8nq27'] = isCodecExists($audio_formats, 'RTTTL'); // todo exadd +
            $result_data_turkish['f3n8nq28'] = isCodecExists($audio_formats, 'Vorbis'); // todo exadd +
            $result_data_turkish['f3n8nq29'] = isCodecExists($audio_formats, '3GA'); // todo exadd +
        }

        // get water resistance level
        $water_resistance_level = ($htmlDom2->query('//strong[@class="ozellik114 tip"]/following::span[1]')->item(0)->nodeValue) ?? null; // water resistant standart
        if($water_resistance_level) {
            $arr_wt_resist = [ // todo exadd + all
                'Sadece Sıçramalara Karşı' => 'IPX4',
                'Yok' => '-'
            ];
            $result_data_turkish['cxeplx1'] = (str_ireplace(array_keys($arr_wt_resist),array_values($arr_wt_resist),$water_resistance_level)); // todo exadd -
        }

        // get dust resistance
        $dust_resistance = $htmlDom2->query('//strong[@class="ozellik330 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($dust_resistance) {$result_data_turkish['cxeplx2'] = getAnswerTurkish(trim($dust_resistance));}

        // get resistance dust level
        $resistance_dust_info = ($htmlDom2->query('//strong[@class="ozellik113 tip"]/following::span[1]')->item(0)->nodeValue) ?? null; // resistance_level // todo exadd +
        if($resistance_dust_info) $result_data_turkish['cxeplx3'] = trim($resistance_dust_info);


        // get 3g video call
        $video_call_3g = $htmlDom2->query('//strong[@class="ozellik1708 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($video_call_3g) {$result_data_turkish['p4zld1lk'] = getAnswerTurkish(trim($video_call_3g));}

        // get video conversation
        $video_conversation = $htmlDom2->query('//strong[@class="ozellik2734 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($video_conversation) {$result_data_turkish['p4zld1lq'] = getAnswerTurkish(trim($video_conversation));}

        // get sensors
        $feat_sensors = $htmlDom2->query('//strong[@class="ozellik75 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($feat_sensors) {
            $result_data_turkish['h1ddzrt'] = isValueExists($feat_sensors, 'İvmeölçer'); // accelerometer
            $result_data_turkish['ywtcejg'] = isValueExists($feat_sensors, 'Jiroskop'); // gyroskop
            $result_data_turkish['x399jxz'] = isValueExists($feat_sensors, 'Barometre'); // barometer
            $result_data_turkish['x0xgsbn'] = isValueExists($feat_sensors, 'Pusula'); // compass
            $result_data_turkish['h88pkmdy'] = isValueExists($feat_sensors, 'Ortam Işığı Sensörü'); // light sensor
            $result_data_turkish['h88pkmd1'] = isValueExists($feat_sensors, 'Ortam Işığı Sensörü'); // proxymiti sensor
        }

        // get fingerprint
        $finger_print = $htmlDom2->query('//strong[@class="ozellik1511 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($finger_print) {
            $result_data_turkish['rsub3l9c'] = getAnswerTurkish($finger_print); // fingerprint
        }

        // get notification light
        $light_indicator = $htmlDom2->query('//strong[@class="ozellik111 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // notify_led
        if($light_indicator) {$result_data_turkish['xdet6dq'] = getAnswerTurkish($light_indicator);}

        // get SAR value head
        $sar_head_info = $htmlDom2->query('//strong[@class="ozellik92 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // todo exadd + all
        if($sar_head_info) $result_data_turkish['5cp2ol9j'] = str_replace('W/kg (10g)','', $sar_head_info);

        // get SAR value body
        $sar_body_info = ($htmlDom2->query('//strong[@class="ozellik91 tip"]/following::span[1]')->item(0)->nodeValue) ?? null; // todo exadd +
        if($sar_body_info) $result_data_turkish['owpcmmmy'] = str_replace('W/kg (10g)','', $sar_body_info);

        // todo exadd + del

        // number of lines // todo exadd +
        $number_lines = $htmlDom2->query('//strong[@class="ozellik104 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($number_lines) {
            $line_info = (mb_stripos($number_lines, 'Tek') !== false) ? '1' : '2';
            $result_data_turkish['0q3ucns1'] = $line_info;
        }

        // standby 3g // todo exadd +
        $standby_3g = $htmlDom2->query('//strong[@class="ozellik86"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($standby_3g) {
            $result_data_turkish['6ojsm291'] = str_replace('Saat','', $standby_3g);
        }

        //  gpu freq // todo exadd +
        $gpu_freq = $htmlDom2->query('//strong[@class="ozellik221 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($gpu_freq) {
            $result_data_turkish['y1xo6x6'] = $gpu_freq;
        }

        //  antutu v5 // todo exadd +
        $antutu_v5 = $htmlDom2->query('//strong[@class="ozellik789"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($antutu_v5) {
            $result_data_turkish['q85w6qma'] = trim(explode(' ', $antutu_v5)[0]);
        }

        // user interface // todo exadd +
        $user_interface = $htmlDom2->query('//strong[@class="ozellik35 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($user_interface) {
            $result_data_turkish['a1xo6x6'] = str_ireplace('Saf','Pure',$user_interface); // todo exadd +
        }

        // second front display // todo exadd +
        $second_front_display = $htmlDom2->query('//strong[@class="ozellik1543"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($second_front_display) {
            $result_data_turkish['aakph19b'] = getAnswerTurkish($second_front_display);
        }

        // second front display size // todo exadd +
        $second_front_display_size = $htmlDom2->query('//strong[@class="ozellik1544"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($second_front_display_size) {
            $result_data_turkish['aakph19z'] = str_replace('Inch','',$second_front_display_size);
        }

        // second front display resol // todo exadd +
        $second_front_display_resol = $htmlDom2->query('//strong[@class="ozellik1545"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($second_front_display_resol) {
            $result_data_turkish['aakph22z'] = str_replace('Pixel','',$second_front_display_resol);
        }

        // second front display feat // todo exadd +
        $second_front_display_feat = $htmlDom2->query('//strong[@class="ozellik1547"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($second_front_display_feat) {
            $result_data_turkish['aakph23z'] = isCodecExists($second_front_display_feat, 'Always-on Display');
        }

        // stand by 4g // todo exadd +
        $standby_4g = $htmlDom2->query('//strong[@class="ozellik88"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($standby_4g) {
            $result_data_turkish['6ojsm293'] = str_replace('Saat','', $standby_4g);
        }

        // talk time 4g // todo exadd +
        $talk_time_4g = $htmlDom2->query('//strong[@class="ozellik87"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($talk_time_4g) {
            $result_data_turkish['6ojsm292'] = str_replace('Saat','', $talk_time_4g);
        }

        // focal length // todo exadd +
        $focal_length = $htmlDom2->query('//strong[@class="ozellik103 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($focal_length) {
            $result_data_turkish['gn4gn6x7'] = $focal_length;
        }

        // ram channels // todo exadd +
        $ram_channels = $htmlDom2->query('//strong[@class="ozellik333"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($ram_channels) {
            $result_data_turkish['v3xo6x6'] = str_replace(['Çift Kanal'], ['Dual Channel'], $ram_channels);
        }

        // get available memory // todo exadd +
        $available_memory = $htmlDom2->query('//strong[@class="ozellik822"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($available_memory) {
            $result_data_turkish['ui65qc2'] = $available_memory;
        }

        // get thinnest point // todo exadd +
        $thick_point = $htmlDom2->query('//strong[@class="ozellik891"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($thick_point) $result_data_turkish['ywkphzzz'] = str_replace('mm', '', $thick_point);


        // get services and apps list todo exadd + del
        $result_data_turkish['awkph141'] = [];

//        // todo delete
//        $all_services_info = $htmlDom2->query('//strong[@class="ozellik217 tip"]/following::span[1]')->item(0)->childNodes->item(2)->nodeValue ?? null;
//        var_dump($all_services_info);
//        exit(); // todo

        $a = 0; // todo exadd +
		$all_services_parent_node = $htmlDom2->query('//strong[@class="ozellik217 tip"]/following::span[1]')->item(0)->childNodes;

		do {
//            $all_services_info = $htmlDom2->query('//strong[@class="ozellik217 tip"]/following::span[1]/span/a/text()')->item($a)->childNodes ?? null;
			$all_services_info = $all_services_parent_node->item($a)->nodeValue ?? null;
			if(trim($all_services_info)) $result_data_turkish['awkph141'][] = getFeatures($all_services_info);
			$a++;
		} while($all_services_info);
		$result_data_turkish['awkph141'] = array_map('trim', $result_data_turkish['awkph141']);


//        for($a=0;$a<20;$a++) {
//            $all_services_info = $htmlDom2->query('//strong[@class="ozellik217 tip"]/following::span[1]/span/a/text()')->item($a)->nodeValue ?? null; // services_apps
//            if($all_services_info) $result_data_turkish['awkph141'][] = getFeatures($all_services_info);
//        }


        // todo exadd +
//        $result_data_turkish['awkph141'] = [];
//        $all_services_info = $htmlDom2->query('//strong[@class="ozellik217 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // services_apps
//        if($all_services_info) $result_data_turkish['awkph141'] = getFeatures($all_services_info);


        // get services todo exadd +
        if(isset($result_data_turkish['awkph141']) && !empty($result_data_turkish['awkph141'])) {
                $str_services = implode(',', $result_data_turkish['awkph141']);
                $result_data_turkish['azxo4x7'] = isCodecExists($str_services, 'Screen Mirroring');
                $result_data_turkish['azxo4x8'] = isCodecExists($str_services, 'Changeable Themes');
                $result_data_turkish['azxo4x9'] = isCodecExists($str_services, 'AirPrint');
                $result_data_turkish['azxo410'] = isCodecExists($str_services, 'Spotlight Call');
                $result_data_turkish['azxo411'] = isCodecExists($str_services, 'MirrorLink');
                $result_data_turkish['azxo412'] = isCodecExists($str_services, 'Easy mode');
                $result_data_turkish['azxo413'] = isCodecExists($str_services, 'Samsung KNOX');
                $result_data_turkish['azxo414'] = isCodecExists($str_services, 'ANT+');
                $result_data_turkish['azxo415'] = isCodecExists($str_services, 'Dual/Multi Window');
                $result_data_turkish['azxo416'] = isCodecExists($str_services, 'Ultra Power Saving Mode');
                $result_data_turkish['azxo416'] = isCodecExists($str_services, 'Ultra Power Saving Mode');
                $result_data_turkish['azxo417'] = isCodecExists($str_services, 'Turn On Voice Screen Lock');
                $result_data_turkish['azxo418'] = isCodecExists($str_services, 'Single-Hand Use Mode');
                $result_data_turkish['azxo419'] = isCodecExists($str_services, 'S Pen');
                $result_data_turkish['azxo420'] = isCodecExists($str_services, 'Air Command');
                $result_data_turkish['azxo421'] = isCodecExists($str_services, 'DAC');
                $result_data_turkish['azxo422'] = isCodecExists($str_services, 'Hidden Mode');
                $result_data_turkish['azxo423'] = isCodecExists($str_services, '(AOP) Microphone');
                $result_data_turkish['azxo424'] = isCodecExists($str_services, 'HWA');
                $result_data_turkish['azxo425'] = isCodecExists($str_services, 'Voice Command');
                $result_data_turkish['azxo426'] = isCodecExists($str_services, 'Virtual reality');
                $result_data_turkish['azxo427'] = isCodecExists($str_services, 'LDAC');
                $result_data_turkish['wnqjx4j9'] = isCodecExists($str_services, 'Samsung Pay');
        }

        // get face_id // todo exadd + all
        $services_info = $htmlDom2->query('//strong[@class="ozellik217 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // services_apps
        if($services_info) {
            $result_data_turkish['c4awfagk'] = isValueExists($services_info, 'Face ID'); // face id
            $result_data_turkish['a4xo6x6'] = isValueExists($services_info, 'AirDrop'); // todo exadd +
            $result_data_turkish['a4xo1x6'] = isValueExists($services_info, 'AirPlay'); // todo exadd +
            $result_data_turkish['azxo1x6'] = isValueExists($services_info, 'FaceTime'); // todo exadd +
            $result_data_turkish['azxo2x6'] = isValueExists($services_info, 'iBeacon'); // todo exadd +
            $result_data_turkish['azxo3x6'] = isValueExists($services_info, 'iCloud'); // todo exadd +
            $result_data_turkish['azxo4x6'] = isValueExists($services_info, 'Siri'); // todo exadd +
        }


        // get package include // todo exadd + all
        $package_info = $htmlDom2->query('//strong[@class="ozellik218 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($package_info) {
            $result_data_turkish['v464xz6'] = isValueExists($package_info, 'Belgeler');
            $result_data_turkish['4yetljhv'] = isCodecExists($package_info, 'Çıkartma İğnesi'); // todo exadd +
            $result_data_turkish['vkladush'] = isValueExists($package_info, 'Kulaklık için Yedek');
            $result_data_turkish['2uecljhv'] = isValueExists($package_info, 'Kulaklık');
            $result_data_turkish['h1btbtw'] = isValueExists($package_info, 'Güç Adaptörü');
            $result_data_turkish['ybiwt2b'] = isValueExists($package_info, 'OTG');
        }

        // get usb 2.0// todo exadd + all
        $usb_info = $htmlDom2->query('//strong[@class="ozellik64 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // usb type
        if($usb_info) {$result_data_turkish['0arcae64'] = isValueExists($usb_info, '2.0');}
        if($usb_info) {$result_data_turkish['p7s2uenu'] = isValueExists($usb_info, '3.0');}
        if($usb_info) {$result_data_turkish['rmjj6m5t'] = isValueExists($usb_info, 'Type-C');}


        // get usb connection type
        $usb_info = $htmlDom2->query('//strong[@class="ozellik65 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // usb type
        if($usb_info) { // todo exadd +
            $result_data_turkish['q8o92fk'] = trim($usb_info);
            if(isset($result_data_turkish['rmjj6m5t']) && $result_data_turkish['rmjj6m5t'] == '-') {
                $result_data_turkish['rmjj6m5t'] = isValueExists($result_data_turkish['q8o92fk'], 'Type-C');
            }
        }

        // get usb features
        $usb_features = $htmlDom2->query('//strong[@class="ozellik66"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($usb_features) {
            $result_data_turkish['9qsw0l7d'] = isValueExists($usb_features, 'OTG'); // todo exadd +
            $result_data_turkish['rmjj6m56'] = isValueExists($usb_features, 'DisplayPort'); // todo exadd +
			// todo exadd + all
			preg_match('/DisplayPort\s+\((.+)\)/mui', $usb_features, $out_dp_params);
			if(isset($out_dp_params[1]) && !empty($out_dp_params[1])) {
				$result_data_turkish['rmjj6m57'] = trim($out_dp_params[1]);
			};
        }

//        var_dump($result_data_turkish['rmjj6m57']);exit();

        // get sim info
        $sim_info = $htmlDom2->query('//strong[@class="ozellik44 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // sim
        if($sim_info) {
            $sim_info = str_ireplace(['Mikro','(4FF)','(fFF)'],['Micro','',''],trim($sim_info));
            // get nano-sim
            $result_data_turkish['8q7wrlul'] = isValueExists($sim_info,'Nano'); // todo exadd +
            // get micro-sim
            $result_data_turkish['lawrulap'] = isValueExists($sim_info,'Micro'); // todo exadd +
			// get sim standard // todo exadd -
			$result_data_turkish['mdmfh57'] = isValueExists($sim_info,'Micro');
        }

        // get dual sim // todo exadd +
        $sim_info = $htmlDom2->query('//strong[@class="ozellik326"]/following::span[1]')->item(0)->nodeValue ?? null; // sim
        if($sim_info) {
            $result_data_turkish['0q3ucnsi'] = isCodecExists($sim_info, 'Dual Standby');
        }

        // get announcement date
        $announcement_date = $htmlDom2->query('//strong[@class="ozellik599"]/following::span[1]')->item(0)->nodeValue ?? null; // announcement_date
        if($announcement_date) {$result_data_turkish['zgxvylx'] = trim($announcement_date); }

        // get release date
        $release_date_info = ($htmlDom2->query('//strong[@class="ozellik600"]/following::span[1]')->item(0)->nodeValue) ?? null; // release_date
        if($release_date_info) {$result_data_turkish['2lbcv9f'] = trim($release_date_info); }

        // ger user rating
        $user_rating = $htmlDom2->query('//span[@class="kpuan"]')->item(0)->nodeValue ?? null; // user ratings
        if($user_rating) { // todo exadd +
            preg_match_all('/\d+\.?\d+\s+/', $user_rating, $output_rating);
            if(isset($output_rating[0][0]) && !empty($output_rating[0][0])) { $rank = trim(($output_rating[0][0]));}
            if(isset($output_rating[0][1]) && !empty($output_rating[0][1])) {  $opinions = ($output_rating[0][1]);}
            if(isset($rank) && isset($opinions)) $result_data_turkish['bkaqn4m'] = [$rank => $opinions]; // todo exadd +
        }

        // get type
        $result_data_turkish['drbmx1r'] = 1;

    } else {
        /** get smartwatch & fitness tracker data */
        // get display
        $display_exist = $htmlDom2->query('//strong[@class="ozellik1246"]/following::span[@class="cell cs1"]/span/a/text()')->item(0)->nodeValue ?? null; // screen_size
        if($display_exist) { // todo exadd +
            $result_data_turkish['aakph191'] = getAnswerTurkish($display_exist);
        }

        // get screen size
        $screen_size = $htmlDom2->query('//strong[@class="ozellik1117"]/following::span[@class="cell cs1"]/span/a/text()')->item(0)->nodeValue ?? null; // screen_size
        if($screen_size) {
            $result_data_turkish['1n820fz'] = explode(' ', $screen_size)[0];
        }

        // get image from product // todo + del
        /*
        $image = $htmlDom2->query('//ul[@class="galerik"]/li/a/img/@src')->item(0)->nodeValue ?? null;
        $result_data_turkish['photos'] = [];
        if($image) {
            preg_match('/\/s.+-(\d+)\.[png|jpg|jpeg]+/uim', $image, $out_url);
            if(isset($out_url[0]) && !empty($out_url[0])) {
                $main_url = substr($image, 0, strlen($image) - strlen($out_url[0]));
                $fin_url = str_replace('/s_', '/b_', $out_url[0]);
                $fin_url = preg_replace('/-\d+\.[png|jpg|jpeg]+/uim', "", $fin_url);
                for($i = 1; $i <= 15; $i ++) {
                    $image_url = $main_url . $fin_url . '-' . $i . '.png';
                    $picture_png = getImage($image_url);
                    if($picture_png) {
                        $result_data_turkish['photos'][] = $picture_png;
                    }
                }
                for($i = 1; $i <= 25; $i ++) {
                    $image_url =  $main_url . $fin_url . '-' . $i . '.jpg';
                    $picture_jpg = getImage($image_url);
                    if($picture_jpg) {
                        $result_data_turkish['photos'][] = $picture_jpg;
                    }
                }
            }
        }
        */

        // get display resolution
        $display_resolution = $htmlDom2->query('//strong[@class="ozellik1116"]/following::span[@class="cell cs1"]/span/text()')->item(0)->nodeValue ?? null;
        if($display_resolution) {
            $result_data_turkish['nggks18'] = trim(explode('x', $display_resolution)[0]); // display width
            preg_match("/[0-9]+/", explode('x', $display_resolution)[1], $output_array);
            $result_data_turkish['j2p7bju'] = trim($output_array[0]); // display height
        }

        // get display number of colors
        $display_colors = $htmlDom2->query('//strong[@class="ozellik1177"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_colors) {
            $result_data_turkish['8vzzca7'] = str_replace(['Renkli'], ['Color'], explode(' ', $display_colors)[0]); // number of colors
        }

        // get ppi
        $ppi = $htmlDom2->query('//strong[@class="ozellik1124"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($ppi) {
            $ppi = preg_match('/\d+/ui', $ppi, $output_array);
            if($output_array[0] && !empty($output_array[0])) {
                $result_data_turkish['7x8x76o'] = $output_array[0];
            }
        }

        // get display technology
        $display_technology = ($htmlDom2->query('//strong[@class="ozellik1125"]/following::span[1]')->item(0)->nodeValue) ?? null; // display_technology
        if($display_technology) { $result_data_turkish['xxyv5nx'] = getEngNameFromTurkish(trim($display_technology)); }

        // get display features(touch)
        $display_info = $htmlDom2->query('//strong[@class="ozellik1126"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_info) {
            $result_data_turkish['pcao0re'] = isValueExists($display_info, 'Dokunmatik'); // multitouch
        }

        // compatible brand
        $compatible_brand = $htmlDom2->query('//strong[@class="ozellik1266"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($compatible_brand) $result_data_turkish['ywkph11b'] = trim($compatible_brand);

        // compatible devices
        $compatible_devices = $htmlDom2->query('//strong[@class="ozellik1099"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($compatible_devices) $result_data_turkish['ywkph12b'] = trim($compatible_devices);

        // get battery capacity
        $battery_capacity = $htmlDom2->query('//strong[@class="ozellik1108"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($battery_capacity) $result_data_turkish['wbswcml'] = str_replace('mAh','',trim($battery_capacity)); // todo exadd +

        // get battery type
        $battery_type = $htmlDom2->query('//strong[@class="ozellik1109"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($battery_type) {
            $battery_type = preg_replace('/\(.+\)/uim', "", $battery_type); // todo exadd +
            $battery_type_info = getEngNameFromTurkish(trim($battery_type)); // todo exadd +
            $result_data_turkish['63r9r99'] = str_replace('Li-Polymer', 'Lithium Polymer', $battery_type_info); // todo exadd +
        };

        // charging format
        $charg_format = $htmlDom2->query('//strong[@class="ozellik1161"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($charg_format) $result_data_turkish['charg_type'] = trim(str_replace(['Şarj İstasyonu'], ['Charging Station'], $charg_format));

        // get camera
        $camera = $htmlDom2->query('//strong[@class="ozellik1162"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($camera) $result_data_turkish['lggn0m2'] = getAnswerTurkish($camera);



        // get video
        $video = $htmlDom2->query('//strong[@class="ozellik1182"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($video) $result_data_turkish['t9q0h7hd'] = getAnswerTurkish($video);

        // get required apps adroid
        $required_apps = $htmlDom2->query('//strong[@class="ozellik1189"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($required_apps) $result_data_turkish['ywkph13b'] = trim(str_replace(['Gerekli Uygulama'], [''], $required_apps));

        // get required apps ios
        $required_apps_ios = $htmlDom2->query('//strong[@class="ozellik1230"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($required_apps_ios) $result_data_turkish['awkph14b'] = trim(str_replace(['Gerekli Uygulama'], [''], $required_apps_ios));

        // average battery time life
        $average_battery_life = $htmlDom2->query('//strong[@class="ozellik1158"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($average_battery_life) {
            preg_match('/\d+/ui', $average_battery_life, $output_array);
            if(isset($output_array[0]) &&  !empty($output_array[0])) {
                $result_data_turkish['ywkph14b'] = trim($output_array[0]);
            }
        }

        // low battery life
        $low_battery_life = $htmlDom2->query('//strong[@class="ozellik1159"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($low_battery_life) {
            preg_match('/\d+/ui', $low_battery_life, $output_array);
            if(isset($output_array[0]) && !empty($output_array[0])) {
                $result_data_turkish['ywkph15b'] = trim($output_array[0]);
            }
        }

        // charging time
        $charging_time_battery = $htmlDom2->query('//strong[@class="ozellik1160"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;

        if($charging_time_battery) {
            if(stripos($charging_time_battery, 'akika') !== false) {
                preg_match('/\d+/ui', $charging_time_battery, $out_arr);
                if(isset($out_arr[0]) && !empty($out_arr[0])) {
                    $result_data_turkish['le00i0c'] = number_format( trim($out_arr[0] / 60), 2 );
                }
            } else {
                preg_match('/\d+/ui', $charging_time_battery, $out_arr);
                if(isset($out_arr[0]) && !empty($out_arr[0])) {
                    $result_data_turkish['le00i0c'] = trim($out_arr[0]);
                }
            }
        } // todo exadd +

        // length(height)
        $size = $htmlDom2->query('//strong[@class="ozellik1101"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($size) {$result_data_turkish['qorav98'] = str_replace('mm','',trim($size));}

        // width
        $width = $htmlDom2->query('//strong[@class="ozellik1102"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($width) { $result_data_turkish['65ihv16'] = trim(str_replace('mm','', $width)); }

        // length alternative
        $length_alternative = $htmlDom2->query('//strong[@class="ozellik1300"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($length_alternative) $result_data_turkish['qorav98'] = str_replace('mm','', trim($length_alternative)); // todo exadd +

        // width alternative
        $width_alternative = $htmlDom2->query('//strong[@class="ozellik1301"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($width_alternative) $result_data_turkish['65ihv16'] = str_replace('mm','',trim($width_alternative));

        // thick alternative
        $thick_alternative = $htmlDom2->query('//strong[@class="ozellik1302"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($thick_alternative) $result_data_turkish['vbryix7'] = str_replace('mm','',trim($thick_alternative));

        // thickness
        $thickness = $htmlDom2->query('//strong[@class="ozellik1103"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($thickness) { $result_data_turkish['vbryix7'] = str_replace('mm','',trim($thickness)); }

        // weight
        $weight = $htmlDom2->query('//strong[@class="ozellik1104"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($weight) { $result_data_turkish['uanzwi8'] = trim(str_replace(['gr'],'', $weight)); }

        // body weight
        $body_weight = $htmlDom2->query('//strong[@class="ozellik1179"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($body_weight) { $result_data_turkish['ywkph16b'] = trim(str_replace(['gr'], '', $body_weight));}

        // cord weight
        $cord_weight = $htmlDom2->query('//strong[@class="ozellik1180"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($cord_weight) { $result_data_turkish['ywkph17b'] = trim(str_replace(['gr'], '', $cord_weight)); }

        // screen shaped
        $screen_shaped = $htmlDom2->query('//strong[@class="ozellik1120"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($screen_shaped) {
            $arr_screen_shaped = [ // todo exadd + all
                'Dikdörtgen' => 'Rectangle',
                'Kare' => 'Frame',
                'Daire' => 'Circle'
            ];
            $result_data_turkish['ywkph18b'] = trim(str_replace(array_keys($arr_screen_shaped),array_values($arr_screen_shaped), $screen_shaped));
        }

        // body color
        $body_colors = $htmlDom2->query('//strong[@class="ozellik1100"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($body_colors) { // todo exadd +
            $body_colors_watch = getEngNameFromTurkish(trim($body_colors));
            $body_colors_watch = preg_replace('/\s/ui', ".", $body_colors_watch);
            $result_data_turkish['ywkph19b'] = explode('...',$body_colors_watch);
        }

        // body material
        $body_material = $htmlDom2->query('//strong[@class="ozellik1113"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($body_material) {
            $body_material_info = trim(str_replace(['Fiber Takviyeli Polimer'],['Fiber Reinforced Polymer'], $body_material));
            $result_data_turkish['rt0qxrl'] = getEngNameFromTurkish($body_material_info);
        }

        // cord colors
        $cord_colors = $htmlDom2->query('//strong[@class="ozellik1123"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cord_colors) {
            // todo exadd +
            $color_info_watch = getEngNameFromTurkish(trim($cord_colors));
            $color_info_watch = preg_replace('/\s/ui', ".", $color_info_watch);
            $result_data_turkish['ywkph20b'] = explode('...',$color_info_watch);
        }

        // wifi exist
//        $wifi_exist = $htmlDom2->query('//strong[@class="ozellik1129"]/following::span[1]')->item(0)->nodeValue ?? null;
//        if($wifi_exist) {$result_data_turkish['2pinrcs'] = getAnswerTurkish($wifi_exist);} // todo exadd +

        // cord material
        $cord_material = $htmlDom2->query('//strong[@class="ozellik1121"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($cord_material) {$result_data_turkish['3bjbzrk'] = getEngNameFromTurkish(trim($cord_material));} // translate

        // operating system of device
//        $operating_sys = $htmlDom2->query('//strong[@class="ozellik1143"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
//        if($operating_sys) {
//            $result_data_turkish['ui65qcn'] = trim($operating_sys); // todo exadd + delete
//        }

        // operating system version
        $os_version = $htmlDom2->query('//strong[@class="ozellik1144"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($os_version) { $result_data_turkish['ui65qcn'] = trim($os_version); } // todo exadd +

        // replace cord
        $replace_cord = $htmlDom2->query('//strong[@class="ozellik1122"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($replace_cord) {$result_data_turkish['cc1cqt0'] = getAnswerTurkish($replace_cord);}

        // vibration
        $vibration = $htmlDom2->query('//strong[@class="ozellik1146"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($vibration) {$result_data_turkish['u8sj5wc'] = getAnswerTurkish($vibration);}

        // microfone
        $microfone =  $htmlDom2->query('//strong[@class="ozellik1147"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($microfone) {$result_data_turkish['yq2jcrll'] = getAnswerTurkish($microfone);}

        // microphone features
        $microphone_features = $htmlDom2->query('//strong[@class="ozellik1186"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($microphone_features) { $result_data_turkish['yq2jcrlw'] = trim(str_replace('Gürültü önleyici ikinci mikrofon', 'Second microphone for noise-cancelling', $microphone_features));}

        // speaker
        $speaker = $htmlDom2->query('//strong[@class="ozellik1148"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($speaker) {$result_data_turkish['8l2ljo2'] = getAnswerTurkish($speaker);}

        // infrared
        $infrared = $htmlDom2->query('//strong[@class="ozellik1191"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($infrared) {$result_data_turkish['hwst1n7'] = getAnswerTurkish($infrared);}

        // speaker features
        $speaker_features = $htmlDom2->query('//strong[@class="ozellik1188"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($speaker_features) {$result_data_turkish['yq2jcrlw'] = trim(str_replace(['Tümleşik Ahize'], ['Integrated Handsfree'], $speaker_features));}

        // dust resistance
        $dust_resistance = $htmlDom2->query('//strong[@class="ozellik1150"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($dust_resistance) { $result_data_turkish['cxeplx2'] = getAnswerTurkish($dust_resistance);}

        // dust resistance properties
        $dust_resistance_prop = $htmlDom2->query('//strong[@class="ozellik1152"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($dust_resistance_prop) {$result_data_turkish['cxeplx3'] = trim($dust_resistance_prop);}

        // water resistance
        $water_resistance = $htmlDom2->query('//strong[@class="ozellik1151"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($water_resistance) { $result_data_turkish['cxeplx1'] = getAnswerTurkish($water_resistance);}

        // water resistance prop
        $water_resistance_prop = $htmlDom2->query('//strong[@class="ozellik1153"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($water_resistance_prop) {$result_data_turkish['cxeplx4'] = getEngNameFromTurkish(trim($water_resistance_prop));}

        // services and apps
        $services_apps = $htmlDom2->query('//strong[@class="ozellik1149"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($services_apps) {
            $result_data_turkish['iw93r5f8'] = isValueExists($services_apps, 'larmlar'); // alarm
            $result_data_turkish['19xfliw'] = isValueExists($services_apps, 'atırlatıcılar'); // reminder
            $result_data_turkish['yq2jcrlz'] = isValueExists($services_apps, 'ulaklık'); // headphone
            $result_data_turkish['2pinrcz'] = isValueExists($services_apps, 'hizesi'); // handset
            $result_data_turkish['rd3kh2w'] = isValueExists($services_apps, 'elefonumu Bul'); // find my phone
            $result_data_turkish['pjthco3'] = isValueExists($services_apps, 'yku Mönitör'); // sleep monitor
            $result_data_turkish['vubpb9d'] = isValueExists($services_apps, 'Aktivite Tanımlama');
            $result_data_turkish['58ue5nd'] = isValueExists($services_apps, 'Arayan İsmi Gösterimi');
            $result_data_turkish['h3dd5mz'] = isValueExists($services_apps, 'Ayın Evreleri');
            $result_data_turkish['19xfli1'] = isValueExists($services_apps, 'Ok Google');
            $result_data_turkish['jbsxi9o'] = isValueExists($services_apps, 'Uygulama Bildirimlerini Görüntüleme'); // apps notify
            $result_data_turkish['7ue2z84'] = isValueExists($services_apps, 'Yüzme'); // swim
            $result_data_turkish['zrxr18u1'] = isValueExists($services_apps, 'Atlayış'); // jump
            $result_data_turkish['xpff407'] = isValueExists($services_apps, 'Bisiklet'); // bicycle
            $result_data_turkish['2wb37pk'] = isValueExists($services_apps, 'Golf'); // golf
            $result_data_turkish['x0xgsbl'] = isValueExists($services_apps, 'GPS Saat Senkronizasyonu'); // gps synchro
            $result_data_turkish['zdsda7a'] = isValueExists($services_apps, 'Hava Durumu'); // display weather
            $result_data_turkish['bjlwf02'] = isValueExists($services_apps, 'Kalori Takibi'); // calories
            $result_data_turkish['581d8u2'] = isValueExists($services_apps, 'Kayak'); // calories
            $result_data_turkish['9haky35'] = isValueExists($services_apps, 'Koşu'); // run
            $result_data_turkish['gny9uz9'] = isValueExists($services_apps, 'Kronometre'); // stopwatch
            $result_data_turkish['zrxr18ut'] = isValueExists($services_apps, 'Kürek Çekme'); // rowing
            $result_data_turkish['f7lsmmw9'] = isValueExists($services_apps, 'Müzik Çalar'); // music player
            $result_data_turkish['myof5la'] = isValueExists($services_apps, 'Otomatik Uyku Algılama'); // auto sleep
            $result_data_turkish['2exqey7'] = isValueExists($services_apps, 'Saatimi/Bilekliğimi Bul'); // find my device
            $result_data_turkish['zrxr18u3'] = isValueExists($services_apps, 'Sanal Antreman Partneri'); // virt execis part
            $result_data_turkish['zrxr18u4'] = isValueExists($services_apps, 'Snowboarding'); // snowboard exadd +
            $result_data_turkish['h3dd5mg'] = isValueExists($services_apps, 'Snowboarding'); // calendar
            $result_data_turkish['sicux2c'] = isValueExists($services_apps, 'Snowboarding'); // climbing
            $result_data_turkish['19xfli2'] = isValueExists($services_apps, 'Dünya Saatleri');
            $result_data_turkish['rdxjplx'] = isValueExists($services_apps, 'Gelen Çağrı ve Bildirimleri'); // incoming call info
            $result_data_turkish['1rez7re'] = isValueExists($services_apps, 'Geri Sayın Sayacı'); // timer
            $result_data_turkish['2pinrc1'] = isValueExists($services_apps, 'Hands Free Görüşme'); // hands free
            $result_data_turkish['zdsda7a'] = isValueExists($services_apps, 'Hava Durumu');
            $result_data_turkish['vubpb9d'] = isValueExists($services_apps, 'Idle Alert'); // idle alert // todo exadd +
            $result_data_turkish['bjlwf02'] = isValueExists($services_apps, 'Kalori');
            $result_data_turkish['nh7sleo'] = isValueExists($services_apps, 'Kalp');
            $result_data_turkish['lggn0m2'] = isValueExists($services_apps, 'Kamera');
            $result_data_turkish['gny9uz9'] = isValueExists($services_apps, 'Kronometre');
            $result_data_turkish['x0xgsbz'] = isValueExists($services_apps, 'Navigasyon'); // navigator
            $result_data_turkish['19xfli4'] = isValueExists($services_apps, 'Passbook'); // passbook
            $result_data_turkish['c534jxf'] = isValueExists($services_apps, 'Ses ile komut verme');
            $result_data_turkish['2q53kqi'] = isValueExists($services_apps, 'SMS Görüntüleme ve Yanıtlama');
            $result_data_turkish['h3dd5mg'] = isValueExists($services_apps, 'Takvim');
            $result_data_turkish['bmpc3f3j'] = isValueExists($services_apps, 'Uygulama Yükleyebilme');
            $result_data_turkish['e61kgyj'] = isValueExists($services_apps, 'Uyku Mönitörü');

            if(isValueExists($services_apps, 'Medya Oynatıcı') == '+') {
                $result_data_turkish['qlsq9yh'] = '+';
                $result_data_turkish['a7j5c0b'] = '+';
                $result_data_turkish['ja7w3sb'] = '+';
            }
        }
        // pulsometer
        $pulsometer = $htmlDom2->query('//strong[@class="ozellik1139"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($pulsometer) {$result_data_turkish['nx6ywkn'] = getAnswerTurkish($pulsometer);}

        // ambient light sensor
        $ambient_light_sensor = $htmlDom2->query('//strong[@class="ozellik1140"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($ambient_light_sensor) {$result_data_turkish['h88pkmdy'] = getAnswerTurkish($ambient_light_sensor);}

        // compass
        $compass = $htmlDom2->query('//strong[@class="ozellik1142"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($compass) $result_data_turkish['x0xgsbn'] = getAnswerTurkish($compass);

        // barometer
        $barometer = $htmlDom2->query('//strong[@class="ozellik1175"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($barometer) $result_data_turkish['x399jxz'] = getAnswerTurkish($barometer);

        // uv sensor
        $uv_sensor = $htmlDom2->query('//strong[@class="ozellik1176"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($uv_sensor) $result_data_turkish['ywtcej1'] = getAnswerTurkish($uv_sensor);

        // thermometer
        $thermometer = $htmlDom2->query('//strong[@class="ozellik1190"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($thermometer) $result_data_turkish['k626aelh'] = getAnswerTurkish($thermometer);

        // proximity sensor
        $proxim_sensor = $htmlDom2->query('//strong[@class="ozellik1250"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($proxim_sensor) $result_data_turkish['h88pkmd1'] = getAnswerTurkish($proxim_sensor);

        // pedometer
        $pedometer = $htmlDom2->query('//strong[@class="ozellik1141"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($pedometer) {$result_data_turkish['guoawdo'] = getAnswerTurkish($pedometer);}

        // accelerometer
        $accelerometer = $htmlDom2->query('//strong[@class="ozellik1137"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($accelerometer) { $result_data_turkish['h1ddzrt'] = getAnswerTurkish($accelerometer); }

        // gyroscope
        $gyroscope = $htmlDom2->query('//strong[@class="ozellik1138"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($gyroscope) { $result_data_turkish['ywtcejg'] = getAnswerTurkish($gyroscope);}

        // compability os
        $compability_os = $htmlDom2->query('//strong[@class="ozellik1264"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($compability_os) { // todo exadd +
            $compability_syn = trim(str_replace(' ', '', $compability_os));
            $result_data_turkish['0v8w2sz'] = getOS($compability_syn, 'iOS'); // iOS
            $result_data_turkish['a5sj3l2'] = getOS($compability_syn, 'indow'); // windows
            $result_data_turkish['vxq3g1f'] = getOS($compability_syn, 'lackBerry'); // blackberry
            $result_data_turkish['llulwif'] = getOS($compability_syn, 'ndroid'); // android todo exadd +
//            $result_data_turkish['ui65qc1'] = ; // todo exadd + delete
        }

        // compability version
//        $compability_ver = $htmlDom2->query('//strong[@class="ozellik1265"]/following::span[1]')->item(0)->nodeValue ?? null;
//        if($compability_ver) {$result_data_turkish['ui65qc2'] = trim($compability_ver);} // todo exadd +

        // bluetooth
//        $bluetooth = $htmlDom2->query('//strong[@class="ozellik1127"]/following::span[1]')->item(0)->nodeValue ?? null;
//        if($bluetooth) {  // todo exadd + delete
//            $result_data_turkish['p4zld6l'] = getAnswerTurkish($bluetooth); }

        // bluetooth version
        $bluetooth_version = $htmlDom2->query('//strong[@class="ozellik1114"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($bluetooth_version) {    // todo exadd +
            $result_data_turkish['p4zld5l'] = trim($bluetooth_version);
        } else {
            $result_data_turkish['p4zld5l'] = '-';
        }

        // wifi
        $wifi = $htmlDom2->query('//strong[@class="ozellik1130"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($wifi) $result_data_turkish['2pinrcv'] = str_replace(['Yazılım güncellemesi gerektirebilir'],['Software update may require'], trim($wifi));

        // nfc
        $nfc = $htmlDom2->query('//strong[@class="ozellik1134"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($nfc) $result_data_turkish['9ee4viy'] = getAnswerTurkish($nfc);

        // usb
        $usb = $htmlDom2->query('//strong[@class="ozellik1131"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($usb) {$result_data_turkish['2q8o92fk'] = getAnswerTurkish($usb);}

        // usb type
        $usb_type = $htmlDom2->query('//strong[@class="ozellik1132"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($usb_type) { $result_data_turkish['p85t8s8z'] = isValueExists($usb_type, 'Micro-USB');} // micro-usb

        // release date
        $release_date = $htmlDom2->query('//strong[@class="ozellik1154"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($release_date) { $result_data_turkish['2lbcv9f'] = trim($release_date); }

        // series
        $series = $htmlDom2->query('//strong[@class="ozellik2137"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($series) { $result_data_turkish['34fksng'] = trim($series);}

        // chipset
        $chipset = $htmlDom2->query('//strong[@class="ozellik1136"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($chipset) {$result_data_turkish['dkg7n4e'] = trim($chipset);}

        // cpu info
        $cpu = $htmlDom2->query('//strong[@class="ozellik1110"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu) { // todo exadd + all
            preg_match('/GHz\s+(.+)/mui', $cpu, $out_cpu_wear);
            if(isset($out_cpu_wear[1]) && !empty($out_cpu_wear[1])) {
                $result_data_turkish['y5xo6x4'] = str_replace('ARM','', trim($out_cpu_wear[1]));
            }
        }

        // cpu core
        $cpu_core = $htmlDom2->query('//strong[@class="ozellik1112"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_core) $result_data_turkish['y5xo6x5'] = trim($cpu_core);

        // ram size
        $ram_size = $htmlDom2->query('//strong[@class="ozellik1106"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($ram_size) $result_data_turkish['ej4wq1y'] = trim($ram_size);

        // internal storage
        $flash = $htmlDom2->query('//strong[@class="ozellik1105"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($flash) $result_data_turkish['c8xo6x6'] = str_replace(' ',' ',trim($flash)); // todo exadd +

        // GPS
        $gps = $htmlDom2->query('//strong[@class="ozellik1167"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($gps) $result_data_turkish['yfvshn2'] = getAnswerTurkish($gps);

        // sim support
        $sim = $htmlDom2->query('//strong[@class="ozellik1169"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($sim) $result_data_turkish['mdmfh57'] = getAnswerTurkish($sim); // todo exadd +

        // ger user rating
        $user_rating = $htmlDom2->query('//span[@class="kpuan"]')->item(0)->nodeValue ?? null; // user ratings
        if($user_rating) { // todo exadd +
            preg_match_all('/\d+\.?\d+\s+/', $user_rating, $output_rating);
            if(isset($output_rating[0][0]) && !empty($output_rating[0][0])) { $rank = trim(($output_rating[0][0])) ;}
            if(isset($output_rating[0][1]) && !empty($output_rating[0][1])) {  $opinions = ($output_rating[0][1]);}
            if(isset($rank) && isset($opinions)) $result_data_turkish['bkaqn4m'] = [$rank => $opinions]; // todo exadd +
        }

        // get type
        $result_data_turkish['drbmx1r'] = 2;

    }

    // todo exadd +
	foreach($result_data_turkish  as $index => $value) {
		if(is_string($value) && empty($value)) unset($result_data_turkish[$index]);
	}

	$result_summary_info_turkish[$url] = array_map(function ($item) { // todo exadd +
		return (is_string($item)) ? (trim(strip_tags($item))) : $item;
	}, $result_data_turkish);

    // todo exadd +
    $newObj = WebPage::find()->filterWhere(['path_hash' => $path_hash])->one();
    if($newObj) {
        $newObj->path_hash = $path_hash;
        $newObj->source = 'epey';
        $newObj->url = $url;
        $newObj->nid = WebPage::$version;	// ... version <WebPage::version> // todo exadd +
        $newObj->desc = Json::encode($result_summary_info_turkish[$url]);
        $newObj->save();
    }
}
?>
<?
//pretty_print($result_summary_info_turkish);
echo(Render::render($result_summary_info_turkish, $code));
//Render::pretty_print($result_summary_info_turkish);


// todo exadd +
//foreach ($result_summary_info_turkish as $row) {
//    if (isset($row['awkph141'])) {
//        foreach ($row['awkph141'] as $service) {
//            if ( ! isset($services[$service])) {
//                $services[$service] = 0;
//            };
//            $services[$service]++;
//        }
//    }
//};
//arsort($services);
//Render::pretty_print($services);
echo '<br>';
Render::pretty_print($result_summary_info_turkish);

///// functions for epey //////
// todo exadd +
function getImage($path)
{
    $imginfo = @getimagesize($path);
    if($imginfo) {
        return $path;
    } else {
        return null;
    }
}
function isModelExists($summary_arr, $model_name) // todo exadd +
{
    foreach($summary_arr as $k => $value) {
        if(isset($value['model_name']) && !empty($value['model_name']) && $value['model_name'] == $model_name) {
			return true;
        }
    }
    return false;
}
function getFeatures($data) // todo exadd + all
{
    $arrCombine = [
    'Çoklu Pencere (Dual/Multi Window)' => 'Dual/Multi Window',
    'Arka Kapak' => 'tailgate',
    'Değiştirilebilir Temalar' => 'Changeable Themes',
    'Gürültü Önleyici İkinci Mikrofon' => 'Second Microphone For Noise-Cancelling',
    'İris Tanımlama' => 'Identification Of Iris',
    'Kısayol Tuşu' => 'Shortcut Keys',
    'Sanal Ekran Tuşları' => 'Virtual Keypad',
    'Sanal Gerçeklik (VR) Uyumu' => 'Virtual reality (VR) adaptation',
    'Yüz Tanımlama' => 'Face Identification',
    'Canlı Yayın (Live Broadcast)' => 'Live Broadcast',
    'Uyumu' => 'Compliance',
    'Gizli Mod' => 'Hidden Mode',
    'Kolay Arayüz (Easy Mode)' => 'Easy mode',
    'Mikrofon' => 'Microphone',
    'Aydınlatmalı Kapasitif Tuşlar' => 'Illuminated Capacitive Keys',
    'Ekrana Çift Dokunarak Açma (KnockON)' => 'Double-Tap The Screen To Open (KnockON)',
    'Gürültü Önleyici Dördüncü' => 'Noise Reduction Fourth',
    'Sertifikası' => 'Certificate',
    'Sesle Ekran Kilidi Açma' => 'Turn On Voice Screen Lock',
    'Sesle Komut (Yanıt/Red)' => 'Voice Command',
    'Yüksek Kalitede' => 'High Quality',
    'Ses Kaydı' => 'Audio Recording',
    'Yüksek Kalite' => 'High Quality',
    'Ses' => 'Sound',
    'Kulaklık Ses Çıkışı' => 'Audio Output',
    'Tek Elde Kullanım Modu' => 'Single-Hand Use Mode',
    'ile Başka cihazları Şarj Edebilme' => 'charging other devices',
    'MaxxAudio Ses Geliştirme' => 'Audio Enhancement',
    'Ses Geliştirme' => 'Sound Development',
    'Ekran' => 'screen',
    'Çocuk Modu' => 'Child Mode',
    'El Haraketi (Gesture) Algılama' => 'Hand Movement (Gesture) Detection',
    'Geliştirme' => 'Development ',
    'Araması' => 'Call',
    'Entegrasyonu' => 'Integration',
    'Kulaklık' => 'Headphone',
    'Çıkışı' => 'Output',
    'Oynatma' => 'Playback',
    'Çipi' => 'Chip',
    'Aydınlatmasız Kapasitif Tuşlar' => 'Illuminated Capacitive Keys',
    'Sertiifikası' => 'Certification', // todo exadd +
    'Gürültü Önleyici'  => 'Noise-Canceling', // todo exadd +
    'Üçüncü' => ' Third',
    'Yıl Güncelleme Garantisi' => 'Year Warranty Upgrade',
    'Tuşu' => 'Keys',
    'Arttırılmış Gerçeklik' => '',   // todo exadd +
    'Genişletilebilir Kenar screen Özellikleri' => 'Expandable sidebar properties',   // todo exadd +
];
    return $result = str_ireplace(array_keys($arrCombine), array_values($arrCombine), $data);
}
function getEngNameMaterial($data) // todo exadd + all
{
    $arrCombine = [
    'Cam' => 'Glass',
    'Yekpare' => '',
    'Polikarbonat' => 'Polycarbonate',
    'Alüminyum' => 'Aluminum',
    'Plastik' => 'Plastic',
    'Metalik' => 'Metallic color',
    'Görünümlü' => '',
    'Deri' => 'Leather',
    'Kauçuk' => 'Rubber',
    'Seramik' => 'Ceramic',
    'Paslanmaz' => 'Stainless',
    'Çelik' => 'Steel',
    'Aluminum-Magnezyum Alaşımlı Metal' => 'Aluminum-Magnesium Alloy Metal',
    'Değiştirilebilir' => 'Replaceable',
    ' ya da ' => ' or ',
    'PoliKarbonat' => 'Polycarbonate',
    'Suni' => 'Artificial',
    'Silikon' => 'Silicone',
    'Titanyum' => 'Titanium',
];
    return $result = str_ireplace(array_keys($arrCombine), array_values($arrCombine), $data);
}
function getEngNameFromTurkish($data) // todo exadd + al
{
	$arrCombine = [
		' ve '                            => ' and ',
		'Altın'                           => 'Gold',
		'Alüminyum'                       => 'Aluminum',
		'Kasa'                            => 'Safe',
		'Kum'                             => 'Sand',
		'Pembesi'                         => 'Pink',
		' Spor '                          => ' Sport ',
		'Kordon'                          => 'cord',
		'Uzay'                            => 'Space',
		'Grisi'                           => 'Grey',
		'Gri'                             => 'Gray',
		'Siyah'                           => 'Black',
		'Gümüş'                           => 'Silver',
		' Saf '                           => ' Pure ',
		'Platin'                          => 'Platinum',
		'Antrasit'                        => 'Anthracite',
		'Puslu'                           => 'Misty',
		'Gece'                            => 'Night',
		'Mavisi'                          => 'Blue',
		'Beyaz'                           => 'White',
		'Paslanmaz'                       => 'Stainless',
		'Çelik'                           => 'Steel',
		'Milano'                          => 'Milan',
		'Mat'                             => 'Matte',
		'Roze'                            => 'Rose',
		'Taş'                             => 'Stone',
		'Klasik'                          => 'Classic',
		'Tokalı'                          => 'Buckle',
		'Kayış'                           => 'Slip',
		'Akıllı'                          => 'Smart',
		'Bileklik'                        => 'Wrist',
		'Bilezik'                         => 'Bracelet',
		'Baklalı'                         => 'Broad beans',
		'Orta'                            => 'Middle',
		'Büyük'                           => 'Large',
		'Soğuk'                           => 'Cold',
		'Beton'                           => 'Concrete',
		'Naylon'                          => 'Nylon',
		'Örme'                            => 'Knitting',
		'İnci'                            => 'Pearl',
		'Açık'                            => 'Open',
		'Okyanus'                         => 'Ocean',
		'Deri'                            => 'Leather',
		'Kraliyet'                        => 'Royal',
		'Tropik'                          => 'Tropical',
		'Kırmızı'                         => 'Red',
		'Turuncusu'                       => 'Orange',
		'Pembe'                           => 'Pink',
		'Kahverengi'                      => 'Brown',
		'Ayar'                            => 'Setting',
		'Parlak'                          => 'Bright',
		'Mavi'                            => 'Blue',
		'Kutup'                           => 'Pole',
		'Seramik'                         => 'Ceramic',
		'Polikarbonat'                    => 'Polycarbonate',
		'Eloksal '                        => 'Anodizing',
		'Termoplastik'                    => 'Thermoplastic',
		'Poliüretan'                      => 'Polyurethane',
		'Titanyum'                        => 'Titanium',
		'Hipoalerjenik'                   => 'Hypoallergenic',
		'Kauçuk'                          => 'Rubber',
		'Silikon'                         => 'Silicone',
		'Manyetik'                        => 'Magnetic',
		'Kilit'                           => 'Lock',
		'Klips'                           => 'Clipping',
		'Örgü'                            => 'Mesh',
		'Vulkanize'                       => 'Vulcanized',
		'Tam'                             => 'Full',
		'Daire'                           => '',
		'Çift'                            => 'Double',
		'Katmanlı'                        => 'Layer',
		'Kavisli'                         => 'Curved',
		'Renkli'                          => 'Color',
		'Değiştirilebilir Para pil'       => 'Replaceable battery',
		'Adet'                            => 'Piece',
		'Bordo'                           => 'Maroon',
		'Lacivert'                        => 'Blue',
		'Mor'                             => 'Purple',
		'Fuşya'                           => 'Fushya',
		'Turkuaz'                         => 'Turquoise',
		'Yeşil'                           => 'Green',
		'Bakır'                           => 'Copper',
		'Krem'                            => 'Cream',
		"mt'ye kadar su geçirmez"         => "mt waterproof",
		'Yalnızca su sıçramalarına karşı' => 'Only against water splashes',
		'Uçuk'                            => 'Herpes',
		'Rengi'                           => 'Color',
		'Kahve'                           => 'Coffee',
		'Plastik'                         => 'Plastic',
		'Değiştirilebilir Para Pil'       => 'Replaceable Battery',
		'Üretan'                          => 'Urethane',
		' ı '                             => '',
		'Sarı'                            => 'Yellow',
		'Krımızı'                         => 'Red',
		'Vestel Akıllı Bileklik'          => 'Vestel Smart Wristband',
		'Değişebilir Kapak'               => 'Cover May Vary',
		'Fenerbahç'                       => 'Fenerbahce',
		'Yanık'                           => '',
		'Fırtına'                         => 'Storm',
		'Turuncu'                         => 'Orange',
		'Hat'                         	  => 'line', // todo exadd +
		'Tek'                         	  => 'Single', // todo exadd +
	];
    return $result = str_ireplace(array_keys($arrCombine), array_values($arrCombine), $data);
}
function getLinksFromEpey($categoryId)
{
	$url = 'https://www.epey.com/kat/listele/';
	$result_links = [];

	/*
		for($i = 1; $i <= 45; $i++ ) { // page // todo uncomment '45'
		$html_turkish = getDataFromApiWithCategory($url, ['sayfa' => $i], $categoryId);

		$htmlDom2 = dom($html_turkish);

		for($k = 0; $k <= 60; $k++) { // item per page todo uncomment '60'
			$links = $htmlDom2->query('//div[@class="detay cell"]/a/@href')->item($k)->nodeValue ?? null;
			if($links) $result_links[] = $links;
		}
	}*/

	$i = 1;
	do {
		$html_turkish = getDataFromApiWithCategory($url, ['sayfa' => $i], $categoryId);
		$htmlDom2 = dom($html_turkish);

		$k = 0;
		do {
			$links = $htmlDom2->query('//div[@class="detay cell"]/a/@href')->item($k)->nodeValue ?? null;
			if($links) $result_links[] = $links;
			$k++;
		} while($links);

		$i++;

	} while(strlen($html_turkish) >= 8000);

	return $result_links;
}
function getAnswerTurkish($str)
{
    return  (strpos(trim($str), 'ar') !== false) ? '+' : '-';
}
/////////////////////
// helper functions

function getOS($haystack, $string)
{
    if(mb_strpos($haystack, $string) !== false) { // todo exadd +
        return '+';
    } else {
        return '-';
    }
}
function isValueExists($item, $name)
{
    if(mb_strpos($item, $name) !== false) { // todo exadd +
        return '+';
    } else {
        return '-';
    }
}

function isCodecExists($item, $name) // todo exadd +
{
    if(mb_stripos($item, $name) !== false) {
        return '+';
    } else {
        return ''; // todo exadd +
    }
}
function getAnswer($str)
{
    return  (strpos(trim($str), 'es') !== false) ? '+' : '-';
}
function getDataFromApiWithCategory($endpoint, $params = [], $category_id = false)
{
    $url = makeUrl($endpoint, $params);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if($category_id) {
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, "kategori_id=$category_id");
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
/**
 * @param $url
 * @param array $urlParams
 * @param array $ignoreParams
 * @return string
 */
function makeUrl($url, $urlParams = [], $ignoreParams = []): string
{
    foreach ($ignoreParams as $key) {
        unset($urlParams[$key]);
    };
    if ( ! empty($urlParams)) {
        $url .= "?" . http_build_query($urlParams);
    }
    return $url;
}
/**
 * @param $content
 * @return DOMXPath
 */
function dom($content): DOMXPath
{
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    return new DOMXpath($doc);
}
function pretty_print($str)
{
    echo '<pre>';
    print_r($str);
    echo '</pre>';
}