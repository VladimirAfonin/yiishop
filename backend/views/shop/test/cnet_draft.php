<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', '256M');

use backend\entities\WebPage;
use backend\views\shop\test\H;
use yii\db\Expression;
use yii\helpers\Json;
//use app\helpers\H;

global $dom;

class Cnet
{
	public static $version = 1;
	public static $main_url = 'https://www.cnet.com/search/';
	public static $cnetUrl = 'https://cnet.com';

	const PHONE = 'phone'; // todo exadd +
	const VR = 'vr';

	public static $itemUrl = [  // todo exadd +
		self::PHONE => 'https://www.cnet.com/topics/phones/products/',
		self::VR    => 'https://www.cnet.com/topics/wearable-tech/products/?filter=12-product-type_vr-ar-headset',
	];
	public static $phoneUrl = 'https://www.cnet.com/topics/phones/products/';


	public static $errorMsg = "Unfortunately";

	public static $cleaners = [
		'head'   => "/\<head([\s\S]+?)\<\/head\>/",
		'script' => "/\<script([\s\S]+?)\<\/script\>/",
		'style'  => "/\<style([\s\S]+?)\<\/style\>/",
	];

	public static function getDataFromApi($endpoint, $params = [], $redirect = false)
	{

		$url = self::makeUrl($endpoint, $params);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $redirect);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
	public static function makeUrl($url, $urlParams = [], $ignoreParams = []): string
	{
		foreach($ignoreParams as $key) {
			unset($urlParams[$key]);
		};
		if(!empty($urlParams)) {
			$url .= "?" . http_build_query($urlParams);
		}
		return $url;
	}

	/**
	 * @param $content
	 * @return DOMXPath
	 */
	public static function dom($content): DOMXPath
	{
		$doc = new DOMDocument();
		@$doc->loadHTML($content);
		return new DOMXpath($doc);
	}

	public static function query($pattern, $count = 0)
	{
		global $dom;
		return $dom->query($pattern)->item($count)->nodeValue ?? null;
	}
//
//	public static function getLinksFromJsonFile()
//	{
////		$path = Url::to("@-parsers/epey/files/map_{$category}.json");
//		$path = dirname(__FILE__) . "/../tag/map_cnet.json";
//		if(isset($path)) return Json::decode(file_get_contents($path));
//	}

	/**
	 * load links with phones
	 * @param string $cat
	 * @return array
	 */
	public static function getLinks($cat = 'phone') // todo exadd +
	{
		$flag = true;
		$res_arr = [];

		$i = 1;
		do {
			if($i == 1) {
				$item_html = Cnet::getDataFromApi(self::$itemUrl[$cat]); // todo exadd +
			}  else {
				if($cat == self::PHONE) { // todo exadd +
					$item_html = Cnet::getDataFromApi(self::$itemUrl[$cat] . $i . '/'); // todo exadd +
				} elseif($cat == self::VR) {
					$url = preg_replace('/\/()\?/mui', "/$i/?", self::$itemUrl[self::VR]);
					$item_html = Cnet::getDataFromApi($url); // todo exadd +
				}
			}


			$clean = ['script'];
			foreach($clean as $cleaner) {
				$item_html = preg_replace(Cnet::$cleaners[$cleaner], '', $item_html); // todo exadd +
			}


			$dom = Cnet::dom($item_html); // todo exadd +
			// checking for 'no-result'
			$check = $dom->query('//section[@id="dfllResults"]/div[@class="items"]/h2/text()')->item(0)->nodeValue ?? null;
			if(strpos($check, Cnet::$errorMsg) !== false) $flag = false;

			$a = 0;
			do {
				$elem = $dom->query("//section[contains(@class,'col-3 searchItem product')]")->item($a)->nodeValue ?? null;
				$item_title = $dom->query("//section[contains(@class,'col-3 searchItem product')]/div[@class='itemInfo']/a/h3")->item($a)->nodeValue ?? null; // todo exadd +
				if($item_title) $res_arr[$i][$a]['title'] = trim(preg_replace('#\s+#', ' ', $item_title)); // todo exadd +
				if($item_title) { // todo exadd +
					$res_arr[$i][$a]['url'] = Cnet::$cnetUrl . ($dom->query("//section[contains(@class,'col-3 searchItem product')]/div[@class='itemInfo']/a/@href")->item($a)->nodeValue ?? null);
				}
				$a++;
			} while($elem);

			$i++;
		} while($flag);

		foreach($res_arr as $k => $item) {
			foreach($item as $value) {
				$products[] = $value; // todo exadd +
			}
		}

		return $products; // todo exadd +
	}

	/**
	 * get links from file
	 * @param string $cat
	 * @return array|mixed
	 */
	public static function links($cat = 'phone') // todo exadd +
	{
		$path = dirname(__FILE__) . "/../tag/map_cnet_$cat.json";
		$period = 3600 * 24; // 1 hour
		if(file_exists($path)) {
			$cron_time = filemtime($path);
			if(time() - $cron_time >= $period) {
				$links = Cnet::getLinks($cat); // todo exadd +
				file_put_contents($path, Json::encode($links));
				return $links;
			} else {
				return $links = Json::decode(file_get_contents($path));
			}
		} else {
			$links = Cnet::getLinks($cat); // todo exadd +
			file_put_contents($path, Json::encode($links));
			return $links;
		}
	}
}


// get list with all phones
//$products = Cnet::links(Cnet::PHONE); // todo exadd +


//$i = 3;
//$url = 'https://www.cnet.com/topics/wearable-tech/products/?filter=12-product-type_vr-ar-headset';
//$t = preg_replace('/\/()\?/mui', "/$i/?", $url);

//var_dump($t);
//exit(); // todo
// https://www.cnet.com/topics/wearable-tech/products/?filter=12-product-type_vr-ar-headset

$products = Cnet::links(Cnet::VR);


/*$products = [
	[
		'title' => 'iphone x',
		'url'   => 'https://www.cnet.com/reviews/apple-iphone-x-review/'
	],
	[
		'title' => 'moto',
		'url'   => 'https://www.cnet.com/products/motorola-i1-sprint-nextel/review/',
	],
	[
		'title' => 'moto g6',
		'url'   => 'https://www.cnet.com/reviews/motorola-moto-g6-review/',
	],
];*/

//https://www.cnet.com/products/motorola-i1-sprint-nextel/review/

//H::print_r($products);
//exit('[exit]');

//$products = array_filter($products, function ($k) {return $k % 500 === 0;}, ARRAY_FILTER_USE_KEY);

//H::print_r($products);
//exit(); // todo

$res_info = [];

/* get list of product */
//$html = Cnet::getDataFromApi(Cnet::$main_url, ['query' => 'Samsung Galaxy S9']);
foreach($products as $k => $item) {

	$res_price = []; $res_rating = [];
	$url = $item['url'];

	// get data from cache // todo exadd +
	$html = WebPage::get($url);

	/* load html page */
//	$html = Cnet::getDataFromApi($url, [], true); // todo exadd +
	$clean = ['script'];
	foreach($clean as $cleaner) {
		$html = preg_replace(Cnet::$cleaners[$cleaner], '', $html);
	}
	$dom = Cnet::dom($html);

	// get rating
	$rating = Cnet::query("//div[contains(@class,'ratings')]");
	if($rating) {
		$res_rating['rating']['overall'] = Cnet::query("//div[contains(@class,'ratings')]/div[@class='col-1 overall']/div/span[@class='text']/text()"); // todo exadd -
		$z = 0;
		while($res = Cnet::query('//div[@id="editorSubRating"]/ul[@class="ratingsBars edBars"]/li[@class="ratingBarStyle"]', $z)) {
			$rating_value = preg_split('#\s+#mui', trim($res));
			$res_rating['rating'][$rating_value[0]] = $rating_value[1];
			$z++;
		}
		$res_rating['rating'] = array_map(function ($item) {
			return is_string($item) ? trim(preg_replace('#\s+#', ' ', ($item))) : $item;
		}, $res_rating['rating']);
	}

	H::print_r($res_rating);
	exit(); // todo

	// get prices
	$elem = Cnet::query('//div[@section="wtbSmall"]/h3[@class="wtbHed"]/a/@href');
	if($elem) {
		$url = Cnet::$cnetUrl . $elem;
		$html_price = WebPage::get($url);
//		$html_price = Cnet::getDataFromApi($url, [], true); // todo exadd +
		foreach($clean as $cleaner) {
			$html_price = preg_replace(Cnet::$cleaners[$cleaner], '', $html_price);
		}

		$dom = Cnet::dom($html_price);

		$a = 0;
		do {
			$elem = Cnet::query('//div[@class="resellerContent"]/div[@class="row"]', $a);
			$res_price['prices'][$a]['price'] = Cnet::query('//div[@class="resellerContent"]/div[@class="row"]/div[@class="col-1 totalPrice"]/a/text()', $a);
			$res_price['prices'][$a]['seller'] = Cnet::query('//div[@class="resellerContent"]/div[@class="row"]/div[@class="col-2 productStore"]/@title', $a);
			$res_price['prices'][$a]['link'] = Cnet::query('//div[@class="resellerContent"]/div[@class="row"]/div[@class="col-1 seeItBtn"]/a/@href', $a);
			$a++;
		} while($elem);
		array_pop($res_price['prices']);

		$res_info[$item['title']] = array_merge($res_rating, $res_price);
	}
}

// print summary array
if(isset($res_info)) H::print_r($res_info);