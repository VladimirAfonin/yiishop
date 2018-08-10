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
	'https://www.amazon.com/Original-Xiaomi-Monitor-Wristband-Display/dp/B01GMQ4Y3O',
	/*'https://www.amazon.com/Fitbit-Charge-Fitness-Wristband-Version/dp/B01K9S24EM',
	'https://www.amazon.com/dp/B072N57LCD',
	'https://www.amazon.com/Amazfit-A1603-Activity-Tracker-Touchscreen/dp/B01MQ0ALDO',
	'https://www.amazon.com/Apple-Watch-Space-Aluminum-Black/dp/B075TCR2NZ',
	'https://www.amazon.com/Huawei-Watch-Carbon-Android-Warranty/dp/B06XDMCH6Z',
	'https://www.amazon.com/Huawei-Watch-Stainless-Steel-Warranty/dp/B013LKLS2E',
	'https://www.amazon.com/Fitbit-Ionic-Smartwatch-Charcoal-Included/dp/B074VDF16R',
	'https://www.amazon.com/Amazfit-Activity-Tracker-Charcoal-A1702/dp/B07BB4KGPZ',
	'https://www.amazon.com/Samsung-SM-R770NZSAXAR-Gear-S3-Classic/dp/B01M1OXXT8',
	'https://www.amazon.com/Apple-Watch-Smartwatch-Space-Aluminum/dp/B078YHNF62/',
	'https://www.amazon.com/OnePlus-5T-A5010-Version-Midnight/dp/B077TFS54V',
	'https://www.amazon.com/Samsung-Galaxy-S9-Unlocked-Smartphone/dp/B079JSZ1Z2',
	'https://www.amazon.com/Razer-Phone-Display-Front-Facing-Speakers/dp/B077B91954',
	'https://www.amazon.com/gp/product/B077T8FB3M',
	'https://www.amazon.com/Fitness-Tracker-Audio-Coach-Moov/dp/B01N5AJPTG/',
	'https://www.amazon.com/Oculus-Go-Standalone-Virtual-Reality-Headset/dp/B076CWS8C6',
	'https://www.amazon.com/Samsung-Gear-VR-Discontinued-Manufacturer/dp/B016OFYGXQ',
	'https://www.amazon.com/View-Master-Virtual-Reality-Starter-Pack/dp/B011EG5HJ2',
	'https://www.amazon.com/HTC-VIVE-Virtual-Reality-System-pc/dp/B00VF5NT4I',
	'https://www.amazon.com/Samsung-Gear-VR-Discontinued-Manufacturer/dp/B01HU3J9QA',
	'https://www.amazon.com/Acer-AH101-D8EY-Windows-Reality-VD-R05AP-002/dp/B075PVLN2P',
	'https://www.amazon.com/Oculus-Touch-Virtual-Reality-System-pc/dp/B073X8N1YW',
	'https://www.amazon.com/PlayStation-VR-4/dp/B01DE9DY8S',
	'https://www.amazon.com/dp/B01N634P7O/ref=asc_df_B01N634P7O5561955',
	'https://www.amazon.com/Sony-HMZ-T3W-Mounted-Viewer-Model/dp/B00FNJGJN0',
	'https://www.amazon.com/Epson-V11H423020-Moverio-See-Through-Wearable/dp/B007ORN0LS',
	'https://www.amazon.com/Google-Glass-Explorer-Version-Charcoal/dp/3283005737',
	'https://www.amazon.com/Royole-Moon-Virtual-Mobile-Theater/dp/B01M8FE2U4',
	'https://www.amazon.es/Woxter-Neo-VR100-Silver-incorporada/dp/B06XBWFT35',
	'https://www.amazon.com/Pico-Interactive-Goblin-VR-Headset-Android/dp/B073XNW4TK',*/
];


$access_key = 'AKIAJMODN7SLEMTUMYOA';
$secret_access_key = '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';

//$access_key2 = 'AKIAI37ZMAOS64ATSC7A';
//$secret = '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';

$item_isbn = 'B01GMQ4Y3O';

$info = Amazon::getBookInfo($item_isbn, $access_key, $secret_access_key);
print $info;

exit('[:exit:]');



$items = [];
foreach($urls as $k => $url) {

//	var_dump($url);
	$data = [];

	$html = WebPage::get($url);
//	$html = WebPage::getDataFromApi($url);

	var_dump($html);
	exit(); // todo

	$dom = Amazon::dom($html);

	// get model
	$model_name = Amazon::getValue($dom->query('//div[@class="n-product-title"]/div[@class="n-title__text"]/h1[@class="title title_size_22"]')->item(0)->nodeValue ?? null); 
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
		$elem = Amazon::getValue($elem);

//		var_dump($elem);

		switch($elem) {
			case "Самостоятельное устройство":
				// get 'alone'
				$alone = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($alone) {
					$data['ywkphr2'] = Amazon::getAnswer($alone);
				}
				break;
			case "Вывод изображения":
				// get output image
				$display_output = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($display_output){
					$data['aakph191'] = '+';
					$data['ywkph212'] = Amazon::getTypeImageOutput($display_output);
				}
				break;
			case "Устройство подходит":
				$approaches = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($approaches) {
					$platform = Amazon::getValue($approaches);
					if(isset(Amazon::$platforms[$platform])){
						$data = array_merge(Amazon::$platforms[$platform],$data);
					}
				}
				break;
			case 'Объем встроенной памяти':
				// get storage
				$storage = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($storage) $data['c8xo6x6'] = str_replace(' Гб', 'Gb' ,Amazon::getValue($storage)); 
				break;
			case "Частота обновления":
				// display freq
				$display_freq = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null; // todo +

				if($display_freq) $data['e16jcrzd'] = str_replace('Гц', '' , Amazon::getValue($display_freq));
				break;
			case "Цвет:":
				// color
				$z = 0;
				while($color = $dom->query("//div[contains(@class, 'n-filter-picker__img radiobox__box')]/@data-name")->item($z)->nodeValue ?? null) {
					$data['color'][] = (Amazon::getValue($color));
					$z++;
				}
				$data['color'] = array_unique($data['color']);
				break;
			case "Цвет товара:":
				// color
				$z = 0;
				while($color = $dom->query("//div[contains(@class, 'n-filter-picker__img radiobox__box')]/@data-name")->item($z)->nodeValue ?? null) {
					$data['color'][] = (Amazon::getValue($color));
					$z++;
				}
				$data['color'] = array_unique($data['color']);
				break;
			case "Диагональ экрана смартфона":
				$diagonal = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($diagonal) $data['ywkph216'] = str_replace('"','',Amazon::getValue($diagonal));
				break;
			case "Разрешение дисплея":
				// resolution
				$display_res = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($display_res) {
					$display_res = Amazon::getValue($display_res);

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
					$display_res = Amazon::getValue($display_res);
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

				if($display_angle) $data['a62jcrld'] = str_replace('°', '',Amazon::getValue($display_angle));
				break;
			case 'Размеры (ШxВ)':
				// get size of item
				$size = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($size) {
					$params = explode('x', str_ireplace('мм', '', Amazon::getValue($size)));
					$data['65ihv16'] = $params[0];
					$data['qorav98'] = $params[1];
				}
				break;
			case 'Ширина устройства':
				// get size of item
				$size = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($size) {
					$data['65ihv16'] = str_ireplace('мм', '', Amazon::getValue($size));
				}
				break;
			case 'Размеры (ШxГ)':
				// get size of item
				$size = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($size) {
					$params = explode('x', str_ireplace('мм', '', Amazon::getValue($size)));
					$data['65ihv16'] = $params[0];
					$data['vbryix7'] = $params[1];
				}
				break;
			case 'Диаметр линз':
				// view angle
				$diameter = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($diameter) $data['ywkph003'] = str_replace('мм','',Amazon::getValue($diameter));
				break;
			case 'Разъемы':
				// 3.5 mm jack rmjj6m58
				$jack = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($jack) {
					$data['yh7xh3q'] = Amazon::isValueExists($jack,'3.5');
					$data['rmjj6m58'] = Amazon::isValueExists($jack,'разъем для подключения зарядного устройства');
				}
				break;
			case 'Датчики':
				// sensors
				$sensor = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($sensor) {
					$data['h1ddzrt'] = Amazon::isValueExists($sensor,'акселерометр');
					$data['ywtcejg'] = Amazon::isValueExists($sensor,'гироскоп');
					$data['h88pkmd1'] = Amazon::isValueExists($sensor,'датчик приближения');
					$data['ywtcej2'] = Amazon::isValueExists($sensor,'магнитометр');
				}
				break;
			case 'Наушники':
				// headphone
				$headphone = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($headphone) {
					$data['2uecljhv'] = Amazon::isValueExists($headphone,'в комплекте');
				}
				break;
			case 'Пульт управления в комплекте':
				// console
				$console = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]/text()')->item($i)->nodeValue ?? null;
				if($console) {
					$data['4yetlj1'] = Amazon::isValueExists($console,'есть');
				}
				break;
			case 'Геймпад в комплекте': 
				// console
				$console = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]/text()')->item($i)->nodeValue ?? null;
				if($console) {
					$data['4yetlj3'] = Amazon::isValueExists($console,'есть');
				}
				break;
			case "Сенсорная панель управления":
				// console
				$console = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($console) {
					$data['4yetljh'] = Amazon::isValueExists($console,'есть');
				}
				break;
			case "Размеры (ШxВxГ)":
				// dimensions
				$dimensions = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($dimensions) {
					$params = explode('x', str_ireplace('мм', '', Amazon::getValue($dimensions)));
					$data['65ihv16'] = $params[0];
					$data['qorav98'] = $params[1];
					$data['vbryix7'] = $params[2];
				}
				break;
			case 'Вес':
				// weight
				$weight = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($weight) {
					$data['ywkph16b'] = str_ireplace('г', '', Amazon::getValue($weight));
				}
				break;
			case 'Очки дополненной реальности':
				$glass = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

			if($glass) { $data['ywkph215'] = Amazon::getAnswer($glass); }
				break;
			case 'Дополнительная информация':
				// add info
				$addit_info = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				$addit_info = Amazon::getValue($addit_info);

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
						$data['p85t8s8a'] = Amazon::isValueExists($item, 'HDMI');
						$data['0arcae64'] = Amazon::isValueExists($item, 'USB 2.0');
						$data['2q8o92fk'] = Amazon::isValueExists($item, 'USB');
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
					$data['ywkph001'] = Amazon::isValueExists($adjastment,'есть');
				}
				break;
			case "Регулировка фокуса":
				// focus
				$focus = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($focus) {
					$data['ywkph002'] = Amazon::isValueExists($focus,'есть');
				}
				break;
			case "Совместимость с ОС":
				// focus
				$focus = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($focus) {
					$data['ui65qc1'] = Amazon::getValue($focus);
				}
				break;
			case 'Технология экрана':
				// display tech
				$compatible = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($compatible) {
					$data['xxyv5nx'] = Amazon::getValue($compatible);
				}
				break;
			case 'Минимальные системные требования':
				$requirements = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($requirements) {
					$data['ui65q11'] = Amazon::getValue($requirements);
				}
				break;
			case 'Контроллер движений в комплекте':
				$controller = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($controller) {
					$data['4yetlj2'] = Amazon::isValueExists($controller,'есть');
				}
				break;
			case 'Внешний датчик положения в пространстве':
				$controller = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;

				if($controller) {
					$data['ywtcej3'] = Amazon::isValueExists($controller,'есть');
				}
				break;
			case 'Запись видео':
				// recording video
				$video_rec = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($video_rec) {
					$data['lggn122'] = Amazon::isValueExists($video_rec,'есть');
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
