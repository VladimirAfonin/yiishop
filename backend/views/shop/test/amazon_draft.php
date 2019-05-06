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

class Amazon
{
	public static $version = 1;
	public static $proxyUrl = 'https://free-proxy-list.net/';

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
		return iconv("UTF-8", "ISO-8859-1//IGNORE", trim($value));
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

		$dom = Amazon::dom($html);

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


	public static function getBookInfo($isbn, $access_key, $secure_access_key)
	{
		// формируем список параметров запроса
		$fields = [];
		$fields['AWSAccessKeyId'] = $access_key;
		$fields['AssociateTag'] = 'amazon0d4c0-20';
		$fields['ItemId'] = $isbn;
		$fields['MerchantId'] = 'All';
		$fields['Operation'] = 'ItemLookup';
		$fields['ResponseGroup'] = 'Request,Large';
		$fields['Service'] = 'AWSECommerceService';
//		$fields['Version'] = '2011-08-01';
		$fields['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');

		// сортируем параметры согласно спецификации Amazon API
		ksort($fields);

		$query = array();
		foreach ($fields as $key=>$value) {
			$query[] = "$key=" . urlencode($value);
		}

		// подписываем запрос секретным ключом
		$string = "GET\nwebservices.amazon.com\n/onca/xml\n" . implode('&', $query);

		$signed = urlencode(base64_encode(hash_hmac('sha256', $string, $secure_access_key, true)));

		// формируем строку запроса к сервису
		$url = 'http://webservices.amazon.com/onca/xml?' . implode('&', $query) . '&Signature=' . $signed;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$data = curl_exec($ch);
		$info = curl_getinfo($ch);
//		if ($info['http_code'] != '200') return false;
		return $data;
	}
}

$urls = [
//	'https://www.amazon.com/Original-Xiaomi-Monitor-Wristband-Display/dp/B01GMQ4Y3O', // ??
//    'https://www.amazon.com/HTC-VIVE-Virtual-Reality-System-pc/dp/B00VF5NT4I',

//	'https://www.amazon.com/Fitbit-Charge-Fitness-Wristband-Version/dp/B01K9S24EM',
//	'https://www.amazon.com/dp/B072N57LCD',
//	'https://www.amazon.com/Amazfit-A1603-Activity-Tracker-Touchscreen/dp/B01MQ0ALDO',
//	'https://www.amazon.com/Apple-Watch-Space-Aluminum-Black/dp/B075TCR2NZ',
	'https://www.amazon.com/Huawei-Watch-Carbon-Android-Warranty/dp/B06XDMCH6Z',
//	'https://www.amazon.com/Huawei-Watch-Stainless-Steel-Warranty/dp/B013LKLS2E',
//	'https://www.amazon.com/Fitbit-Ionic-Smartwatch-Charcoal-Included/dp/B074VDF16R',
//	'https://www.amazon.com/Amazfit-Activity-Tracker-Charcoal-A1702/dp/B07BB4KGPZ',
//	'https://www.amazon.com/Samsung-SM-R770NZSAXAR-Gear-S3-Classic/dp/B01M1OXXT8',
//	'https://www.amazon.com/Apple-Watch-Smartwatch-Space-Aluminum/dp/B078YHNF62/',
//	'https://www.amazon.com/OnePlus-5T-A5010-Version-Midnight/dp/B077TFS54V',
//	'https://www.amazon.com/Samsung-Galaxy-S9-Unlocked-Smartphone/dp/B079JSZ1Z2',
//	'https://www.amazon.com/Razer-Phone-Display-Front-Facing-Speakers/dp/B077B91954',
//	'https://www.amazon.com/gp/product/B077T8FB3M',
//	'https://www.amazon.com/Fitness-Tracker-Audio-Coach-Moov/dp/B01N5AJPTG/',
//	'https://www.amazon.com/Oculus-Go-Standalone-Virtual-Reality-Headset/dp/B076CWS8C6',
//	'https://www.amazon.com/Samsung-Gear-VR-Discontinued-Manufacturer/dp/B016OFYGXQ',
//	'https://www.amazon.com/View-Master-Virtual-Reality-Starter-Pack/dp/B011EG5HJ2',
//	'https://www.amazon.com/HTC-VIVE-Virtual-Reality-System-pc/dp/B00VF5NT4I',
//	'https://www.amazon.com/Samsung-Gear-VR-Discontinued-Manufacturer/dp/B01HU3J9QA',
//	'https://www.amazon.com/Acer-AH101-D8EY-Windows-Reality-VD-R05AP-002/dp/B075PVLN2P',
//	'https://www.amazon.com/Oculus-Touch-Virtual-Reality-System-pc/dp/B073X8N1YW',
//	'https://www.amazon.com/PlayStation-VR-4/dp/B01DE9DY8S',
//	'https://www.amazon.com/dp/B01N634P7O/ref=asc_df_B01N634P7O5561955',
//	'https://www.amazon.com/Sony-HMZ-T3W-Mounted-Viewer-Model/dp/B00FNJGJN0',
//	'https://www.amazon.com/Epson-V11H423020-Moverio-See-Through-Wearable/dp/B007ORN0LS',
//	'https://www.amazon.com/Google-Glass-Explorer-Version-Charcoal/dp/3283005737',
//	'https://www.amazon.com/Royole-Moon-Virtual-Mobile-Theater/dp/B01M8FE2U4',
//	'https://www.amazon.es/Woxter-Neo-VR100-Silver-incorporada/dp/B06XBWFT35',
//	'https://www.amazon.com/Pico-Interactive-Goblin-VR-Headset-Android/dp/B073XNW4TK',
];

/*
$access_key = 'AKIAJMODN7SLEMTUMYOA';
$secret_access_key = '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';
$item_isbn = 'B01GMQ4Y3O';
$info = Amazon::getBookInfo($item_isbn, $access_key, $secret_access_key);
*/

Amazon::getProxyList(Amazon::$proxyUrl);

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
//	$html = WebPage::getDataFromApi($url);

//	var_dump($html);
//	exit('[:exit:]'); // todo

	$dom = Amazon::dom($html);

	exit();

	// get price
    $price = $dom->query('//div[@class="a-section a-spacing-small a-text-center"]/span/a')->item(0)->nodeValue ?? null;
    if(preg_match('/\$\d+\.?\d+/mui', $price, $out_price)) {
        $data['price'] = $out_price[0];
    }
    if(!isset($data['price'])) {
        $price = $dom->query('//div[@class="a-text-center a-spacing-mini"]')->item(0)->nodeValue ?? null;
        if(preg_match('/\$\d+\.?\d+/mui', $price, $out_price)) {
            $data['price'] = $out_price[0];
        }
    }
    if(!isset($data['price'])) {
        $price = $dom->query('//span[@id="priceblock_ourprice"]')->item(0)->nodeValue ?? null;
        if(preg_match('/\$\d+\.?\d+/mui', $price, $out_price)) {
            $data['price'] = $out_price[0];
        }
    }
    if(!isset($data['price'])) {
        $price = $dom->query('//li[@id="color_name_0"]')->item(0)->nodeValue ?? null;
        if(preg_match('/\$\d+\.?\d+/mui', $price, $out_price)) {
            $data['price'] = $out_price[0];
        }
    }

    // get rating
    $rating = $dom->query('//div[@id="averageCustomerReviews"]/span')->item(0)->nodeValue ?? null;
    $data['rating'] = trim($rating);
    if(!isset($data['rating']) || empty($data['rating'])) {
        $rating = $dom->query('//span[@class="a-text-beside-button averageStarRatingText"]')->item(0)->nodeValue ?? null;
        $data['rating'] = trim($rating);
    }

    // get reviews
    $reviews = $dom->query('//div[@id="averageCustomerReviews"]/span')->item(2)->nodeValue ?? null;
    $data['reviews'] = str_replace(' customer reviews', '', trim($reviews));
    if(!isset($data['reviews']) || empty($data['reviews'])) {
        $reviews = $dom->query('//h2[@class="customerReviewsTitle totalReviewCount"]/text()')->item(0)->nodeValue ?? null;
        $data['reviews'] = str_replace(' customer reviews', '', trim($reviews));
    }

    // get images
    $image = $dom->query("//@*[name()='data-old-hires']")->item(0)->nodeValue ?? null;
    $data['image'] = $image;
    if(!isset($data['image']) || empty($data['image'])) {
        $image = $dom->query("//img[@data-fling-refmarker='detail_main_image_block']/@src")->item(0)->nodeValue ?? null; // /div[@id='landing-image-wrapper']/img/@src
        $data['image'] = $image;
    }



	$items[$url] = $data;
}

H::print_r($items);
exit('[:exit:]');

echo '<br><br>';
//$code = Sheet::rf('@backend/views/shop/test/specs.csv', ['indexFrom' => 'code']);
$code = Sheet::rf('@backend/views/shop/test/last.csv', ['indexFrom' => 'code']);
echo Render::render($items, $code,['category','group_ru','title_ru', 'code', 'units_en']);
exit('[out]');