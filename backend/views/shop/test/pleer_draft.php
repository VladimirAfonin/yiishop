<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', '256M');

use backend\entities\Render;
//use backend\views\shop\ftp_parser\helpers\Render;
use backend\views\shop\test\H;
//use app\helpers\H;
use backend\views\shop\test\Sheet;
use yii\helpers\Json;
//use app\helpers\H;
use backend\entities\WebPage;
//use app\helpers\WebPage;

global $dom;

class Pleer
{
	public static $version = 1;
	public static $proxyUrl = 'https://free-proxy-list.net/';
	public static $clean = ['script', 'meta', 'noindex'];
	public static $cleaners = [
		'head'  =>"/\<head([\s\S]+?)\<\/head\>/",
		'script'=>"/\<script([\s\S]+?)\<\/script\>/",
		'style' =>"/\<style([\s\S]+?)\<\/style\>/",
		'noindex' =>"/\<noindex([\s\S]+?)\<\/noindex\>/",
		'meta' =>"/\<meta([\s\S]+?)\>/",
		'symbol'=>'/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u',
	];

	/**
	 * @param $content
	 * @return DOMXPath
	 */
	public static function dom($content): DOMXPath
	{
		$doc = new DOMDocument();
		$doc->loadHTML($content);
		return new DOMXpath($doc);
	}

	public static function query($pattern, $count = 0)
	{
		global $dom;
		return $dom->query($pattern)->item($count)->nodeValue ?? null;
	}

	public static function getValue($value)
	{
		return iconv("UTF-8","ISO-8859-1//IGNORE",  ($value));
	}

	public static function getAnswer($str)
	{
		$str = self::getValue($str);
		return (mb_stripos(trim($str), 'да') !== false) ? '+' : '-';
	}

	public static function isValueExists($haystack, $str, $needIconV = true)
	{
		if($needIconV) {
			$haystack = self::getValue($haystack);
		}
		return (mb_stripos($haystack, $str) !== false) ? '+' : '-';
	}

	public static function getTypeImageOutput($str)
	{
		$str = self::getValue($str);
		$str = str_replace(['экран смартфона', 'собственный экран'], ['smartphone screen','own screen'], $str);
		return $str;
	}

	public static function getProxyList($proxyUrl)
	{
		$row = [];
		$html = WebPage::getDataFromApi($proxyUrl);

		$dom = Pleer::dom($html);

		for($i = 1; $i <= 300; $i++) {
			$elite_proxy = $dom->query('//table[@id="proxylisttable"]/tbody/tr[' . $i . ']/td')->item(4)->nodeValue ?? null;
			if($elite_proxy == 'elite proxy') {
//			if($elite_proxy == 'anonymous') {
				$row[$i]['address'] = $dom->query('//table[@id="proxylisttable"]/tbody/tr[' . $i . ']/td')->item(0)->nodeValue ?? null;
				$row[$i]['ip'] = $dom->query('//table[@id="proxylisttable"]/tbody/tr[' . $i . ']/td')->item(1)->nodeValue ?? null;
			}
		}
		$row = array_values($row);

		$file = __DIR__ . '/../../../entities/proxy.txt';

		if(isset($row)) {
			$fp = fopen($file, 'w');
			foreach(array_values($row) as $index => $item) {
				fwrite($fp, trim($item['address']) . ':' . trim($item['ip']) . (($index == count($row) - 1) ? '' : PHP_EOL));
			}
			fclose($fp);
			return $row;
		}
		return null;
	}

}

$urls = [
//	'https://on.pleer.ru/product_312321_Xiaomi_Mi_Band_2.html',
//	'http://www.pleer.ru/_312321_Xiaomi_Mi_Band_2.html',
//    'https://www.pleer.ru/product_351608_iWOWN_i6_Pro.html',
//    'https://www.pleer.ru/product_417713_OnePlus_5_64Gb.html',
    'https://www.bestbuy.com/site/fitbit-charge-2-activity-tracker-heart-rate-large-black-silver/5579211.p',
];


$items = [];
foreach($urls as $k => $url) {
	/*
	[price] стоимость товара
	[discounted] цена после скидки
	[rating] ]рейтинг
	[reviews] количество отзывов
	[image] ссылка на изображение
	*/

	$data = [];
	$html = WebPage::get($url);

	var_dump($html);exit('ext');

	foreach (Pleer::$clean as $cleaner){
		$html = preg_replace(Pleer::$cleaners[$cleaner], '', $html);
		$html = str_replace(['display: none;', 'display:none;'],'', $html);
	}

    libxml_use_internal_errors(true);
	$dom = Pleer::dom($html);

	// get price
	$price = $dom->query('//table[@class="product_price"]/tr/td[@class="id3 "]/span[@class="inlineb"]')->item(0)->nodeValue; // ?? null;

    // get

	$data['price'] = $price;
	$items[$url] = $data;
}

H::print_r($items);
var_dump(count($items));
exit('[out]');

echo '<br><br>';
//$code = Sheet::rf('@backend/views/shop/test/specs.csv', ['indexFrom' => 'code']);
$code = Sheet::rf('@backend/views/shop/test/last.csv', ['indexFrom' => 'code']);
echo Render::render($items, $code,['category','group_ru','title_ru', 'code', 'units_en']);


exit('[out]');
