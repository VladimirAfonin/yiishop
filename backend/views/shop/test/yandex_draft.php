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

class Ya
{
	public static $version = 1;
	public static $proxyUrl = 'https://free-proxy-list.net/';
	public static $platforms = [
		//'platforms'                            => [ PC               'android           iOS              Windows           PlayStation        Smartphone        quadcopter         Blackberry
		'для ПК'                               => ['dkeh1jd' => '+', 'llulwif' => '-', '0v8w2sz' => '-', 'a5sj3l2' => '+', '3zkrmzx' => '-', 'd2ejgh3' => '-', '0tizjem' => '-'], //'vxq3g1f' => '-',
		'для консолей'                         => ['dkeh1jd' => '+', 'llulwif' => '-', '0v8w2sz' => '-', 'a5sj3l2' => '-', '3zkrmzx' => '+'],                                     //'vxq3g1f' => '-',
		'для смартфонов'                       => ['dkeh1jd' => '-', 'llulwif' => '+', '0v8w2sz' => '+', 'a5sj3l2' => '-', '3zkrmzx' => '-', 'd2ejgh3' => '+', '0tizjem' => '-'], //'vxq3g1f' => '+',
		'для квадрокоптеров'                   => ['dkeh1jd' => '-', '3zkrmz1' => '+', 'd2ejgh3' => '-', '0tizjem' => '+'],
		'для ПК, для смартфонов, для консолей' => ['dkeh1jd' => '+', 'llulwif' => '+', '0v8w2sz' => '+', 'a5sj3l2' => '+', 'd2ejgh3' => '+'],
		'для смартфонов, для квадрокоптеров'   => ['dkeh1jd' => '-',
												   'llulwif' => '+',
												   '0v8w2sz' => '+',
												   'a5sj3l2' => '-',
												   '3zkrmzx' => '-',
												   'd2ejgh3' => '+',
												   '3zkrmz1' => '+',
												   '0tizjem' => '+'
		],
		'для ПК, для смартфонов, для консолей, для квадрокоптеров' => ['dkeh1jd' => '+', 'a5sj3l2' => '+', 'llulwif' => '+', '0v8w2sz' => '+', 'd2ejgh3' => '+', '3zkrmzx' => '+', '3zkrmz1' => '+', '0tizjem' => '+'],
		'для ПК, для консолей' => ['dkeh1jd' => '+', 'llulwif' => '-', '0v8w2sz' => '-', 'a5sj3l2' => '+', 'd2ejgh3' => '-', '0tizjem' => '-', '3zkrmzx' => '+'],
		'для ПК, для квадрокоптеров' => ['dkeh1jd' => '+', 'llulwif' => '-', '0v8w2sz' => '-', 'a5sj3l2' => '+', '3zkrmzx' => '-', '3zkrmz1' => '+', 'd2ejgh3' => '-', '0tizjem' => '+'],
		'для ПК, для консолей, для квадрокоптеров' => ['dkeh1jd' => '+', '0v8w2sz' => '-', 'a5sj3l2' => '+', 'llulwif' => '-', '3zkrmzx' => '+', '3zkrmz1' => '+', 'd2ejgh3' => '-', '0tizjem' => '+'],

	];

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

		$dom = Ya::dom($html);

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

//$url = 'https://market.yandex.ru/product/14177518/spec'; // HTC Vive   +
//$url = 'https://market.yandex.ru/product/1732361942/spec'; // Oculus Rift CV1  +
//$url = 'https://market.yandex.ru/product/36576710/spec'; // HTC Vive Pro  +
//$url = 'https://market.yandex.ru/product/14181070/spec'; // PlayStation VR  ++
//$url = 'https://market.yandex.ru/product/1801368291/spec'; // Lenovo Explorer  ++
//$url = 'https://market.yandex.ru/product/1912271525/spec'; // Samsung Odyssey  ++
//$url = 'https://market.yandex.ru/product/1959094199/spec'; // ASUS Headset  ++
//$url = 'https://market.yandex.ru/product/14191916/spec'; // Microsoft HoloLens  ++
//$url = 'https://market.yandex.ru/product/14207158/spec'; // Oculus Rift DK2  ++
//$url = 'https://market.yandex.ru/product/1803348260/spec'; // Acer Headset  ++
//$url = 'https://market.yandex.ru/product/1713635874/spec'; // Google Glass;3.0 ++
//$url = 'https://market.yandex.ru/product/14205773/spec'; // Google Glass;2.0 ++
//$url = 'https://market.yandex.ru/product/1950077317/spec'; // DELL Visor ++
//$url = 'https://market.yandex.ru/product/43208055/spec'; // Oculus Go ++
//$url = 'https://market.yandex.ru/product/42830063/spec'; // Lenovo Mirage Solo ++
//$url = 'https://market.yandex.ru/product/1715204844/spec'; // Pimax 4K ++
//$url = 'https://market.yandex.ru/product/14193597/spec'; // Zeiss Cinemizer Oled ++
//$url = 'https://market.yandex.ru/product/1716269848/spec'; // ACV HYPE;SVR-FHD + ?
//$url = 'https://market.yandex.ru/product/14203257/spec'; // Epson Moverio;BT-100 ++
//$url = 'https://market.yandex.ru/product/14201450/spec'; // Epson Moverio;BT-200 ++
//$url = 'https://market.yandex.ru/product/1713855814/spec'; // Epson Moverio;BT-300 ++
//$url = 'https://market.yandex.ru/product/1882823152/spec'; // Epson Moverio;BT-350 ++
//$url = 'https://market.yandex.ru/product/14201451/spec'; // Epson Moverio Pro;BT-2000 ++
//$url = 'https://market.yandex.ru/product/115757333/spec'; // Epson Moverio Pro;BT-2200 ++
//$url = 'https://market.yandex.ru/product/14202623/spec'; // LG 360 VR ++

//$url = 'https://market.yandex.ru/product/1713855814/spec';
//$url = 'https://market.yandex.ru/product/14177518/spec';
//$url = 'https://market.yandex.ru/product/42830063/spec'; //
//$url = 'https://market.yandex.ru/product/14177518/spec'; // -
//$url = 'https://market.yandex.ru/product/1713855814/spec'; // -
//$url = 'https://market.yandex.ru/product/14181070/spec'; // -
//$url = 'https://market.yandex.ru/product/1803348260/spec'; // -
//$url = 'https://market.yandex.ru/product/14181070/spec'; // -
//$url = 'https://market.yandex.ru/product/1801368291/spec'; // -
//$url = 'https://market.yandex.ru/product/1912271525/spec'; // -
//$url = 'https://market.yandex.ru/product/1959094199/spec'; // - current
//$url = 'https://market.yandex.ru/product/14191916/spec'; // - current
//$urls = [
//	'https://market.yandex.ru/product/14177518/spec',
//	'https://market.yandex.ru/product/1732361942/spec',
//	'https://market.yandex.ru/product/36576710/spec',
//	'https://market.yandex.ru/product/14181070/spec',
//	'https://market.yandex.ru/product/1801368291/spec',
//	'https://market.yandex.ru/product/1912271525/spec',
//	'https://market.yandex.ru/product/1959094199/spec',
//	'https://market.yandex.ru/product/14191916/spec',
//	'https://market.yandex.ru/product/14207158/spec',
//	'https://market.yandex.ru/product/1803348260/spec',
//	'https://market.yandex.ru/product/1713635874/spec',
//	'https://market.yandex.ru/product/14205773/spec',
//	'https://market.yandex.ru/product/1950077317/spec',
//	'https://market.yandex.ru/product/43208055/spec',
//	'https://market.yandex.ru/product/42830063/spec',
//	'https://market.yandex.ru/product/1715204844/spec',
//	'https://market.yandex.ru/product/14193597/spec',
//	'https://market.yandex.ru/product/1716269848/spec',
//	'https://market.yandex.ru/product/14203257/spec',
//	'https://market.yandex.ru/product/14201450/spec',
//	'https://market.yandex.ru/product/1713855814/spec',
//	'https://market.yandex.ru/product/1882823152/spec',
//	'https://market.yandex.ru/product/14201451/spec',
//	'https://market.yandex.ru/product/115757333/spec',
//	'https://market.yandex.ru/product/14202623/spec',
//];

/*$urls = [
	'https://market.yandex.ru/product/14191801/spec', // +
	'https://market.yandex.ru/product/14181070/spec', // +
	'https://market.yandex.ru/product/14177518/spec', // +
	'https://market.yandex.ru/product/14191906/spec', // +
	'https://market.yandex.ru/product/1730137463/spec', // +
	'https://market.yandex.ru/product/67698398/spec', // +-
	'https://market.yandex.ru/product/1725687289/spec', // +-
	'https://market.yandex.ru/product/1732415004/spec', // +-
	'https://market.yandex.ru/product/14200534/spec', // +-
	'https://market.yandex.ru/product/14202501/spec', // +
	'https://market.yandex.ru/product/36576710/spec', // +
	'https://market.yandex.ru/product/1732361942/spec',
	'https://market.yandex.ru/product/14202018/spec',
	'https://market.yandex.ru/product/1803348260/spec',
	'https://market.yandex.ru/product/14191898/spec',
	'https://market.yandex.ru/product/67565525/spec',
	'https://market.yandex.ru/product/14216355/spec',
	'https://market.yandex.ru/product/1714017645/spec',
	'https://market.yandex.ru/product/1719962043/spec',
	'https://market.yandex.ru/product/14216351/spec',
	'https://market.yandex.ru/product/1713855814/spec',
	'https://market.yandex.ru/product/14193559/spec',
	'https://market.yandex.ru/product/1722599130/spec',
	'https://market.yandex.ru/product/14202017/spec',
	'https://market.yandex.ru/product/1801368291/spec',
	'https://market.yandex.ru/product/14191802/spec',
	'https://market.yandex.ru/product/43208034/spec',
	'https://market.yandex.ru/product/14191924/spec',
	'https://market.yandex.ru/product/14191904/spec',
	'https://market.yandex.ru/product/43208055/spec',
	'https://market.yandex.ru/product/1715203936/spec',
	'https://market.yandex.ru/product/14191916/spec',
	'https://market.yandex.ru/product/36815156/spec',
	'https://market.yandex.ru/product/43203121/spec',
	'https://market.yandex.ru/product/1713635874/spec',
	'https://market.yandex.ru/product/1817275267/spec',
	'https://market.yandex.ru/product/1716269848/spec',
	'https://market.yandex.ru/product/14193612/spec',
	'https://market.yandex.ru/product/1730134268/spec',
	'https://market.yandex.ru/product/14263543/spec',
	'https://market.yandex.ru/product/1801367709/spec',
	'https://market.yandex.ru/product/1711450099/spec',
	'https://market.yandex.ru/product/14191942/spec',
	'https://market.yandex.ru/product/14191799/spec',
	'https://market.yandex.ru/product/121979458/spec',
	'https://market.yandex.ru/product/1714017666/spec',
	'https://market.yandex.ru/product/14216354/spec',
	'https://market.yandex.ru/product/1972368945/spec',
	'https://market.yandex.ru/product/1721930661/spec',
	'https://market.yandex.ru/product/1712126384/spec',
	'https://market.yandex.ru/product/1711744823/spec',
	'https://market.yandex.ru/product/1882823152/spec',
	'https://market.yandex.ru/product/36148510/spec',
	'https://market.yandex.ru/product/1801367694/spec',
	'https://market.yandex.ru/product/14191940/spec',
	'https://market.yandex.ru/product/1722599133/spec',
	'https://market.yandex.ru/product/1721173805/spec',
	'https://market.yandex.ru/product/14193572/spec',
	'https://market.yandex.ru/product/14191900/spec',
	'https://market.yandex.ru/product/14202108/spec',
	'https://market.yandex.ru/product/14193572/spec',
	'https://market.yandex.ru/product/14200535/spec',
	'https://market.yandex.ru/product/1882823152/spec',
	'https://market.yandex.ru/product/1968821701/spec',
	'https://market.yandex.ru/product/14193597/spec',
	'https://market.yandex.ru/product/14191800/spec',
	'https://market.yandex.ru/product/1722599140/spec',
	'https://market.yandex.ru/product/1721930661/spec',
	'https://market.yandex.ru/product/1721173805/spec',
	'https://market.yandex.ru/product/1971138185/spec',
	'https://market.yandex.ru/product/30112436/spec',
	'https://market.yandex.ru/product/14202031/spec',
	'https://market.yandex.ru/product/14193549/spec',
	'https://market.yandex.ru/product/14191894/spec',
	'https://market.yandex.ru/product/14191900/spec',
	'https://market.yandex.ru/product/14205925/spec',
	'https://market.yandex.ru/product/14206056/spec',
	'https://market.yandex.ru/product/14219543/spec',
	'https://market.yandex.ru/product/1722599133/spec',
	'https://market.yandex.ru/product/14191944/spec',
	'https://market.yandex.ru/product/14191918/spec',
	'https://market.yandex.ru/product/14192071/spec',
	'https://market.yandex.ru/product/14202108/spec',
	'https://market.yandex.ru/product/14205977/spec',
	'https://market.yandex.ru/product/36148510/spec',
	'https://market.yandex.ru/product/14177512/spec',
	'https://market.yandex.ru/product/14201450/spec',
	'https://market.yandex.ru/product/14191925/spec',
	'https://market.yandex.ru/product/14201810/spec',
	'https://market.yandex.ru/product/1711744839/spec',
	'https://market.yandex.ru/product/14191766/spec',
	'https://market.yandex.ru/product/14191925/spec',
	'https://market.yandex.ru/product/14193485/spec',
	'https://market.yandex.ru/product/14201810/spec',
	'https://market.yandex.ru/product/14203258/spec',
	'https://market.yandex.ru/product/14201450/spec',
	'https://market.yandex.ru/product/14219520/spec',
	'https://market.yandex.ru/product/1722099094/spec',
	'https://market.yandex.ru/product/14203265/spec',
	'https://market.yandex.ru/product/14192046/spec',
	'https://market.yandex.ru/product/1711744839/spec',
	'https://market.yandex.ru/product/14207140/spec',
	'https://market.yandex.ru/product/1882826539/spec',
	'https://market.yandex.ru/product/14201808/spec',
	'https://market.yandex.ru/product/14201471/spec',
];*/

/*$urls = [
	'https://market.yandex.ru/product/1711744839/spec',
	'https://market.yandex.ru/product/14207140/spec',
	'https://market.yandex.ru/product/1882826539/spec',
	'https://market.yandex.ru/product/14201808/spec',
	'https://market.yandex.ru/product/14201471/spec',
	'https://market.yandex.ru/product/14203265/spec',
	'https://market.yandex.ru/product/14192046/spec',
];*/

$codes = [
	14191801,
	1732415004,
	14181070,
	14202501,
	1730137463,
	14202018,
	14200534,
	1732361942,
	14177518,
	14216355,
	14191898,
	14191906,
	36576710,
	1801368291,
	1723808705,
	43208034,
	1719962043,
	67698398,
	14193559,
	67565525,
	14229301,
	1713635874,
	14202017,
	1722599130,
	43208055,
	14216351,
	1803348260,
	14191916,
	1725687289,
	14191904,
	14191802,
	1711450099,
	14177537,
	14191799,
	14193485,
	44914221,
	1715204844,
	1730134268,
	14191899,
	1716269848,
	42830063,
	14216354,
	1912271525,
	1817275267,
	1950077317,
	14202623,
	1801367709,
	1711744839,
	1714017645,
	14205925,
	1721307360,
	36815156,
	1714223461,
	14263543,
	1968821701,
	1713855814,
	43203121,
	14191942,
	1715203799,
	1729355916,
	14207158,
	1721211717,
	1715203936,
	14201450,
	14193612,
	1972368945,
	14191924,
	14206053,
	14193597,
	14205933,
	1721930661,
	1801367694,
	30017787,
	1959094199,
	14177536,
	14193572,
	1711744823,
	14205977,
	1882826539,
	14206056,
	14191900,
	1722599133,
	30112436,
	14177513,
	14192071,
	121979458,
	14200535,
	80232281,
	1725278751,
	14191944,
	1722599140,
	14191800,
	1969388812,
	127722298,
	1714017666,
	14192046,
	14219520,
	14263544,
	14219543,
	14206456,
	36873669,
	14201471,
	14193549,
	1715284542,
	127724282,
	14191894,
	14177512,
	1882823152,
	1713855811,
	14205773,
	14202108,
	14191925,
	115757333,
	14202031,
	1971138185,
	1730094134,
	1712126384,
	1730094113,
	36148510,
	14191766,
	14191918,
	1714825899,
	1720811273,
	1721173805,
	1724662468,
	1971138587,
	1729172231,
	1711744850,
	1719103904,
	1723048083,
	1729172250,
	14201451,
	1727521601,
	14191729,
	1842589629,
	1971138414,
	1912268560,
	106651213,
	14193543,
	14191889,
	14191806,
	14191892,
	67622454,
	14230941,
	14202738,
	1724896042,
	1717030782,
	1722099094,
	14201810,
	1714825802,
	14202211,
	14263546,
	1729910918,
	1720451570,
	14203288,
	14192083,
	14203258,
	14191940,
	1730343907,
	1964668985,
	1806497131,
	1722362721,
	14191920,
	14205358,
	14191890,
	1868382575,
	1715284515,
	1729286630,
	1715982987,
	1711744835,
	1729286629,
	1732411475,
	14193546,
	1723688251,
	1954196511,
	14207140,
	1713635888,
	1971138590,
	1732477656,
	1715983018,
	1723809370,
	1719981681,
	14203345,
	14201467,
	1806056879,
	1719163118,
	1780988292,
	1806495793,
	1721782608,
	1729357064,
	1714825927,
	1806494606,
	14263545,
	1806493250,
	1732821445,
	1721782425,
	1721782772,
	14230802,
	1721782770,
	1721782714,
	1721782132,
	1719983532,
	1719163413,
	1716568900,
	1780988325,
	1806497368,
	1723048066,
	1719103611,
	1716568891,
	1719968983,
	1719163642,
	1724896026,
	1722362716,
	1714336286,
	1729910920,
	14206054,
	1729357069,
	1732856417,
	1721782347,
	1719163491,
	1719163012,
	1720994059,
	1719982692,
	1721782625,
	1717040319,
	1719163249,
	1722362747,
	1721782717,
	1719981583,
	14205989,
	14193521,
	1915924576,
	1723809372,
	14191893,
	1725278899,
	1723048067,
	1728416968,
	14206216,
	14205992,
	1729286260,
	14231795,
	14192101,
	14191921,
	14203378,
	1715643166,
	14191736,
	1715643164,
	14203257,
	1721307381,
	1713688962,
	1711744844,
	14192085,
	14196730,
	14196732,
	14206078,
	14196734,
	1732773652,
	14202106,
	14191808,
	14206220,
	14202103,
	14206086,
	1715571651,
	14196737,
	14196739,
	14219519,
	14231791,
	14231789,
	14196736,
	14192106,
	1715284539,
	14202102,
	14191743,
	14206463,
	14200509,
	14196738,
	14206082,
	1806493516,
	1715204605,
	14207192,
	14206013,
	14193554,
	14203265,
	1759497408,
	14206052,
	14191770,
	14205763,
	14193629,
	14201860,
	14201808,
	14205771,
	14202212,
];
//$codes = [14205771];

$urls = [];
foreach($codes as $item) {
	$urls[] = 'https://market.yandex.ru/product/'.$item.'/spec';
}

//H::print_r($urls);
//exit('[exit]'); // todo

 	/*$urls = [
		'https://market.yandex.ru/product/1711744839/spec',
		'https://market.yandex.ru/product/14207140/spec',
		'https://market.yandex.ru/product/1882826539/spec',
		'https://market.yandex.ru/product/14201808/spec',
		'https://market.yandex.ru/product/14201471/spec',
		'https://market.yandex.ru/product/14203265/spec',
		'https://market.yandex.ru/product/14192046/spec'
	];*/


//$urls = [
////	'https://market.yandex.ru/product/1719103904/spec',
//	'https://market.yandex.ru/product/1723809370/spec'
//];

// get proxy to file

$t1 = microtime(true);
Ya::getProxyList(Ya::$proxyUrl);
$t2 = microtime(true);
var_dump($t2 - $t1);
//exit('[]');

$items = [];
foreach($urls as $k => $url) {
	$data = [];

//	H::print_r($row);
//	exit(); // todo

	$html = WebPage::get($url);
//	continue;
//	var_dump($html);
//	exit('[exit]');



	/** @var WebPage $page */
//	$page = WebPage::findOne(['url' => $url]);
//	$redir_count = $page->redirect_count;

//	var_dump($redir_count);
//	exit('redir'); // todo

/*	while($redir_count != 0) {
//		var_dump('ne ravno');exit('in while');
		$delay = rand(30,81);
		sleep($delay);
		$page->delete();

		$html = WebPage::get($url);
		$page = WebPage::findOne(['url' => $url]);

		var_dump($page->redirect_count);
		var_dump($page->url);

//		exit('[in while]'); // todo
		$redir_count = $page->redirect_count;
	}
*/

//	var_dump($url);
//	exit('[exit]'); // todo
	// /.

	$dom = Ya::dom($html);

	// get model
	$model_name = Ya::getValue($dom->query('//div[@class="n-product-title"]/div[@class="n-title__text"]/h1[@class="title title_size_22"]')->item(0)->nodeValue ?? null); 
	$model_name = str_replace('Очки виртуальной реальности ', '', $model_name); 
	$full_name = explode(' ', $model_name);
	$data['w81a9u0'] = ucfirst($full_name[0]);

	$name = '';
	$b = 1;
	while(isset($full_name[$b])) {
		$name .= $full_name[$b] . ' ';
		$b++;
	}
	$data['33fksng'] = preg_replace('/-\s+\d+\s+GB/mui', "", $name);

	//////////////////
	$i = 0;
	while($elem = $dom->query('//dt[@class="n-product-spec__name"]/span[@class="n-product-spec__name-inner"]/text()')->item($i)->nodeValue ?? null) {
		$elem = Ya::getValue($elem);

//		var_dump($elem);

		switch($elem) {
			case "Самостоятельное устройство":
				// get 'alone'
				$alone = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($alone) {
					$data['ywkphr2'] = Ya::getAnswer($alone);
				}
				break;
			case "Вывод изображения":
				// get output image
				$display_output = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($display_output){
					$data['aakph191'] = '+';
					$data['ywkph212'] = Ya::getTypeImageOutput($display_output);
				}
				break;
			case "Устройство подходит":
				$approaches = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($approaches) {
					$platform = Ya::getValue($approaches);
					if(isset(Ya::$platforms[$platform])){
						$data = array_merge(Ya::$platforms[$platform],$data);
					}
				}
				break;
			case 'Объем встроенной памяти':
				// get storage
				$storage = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($storage) $data['c8xo6x6'] = str_replace(' Гб', 'Gb' ,Ya::getValue($storage)); 
				break;
			case "Частота обновления":
				// display freq
				$display_freq = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null; // todo +

				if($display_freq) $data['e16jcrzd'] = str_replace('Гц', '' , Ya::getValue($display_freq));
				break;
			case "Цвет:":
				// color
				$z = 0;
				while($color = $dom->query("//div[contains(@class, 'n-filter-picker__img radiobox__box')]/@data-name")->item($z)->nodeValue ?? null) {
					$data['color'][] = (Ya::getValue($color));
					$z++;
				}
				$data['color'] = array_unique($data['color']);
				break;
			case "Цвет товара:":
				// color
				$z = 0;
				while($color = $dom->query("//div[contains(@class, 'n-filter-picker__img radiobox__box')]/@data-name")->item($z)->nodeValue ?? null) {
					$data['color'][] = (Ya::getValue($color));
					$z++;
				}
				$data['color'] = array_unique($data['color']);
				break;
			case "Диагональ экрана смартфона":
				$diagonal = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($diagonal) $data['ywkph216'] = str_replace('"','',Ya::getValue($diagonal));
				break;
			case "Разрешение дисплея":
				// resolution
				$display_res = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($display_res) {
					$display_res = Ya::getValue($display_res);

					///////////// new
					if(preg_match('#\s+((\d+)\×(\d+)\s+)#mui', $display_res, $out_resolution)) {
						$data['3vzdcz1'] = $out_resolution[1];
						$data['nggks18'] = $out_resolution[2];
						$data['j2p7bju'] = $out_resolution[3];
					} else {
						$data['3vzdcz1'] = $display_res; 
						$res_info = explode('×', $display_res);
						$data['nggks18'] = $res_info[0];
						$data['j2p7bju'] = $res_info[1];
					}
					/////////////


					/*preg_match('#\s+(\d+\×\d+\s+)#mui', $display_res, $out_resolution); 
					if(isset($out_resolution[1]) && !empty($out_resolution[1])) {
						$res_info = explode('×', $out_resolution[1]); 
						$data['3vzdcz1'] = $out_resolution[1];
						$data['nggks18'] = $res_info[0]; 
						$data['j2p7bju'] = $res_info[1];
					} else {
						$data['3vzdcz1'] = $display_res; 
						$res_info = explode('×', $display_res);
						$data['nggks18'] = $res_info[0];
						$data['j2p7bju'] = $res_info[1];
					}*/


				}
				break;
			case "Разрешение дисплея для каждого глаза":
				// resolution
				$display_res = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($display_res) {
					$display_res = Ya::getValue($display_res);
					$data['3vzdcz1'] = $display_res;
					$res_info = explode('×', $display_res);
					$width_res = $res_info[0]; 
					$height_res = $res_info[1];
					$data['nggks18'] = $width_res;
					$data['j2p7bju'] = $height_res;
				}
				break;
			case 'Угол обзора':
				// view angle
				$display_angle = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($display_angle) $data['a62jcrld'] = str_replace('°', '',Ya::getValue($display_angle));
				break;
			case 'Размеры (ШxВ)':
				// get size of item
				$size = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($size) {
					$params = explode('x', str_ireplace('мм', '', Ya::getValue($size)));
					$data['65ihv16'] = $params[0];
					$data['qorav98'] = $params[1];
				}
				break;
			case 'Ширина устройства':
				// get size of item
				$size = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($size) {
					$data['65ihv16'] = str_ireplace('мм', '', Ya::getValue($size));
				}
				break;
			case 'Размеры (ШxГ)':
				// get size of item
				$size = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($size) {
					$params = explode('x', str_ireplace('мм', '', Ya::getValue($size)));
					$data['65ihv16'] = $params[0];
					$data['vbryix7'] = $params[1];
				}
				break;
			case 'Диаметр линз':
				// view angle
				$diameter = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($diameter) $data['ywkph003'] = str_replace('мм','',Ya::getValue($diameter));
				break;
			case 'Разъемы':
				// 3.5 mm jack rmjj6m58
				$jack = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($jack) {
					$data['yh7xh3q'] = Ya::isValueExists($jack,'3.5');
					$data['rmjj6m58'] = Ya::isValueExists($jack,'разъем для подключения зарядного устройства');
				}
				break;
			case 'Датчики':
				// sensors
				$sensor = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($sensor) {
					$data['h1ddzrt'] = Ya::isValueExists($sensor,'акселерометр');
					$data['ywtcejg'] = Ya::isValueExists($sensor,'гироскоп');
					$data['h88pkmd1'] = Ya::isValueExists($sensor,'датчик приближения');
					$data['ywtcej2'] = Ya::isValueExists($sensor,'магнитометр');
				}
				break;
			case 'Наушники':
				// headphone
				$headphone = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($headphone) {
					$data['2uecljhv'] = Ya::isValueExists($headphone,'в комплекте');
				}
				break;
			case 'Пульт управления в комплекте':
				// console
				$console = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]/text()')->item($i)->nodeValue ?? null;
				if($console) {
					$data['4yetlj1'] = Ya::isValueExists($console,'есть');
				}
				break;
			case 'Геймпад в комплекте': 
				// console
				$console = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]/text()')->item($i)->nodeValue ?? null;
				if($console) {
					$data['4yetlj3'] = Ya::isValueExists($console,'есть');
				}
				break;
			case "Сенсорная панель управления":
				// console
				$console = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($console) {
					$data['4yetljh'] = Ya::isValueExists($console,'есть');
				}
				break;
			case "Размеры (ШxВxГ)":
				// dimensions
				$dimensions = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($dimensions) {
					$params = explode('x', str_ireplace('мм', '', Ya::getValue($dimensions)));
					$data['65ihv16'] = $params[0];
					$data['qorav98'] = $params[1];
					$data['vbryix7'] = $params[2];
				}
				break;
			case 'Вес':
				// weight
				$weight = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($weight) {
					$data['ywkph16b'] = str_ireplace('г', '', Ya::getValue($weight));
				}
				break;
			case 'Очки дополненной реальности':
				$glass = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

			if($glass) { $data['ywkph215'] = Ya::getAnswer($glass); }
				break;
			case 'Дополнительная информация':
				// add info
				$addit_info = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				$addit_info = Ya::getValue($addit_info);

				if($url == 'https://market.yandex.ru/product/1716269848/spec') {
					$add_info = explode('; ', $addit_info);
				} else {
					$add_info = explode('; ', $addit_info);
					if(count($add_info) < 4) {
						$add_info = explode(', ', $addit_info);
					};
				}
//				H::print_r($add_info);
				foreach($add_info as $item) {

//					var_dump($item);
//					exit(); // todo
					///////////
					if(strpos($item, 'ОС ') !== false) {
						$data['ui65qcn'] = trim(str_replace(['ОС ', '-'], [''], $item));
					}
					if(strpos($item, 'время отклика') !== false) {
						$data['3jsiejz'] = trim(str_replace(['время отклика', 'миллисекунд'], [''], $item));
					}
					if(strpos($item, 'cкорость смены пикселей:') !== false) {
						$data['3jsiejz'] = trim(str_replace(['cкорость смены пикселей:', 'ms'], [''], $item));
					}
					if(strpos($item, 'графический процессор - ') !== false) {
						$data['4kzmswo'] = trim(str_replace(['графический процессор - '], [''], $item));
					}
					if(strpos($item, 'графический процессор ') !== false) {
						$data['4kzmswo'] = trim(str_replace(['графический процессор ', 'до 700МГц (44,8 Гфлоп/с)', '-'], [''], $item));
					}
					if(strpos($item, 'процессор -') !== false && (strpos($item, 'графический') === false)) {
						// get freq
						if(preg_match('/\d+\.\d+ГГц/mu', $item, $cpu_freq)) {
							$data['y5xo6x6'] = str_replace('ГГц', 'Ghz',$cpu_freq[0]);
						}
						if(isset($data['y5xo6x6'][0])) {
							$data['y5xo6x4'] = trim(str_replace(['процессор - ', $cpu_freq[0]], [''], $item));
						} else {
							$data['y5xo6x4'] = trim(str_replace(['процессор - '], [''], $item));
						}
					}
					if(strpos($item, 'Процессор ') !== false && (strpos($item, 'графический') === false)) {
						$data['y5xo6x4'] = trim(str_replace('Процессор ', '', $item));
					}
					if(strpos($item, 'Аккумулятор на') !== false) {
						$data['wbswcml'] = trim(str_replace(['Аккумулятор на ', 'мВч'], [''], $item));
					}
					if(strpos($item, 'оперативная память ') !== false) {
						$data['ej4wq1y'] = trim(str_replace(['оперативная память ', 'встроенная -'], [''], str_replace([' Гб', 'Гб', 'ГБ', 'DDR3'], ['Gb', 'Gb', 'Gb', ''], $item)));
						if(strpos($item, 'DDR3') !== false) $data['z3xo6x6'] = 'DDR3';
					}
					if(strpos($item, 'оперативной памяти') !== false) {
						$data['ej4wq1y'] = trim(str_replace('оперативной памяти', '', str_replace([' ГБ', 'ГБ', 'DDR3'], ['Gb', 'Gb', ''], $item)));
					}
					if(strpos($item, 'RAM -') !== false) {
						$data['ej4wq1y'] = trim(str_replace(['RAM -', 'встроенная -'], [''], str_replace([' Гб', 'Гб', 'ГБ', 'DDR3'], ['Gb', 'Gb', 'Gb', ''], $item)));
						if(strpos($item, 'DDR3') !== false) $data['z3xo6x6'] = 'DDR3';
					}
					if(strpos($item, '32-рязрядная Windows') !== false) {
						$data['ui65qcn'] = str_replace('рязрядная', 'bit', trim($item));
					}
					if(strpos($item, 'WiFi 802') !== false) {
						preg_match('/WiFi(\s+\d+\.\d+\s+[a-z]+\/[a-z]+)/mui', trim($item), $output_arr);
						if(isset($output_arr[1]) && !empty($output_arr[1])) {
							$data['2pinrcv'] = trim($output_arr[1]);
						}
					}
					if(!isset($data['2pinrcv'])) {
						if(strpos($item, 'WiFi') !== false) {
							{
								$data['2pinrcv'] = $item;
							}
						}
					}
					if(strpos($item, 'MIMO ') !== false) {
						$data['p4zld1l5'] = '+';
					}
					if(strpos($item, 'Dual Band') !== false) {
						$data['p4zld1l2'] = '+';
					}
					if(strpos($item, 'Bluetooth') !== false) {
						preg_match('/Bluetooth\s+(\d+\.\d+)/mui', $item, $out_bluetooth);
						if(isset($out_bluetooth[1]) && !empty($out_bluetooth[1])) {
							$data['p4zld5l'] = trim($out_bluetooth[1]);
						}
					}
					if(strpos($item, 'USB Type-C') !== false) {
						$data['rmjj6m5t'] = '+';
					}
					if(strpos($item, 'USB-C') !== false) {
						$data['rmjj6m5t'] = '+';
					}
					if(strpos($item, 'поддержка карт памяти') !== false) {
						if(strpos($item, 'Micro SD')) {
							$data['yz90cwl'] = 'Micro SD';
						};
						if(strpos($item, 'MicroSD')) {
							$data['yz90cwl'] = 'MicroSD';
						};
						if(strpos($item, 'TF')) {
							$data['yz90cwl'] = 'TF';
						};
						preg_match('/(\d+)\s?(Гб)|(\d+)\s?(Тб)/mui', $item, $out_max_card);
						$out_max_card = array_values(array_diff($out_max_card, ['']));

						if(isset($out_max_card[1]) && !empty($out_max_card[1])) {
							$data['yz90cwq'] = str_replace(['Гб', 'Тб'], ['Gb', 'Tb'], $out_max_card[1] . ($out_max_card[2] ?? ''));
						}
					}
					if(strpos($item, 'разъем для карт памяти') !== false) {
						if(strpos($item, 'Micro-SD')) {
							$data['yz90cwl'] = 'Micro SD';
						};
						preg_match('/(\d+)\s?(Гб)|(\d+)\s?(Тб)/mui', $item, $out_max_card);
						$out_max_card = array_values(array_diff($out_max_card, ['']));

						if(isset($out_max_card[1]) && !empty($out_max_card[1])) {
							$data['yz90cwq'] = str_replace(['Гб', 'Тб'], ['Gb', 'Tb'], $out_max_card[1] . ($out_max_card[2] ?? ''));
						}
					}
					if(strpos($item, 'двойная фронтальная камера') !== false) {
						$data['gtgn2m6'] = 2;
					}
					if(strpos($item, 'Графический комплекс ') !== false) {
						$data['4kzmswo'] = trim(str_replace(['Графический комплекс '], [''], $item));
					}
					if(strpos($item, ' видеопамяти') !== false) {
						$video_memory = trim(str_replace(['видеопамяти '], [''], $item));
						preg_match('/\d+\s+МБ/mui', $item, $out_video);
						if(isset($out_video[0]) && !empty($out_video[0])) {
							$data['y1xo6x7'] = str_replace(' МБ', 'Mb', ($out_video[0]));
						}
					}
					if(strpos($item, 'возможность голосового управления') !== false) {
						$data['c534jxf'] = '+';
						$data['yq2jcrll'] = '+';
					}
					if(strpos($item, 'поддержка голосового управления') !== false) {
						$data['c534jxf'] = '+';
					}
					if(strpos($item, '2 фронтальные камеры ') !== false) {
						$data['gtgn2m6'] = 2;
					}
					if(strpos($item, 'двойная фронтальная камера') !== false) {
						$data['gtgn2m6'] = 2;
					}
					if(strpos($item, 'встроенный микрофон') !== false) {
						$data['yq2jcrll'] = '+';
					}
					if(strpos($item, 'интегрированный микрофон') !== false) {
						$data['yq2jcrll'] = '+';
					}
					if(strpos($item, 'встроенные микрофоны и камеры') !== false) {
						$data['yq2jcrll'] = '+';
						$data['gtgn2m6'] = 2;
					}
					if(strpos($item, 'фронтальная камера') !== false) {
						if(!isset($data['gtgn2m6'])) {
							$data['gtgn2m6'] = 1;
						}
					}
					if(strpos($item, 'размер экрана:') !== false) {
						preg_match('/\d+[\.|\,]\d+/mui', $item, $disp_size);
						if(isset($disp_size[0]) && !empty($disp_size[0])) {
							$data['1n820fz'] = $disp_size[0];
						}
					}
					if(strpos($item, 'порты') !== false) {
						$data['p85t8s8a'] = Ya::isValueExists($item, 'HDMI');
						$data['0arcae64'] = Ya::isValueExists($item, 'USB 2.0');
						$data['2q8o92fk'] = Ya::isValueExists($item, 'USB');
					}
					if(strpos($item, 'HDMI 2.0') !== false) {
						$data['cie73ha'] = '+';
					}
					if(strpos($item, 'USB 3.0') !== false) {
						$data['p7s2uenu'] = '+';
					}
					if(strpos($item, 'USB 2.0') !== false) {
						$data['0arcae64'] = '+';
					}
					if(strpos($item, 'фронтальная камера') !== false || strpos($item, 'встроенная стерео-камера') !== false) {
						preg_match('/(\d+)/mui', $item, $out_res);
						if(isset($out_res[1]) && !empty($out_res[1])) {
							$data['06wzu4yz'] = $out_res[1];
						}
					}
					if(strpos($item, 'GPS') !== false) {
						$data['yfvshn2'] = '+';
					}
					if(strpos($item, 'Micro-USB') !== false) {
						$data['p85t8s8z'] = '+';
					}
					if(strpos($item, 'Micro USB') !== false) {
						$data['p85t8s8z'] = '+';
					}
					if(strpos($item, 'micro USB') !== false) {
						$data['p85t8s8z'] = '+';
					}
					if(strpos($item, 'OTG') !== false) {
						$data['9qsw0l7d'] = '+';
					}
					if(strpos($item, 'степень защиты') !== false) {
						if(strpos($item, 'IP54')) {
							$data['cxeplx1'] = '+';
							$data['cxeplx2'] = '+';
						}
					}
					if(strpos($item, 'камера Playstation') !== false) {
						$data['gtgn2m6'] = 1;
					}
					if(strpos($item, 'кабель HDMI') !== false) {
						$data['dkzm1ds'] = '+';
					}
					if(strpos($item, 'стереонаушники') !== false) {
						$data['2uecljhv'] = '+';
					}
					if(strpos($item, 'адаптер переменного тока') !== false) {
						$data['9deiqkz'] = '+';
					}
					if(strpos($item, 'силовой кабель переменного тока') !== false) {
						$data['01dksua'] = '+';
					}
					if(strpos($item, 'HDMI') !== false) {
						$data['p85t8s8a'] = '+';
					}
					if(strpos($item, 'MicroSD') !== false) {
						$data['yz90cwl'] = 'MicroSD';
					}
					if(strpos($item, 'microSD') !== false) {
						$data['yz90cwl'] = 'MicroSD';
					}
					if(strpos($item, 'USB-держателя') !== false) {
						$data['4yetlj4'] = '+';
					}
					if(strpos($item, 'ткань для удаления пыли') !== false) {
						$data['4yetlj5'] = '+';
					}
					if(strpos($item, 'очищающая салфетка') !== false) {
						$data['4yetlj5'] = '+';
					}
					if(strpos($item, 'DP 1.2') !== false) {
						$data['rmjj6m56'] = '+';
					}
					if(strpos($item, 'DisplayPort 1.2') !== false) {
						$data['rmjj6m56'] = '+';
					}
					if(strpos($item, 'контроллеры Touch') !== false) {
						$data['4yetlj1'] = '+';
					}
					if(strpos($item, 'чехол') !== false) {
						$data['h2bybiw'] = '+';
					}
					if(strpos($item, 'встроенное окно камеры') !== false) {
						$data['gtgn223'] = '+';
					}
					if(strpos($item, 'SteamVR Tracking') !== false) {
						$data['4yetlj6'] = '+';
					}
					if(strpos($item, 'управление с помощью поворотов головы') !== false) {
						$data['ywkph213'] = '+';
					}
					if(strpos($item, 'дополнительные линзы') !== false) {
						$data['4yetlj8'] = '+';
					}
					if(strpos($item, 'руководство пользователя') !== false) {
						$data['v464xz6'] = '+';
					}
					if(strpos($item, 'складные') !== false) {
						$data['ywkph214'] = '+';
					}
					if(strpos($item, 'складная конструкция') !== false) {
						$data['ywkph214'] = '+';
					}
					if(strpos($item, 'встроенный приемник') !== false) {
						$data['4yetlj9'] = '+';
					}
					if(strpos($item, 'возможно беспроводное подключение') !== false) {
						$data['ppzld1l4'] = '+';
					}
					if(strpos($item, 'AV вход/выход') !== false) {
						$data['ppzld1q4'] = '+';
					}
					if(strpos($item, 'вход AV') !== false) {
						$data['ppzld1q4'] = '+';
					}
					if(strpos($item, 'камера') !== false && !isset($data['06wzu4yz'])) {
						preg_match('/(\d+)Мп/mui', $item, $out_res);
						if(isset($out_res[1]) && !empty($out_res[1])) {
							$data['06wzu4yz'] = $out_res[1];
							if(!isset($data['gtgn2m6'])) $data['gtgn2m6'] = 1;
						}
					}
				}
				break;
			case "Регулировка межзрачкового расстояния":
				$adjastment = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($adjastment) {
					$data['ywkph001'] = Ya::isValueExists($adjastment,'есть');
				}
				break;
			case "Регулировка фокуса":
				// focus
				$focus = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($focus) {
					$data['ywkph002'] = Ya::isValueExists($focus,'есть');
				}
				break;
			case "Совместимость с ОС":
				// focus
				$focus = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($focus) {
					$data['ui65qc1'] = Ya::getValue($focus);
				}
				break;
			case 'Технология экрана':
				// display tech
				$compatible = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($compatible) {
					$data['xxyv5nx'] = Ya::getValue($compatible);
				}
				break;
			case 'Минимальные системные требования':
				$requirements = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($requirements) {
					$data['ui65q11'] = Ya::getValue($requirements);
				}
				break;
			case 'Контроллер движений в комплекте':
				$controller = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($controller) {
					$data['4yetlj2'] = Ya::isValueExists($controller,'есть');
				}
				break;
			case 'Внешний датчик положения в пространстве':
				$controller = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($controller) {
					$data['ywtcej3'] = Ya::isValueExists($controller,'есть');
				}
				break;
			case 'Запись видео':
				// recording video
				$video_rec = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($video_rec) {
					$data['lggn122'] = Ya::isValueExists($video_rec,'есть');
				}
				break;
			default:
				echo '<hr><br>';
				var_dump($elem) . '->' . var_dump($url);
				echo '<hr><br>';
		}
		$i++;
	}
	//////////////////

	$items[$url] = $data;

//	sleep(rand(54,78));
}

H::print_r($items);
var_dump(count($items));
echo '<br><br>';
//$code = Sheet::rf('@backend/views/shop/test/specs.csv', ['indexFrom' => 'code']);
$code = Sheet::rf('@backend/views/shop/test/last.csv', ['indexFrom' => 'code']);
echo Render::render($items, $code,['category','group_ru','title_ru', 'code', 'units_en']);


exit('[out]');
