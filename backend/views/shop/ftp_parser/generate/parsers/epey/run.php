<?
use app\helpers\H;
use app\helpers\Clerk;
use app\helpers\WebPage;
use app\helpers\Sheet;
use app\helpers\Render;
use yii\helpers\Url;
use yii\helpers\Json;

global $dom;

class Epey
{
	public static $version = 20;
	public static $unification = [
		'cxeplx1' => [
			'3 ATM'                       => 'WR 30',
			'5 ATM'                       => 'WR 50',
			'10 ATM'                      => 'WR 100',
			'30 mt waterproof'            => 'WR 30',
			'50 mt waterproof'            => 'WR 50',
			'100 mt waterproof'           => 'WR 100',
			'Only against water splashes' => 'IPX4',
		],
		'xxyv5nx' => [
			'Double Layer (Dual-layer) (LCD)'            => 'Double Layer LCD',
			'Transflective MePurpley-In-Pixel (MIP) LCD' => 'MIP LCD',
		]
	];
	public static $translations = [
		'materials' => [
			'Cam'                               => 'Glass',
			'Yekpare'                           => '',
			'Polikarbonat'                      => 'Polycarbonate',
			'Alüminyum'                         => 'Aluminum',
			'Plastik'                           => 'Plastic',
			'Metalik'                           => 'Metallic color',
			'Görünümlü'                         => '',
			'Deri'                              => 'Leather',
			'Kauçuk'                            => 'Rubber',
			'Seramik'                           => 'Ceramic',
			'Paslanmaz'                         => 'Stainless',
			'Çelik'                             => 'Steel',
			'Aluminum-Magnezyum Alaşımlı Metal' => 'Aluminum-Magnesium Alloy Metal',
			'Değiştirilebilir'                  => 'Replaceable',
			' ya da '                           => ' or ',
			'PoliKarbonat'                      => 'Polycarbonate',
			'Suni'                              => 'Artificial',
			'Silikon'                           => 'Silicone',
			'Titanyum'                          => 'Titanium',
			'Alaşım'                            => 'Alloy',
			'Çinko'                             => 'Zink',
			'Alaşımlı'                          => 'Alloy',
			'Magnezyum' => 'Magnesium',
			'Alloylı' => 'Alloy',
		],
		'features' => [
			'Çoklu Pencere (Dual/Multi Window)'           => 'Dual/Multi Window',
			'Arka Kapak'                                  => 'tailgate',
			'Değiştirilebilir Temalar'                    => 'Changeable Themes',
			'Gürültü Önleyici İkinci Mikrofon'            => 'Second Microphone For Noise-Cancelling',
			'İris Tanımlama'                              => 'Identification Of Iris',
			'Kısayol Tuşu'                                => 'Shortcut Keys',
			'Sanal Ekran Tuşları'                         => 'Virtual Keypad',
			'Sanal Gerçeklik (VR) Uyumu'                  => 'Virtual reality (VR) adaptation',
			'Yüz Tanımlama'                               => 'Face Identification',
			'Canlı Yayın (Live Broadcast)'                => 'Live Broadcast',
			'Uyumu'                                       => 'Compliance',
			'Gizli Mod'                                   => 'Hidden Mode',
			'Kolay Arayüz (Easy Mode)'                    => 'Easy mode',
			'Mikrofon'                                    => 'Microphone',
			'Aydınlatmalı Kapasitif Tuşlar'               => 'Illuminated Capacitive Keys',
			'Ekrana Çift Dokunarak Açma (KnockON)'        => 'Double-Tap The Screen To Open (KnockON)',
			'Gürültü Önleyici Dördüncü'                   => 'Noise Reduction Fourth',
			'Sertifikası'                                 => 'Certificate',
			'Sesle Ekran Kilidi Açma'                     => 'Turn On Voice Screen Lock',
			'Sesle Komut (Yanıt/Red)'                     => 'Voice Command',
			'Yüksek Kalitede'                             => 'High Quality',
			'Ses Kaydı'                                   => 'Audio Recording',
			'Yüksek Kalite'                               => 'High Quality',
			'Ses'                                         => 'Sound',
			'Kulaklık Ses Çıkışı'                         => 'Audio Output',
			'Tek Elde Kullanım Modu'                      => 'Single-Hand Use Mode',
			'ile Başka cihazları Şarj Edebilme'           => 'charging other devices',
			'MaxxAudio Ses Geliştirme'                    => 'Audio Enhancement',
			'Ses Geliştirme'                              => 'Sound Development',
			'Ekran'                                       => 'Screen',
			'Çocuk Modu'                                  => 'Child Mode',
			'El Haraketi (Gesture) Algılama'              => 'Hand Movement (Gesture) Detection',
			'Geliştirme'                                  => 'Development ',
			'Araması'                                     => 'Call',
			'Entegrasyonu'                                => 'Integration',
			'Kulaklık'                                    => 'Headphone',
			'Çıkışı'                                      => 'Output',
			'Oynatma'                                     => 'Playback',
			'Çipi'                                        => 'Chip',
			'Aydınlatmasız Kapasitif Tuşlar'              => 'Illuminated Capacitive Keys',
			'Sertiifikası'                                => 'Certification',
			'Gürültü Önleyici'                            => 'Noise-Canceling',
			'Üçüncü'                                      => 'Third',
			'Yıl Güncelleme Garantisi'                    => 'Year Warranty Upgrade',
			'Tuşu'                                        => 'Keys',
			'Arttırılmış Gerçeklik'                       => '',
			'Genişletilebilir Kenar screen Özellikleri'   => 'Expandable sidebar properties',
			'Desteği'                                     => 'Support',
			'Askeri'                                      => 'Military',
			'Standartlarda'                               => 'Standards',
			'Sağlamlık'                                   => 'Strength',
			'Gelişmiş'                                    => 'Advanced',
			'Kaydedici'                                   => 'Register',
			'Yükseklikten'                                => 'From height',
			'Düşmeye'                                     => 'To fall',
			'Dayanıklı'                                   => 'Resistant',
			'Akıllı'                                      => 'Smart',
			'Klavye'                                      => 'Keyboard',
			'Ayar'                                        => 'Setting',
			'Altın'                                       => 'Gold',
			'Kaplama'                                     => 'Coating',
			'Kullanımı'                                   => 'Use',
			'Hızlı'                                       => 'Speed',
			'Bildirim'                                    => 'Notification',
			'Çözünürlüklü'                                => 'Resolution',
			'Yakınlaştırma'                               => 'Zoom up to 3x',
			'Yüksek'                                      => 'High',
			'Baş aşağı Kullanabilme'                      => 'Upside down',
			'Hoparlör'                                    => 'Speaker',
			'Kutu İçeriğinde'                             => 'Box Contents',
			'%10 Şarj ile 24 saat açık kalabilme'         => 'Up to 24 hours with 10% charge',
			'Kişiselleştirilebilir'                       => 'Customizable',
			'Toz'                                         => 'dust',
			'Titreşim'                                    => 'Vibration',
			'Düşme ve Darbelere Dirençli'                 => 'Shock and fall resistant',
			'-25°C ve 55°C Isı aralığında Çalışabilme'    => 'Working in the temperature range of -25°C and 55°C',
			'Güncelleme'                                  => 'update',
			'Magnezyum'                                   => 'Magnesium',
			'Alaşımlı'                                    => 'Alloy',
			'Çerçeve'                                     => 'Casing',
			'-20°C ve 60°C Isı aralığında Çalışabilme'    => '-20°C and 60°C heat range to work',
			'Dosya Yönetim Sistemi'                       => 'File Management System',
			'Kızılötesi'                                  => 'Infrared',
			'Artırılmış Gerçeklik'                        => '',
			'Dahili'                                      => 'Internal',
			'Likit'                                       => 'Liquid',
			'Soğutma'                                     => 'Cooling',
			'Sistemi'                                     => 'System',
			'Kart'                                        => 'Card',
			'Şifre'                                       => 'Password',
			'Koruması'                                    => 'Protection',
			'Sanal Tur Çekimi'                            => 'Virtual Tour',
			'Canlı Destek (Teamviewer ile)'               => 'Live support (with TeamViewer)',
			'Depolama'                                    => 'Storage',
			'Alanı'                                       => 'Space',
			'Alloylı'                                     => 'Altaylı',
			'Aile'                                        => 'Family',
			'Odası'                                       => 'Room',
			'Bağlantısı'                                  => 'Connection',
			'Sensör'                                      => 'Sensor',
			'Sensörü'                                     => 'Sensor',
			'Arka'                                        => 'Back',
			'Kamera'                                      => 'Camera',
			'Çocuk'                                       => 'Child',
			'Köşesi'                                      => 'Corner',
			'Dosya'                                       => 'File',
			'Yöneticisi'                                  => 'Manager',
			'Güç'                                         => 'Power',
			'Tasarruf'                                    => 'Saving',
			'Modu'                                        => 'Mode',
			'Spor'                                        => 'Sport',
			'Ay Ücretsiz'                                 => 'Month Free',
			'Sihirli'                                     => 'Magic',
			'Tuş'                                         => 'Key',
			'Yardım'                                      => 'Helping',
			'Müzik'                                       => 'Music',
			'Hesabım'                                     => 'My account',
			'Kayıt'                                       => 'Recording',
			'Teknolojisi'                                 => 'Technology',
			'Görüntü'                                     => 'Display',
			'Çözünürlü'                                   => 'Resolution',
			'Videolardan'                                 => 'Videos',
			'Oluşturma'                                   => 'Formation',
			'Yuvarlak'                                    => 'Round',
			'Köşeli'                                      => 'Corner',
			'Tasarım'                                     => 'Design',
			'Füme'                                        => 'Smoked',
			'Kişisel'                                     => 'Personal',
			'Profili'                                     => 'Profile',
			'Oluşturabilme'                               => 'Creating',
			'Kaydı'                                       => 'Recording',
			'Duraklatma'                                  => 'Pause',
			've Kaldığı'                                  => 'and starting',
			'Yerden'                                      => 'from where it left off',
			'Göz'                                         => 'Eye',
			'Kapağa'                                      => 'Cover',
			'Uygun'                                       => 'Appropriate',
			'İndirilebilir'                               => 'Reducible',
			'Temalar'                                     => 'Themes',
			'Deklanşör'                                   => 'Shutter',
			'Kaydı Duraklatma ve Kaldığı Yerden Başlatma' => 'Pause recording and start from where it left off',
			'Çift'                                        => 'Double',
			'El Yazısı'                                   => 'Handwriting',
			'Tanıma'                                      => 'Recognition',
			'Gücü'                                        => 'Power',
			'Hızlandırıcı'                                => 'Accelerating',
			'İyileştirme'                                 => 'Therapy',
			'Başlatma'                                    => 'Initiation',
			'Kıvrımlı'                                    => 'Folded',
			've Kasa'                                     => 'and safe',
			'Hatlı'                                       => 'Dial',
			'Headphone için'                              => 'For the headphone',
			'Görme'                                       => 'Seeing',
			'Engelliler'                                  => 'Disabled',
			'için'                                        => 'to',
			'Asistan'                                     => 'Assistant',
			'Türk'                                        => 'Turk',
			'Telekom'                                     => 'Telecom',
			'Sensorü'                                     => 'Sensor',
			'Beşinci'                                     => 'Five',
			'????'                                        => '',
			'Standardında'                                => 'Standard',
			'Passwordleme'                                => 'Passwording',
			'İç'                                          => 'In',
			'Mekan'                                       => 'Place',
			'Hava'                                        => 'Air',
			'Kirlilik'                                    => 'Polution',
			'Lazer'                                       => 'Laser',
			'Mesafe'                                      => 'Distance',
			'Ölçüçü'                                      => 'Meter',
			'Aydınlatma'                                  => 'Lighting',
			'Paylaşımlı'                                  => 'Shared',
			'20°C ve 55°C Isı aralığında Çalışabilme'     => 'Working in -20°C and 55°C heat range',
			'Avea Fırsat'                                 => 'Avea Opportunity',
			'Turkcell Cüzdan'                             => '',
			'Amplifikatör'                                => 'Amplifier',
		],
		'words'     => [
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
			'Siyahı'                          => 'Black',
			'Gümüş'                           => 'Silver',
			' Saf '                           => ' Pure ',
			'Platin'                          => 'Platinum',
			'Antrasit'                        => 'Anthracite',
			'Puslu'                           => 'Misty',
			'Gece'                            => 'Night',
			'Mavisi'                          => 'Blue',
			'Beyazı'                          => 'White',
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
			'Eloksal '                        => 'Anodizing ',
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
			'Hat'                             => 'line',
			'Tek'                             => 'single',
			'Hareketk'                        => 'Motion',
			'Haraket'                         => 'Moving',
			'İşlemcisi'                       => 'Processor',
			'Sensör'                          => 'Sensor',
			'Hareket'                         => 'Motion',
			'Kapasitif'                       => 'Capacitive',
			'Ekran'                           => 'Screen',
			'Rezistif'                        => 'Resistive',
			'Konnektörü'                      => 'Connector',
			'Saf'                             => 'Pure',
			'Turkcell Arayüz'                 => '',
			'Belirtilmemiş'                   => 'Unspecified',
			'Türk'                            => 'Turkish',
			'Telekom'                         => 'Telecom',
			'Füme'                            => 'Smoked',
			'Siyah'                           => 'Black',
			'Alimünyum'                       => 'Aluminum',
			"my'ye kadar su geçirmez"         => 'mt waterproof',
			'Bulut'                           => 'Cloud',
			'Beyaz'                           => 'White',
			'Küçük'                           => 'Small',
			'Kayısı'                          => 'Apricot',
		]
	];

	public static function translate($group, $data)
	{
		return $result = str_ireplace(array_keys(self::$translations[$group]), array_values(self::$translations[$group]), trim($data));
	}

	public static function isModelExists($summary_arr, $model_name)
	{
		foreach($summary_arr as $k => $value) {
			if(isset($value['33fksng']) && !empty($value['33fksng']) && $value['33fksng'] == $model_name) {
				return true;
			}
		}
		return false;
	}

	public static function categoryLinks($categoryId)
	{
		$endpoint = 'https://www.epey.com/kat/listele/';
		$urls = [];
		$i = 1;
		do {
			$html = self::getDataFromApiWithCategory($endpoint, ['sayfa' => $i], $categoryId);
			$dom = self::dom($html);

			$k = 0;
			do {
				$links = $dom->query('//div[@class="detay cell"]/a/@href')->item($k)->nodeValue ?? null;
				if($links) $urls[] = $links;
				$k++;
			} while($links);

			$i++;
		} while(strlen($html) >= 8000);
		return $urls;
	}

	public static function answer($str)
	{
		return (strpos(trim($str), 'ar') !== false) ? '+' : '-';
	}

	public static function check($haystack, $string)
	{
		return (mb_strpos($haystack, $string) !== false) ? '+' : '-';
	}

	public static function isCodecExists($item, $name)
	{
		return (mb_stripos($item, $name) !== false) ? '+' : '';
	}

	public static function getDataFromApiWithCategory($endpoint, $params = [], $category_id = false)
	{
		$url = self::makeUrl($endpoint, $params);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if($category_id) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "kategori_id=$category_id");
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
	public static function makeUrl($url, $urlParams = [], $ignoreParams = []): string
	{
		foreach($ignoreParams as $key) {
			unset($urlParams[$key]);
		};
		if( !empty($urlParams)) {
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
		$head = '<meta http-equiv="Content-Language" content="tr" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		$doc = new DOMDocument();
		@$doc->loadHTML($head.$content);
		return new DOMXpath($doc);
	}

	public static function getLinksFromJsonFile($category)
	{
		$path = Url::to("@-parsers/epey/files/map_{$category}.json");
		if(isset($path)) return Json::decode(file_get_contents($path));
	}

	public static function links($category)
	{
		$path = Url::to("@-parsers/epey/files/map_{$category}.json");
		$period = 86400; // 1 day
		if(file_exists($path)) {
			if(time() - filemtime($path) >= $period) {
				$links = Epey::categoryLinks($category);
				file_put_contents($path, Json::encode($links));
				return $links;
			} else {
				return $links = Json::decode(file_get_contents($path));
			}
		} else {
			$links = Epey::categoryLinks($category);
			file_put_contents($path, Json::encode($links));
			return $links;
		}
	}

	public static function query($pattern, $count = 0)
	{
		global $dom;
		return $dom->query($pattern)->item($count)->nodeValue ?? null;
	}
}

WebPage::$clean = ['head','script','style'];
$urls = array_merge(Epey::links(1) ,Epey::links(16)); // PHONE and WEARABLES

$targets = require_once(Url::to('@-parsers/epey/targets.php'));
if(!empty($targets)){
	$urls = array_filter($urls, function ($v) use ($targets) {return (array_search($v,$targets) !== false);});
}

//$urls = array_filter($urls, function ($k) {return $k % 100 === 0;}, ARRAY_FILTER_USE_KEY);

$items = [];
$clerk = new Clerk('@-parsers/epey/files/clerk.txt', ['total' =>count($urls)]);
foreach($urls as $url) {
	$clerk->tick(['version'=>Epey::$version,'url'=>$url]);
	// get data from cache
	$path_hash = hash('sha256', $url);
	$webpage = WebPage::find()->filterWhere(['path_hash' => $path_hash,'format'=>'json'])->one();
	if($webpage !== null && !empty($webpage->desc) && ($webpage->version == Epey::$version)) {
		$data = Json::decode($webpage->desc);
		$items[$url] = $data;
		continue;
	}
	$options = [];
	$html = WebPage::get($url);
	$html = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $html);
	$dom = Epey::dom($html);

	/* common part */
	// get brand name
	$item_info = Epey::query('//div[@class="baslik"]/h1/a/text()');
	if($item_info) {
		$model_info = explode(' ', $item_info);
		$options['w81a9u0'] = trim(str_replace(['Türk'], ['Turkish'], $model_info[0])); // brand
		$model_name = trim(substr($item_info, strlen($model_info[0])));
//		$model_name = preg_replace('/\(+\d+\)+|2\d{3}/ui', "", trim($model_name));
		$options['ywkph222'] = trim(Epey::translate('words', $options['w81a9u0'].' '.$model_name));
		$options['6412sng'] = trim(Epey::query('//div[@class="baslik"]/h1/a/span[@class="aile"]/text()'));
		if(Epey::isModelExists($items, $model_name)) continue;
		$options['33fksng'] = Epey::translate('words', $model_name);
		// get short model name
		if(isset($options['33fksng'])) {
			if(!preg_match('#\(2\d{3}\)#ui', $options['33fksng'])) {
				$options['ywkph223'] = preg_replace('#\(.+\)#ui','', $options['33fksng']);
			}
		}

		$options['url'] = H::a($url, Url::to($url, true));

		// get product version
		$product_version = Epey::query('//div[@class="baslik"]/h1/a/span[@class="kod"]/text()');
		if($product_version) {
			$options['y1kpha1c'] = str_replace(['(', ')'], '', $product_version);
		};
	}

	$options['4jce7ai']= $dom->query('//div[@class="fiyatlar"]/div[@class="fiyat fiyat-1"]/a')->length;
//	$pages = $dom->query('//div[@id="sayfala"]/a');

	for($i = 0; $i <= 16; $i++) {
		$price = Epey::query('//div[@class="fiyatlar"]/div[@class="fiyat fiyat-' . $i . '"]/a/span[@class="urun_fiyat"]/text()');
		if($price) {
			preg_match('/(\d*\.*\d*),*\d*/mui', trim($price), $out_price);
			if(isset($out_price[1]) && !empty($out_price[1])) {
				$options['3n68sce'] = ['TRY' => str_replace('.', '', ($out_price[1]))];
			}
			break;
		}
	}

	// ger user rating
	$user_rating = Epey::query('//span[@class="kpuan"]'); // user ratings
	if($user_rating) {
		preg_match_all('/\d+\.?\d+\s+/', $user_rating, $output_rating);
		if(isset($output_rating[0][0]) && !empty($output_rating[0][0])) {
			$rank = trim(($output_rating[0][0]));
		}
		if(isset($output_rating[0][1]) && !empty($output_rating[0][1])) {
			$opinions = ($output_rating[0][1]);
		}
		if(isset($rank) && isset($opinions)) $options['bkaqn4m'] = [$rank => $opinions];
	}

	// get image from product

	$options['sng25fk'] = Epey::query('//ul[@class="galerik"]/li/a/img/@src');

	if(strpos($url, 'akilli-saat') === false) {
		/** get phones data */

		// get family products
		for($z = 0; $z <= 6; $z++) {
			$family_link = Epey::query('//div[@id="fiyatlar"]/div[@id="varyant"]/div/a[@class="varyant varyant1 cell"]/@href', $z);
			$family_title = Epey::query('//div[@id="fiyatlar"]/div[@id="varyant"]/div/a[@class="varyant varyant1 cell"]/@title', $z);
			if($family_link && $family_title) $options['t40i1m6'][trim(str_ireplace(['Tek Hat', 'Çift Hat', 'Cep Telefonu'], ['one line', 'double line', ''], $family_title))] = trim($family_link);
		}

		// get related products
		for($b = 0; $b <= 12; $b++) {
			$related_link = Epey::query('//div[@id="benzerler"]/div[@id="kiyas"]/div[@id="varyant"]/div[@class="row"]/a[@class="varyant varyant1 cell"]/@href', $b);
			$related_title = Epey::query('//div[@id="benzerler"]/div[@id="kiyas"]/div[@id="varyant"]/div[@class="row"]/a/span[@class="vurunfiyat cell"]/span[@class="vurun row"]/text()', $b);
			if($related_link && $related_title) $options['t5x1lef'][trim(str_ireplace(['Tek Hat', 'Çift Hat'], ['One Line', 'Double Line'], $related_title))] = trim($related_link);
		}

		// get screen size
		$screen_size = Epey::query('//strong[@class="ozellik1 tip"]/following::span[@class="cell cs1"]/span/a/text()'); // screen_size
		if($screen_size) {
			$options['1n820fz'] = explode(' ', $screen_size)[0];
		}

		// get display resolution
		$display_resolution = Epey::query('//strong[@class="ozellik3 tip"]/following::span[@class="cell cs1"]/span/a/text()');
		if($display_resolution) {
			$options['nggks18'] = explode('x', $display_resolution)[0]; // display width
			preg_match("/[0-9]+/", explode('x', $display_resolution)[1], $output_array);
			$options['j2p7bju'] = $output_array[0]; // display height
		}

		// get pixel intensity
		$pixel_intensity = Epey::query('//strong[@class="ozellik2 tip"]/following::span[1]'); // pixel_intensity
		if($pixel_intensity) {
			$options['7x8x76o'] = str_replace(['PPI'], [''], trim($pixel_intensity));
		}

		// get screen protection
		$screen_protection = Epey::query('//strong[@class="ozellik47"]/following::span[1]');
		if($screen_protection) {
			$options['59e6c9r'] = ($screen_protection);
		}

		// get display technology
		$display_technology = Epey::query('//strong[@class="ozellik4"]/following::span[1]'); // display_technology
		if($display_technology) {
			$options['xxyv5nx'] = trim($display_technology);
		}


		// get display features
		$display_info = Epey::query('//strong[@class="ozellik5"]/following::span[1]');
		if($display_info) {
			$options['alrhep0'] = Epey::check($display_info, 'Multi Touch'); // multitouch
			$options['djrp53w'] = Epey::check($display_info, 'Always-on Display'); // always on display
			$options['yq2jcrl1'] = Epey::check($display_info, 'IGZO');
			$options['y20jcrlz'] = Epey::check($display_info, '120Hz');
		}

		// get display touch
		$display_touch = Epey::query('//strong[@class="ozellik46 tip"]/following::span[1]');
		if($display_touch) {
			$options['yq2jcrla'] = Epey::translate('words', $display_touch);
		}

		// get display number of colors
		$display_colors = Epey::query('//strong[@class="ozellik45 tip"]/following::span[1]');
		if($display_colors) {
			$options['8vzzca7'] = str_replace(['Milyon', ' '], ['M', ''], $display_colors); // number of colors
		}

		// get display rate
		$display_rate = Epey::query('//strong[@class="ozellik886"]/following::span[1]'); // body_rate
		if($display_rate) {
			$display_ratio = preg_match('/\d+/ui', $display_rate, $output_array);
			if(isset($output_array[0]) && ( !empty($output_array[0]))) {
				$options['zq2ektp'] = trim($output_array[0]);
			}
		}

		// get battery capacity
		$battery_capacity = Epey::query('//strong[@class="ozellik7 tip"]/following::span[1]'); // battery_capacity
		if($battery_capacity) {
			$options['wbswcml'] = str_replace('mAh', '', trim($battery_capacity));
		}

		// get speech time (3g)
		$speech_time = Epey::query('//strong[@class="ozellik85"]/following::span[1]');// speech_time
		if($speech_time) {
			preg_match('/\d+/ui', $speech_time, $output_array);
			if(isset($output_array[0]) && ( !empty($output_array[0]))) {
				$options['zuqqmwi3'] = $output_array[0];
			}
		}

		// music_time
		$music_time = Epey::query('//strong[@class="ozellik89"]/following::span[1]'); // music time
		if($music_time) {
			preg_match('/\d+/ui', $music_time, $out_speech_time);
			if(isset($out_speech_time[0]) && !empty($out_speech_time[0])) $options['6ojsm29w'] = $out_speech_time[0];
		}

		// video time
		$video_time = Epey::query('//strong[@class="ozellik90"]/following::span[1]');
		if($video_time) {
			preg_match('/\d+/ui', $video_time, $out_video_time);
			if(isset($out_video_time[0]) && !empty($out_video_time[0])) $options['6ojsm29z'] = $out_video_time[0];
		}

		// net_time usage
		$net_time_usage = Epey::query('//strong[@class="ozellik212"]/following::span[1]');
		if($net_time_usage) {
			preg_match('/\d+/ui', $net_time_usage, $out_net_time);
			if(isset($out_net_time[0]) && !empty($out_net_time[0])) $options['6ojsm290'] = $out_net_time[0];
		}

		// net time usage 4g
		$net_time_usage_4g = Epey::query('//strong[@class="ozellik314"]/following::span[1]');
		if($net_time_usage_4g) {
			preg_match('/\d+/ui', $net_time_usage_4g, $out_net_time_4g);
			if(isset($out_net_time_4g[0]) && !empty($out_net_time_4g[0])) $options['6ojs1290'] = $out_net_time_4g[0];
		}

		// HSPA
		$hspa = Epey::query('//strong[@class="ozellik322 tip"]/following::span[1]');
		if($hspa) $options['uointeq4'] = Epey::isCodecExists($hspa, 'HSPA');

		// get sec camera resol
		$sec_camera_resol = Epey::query('//strong[@class="ozellik877"]/following::span[1]');
		if($sec_camera_resol) $options['lggn117'] = $sec_camera_resol;

		// get front camera size sensor
		$front_cam_sensor_size = Epey::query('//strong[@class="ozellik890"]/following::span[1]');
		if($front_cam_sensor_size) $options['lggnzaa'] = str_ireplace('İnç', 'Inc', $front_cam_sensor_size);

		// get internal storage format
		$storage_info = Epey::query('//strong[@class="ozellik1768"]/following::span[1]');
		if($storage_info) $options['c8xo611'] = $storage_info;

		// optical stabilizer
		$optical_stabilizer = Epey::query('//strong[@class="ozellik2592"]/following::span[1]');
		if($optical_stabilizer) $options['lggn066'] = Epey::isCodecExists($optical_stabilizer, 'Var');

		// get antutu score v7
		$antutu_score_7 = Epey::query('//strong[@class="ozellik4591"]/following::span[1]'); // antutu_score
		if($antutu_score_7) {
			$options['q85w6qm7'] = trim(explode(' ', $antutu_score_7)[0]);
		}

		// get internet usage(wi-fi)
		$internet_usage = Epey::query('//strong[@class="ozellik213"]/following::span[1]');
		if($internet_usage) {
			preg_match('/\d+/ui', $internet_usage, $output_array);
			if(isset($output_array[0]) && ( !empty($output_array[0]))) {
				$options['qwkph15b'] = $output_array[0];
			}
		}

		// get charging info
		$charging_info = Epey::query('//strong[@class="ozellik81"]/following::span[1]');
		if($charging_info) {
			$options['xc2onhy'] = Epey::check($charging_info, 'ablosuz'); // wireless charging
		}

		// get battery tech
		$battery_tech = Epey::query('//strong[@class="ozellik331"]/following::span[1]'); // battery_tech
		if($battery_tech) {
			$battery_type = preg_replace('/\(.+\)/uim', "", $battery_tech);
			$options['63r9r99'] = trim($battery_type);
		}

		// get replacement battery
		$battery_replace = Epey::query('//strong[@class="ozellik102 tip"]/following::span[2]'); // replacement_battery
		if($battery_replace) {
			$options['c220c9j'] = Epey::answer($battery_replace);
		}

		// get battery fast charging
		$fast_charging = Epey::query('//strong[@class="ozellik880"]/following::span[1]');
		if($fast_charging) {
			$options['27s8wl4'] = Epey::check($fast_charging, 'Hızlı'); // fast charging
			// get version of quick charge
			$version_info = Epey::check($fast_charging, 'Qualcomm Quick Charge');
			if($version_info == '+') {
				if(preg_match('/\d+\.*\d*/mui', $version_info, $out_arr_version_charge)) $options['27s8wl5'] = trim($out_arr_version_charge[0]);
			}
		}

		// get battery time fast charging
		$time_fast_charging = Epey::query('//strong[@class="ozellik880"]/following::span[3]'); // time fast charging
		if($time_fast_charging) {
			preg_match('/([0-9]+).+(%[0-9]+|[0-9]+\%)/', $time_fast_charging, $out_charging);
			if(isset($out_charging) && !empty($out_charging)) {
				$percent = preg_replace('/(%)(\d+)/uim', "$2", $out_charging[2]);
				$minutes = $out_charging[1];
				$other_minutes = ($minutes * (100 - $percent)) / $percent;
				$full_battery_charging = $minutes + $other_minutes;
				$options['le00i0c'] = str_replace('1.00', '1', number_format($full_battery_charging / 60, 1)); // time fast charging
			}
		}

		// get camera resolution
		$camera_resol = Epey::query('//strong[@class="ozellik19 tip"]/following::span[1]'); // camera_resolution
		if($camera_resol) {
			preg_match('/\d+/ui', $camera_resol, $out_camera);
			if(isset($out_camera[0]) && !empty($out_camera[0])) {
				$options['lggn0m2'] = trim($out_camera[0]);
			}
		}

		// get camera features
		$cam_features = Epey::query('//strong[@class="ozellik69"]/following::span[@class="cell cs1"]');
		if($cam_features) {
			$options['lggn001'] = Epey::check($cam_features, 'Live Photos');
			$options['lggn002'] = Epey::check($cam_features, 'HDR');
			$options['gn4gn6xk'] = Epey::check($cam_features, 'Otomatik'); // autofocus
			$options['lggn003'] = Epey::check($cam_features, 'Karma Kızılötesi (Hybrid IR) Filtresi'); // hybrid IR filter
			$options['lggn004'] = Epey::check($cam_features, 'Sesli komut'); // voice command for camera
			$options['c4awfagk'] = Epey::check($cam_features, 'Yüz Algılama'); // face id
			$options['lggn005'] = Epey::check($cam_features, 'Elle Odaklama'); // manual focus
			$options['lggn006'] = Epey::check($cam_features, 'Coğrafi konum etiketleme'); // geo tag
			$options['lggn007'] = Epey::check($cam_features, 'BSI'); // bsi
			$options['lggn008'] = Epey::check($cam_features, 'Depth of Field (DOF)'); // DOF
			$options['lggn009'] = Epey::check($cam_features, 'Safir Kristal Objektif Kapağı'); // crystal cap lens
			$options['lggn010'] = Epey::check($cam_features, 'Seri Çekim (Burst) Modu'); // burst mode
			$options['lggn011'] = Epey::check($cam_features, 'Zamanlayıcı'); // camera timer
		}

		// get flash alarm
		$flash_alarm = Epey::query('//strong[@class="ozellik72"]/following::span[2]'); // flash_alarm_1
		if($flash_alarm) {
			// led flash
			$arr_led_flash = [
				'Tek Tonlu Flaş' => 'Single-Tone Flash',
				'Yok'            => 'No',
				'Çift Tonlu'     => 'Dual Tone',
				'Halka'          => 'Ring'
			];
			$led_flash_info = str_replace(array_keys($arr_led_flash), array_values($arr_led_flash), trim($flash_alarm));
			$options['jefetfa2'] = Epey::check($led_flash_info, 'LED');
			if($options['jefetfa2'] == '+') {
				$options['zrru3eek'] = '+';
			}
		}

		// get aperture clear
		$aperture_clear = Epey::query('//strong[@class="ozellik73 tip"]/following::span[1]');
		if($aperture_clear) {
			$options['lggn0m3'] = $aperture_clear;
		} // aperture

		// get optical zoom
		$optical_zoom = Epey::query('//strong[@class="ozellik107 tip"]/following::span[1]');
		if($optical_zoom) {
			$options['lggn0m4'] = str_replace(' ', '', trim($optical_zoom));
		}

		// get video recording resoluton 4k
		$video_rec_resol = Epey::query('//strong[@class="ozellik71 tip"]/following::span[1]'); // video_resolution
		if($video_rec_resol) {
			preg_match('/\d+p/mui', $video_rec_resol, $out_video_rec);
			if(isset($out_video_rec[0]) && !empty($out_video_rec[0])) {
				$options['t9q0h7hd'] = trim($out_video_rec[0]);
			}
		}

		// get camera sensor size
		$camera_sensor_size = Epey::query('//strong[@class="ozellik74 tip"]/following::span[1]');
		if($camera_sensor_size) {
			$options['lggnaaa'] = str_ireplace('İnç', '', $camera_sensor_size);
		}

		// get video fps value
		$fps_value = Epey::query('//strong[@class="ozellik70 tip"]/following::span[1]'); // video_fps_value
		if($fps_value) {
			$options['lggn0m5'] = str_replace('fps', '', trim($fps_value));
		}

		// get video recording features
		$video_rec_features = Epey::query('//strong[@class="ozellik216"]/following::span[@class="cell cs1"]');
		if($video_rec_resol) {
			$options['lggn012'] = Epey::isCodecExists($video_rec_resol, 'OIS'); // optical image stabilizer
			$options['lggn013'] = Epey::check($video_rec_resol, 'Time-lapse Video Kayıt'); // time lapse video rec.
			$options['lggn014'] = Epey::isCodecExists($video_rec_resol, 'Video Yakınlaştırma'); // video zoom
			$options['lggn015'] = Epey::isCodecExists($video_rec_resol, 'Slow motion video'); // slow motion video
		}

		// get video recording options
		$video_rec_options = Epey::query('//strong[@class="ozellik793"]/following::span');
		if($video_rec_options) {
			$video_rec_info = trim($video_rec_options);
			preg_match_all('/\d+p\s+@\s+\d+fps/ui', $video_rec_info, $out_video);
			if(isset($out_video[0]) && !empty($out_video[0])) {
				$options['lggn016'] = array_map(function ($item) {
					return str_replace(' ', '', $item);
				}, $out_video[0]);
			}
		}

		// get second rear camera
		$second_rear_camera = Epey::query('//strong[@class="ozellik876"]/following::span[1]');
		if($second_rear_camera) {
			$options['lggn017'] = Epey::answer($second_rear_camera);
		}

		// get second rear camera diaphragm
		$second_rear_camera_dia = Epey::query('//strong[@class="ozellik878"]/following::span[1]');
		if($second_rear_camera_dia) {
			$options['lggn018'] = trim($second_rear_camera_dia);
		}

		// get second rear camera features
		$second_rear_camera_ois = Epey::query('//strong[@class="ozellik879"]/following::span[2]');
		if($second_rear_camera_ois) {
			$options['lggn019'] = Epey::check($second_rear_camera_ois, 'OIS');
		}

		// get second rear camera zoom
		$second_rear_camera_zoom = Epey::query('//strong[@class="ozellik879"]/following::span[3]');
		if($second_rear_camera_zoom) {
			if(Epey::check($second_rear_camera_zoom, 'Optik Zoom') === '+') {
				preg_match('/\d+/ui', $second_rear_camera_zoom, $output_array);
				if(isset($output_array[0]) && ( !empty($output_array[0]))) {
					$options['lggn020'] = $output_array[0];
				}
			}
		}

		// get front camera resolution
		$cam_resolution = Epey::query('//strong[@class="ozellik18 tip"]/following::span[1]'); // front_camera_resolution
		if($cam_resolution) {
			preg_match('/\d+/ui', $cam_resolution, $output_array);
			if(isset($output_array[0]) && !empty($output_array[0])) {
				$options['06wzu4yz'] = trim($output_array[0]);
			}
		}

		// get front camera video resolution
		$cam_video_res = Epey::query('//strong[@class="ozellik27 tip"]/following::span[1]'); // front_cam_video_resolution;
		if($cam_video_res) {
			$options['8041luk6'] = trim($cam_video_res);
		}


		// get front camera fps value
		$cam_fps_value = Epey::query('//strong[@class="ozellik32 tip"]/following::span[1]');
		if($cam_fps_value) {
			$options['lggn0m6'] = str_replace('fps', '', trim($cam_fps_value));
		}

		// get front camera aperture
		$cam_aperture = Epey::query('//strong[@class="ozellik337 tip"]/following::span[1]');
		if($cam_aperture) {
			$options['lggn0m7'] = trim($cam_aperture);
		}

		// get from camera capabilities
		$cam_capabilities_1 = Epey::query('//strong[@class="ozellik31"]/following::span[@class="cell cs1"]');
		if($cam_capabilities_1) {
			$options['lggn021'] = Epey::check($cam_capabilities_1, 'Animoji');
			$options['lggn022'] = Epey::check($cam_capabilities_1, 'HDR');
			$options['lggn023'] = Epey::check($cam_capabilities_1, 'Arka Arkaya Çekim Modu');
			$options['lggn024'] = Epey::check($cam_capabilities_1, 'BSI');
			$options['lggn025'] = Epey::check($cam_capabilities_1, 'Live Photos');
			$options['lggn026'] = Epey::check($cam_capabilities_1, 'Portre Modu');
			$options['lggn027'] = Epey::check($cam_capabilities_1, 'Pozlama Kontrolü');
			$options['lggn028'] = Epey::check($cam_capabilities_1, 'Zamanlayıcı');
		}

		// get 2g frequencies
		$freg_2g = Epey::query('//strong[@class="ozellik41"]/following::span[1]');// network_2g_freq
		if($freg_2g) {
			$freg_2g = str_replace(' MHz', ',', trim(strip_tags($freg_2g)));
			$freq_info = explode(',', trim($freg_2g, ','));
			$options['es77mka'] = array_map(function ($item) {
				return trim(strip_tags($item));
			}, $freq_info);
		}

		// get 2g technology
		$technology_2g = Epey::query('//strong[@class="ozellik56 tip"]/following::span[1]'); // gsm
		if($technology_2g) {
			$options['o3kmrtz'] = Epey::check($technology_2g, 'EDGE');
			$options['6me3pwq'] = Epey::check($technology_2g, 'GSM');
			$options['de60w8u'] = Epey::check($technology_2g, 'GPRS');
		}

		// get 3g freq
		$freg_3g = Epey::query('//strong[@class="ozellik42"]/following::span[1]');// network_3g_freq;
		if($freg_3g) {
			$freg_3g = str_replace('MHz', 'MHz,', trim($freg_3g));
			preg_match_all('/\d{3,}/uim', $freg_3g, $out_3g);
			if(isset($out_3g[0]) && !empty($out_3g[0])) {
				$final_3g = '';
				for($i = 0; $i <= 6; $i++) {
					if(isset($out_3g[0][$i])) $final_3g .= ', ' . $out_3g[0][$i];
				}
			}
			if(isset($final_3g)) {
				$final_3g = explode(',', trim($final_3g, ','));
				$final_3g = array_map(function ($item) {
					return strip_tags(trim($item));
				}, $final_3g);
				$options['lfy3yhr'] = $final_3g;
			}
		}

		// get 3g download
		$speed_3g_download = Epey::query('//strong[@class="ozellik39"]/following::span[1]');
		if($speed_3g_download) {
			$options['p4zld7l'] = str_replace('Mbps', '', trim($speed_3g_download));
		}

		// get 3g upload
		$upload_3g = Epey::query('//strong[@class="ozellik40"]/following::span[1]');
		if($upload_3g) {
			$options['p4zld8l'] = str_replace('Mbps', '', trim($upload_3g));
		}

		// get 4g freq
		$four_g = Epey::query('//strong[@class="ozellik43 tip"]/following::span[1]'); // 4g
		if($four_g) {
			$freq_arr = preg_grep('/\d+\s+\(band\s+\d+\)\s+MHz/mui', explode("\n", $four_g));
			if( !empty($freq_arr)) {
				$options['w77yz4j'] = '';
				foreach($freq_arr as $item) {
					preg_match('/(\d+)\s+\(band\s+(\d+)\)/mui', $item, $out_item);
					if(isset($out_item[1]) && !empty($out_item[1])) {
						$options['w77yz4j'] .= $out_item[2] . "($out_item[1]),";
					}
				}
				$options['w77yz4j'] = explode(',', trim($options['w77yz4j'], ','));
			}
		}

		// get 4g download
		$download_4g = Epey::query('//strong[@class="ozellik52"]/following::span[1]');
		if($download_4g) {
			$options['p4zld9l'] = str_replace('Mbps', '', trim($download_4g));
		}

		// get 4g upload
		$upload_4g = Epey::query('//strong[@class="ozellik53"]/following::span[1]');
		if($upload_4g) {
			$options['p4zld10l'] = str_replace('Mbps', '', trim($upload_4g));
		}

		// get 4g technology
		$lte_info = Epey::query('//strong[@class="ozellik55 tip"]/following::span[1]');
		if($lte_info) {
			$options['k6ddojx'] = Epey::check($lte_info, 'LTE');
		} // LTE

		// get 4g features
		$network_4g_feat = Epey::query('//strong[@class="ozellik1055 tip"]/following::span[1]');
		if($network_4g_feat) {
			$options['p4zld10lw'] = Epey::check($network_4g_feat, 'VoLTE (Voice over LTE)'); // voLTE
		}

		// get support 4.5g
		$support_45g = Epey::query('//strong[@class="ozellik1737"]/following::span[1]');
		if($support_45g) {
			$options['p4zld1l1'] = Epey::answer(trim($support_45g));
		}

		// get chipset
		$chipset = Epey::query('//strong[@class="ozellik15 tip"]/following::span[1]'); // chipset
		if($chipset) {
			$chipset_info = Epey::translate('words', $chipset);
			$options['dkg7n4e'] = str_replace('Â','',$chipset_info);
		}

		// get main CPU
		$main_cpu = Epey::query('//strong[@class="ozellik28 tip"]/following::span[1]'); // main_cpu
		if($main_cpu) {
			preg_match('/GHz\s+(.+)/mui', $main_cpu, $out_cpu);
			if(isset($out_cpu[1]) && !empty($out_cpu[1])) {
				$options['y5xo6x4'] = str_replace('ARM', '', trim($out_cpu[1]));
			}
		}

		// get cpu frequency
		$cpu_freq = Epey::query('//strong[@class="ozellik11 tip"]/following::span[1]');
		if($cpu_freq) {
			$options['y5xo6x6'] = str_replace(['GHz'], [''], trim($cpu_freq));
		}

		// get cpu core
		$cpu_core = Epey::query('//strong[@class="ozellik12 tip"]/following::span[1]');
		if($cpu_core) {
			$arr_cpu_core = ['Çekirdek' => ''];
			$options['y5xo6x5'] = str_replace(array_keys($arr_cpu_core), array_values($arr_cpu_core), trim($cpu_core));
		}

		// get processor architecture
		$cpu_archit = Epey::query('//strong[@class="ozellik347"]/following::span[1]');
		if($cpu_archit) {
			preg_match('/\(*\d{2}-bit\)*/', $cpu_archit, $out_proc);
			if(isset($out_proc[0]) && !empty($out_proc[0])) {
				$options['y4xo655'] = str_replace(['(', ')'], '', $out_proc[0]);
				$options['y4xo6x6'] = trim(str_replace($out_proc[0], '', $cpu_archit));
			} else {
				$options['y4xo6x6'] = trim($cpu_archit);
			}
		}

		// get first auxiliary processor
		$first_aux_proc = Epey::query('//strong[@class="ozellik29"]/following::span[1]');
		if($first_aux_proc) {
			$options['y5xo6x7'] = str_replace('Hareket İşlemcisi', '', trim($first_aux_proc));
		}

		// get cpu production technology
		$cpu_prod_tech = Epey::query('//strong[@class="ozellik2033 tip"]/following::span[1]');
		if($cpu_prod_tech) {
			$options['y3xo6x6'] = trim($cpu_prod_tech);
		}

		// get gpu info
		$gpu_info = Epey::query('//strong[@class="ozellik17 tip"]/following::span[1]');
		if($gpu_info) {
			$options['4kzmswo'] = trim(str_replace(['????'], [''],$gpu_info));
		}

		// get antutu score
		$antutu_score = Epey::query('//strong[@class="ozellik1672"]/following::span[1]'); // antutu_score
		if($antutu_score) {
			$options['q85w6qmq'] = trim(explode(' ', $antutu_score)[0]);
		}

		// get memory RAM
		$memory_ram = Epey::query('//strong[@class="ozellik14 tip"]/following::span[1]');
		if($memory_ram) {
			$options['ej4wq1y'] = str_replace(' ', '', trim($memory_ram));
		}

		// get max card memory
		$max_memory_card = Epey::query('//strong[@class="ozellik22 tip"]/following::span[1]');
		if($max_memory_card) $options['yz90cwq'] = trim($max_memory_card);

		// get memory ram type
		$ram_type = Epey::query('//strong[@class="ozellik332"]/following::span[1]');
		if($ram_type) {
			$options['z3xo6x6'] = str_replace('x', '', trim($ram_type));
		}

		// get memory ram freq
		$ram_freq = Epey::query('//strong[@class="ozellik334"]/following::span[1]');
		if($ram_freq) $options['z3xo6x7'] = trim($ram_freq);

		// get internal storage
		$internal_storage = Epey::query('//strong[@class="ozellik21 tip"]/following::span[1]');
		if($internal_storage) {
			$options['c8xo6x6'] = str_replace(' ', '', trim($internal_storage));
		}

		// get memory card support
		$card_support = Epey::query('//strong[@class="ozellik1557 tip"]/following::span[1]'); // memory_card_support
		if($card_support) {
			$options['yz90cwl'] = Epey::answer($card_support);
		}

		// get other memory options
		$memory_options = Epey::query('//strong[@class="ozellik105 tip"]/following::span[1]');
		if($memory_options) {
			$options['a3xo6x6'] = str_replace(['Depolama seçeneği var'], [''], trim($memory_options));
		}

		// get length
		$length = Epey::query('//strong[@class="ozellik26 tip"]/following::span[1]'); // length
		if($length) {
			$options['qorav98'] = str_replace('mm', '', trim($length));
		}

		// get width
		$width = Epey::query('//strong[@class="ozellik8 tip"]/following::span[1]'); // width
		if($width) {
			$options['65ihv16'] = str_replace('mm', '', trim($width));
		}

		// get also known name
		$aliases_parent_node = $dom->query('//strong[@class="ozellik116 tip"]/following::span[1]')->item(0)->childNodes ?? null;
		if($aliases_parent_node) {
			foreach($aliases_parent_node as $node) {
				$alias = $node->textContent ?? null;
				if(trim($alias)) $options['ywkpha1b'][] = Epey::translate('features', $alias);
			}
			$options['ywkpha1b'] = array_map('trim', $options['ywkpha1b']);
		}

		// get third camera
		$camera_3 = Epey::query('//strong[@class="ozellik2318"]/following::span[1]'); // battery_capacity
		if($camera_3) {
			$options['06wzu5yz'] = Epey::answer($camera_3);
		}

		// get third camera res
		$camera_3_res = Epey::query('//strong[@class="ozellik4659"]/following::span[1]'); // battery_capacity
		if($camera_3_res) {
			$options['06wzu1yz'] = str_replace('MP', '', $camera_3_res);
		}

		// get third camera features
		for($i = 0; $i <= 5; $i++) {
			$camera_3_feat = Epey::query('//strong[@class="ozellik2319"]/following::span[1]/span', $i);
			if($camera_3_feat) {
				$options['06wz11yz'][] = str_replace(['Optik','Derinlik','Algılama','Sensör'], ['Optic','Deep','Detection','Sensor'], trim($camera_3_feat));
			}
		}

		// get second auxiliary processor
		$sec_aux_proc = Epey::query('//strong[@class="ozellik1607"]/following::span[1]');
		if($sec_aux_proc) {
			$options['y5xo6x8'] = Epey::translate('words', $sec_aux_proc);
		}

		$thickness = Epey::query('//strong[@class="ozellik10 tip"]/following::span[1]'); // thick
		if($thickness) {
			$options['vbryix7'] = str_replace('mm', '', trim($thickness));
		}

		// get weight
		$weight = Epey::query('//strong[@class="ozellik9"]/following::span[1]');// weight
		if($weight) {
			$options['uanzwi8'] = str_ireplace('Gram', '', trim($weight));
		}

		// get color's
		$colors = Epey::query('//strong[@class="ozellik80 tip"]/following::span[1]'); // colors
		if($colors) {
			$color_info = trim(Epey::translate('words', $colors));
			$color_info = str_replace(['ZirocÃ±an','Â'],[''],$color_info);
			$color_info = preg_replace('/\s/ui', ".", $color_info);
			$options['ywkph10b'] = explode('...', $color_info);
		}

		// get cover materials
		$cover_materials = Epey::query('//strong[@class="ozellik1320"]/following::span[1]');
		if($cover_materials) {
			$options['3bjbzry'] = Epey::translate('materials', $cover_materials);
		}

		// get frame materials
		$frame_materials = Epey::query('//strong[@class="ozellik1321"]/following::span[1]');
		if($frame_materials) {
			$options['3bjbzra'] = str_ireplace([' )', '+'], [')', ';'], Epey::translate('materials', $frame_materials));
		}

		// get OS
		$platform_os = Epey::query('//strong[@class="ozellik24 tip"]/following::span[1]');
		if($platform_os) {
			$options['0v8w2sz'] = Epey::check($platform_os, 'iOS'); // iOS
			$options['a5sj3l2'] = Epey::check($platform_os, 'indow'); // windows
			$options['vxq3g1f'] = Epey::check($platform_os, 'lackBerry'); // blackberry
			$options['llulwif'] = Epey::check($platform_os, 'ndroid'); // android
		}

		// get os version
		$version_os = Epey::query('//strong[@class="ozellik25"]/following::span[1]');
		if($version_os) {
			$options['ui65qcn'] = trim($version_os);
		}
		// get available ver.
		$available_os = Epey::query('//strong[@class="ozellik34 tip"]/following::span[1]');
		if($available_os) {
			$options['ui71qcn'] = trim($available_os);
		}

		// get radio
		$radio_info = Epey::query('//strong[@class="ozellik76"]/following::span[1]');
		if($radio_info) {
			$options['tix99ot'] = Epey::answer($radio_info);
		} // radio

		// get speaker features
		$speaker_info = Epey::query('//strong[@class="ozellik318"]/following::span[1]');
		if($speaker_info) {
			$options['yq2jcrl2'] = Epey::isCodecExists($speaker_info, 'Çift Hoparlör'); // dual speaker
		}

		// get audio out
		$audio_out = Epey::query('//strong[@class="ozellik324 tip"]/following::span[1]');
		if($audio_out) {
			$options['yh7xh36'] = trim($audio_out);
			$options['yh7xh3q'] = Epey::check($audio_out, '3.5');
		}

		// get wi-fi channels
		$wifi_channels = Epey::query('//strong[@class="ozellik36 tip"]/following::span[1]'); // wifi_channels
		if($wifi_channels) {
			$options['2pinrcv'] = trim($wifi_channels);
		}

		// get wi-fi features
		$wifi_features = Epey::query('//strong[@class="ozellik59"]/following::span');
		if($wifi_features) {
			$options['p4zld1l5'] = Epey::check($wifi_features, 'MIMO');
			$options['p4zld1l2'] = Epey::check($wifi_features, 'Dual-Band');
			$options['p4zld1l3'] = Epey::check($wifi_features, 'Hotspot');
			$options['p4zld1l4'] = Epey::check($wifi_features, 'MiraCast');
			$options['p4zld1l6'] = Epey::check($wifi_features, 'Wi-Fi Direct');
			$options['p4zld1l8'] = Epey::check($wifi_features, 'VoWiFi');
			$options['p4zld1l9'] = Epey::check($wifi_features, 'HT80');
			$options['p4zld111'] = Epey::check($wifi_features, 'VHT80');
			$options['p4zld120'] = Epey::check($wifi_features, '1024QAM');
		}

		// get nfc
		$nfc_info = Epey::query('//strong[@class="ozellik61 tip"]/following::span[1]'); // nfc
		if($nfc_info) {
			$options['9ee4viy'] = Epey::answer($nfc_info);
		}

		// get nfc features
		$nfc_feat = Epey::query('//strong[@class="ozellik325 tip"]/following::span[1]'); // nfc
		if($nfc_feat) {
			$data['9ee4viz'] = Epey::isCodecExists($nfc_feat, 'eSE');
			$data['9ee4vid'] = Epey::isCodecExists($nfc_feat, 'UICC');
		}

		// get bluetooth ver.
		$bluetooth_version = Epey::query('//strong[@class="ozellik48 tip"]/following::span[1]'); // bluetooth_version
		if($bluetooth_version) {
			$options['p4zld5l'] = trim($bluetooth_version);
		} else {
			$options['p4zld5l'] = '-';
		}

		// get bluetooth features
		$bluetooth_hid = Epey::query('//strong[@class="ozellik49 tip"]/following::span[1]');
		if($bluetooth_version) $options['p4zld51'] = Epey::check($bluetooth_hid, 'HID');

		// get infrared
		$is_infrared = Epey::query('//strong[@class="ozellik62 tip"]/following::span[1]');
		if($is_infrared) {
			$options['hwst1n7'] = Epey::answer($is_infrared);
		} // infrared

		// get navigation features
		$glonass = Epey::query('//strong[@class="ozellik79 tip"]/following::span[1]');
		if($glonass) {
			$options['39ji8mm'] = Epey::isCodecExists($glonass, 'GLONASS'); // glonass
			$options['yfvshn2'] = Epey::isCodecExists($glonass, 'GPS'); // gps
			$options['yfvshn2'] = Epey::isCodecExists($glonass, 'Galileo');
			$options['x1xgsb1'] = Epey::isCodecExists($glonass, 'BDS');
			$options['x1xgsbl'] = Epey::isCodecExists($glonass, 'A-GPS');
		}

		// get water resistance
		$water_resistance = Epey::query('//strong[@class="ozellik329 tip"]/following::span[1]');
		if($water_resistance) {
			$options['cxeplx1'] = Epey::answer(trim($water_resistance));
		}

		// get video formats
		$video_formats = Epey::query('//strong[@class="ozellik82 tip"]/following::span[1]');
		if($video_formats) {
			$options['x055z520'] = Epey::isCodecExists($video_formats, 'MP4');
			$options['8j6be1ko'] = Epey::isCodecExists($video_formats, 'DivX');
			$options['xc4bb9kc'] = Epey::isCodecExists($video_formats, 'XviD');
			$options['crrbpcar'] = Epey::isCodecExists($video_formats, 'H.265');
			$options['crrbpca1'] = Epey::isCodecExists($video_formats, 'H.264');
			$options['xd942mit'] = Epey::isCodecExists($video_formats, 'WMV');
			$options['f3n8nq17'] = Epey::isCodecExists($video_formats, 'ASF');
			$options['f3n8nq18'] = Epey::isCodecExists($video_formats, 'FLV');
			$options['f3n8nq19'] = Epey::isCodecExists($video_formats, 'M4V');
			$options['f3n8nq20'] = Epey::isCodecExists($video_formats, 'WEBM');
			$options['f3n8nq21'] = Epey::isCodecExists($video_formats, '3G2');
			$options['f3n8nq22'] = Epey::isCodecExists($video_formats, '3GP');
			$options['f3n8nq24'] = Epey::isCodecExists($video_formats, 'VP8');
			$options['f3n8nq25'] = Epey::isCodecExists($video_formats, 'VP9');
		}

		// get audio formats
		$audio_formats = Epey::query('//strong[@class="ozellik83 tip"]/following::span[1]');
		if($audio_formats) {
			$options['f7lsmmw9'] = Epey::isCodecExists($audio_formats, 'MP3');
			$options['am1zgml8'] = Epey::isCodecExists($audio_formats, 'WAV');
			$options['t1inmosa'] = Epey::isCodecExists($audio_formats, 'FLAC');
			$options['lnk8dr8h'] = Epey::isCodecExists($audio_formats, 'eAAC');
			$options['7zq7neoh'] = Epey::isCodecExists($audio_formats, 'WMA');
			$options['tfuq45ng'] = Epey::isCodecExists($audio_formats, 'AAX');
			$options['f3n8nqp4'] = Epey::isCodecExists($audio_formats, 'AIFF');
			$options['f3n8nqp5'] = Epey::isCodecExists($audio_formats, 'AWB');
			$options['f3n8nqp6'] = Epey::isCodecExists($audio_formats, 'DFF');
			$options['f3n8nqp7'] = Epey::isCodecExists($audio_formats, 'IMY');
			$options['f3n8nqp8'] = Epey::isCodecExists($audio_formats, 'RTX');
			$options['f3n8nqp9'] = Epey::isCodecExists($audio_formats, 'OGA');
			$options['f3n8nq11'] = Epey::isCodecExists($audio_formats, 'OTA');
			$options['f3n8nq10'] = Epey::isCodecExists($audio_formats, 'MXMF');
			$options['f3n8nq13'] = Epey::isCodecExists($audio_formats, 'AMR');
			$options['f3n8nq14'] = Epey::isCodecExists($audio_formats, 'APE');
			$options['f3n8nq15'] = Epey::isCodecExists($audio_formats, 'DSF');
			$options['f3n8nq16'] = Epey::isCodecExists($audio_formats, 'OGG');
			$options['f3n8nq23'] = Epey::isCodecExists($audio_formats, 'PCM');
			$options['f3n8nq26'] = Epey::isCodecExists($audio_formats, 'OPUS');
			$options['f3n8nq27'] = Epey::isCodecExists($audio_formats, 'RTTTL');
			$options['f3n8nq28'] = Epey::isCodecExists($audio_formats, 'Vorbis');
			$options['f3n8nq29'] = Epey::isCodecExists($audio_formats, '3GA');
		}

		// get water resistance level
		$water_resistance_level = Epey::query('//strong[@class="ozellik114 tip"]/following::span[1]'); // water resistant standart
		if($water_resistance_level) {
			$arr_wt_resist = [
				'Sadece Sıçramalara Karşı' => 'IPX4',
				'Yok'                      => '-'
			];
			$options['cxeplx1'] = str_ireplace(array_keys($arr_wt_resist), array_values($arr_wt_resist), $water_resistance_level);
		}

		// get dust resistance
		$dust_resistance = Epey::query('//strong[@class="ozellik330 tip"]/following::span[1]');
		if($dust_resistance) {
			$options['cxeplx2'] = Epey::answer(trim($dust_resistance));
		}

		// get resistance dust level
		$resistance_dust_info = Epey::query('//strong[@class="ozellik113 tip"]/following::span[1]'); // resistance_level
		if($resistance_dust_info) {
			$options['cxeplx2'] = trim($resistance_dust_info);
		}

		// get 3g video call
		$video_call_3g = Epey::query('//strong[@class="ozellik1708 tip"]/following::span[1]');
		if($video_call_3g) {
			$options['p4zld1lk'] = Epey::answer(trim($video_call_3g));
		}

		// get video conversation
		$video_conversation = Epey::query('//strong[@class="ozellik2734 tip"]/following::span[1]');
		if($video_conversation) {
			$options['p4zld1lq'] = Epey::answer(trim($video_conversation));
		}

		// get sensors
		$feat_sensors = Epey::query('//strong[@class="ozellik75 tip"]/following::span[1]');
		if($feat_sensors) {
			$options['h1ddzrt'] = Epey::check($feat_sensors, 'İvmeölçer'); // accelerometer
			$options['ywtcejg'] = Epey::check($feat_sensors, 'Jiroskop'); // gyroskop
			$options['x399jxz'] = Epey::check($feat_sensors, 'Barometre'); // barometer
			$options['x0xgsbn'] = Epey::check($feat_sensors, 'Pusula'); // compass
			$options['h88pkmdy'] = Epey::check($feat_sensors, 'Ortam Işığı Sensörü'); // light sensor
			$options['h88pkmd1'] = Epey::check($feat_sensors, 'Yakınlık Sensörü'); // proxymiti sensor
		}

		// get fingerprint
		$finger_print = Epey::query('//strong[@class="ozellik1511 tip"]/following::span[1]');
		if($finger_print) {
			$options['rsub3l9c'] = Epey::answer($finger_print); // fingerprint
		}

		// get notification light
		$light_indicator = Epey::query('//strong[@class="ozellik111 tip"]/following::span[1]'); // notify_led
		if($light_indicator) {
			$options['xdet6dq'] = Epey::answer($light_indicator);
		}

		// get SAR value head
		$sar_head_info = Epey::query('//strong[@class="ozellik92 tip"]/following::span[1]');
		if($sar_head_info) $options['psbzu2e9'] = str_replace('W/kg (10g)', '', $sar_head_info);

		// get SAR value body
		$sar_body_info = Epey::query('//strong[@class="ozellik91 tip"]/following::span[1]');
		if($sar_body_info) $options['uuapl9gw'] = str_replace('W/kg (10g)', '', $sar_body_info);

		// number of lines
		$number_lines = Epey::query('//strong[@class="ozellik104 tip"]/following::span[1]');
		if($number_lines) {
			$line_info = (mb_stripos($number_lines, 'Tek') !== false) ? '1' : '2';
			$options['0q3ucns1'] = $line_info;
		}

		// standby 3g
		$standby_3g = Epey::query('//strong[@class="ozellik86"]/following::span[1]');
		if($standby_3g) {
			$options['6ojsm291'] = str_replace('Saat', '', $standby_3g);
		}

		//  gpu freq
		$gpu_freq = Epey::query('//strong[@class="ozellik221 tip"]/following::span[1]');
		if($gpu_freq) {
			$options['y1xo6x6'] = $gpu_freq;
		}

		//  antutu v5
		$antutu_v5 = Epey::query('//strong[@class="ozellik789"]/following::span[1]');
		if($antutu_v5) {
			$options['q85w6qma'] = trim(explode(' ', $antutu_v5)[0]);
		}

		// user interface
		$user_interface = Epey::query('//strong[@class="ozellik35 tip"]/following::span[1]');
		if($user_interface) {
			$options['a1xo6x6'] = Epey::translate('words', $user_interface);
		}

		// second front display
		$second_front_display = Epey::query('//strong[@class="ozellik1543"]/following::span[1]');
		if($second_front_display) {
			$options['aakph19b'] = Epey::answer($second_front_display);
		}

		// second front display size
		$second_front_display_size = Epey::query('//strong[@class="ozellik1544"]/following::span[1]');
		if($second_front_display_size) {
			$options['aakph19z'] = str_replace('Inch', '', $second_front_display_size);
		}

		// second front display resol
		$second_front_display_resol = Epey::query('//strong[@class="ozellik1545"]/following::span[1]');
		if($second_front_display_resol) {
			$options['aakph22z'] = str_replace('Pixel', '', $second_front_display_resol);
		}

		// second front display feat
		$second_front_display_feat = Epey::query('//strong[@class="ozellik1547"]/following::span[1]');
		if($second_front_display_feat) {
			$options['aakph23z'] = Epey::isCodecExists($second_front_display_feat, 'Always-on Display');
		}

		// stand by 4g
		$standby_4g = Epey::query('//strong[@class="ozellik88"]/following::span[1]');
		if($standby_4g) {
			$options['6ojsm293'] = str_replace('Saat', '', $standby_4g);
		}

		// talk time 4g
		$talk_time_4g = Epey::query('//strong[@class="ozellik87"]/following::span[1]');
		if($talk_time_4g) {
			$options['6ojsm292'] = str_replace('Saat', '', $talk_time_4g);
		}

		// focal length
		$focal_length = Epey::query('//strong[@class="ozellik103 tip"]/following::span[1]');
		if($focal_length) {
			$options['gn4gn6x7'] = $focal_length;
		}

		// ram channels
		$ram_channels = Epey::query('//strong[@class="ozellik333"]/following::span[1]');
		if($ram_channels) {
			$options['v3xo6x6'] = str_replace(['Çift', 'Tek', 'Dört', 'Kanal'], ['Dual', 'Single', 'Four', 'Channel'], $ram_channels);
		}

		// get available memory
		$available_memory = Epey::query('//strong[@class="ozellik822"]/following::span[1]');
		if($available_memory) {
			$options['ui65qc2'] = $available_memory;
		}

		// get thinnest point
		$thick_point = Epey::query('//strong[@class="ozellik891"]/following::span[1]');
		if($thick_point) $options['ywkphzzz'] = str_replace('mm', '', $thick_point);

		// get services and apps
		$options['awkph141'] = [];
		$a = 0;
		$all_services_parent_node = $dom->query('//strong[@class="ozellik217 tip"]/following::span[1]')->item(0)->childNodes ?? null;
		if(isset($all_services_parent_node)) {
			do {
				$all_services_info = $all_services_parent_node->item($a)->nodeValue ?? null;; // services_apps
				if(trim($all_services_info)) $options['awkph141'][] = Epey::translate('features', $all_services_info);
				$a++;
			} while($all_services_info);
			$options['awkph141'] = array_map('trim', $options['awkph141']);
		}

		// get services
		if(isset($options['awkph141']) && !empty($options['awkph141'])) {
			$str_services = implode(',', $options['awkph141']);
			$options['azxo4x7'] = Epey::isCodecExists($str_services, 'Screen Mirroring');
			$options['azxo4x8'] = Epey::isCodecExists($str_services, 'Changeable Themes');
			$options['azxo4x9'] = Epey::isCodecExists($str_services, 'AirPrint');
			$options['azxo410'] = Epey::isCodecExists($str_services, 'Spotlight Call');
			$options['azxo411'] = Epey::isCodecExists($str_services, 'MirrorLink');
			$options['azxo412'] = Epey::isCodecExists($str_services, 'Easy mode');
			$options['azxo413'] = Epey::isCodecExists($str_services, 'Samsung KNOX');
			$options['azxo414'] = Epey::isCodecExists($str_services, 'ANT+');
			$options['azxo415'] = Epey::isCodecExists($str_services, 'Dual/Multi Window');
			$options['azxo416'] = Epey::isCodecExists($str_services, 'Ultra Power Saving Mode');
			$options['azxo416'] = Epey::isCodecExists($str_services, 'Ultra Power Saving Mode');
			$options['azxo417'] = Epey::isCodecExists($str_services, 'Turn On Voice Screen Lock');
			$options['azxo418'] = Epey::isCodecExists($str_services, 'Single-Hand Use Mode');
			$options['azxo419'] = Epey::isCodecExists($str_services, 'S Pen');
			$options['azxo420'] = Epey::isCodecExists($str_services, 'Air Command');
			$options['azxo421'] = Epey::isCodecExists($str_services, 'DAC');
			$options['azxo422'] = Epey::isCodecExists($str_services, 'Hidden Mode');
			$options['azxo423'] = Epey::isCodecExists($str_services, '(AOP) Microphone');
			$options['azxo424'] = Epey::isCodecExists($str_services, 'HWA');
			$options['azxo425'] = Epey::isCodecExists($str_services, 'Voice Command');
			$options['azxo426'] = Epey::isCodecExists($str_services, 'Virtual reality');
			$options['azxo427'] = Epey::isCodecExists($str_services, 'LDAC');
			$options['wnqjx4j9'] = Epey::isCodecExists($str_services, 'Samsung Pay');
			$options['c4awfagk'] = Epey::isCodecExists($str_services, 'Face ID');
			$options['a4xo6x6'] = Epey::isCodecExists($str_services, 'AirDrop');
			$options['a4xo1x6'] = Epey::isCodecExists($str_services, 'AirPlay');
			$options['azxo1x6'] = Epey::isCodecExists($str_services, 'FaceTime');
			$options['azxo2x6'] = Epey::isCodecExists($str_services, 'iBeacon');
			$options['azxo3x6'] = Epey::isCodecExists($str_services, 'iCloud');
			$options['azxo4x6'] = Epey::isCodecExists($str_services, 'Siri');
		}

		// get package include
		$package_info = Epey::query('//strong[@class="ozellik218 tip"]/following::span[1]');
		if($package_info) {
			$options['v464xz6'] = Epey::check($package_info, 'Belgeler');
			$options['4yetljhv'] = Epey::isCodecExists($package_info, 'Çıkartma İğnesi');
			$options['vkladush'] = Epey::check($package_info, 'Kulaklık için Yedek');
			$options['2uecljhv'] = Epey::check($package_info, 'Kulaklık');
			$options['h1btbtw'] = Epey::check($package_info, 'Güç Adaptörü');
			$options['ybiwt2b'] = Epey::check($package_info, 'OTG');
		}

		// get usb 2.0
		$usb_info = Epey::query('//strong[@class="ozellik64 tip"]/following::span[1]'); // usb type
		if($usb_info) {
			$options['0arcae64'] = Epey::check($usb_info, '2.0');
			$options['p7s2uenu'] = Epey::check($usb_info, '3.0');
			$options['rmjj6m5t'] = Epey::check($usb_info, 'Type-C');
		};

		// get usb connection type
		$usb_info = Epey::query('//strong[@class="ozellik65 tip"]/following::span[1]'); // usb type
		if($usb_info) {
			$options['q8o92fk'] = Epey::translate('words', trim($usb_info));
			if(isset($options['rmjj6m5t']) && $options['rmjj6m5t'] == '-') {
				$options['rmjj6m5t'] = Epey::check($options['q8o92fk'], 'Type-C');
			}
		}

		// get usb features
		$usb_features = Epey::query('//strong[@class="ozellik66"]/following::span[1]');
		if($usb_features) {
			$options['9qsw0l7d'] = Epey::check($usb_features, 'OTG');
			$options['rmjj6m56'] = Epey::check($usb_features, 'DisplayPort');
			// get display port params
			preg_match('/DisplayPort\s+\((.+)\)/mui', $usb_features, $out_dp_params);
			if(isset($out_dp_params[1]) && !empty($out_dp_params[1])) {
				$options['rmjj6m57'] = trim($out_dp_params[1]);
			};
		}

		// get sim info
		$sim_info = Epey::query('//strong[@class="ozellik44 tip"]/following::span[1]'); // sim
		if($sim_info) {
			$sim_info = str_ireplace(['Mikro', '(4FF)', '(fFF)'], ['Micro', '', ''], trim($sim_info));
			// get nano-sim
			$options['8q7wrlul'] = Epey::check($sim_info, 'Nano');
			// get micro-sim
			$options['lawrulap'] = Epey::check($sim_info, 'Micro');
		}

		// get dual sim
		$sim_info = Epey::query('//strong[@class="ozellik326"]/following::span[1]'); // sim
		if($sim_info) {
			$options['0q3ucnsi'] = Epey::isCodecExists($sim_info, 'Dual Standby');
		}

		// get announcement date
		$announcement_date = Epey::query('//strong[@class="ozellik599"]/following::span[1]'); // announcement_date
		if($announcement_date) {
			$options['zgxvylx'] = trim($announcement_date);
		}

		// get release date
		$release_date_info = Epey::query('//strong[@class="ozellik600"]/following::span[1]'); // release_date
		if($release_date_info) {
			$options['2lbcv9f'] = trim($release_date_info);
		}


		// get type
		$options['drbmx1r'] = 1;

	} else {
		/** get smartwatch & fitness tracker data */

		// get family products
		for($z = 0; $z <= 6; $z++) {
			$family_link = Epey::query('//div[@id="fiyatlar"]/div[@id="varyant"]/div/a[@class="varyant varyant16 cell"]/@href', $z);
			$family_title = Epey::query('//div[@id="fiyatlar"]/div[@id="varyant"]/div/a[@class="varyant varyant16 cell"]/@title', $z);
			if($family_link && $family_title) $options['t40i1m6'][trim(str_ireplace(['Tek Hat', 'Çift Hat', 'Cep Telefonu'], ['one line', 'double line', ''], $family_title))] = trim($family_link);
		}

		// get related products
		for($b = 0; $b <= 12; $b++) {
			$related_link = Epey::query('//div[@id="benzerler"]/div[@id="kiyas"]/div[@id="varyant"]/div[@class="row"]/a[@class="varyant varyant16 cell"]/@href', $b);
			$related_title = Epey::query('//div[@id="benzerler"]/div[@id="kiyas"]/div[@id="varyant"]/div[@class="row"]/a/span[@class="vurunfiyat cell"]/span[@class="vurun row"]/text()', $b);
			if($related_link && $related_title) $options['t5x1lef'][trim(str_ireplace(['Tek Hat', 'Çift Hat'], ['One Line', 'Double Line'], $related_title))] = trim($related_link);
		}

		// get screen size
		$screen_size = Epey::query('//strong[@class="ozellik1117"]/following::span[@class="cell cs1"]/span/a/text()'); // screen_size
		if($screen_size) {
			$options['1n820fz'] = explode(' ', $screen_size)[0];
		}


		// get display resolution
		$display_resolution = Epey::query('//strong[@class="ozellik1116"]/following::span[@class="cell cs1"]/span/text()');
		if($display_resolution) {
			$options['nggks18'] = trim(explode('x', $display_resolution)[0]); // display width
			preg_match("/[0-9]+/", explode('x', $display_resolution)[1], $output_array);
			$options['j2p7bju'] = trim($output_array[0]); // display height
		}

		// get display number of colors
		$display_colors = Epey::query('//strong[@class="ozellik1177"]/following::span[1]');
		if($display_colors) {
			$options['8vzzca7'] = str_replace(['Renkli'], ['Multi'], explode(' ', $display_colors)[0]); // number of colors
		}

		// get ppi
		$ppi = Epey::query('//strong[@class="ozellik1124"]/following::span[1]');
		if($ppi) {
			$ppi = preg_match('/\d+/ui', $ppi, $output_array);
			if($output_array[0] && !empty($output_array[0])) {
				$options['7x8x76o'] = $output_array[0];
			}
		}

		// get display technology
		$display_technology = Epey::query('//strong[@class="ozellik1125"]/following::span[1]'); // display_technology
		if($display_technology) {
			$options['xxyv5nx'] = Epey::translate('words', $display_technology);
		}

		// get display features(touch)
		$display_info = Epey::query('//strong[@class="ozellik1126"]/following::span[1]');
		if($display_info) {
			$options['pcao0re'] = Epey::check($display_info, 'Dokunmatik'); // multitouch
		}

		// compatible brand
		$compatible_brand = Epey::query('//strong[@class="ozellik1266"]/following::span[1]');
		if($compatible_brand) $options['ywkph11b'] = trim($compatible_brand);

		// compatible devices
		$compatible_devices = Epey::query('//strong[@class="ozellik1099"]/following::span[1]');
		if($compatible_devices) $options['ywkph12b'] = trim($compatible_devices);

		// get battery capacity
		$battery_capacity = Epey::query('//strong[@class="ozellik1108"]/following::span[1]'); // battery_capacity
		if($battery_capacity) $options['wbswcml'] = str_replace('mAh', '', trim($battery_capacity));

		// get battery type
		$battery_type = Epey::query('//strong[@class="ozellik1109"]/following::span[1]'); // battery_capacity
		if($battery_type) {
			$battery_type = preg_replace('/\(.+\)/uim', "", $battery_type);
			$battery_type_info = Epey::translate('words', $battery_type);
			$options['63r9r99'] = str_replace('Li-Polymer', 'Lithium Polymer', $battery_type_info);
		};

		// get camera
		$camera = Epey::query('//strong[@class="ozellik1162"]/following::span[1]'); // battery_capacity
		if($camera) $options['lggn0m2'] = Epey::answer($camera);

		// get video
		$video = Epey::query('//strong[@class="ozellik1182"]/following::span[1]');
		if($video) $options['t9q0h7hd'] = Epey::answer($video);

		// get required apps adroid
		$required_apps = Epey::query('//strong[@class="ozellik1189"]/following::span[1]');
		if($required_apps) $options['ywkph13b'] = trim(str_replace(['Gerekli Uygulama','(Android)','Vestel Akıllı Bileklik'], ['','Vestel Smart Wristband'], $required_apps));

		// get required apps ios
		$required_apps_ios = Epey::query('//strong[@class="ozellik1230"]/following::span[1]');
		if($required_apps_ios) $options['awkph14b'] = trim(str_replace(['Gerekli Uygulama','(iOS)'], [''], $required_apps_ios));

		// average battery time life
		$average_battery_life = Epey::query('//strong[@class="ozellik1158"]/following::span[1]/span/text()');
		if($average_battery_life) {
			preg_match('/\d+/ui', $average_battery_life, $output_array);
			if(isset($output_array[0]) && !empty($output_array[0])) {
				$options['ywkph14b'] = trim($output_array[0]);
			}
		}

		// low battery life
		$low_battery_life = Epey::query('//strong[@class="ozellik1159"]/following::span[1]/span/text()');
		if($low_battery_life) {
			preg_match('/\d+/ui', $low_battery_life, $output_array);
			if(isset($output_array[0]) && !empty($output_array[0])) {
				$options['ywkph15b'] = trim($output_array[0]);
			}
		}

		// charging time
		$charging_time_battery = Epey::query('//strong[@class="ozellik1160"]/following::span[1]/span/text()');
		if($charging_time_battery) {
			if(stripos($charging_time_battery, 'akika') !== false) {
				preg_match('/\d+/ui', $charging_time_battery, $out_arr);
				if(isset($out_arr[0]) && !empty($out_arr[0])) {
					$options['le00i0c'] = number_format(trim($out_arr[0] / 60), 2);
				}
			} else {
				preg_match('/\d+/ui', $charging_time_battery, $out_arr);
				if(isset($out_arr[0]) && !empty($out_arr[0])) {
					$options['le00i0c'] = trim($out_arr[0]);
				}
			}
		}

		// length(height)
		$size = Epey::query('//strong[@class="ozellik1101"]/following::span[1]/span/text()');
		if($size) {
			$options['qorav98'] = str_replace('mm', '', trim($size));
		}

		// width
		$width = Epey::query('//strong[@class="ozellik1102"]/following::span[1]/span/text()');
		if($width) {
			$options['65ihv16'] = str_replace('mm', '', trim($width));
		}

		// length alternative
		$length_alternative = Epey::query('//strong[@class="ozellik1300"]/following::span[1]/span/text()');
		if($length_alternative) $options['qorav98'] = str_replace('mm', '', trim($length_alternative));

		// width alternative
		$width_alternative = Epey::query('//strong[@class="ozellik1301"]/following::span[1]/span/text()');
		if($width_alternative) $options['65ihv16'] = str_replace('mm', '', trim($width_alternative));

		// thick alternative
		$thick_alternative = Epey::query('//strong[@class="ozellik1302"]/following::span[1]/span/text()');
		if($thick_alternative) $options['vbryix7'] = str_replace('mm', '', trim($thick_alternative));

		// thickness
		$thickness = Epey::query('//strong[@class="ozellik1103"]/following::span[1]/span/text()');
		if($thickness) {
			$options['vbryix7'] = str_replace('mm', '', trim($thickness));
		}

		// weight
		$weight = Epey::query('//strong[@class="ozellik1104"]/following::span[1]/span/text()');
		if($weight) {
			$options['uanzwi8'] = trim(str_replace(['gr'], '', $weight));
		}

		// body weight
		$body_weight = Epey::query('//strong[@class="ozellik1179"]/following::span[1]/span/text()');
		if($body_weight) {
			$options['ywkph16b'] = trim(str_replace(['gr'], '', $body_weight));
		}

		// cord weight
		$cord_weight = Epey::query('//strong[@class="ozellik1180"]/following::span[1]/span/text()');
		if($cord_weight) {
			$options['ywkph17b'] = trim(str_replace(['gr'], '', $cord_weight));
		}

		// screen shaped
		$screen_shaped = Epey::query('//strong[@class="ozellik1120"]/following::span[1]/span/text()');
		if($screen_shaped) {
			$arr_screen_shaped = [
				'Dikdörtgen' => 'Rectangle',
				'Kare'       => 'Frame',
				'Daire'      => 'Circle'
			];
			$options['ywkph18b'] = trim(str_replace(array_keys($arr_screen_shaped), array_values($arr_screen_shaped), $screen_shaped));
		}

		// body color
		$body_colors = Epey::query('//strong[@class="ozellik1100"]/following::span[1]');
		if($body_colors) {
			$body_colors_watch = Epey::translate('words', $body_colors);
			$body_colors_watch = preg_replace('/\s/ui', ".", $body_colors_watch);
			$options['ywkph19b'] = explode('...', $body_colors_watch);
		}

		// body material
		$body_material = Epey::query('//strong[@class="ozellik1113"]/following::span[1]/span/text()');
		if($body_material) {
			$body_material_info = trim(str_replace(['Fiber Takviyeli Polimer'], ['Fiber Reinforced Polymer'], $body_material));
			$options['rt0qxrl'] = Epey::translate('words', $body_material_info);
		}

		// cord colors
		$cord_colors = Epey::query('//strong[@class="ozellik1123"]/following::span[1]');
		if($cord_colors) {
			$color_info_watch = Epey::translate('words', $cord_colors);
			$color_info_watch = preg_replace('/\s/ui', ".", $color_info_watch);
			$options['ywkph20b'] = explode('...', $color_info_watch);
		}

		// cord material
		$cord_material = Epey::query('//strong[@class="ozellik1121"]/following::span[1]/span/text()');
		if($cord_material) {
			$options['3bjbzrk'] = Epey::translate('words', $cord_material);
		} // translate

		// operating system version
		$os_version = Epey::query('//strong[@class="ozellik1144"]/following::span[1]/span/text()');
		if($os_version) {
			$options['ui65qcn'] = trim($os_version);
		}

		// replace cord
		$replace_cord = Epey::query('//strong[@class="ozellik1122"]/following::span[1]/span/a/text()');
		if($replace_cord) {
			$options['cc1cqt0'] = Epey::answer($replace_cord);
		}

		// vibration
		$vibration = Epey::query('//strong[@class="ozellik1146"]/following::span[1]/span/a/text()');
		if($vibration) {
			$options['u8sj5wc'] = Epey::answer($vibration);
		}

		// microfone
		$microfone = Epey::query('//strong[@class="ozellik1147"]/following::span[1]/span/a/text()');
		if($microfone) {
			$options['yq2jcrll'] = Epey::answer($microfone);
		}

		// microphone features
		$microphone_features = Epey::query('//strong[@class="ozellik1186"]/following::span[1]/span/text()');
		if($microphone_features) {
			$options['yq2jcrlw'] = trim(str_replace('Gürültü önleyici ikinci mikrofon', 'Second microphone for noise-cancelling', $microphone_features));
		}

		// speaker
		$speaker = Epey::query('//strong[@class="ozellik1148"]/following::span[1]/span/a/text()');
		if($speaker) {
			$options['8l2ljo2'] = Epey::answer($speaker);
		}

		// infrared
		$infrared = Epey::query('//strong[@class="ozellik1191"]/following::span[1]/span/a/text()');
		if($infrared) {
			$options['hwst1n7'] = Epey::answer($infrared);
		}

		// speaker features
		$speaker_features = Epey::query('//strong[@class="ozellik1188"]/following::span[1]/span/text()');
		if($speaker_features) {
			$options['yq2jcrlw'] = trim(str_replace(['Tümleşik Ahize'], ['Integrated Handsfree'], $speaker_features));
		}

		// dust resistance properties
		$dust_resistance_prop = Epey::query('//strong[@class="ozellik1152"]/following::span[1]/span/text()');
		if($dust_resistance_prop) {
			$options['cxeplx2'] = trim($dust_resistance_prop);
		} else {
			$options['cxeplx2'] = '-';
		}

		// water resistance
		$water_resistance = Epey::query('//strong[@class="ozellik1151"]/following::span[1]/span/a/text()');
		if($water_resistance) {
			$options['cxeplx1'] = Epey::answer($water_resistance);
		}

		// water resistance prop
		$water_resistance_prop = Epey::query('//strong[@class="ozellik1153"]/following::span[1]/span/text()');
		if($water_resistance_prop) {
			$options['cxeplx1'] = Epey::translate('words', $water_resistance_prop);
		}

		// services and apps
		$services_apps = Epey::query('//strong[@class="ozellik1149"]/following::span[1]');
		if($services_apps) {
			$options['iw93r5f8'] = Epey::check($services_apps, 'larmlar'); // alarm
			$options['19xfliw'] = Epey::check($services_apps, 'atırlatıcılar'); // reminder
			$options['yq2jcrlz'] = Epey::check($services_apps, 'ulaklık'); // headphone
			$options['2pinrcz'] = Epey::check($services_apps, 'hizesi'); // handset
			$options['rd3kh2w'] = Epey::check($services_apps, 'elefonumu Bul'); // find my phone
			$options['pjthco3'] = Epey::check($services_apps, 'yku Mönitör'); // sleep monitor
			$options['vubpb9d'] = Epey::check($services_apps, 'Aktivite Tanımlama');
			$options['58ue5nd'] = Epey::check($services_apps, 'Arayan İsmi Gösterimi');
			$options['h3dd5mz'] = Epey::check($services_apps, 'Ayın Evreleri');
			$options['19xfli1'] = Epey::check($services_apps, 'Ok Google');
			$options['jbsxi9o'] = Epey::check($services_apps, 'Uygulama Bildirimlerini Görüntüleme'); // apps notify
			$options['7ue2z84'] = Epey::check($services_apps, 'Yüzme'); // swim
			$options['zrxr18u1'] = Epey::check($services_apps, 'Atlayış'); // jump
			$options['xpff407'] = Epey::check($services_apps, 'Bisiklet'); // bicycle
			$options['2wb37pk'] = Epey::check($services_apps, 'Golf'); // golf
			$options['x0xgsbl'] = Epey::check($services_apps, 'GPS Saat Senkronizasyonu'); // gps synchro
			$options['zdsda7a'] = Epey::check($services_apps, 'Hava Durumu'); // display weather
			$options['bjlwf02'] = Epey::check($services_apps, 'Kalori Takibi'); // calories
			$options['581d8u2'] = Epey::check($services_apps, 'Kayak'); // calories
			$options['9haky35'] = Epey::check($services_apps, 'Koşu'); // run
			$options['gny9uz9'] = Epey::check($services_apps, 'Kronometre'); // stopwatch
			$options['rDxr61um'] = Epey::check($services_apps, 'Kürek Çekme'); // rowing
			$options['f7lsmmw9'] = Epey::check($services_apps, 'Müzik Çalar'); // music player
			$options['myof5la'] = Epey::check($services_apps, 'Otomatik Uyku Algılama'); // auto sleep
			$options['2exqey7'] = Epey::check($services_apps, 'Saatimi/Bilekliğimi Bul'); // find my device
			$options['zrxr18u3'] = Epey::check($services_apps, 'Sanal Antreman Partneri'); // virt execis part
			$options['zrxr18u4'] = Epey::check($services_apps, 'Snowboarding'); // snowboard
			$options['h3dd5mg'] = Epey::check($services_apps, 'Snowboarding'); // calendar
			$options['sicux2c'] = Epey::check($services_apps, 'Snowboarding'); // climbing
			$options['19xfli2'] = Epey::check($services_apps, 'Dünya Saatleri');
			$options['rdxjplx'] = Epey::check($services_apps, 'Gelen Çağrı ve Bildirimleri'); // incoming call info
			$options['1rez7re'] = Epey::check($services_apps, 'Geri Sayın Sayacı'); // timer
			$options['2pinrc1'] = Epey::check($services_apps, 'Hands Free Görüşme'); // hands free
			$options['zdsda7a'] = Epey::check($services_apps, 'Hava Durumu');
			$options['vubpb9d'] = Epey::check($services_apps, 'Idle Alert'); // idle alert
			$options['bjlwf02'] = Epey::check($services_apps, 'Kalori');
			$options['nh7sleo'] = Epey::check($services_apps, 'Kalp');
			$options['lggn0m2'] = Epey::check($services_apps, 'Kamera');
			$options['gny9uz9'] = Epey::check($services_apps, 'Kronometre');
			$options['x0xgsbz'] = Epey::check($services_apps, 'Navigasyon'); // navigator
			$options['19xfli4'] = Epey::check($services_apps, 'Passbook'); // passbook
			$options['c534jxf'] = Epey::check($services_apps, 'Ses ile komut verme');
			$options['2q53kqi'] = Epey::check($services_apps, 'SMS Görüntüleme ve Yanıtlama');
			$options['h3dd5mg'] = Epey::check($services_apps, 'Takvim');
			$options['bmpc3f3j'] = Epey::check($services_apps, 'Uygulama Yükleyebilme');
			$options['e61kgyj'] = Epey::check($services_apps, 'Uyku Mönitörü');

			if(Epey::check($services_apps, 'Medya Oynatıcı') == '+') {
				$options['qlsq9yh'] = '+';
				$options['a7j5c0b'] = '+';
				$options['ja7w3sb'] = '+';
			}
		}
		// pulsometer
		$pulsometer = Epey::query('//strong[@class="ozellik1139"]/following::span[1]/span/a/text()');
		if($pulsometer) {
			$options['nx6ywkn'] = Epey::answer($pulsometer);
		}

		// ambient light sensor
		$ambient_light_sensor = Epey::query('//strong[@class="ozellik1140"]/following::span[1]/span/text()');
		if($ambient_light_sensor) {
			$options['h88pkmdy'] = Epey::answer($ambient_light_sensor);
		}

		// compass
		$compass = Epey::query('//strong[@class="ozellik1142"]/following::span[1]/span/a/text()');
		if($compass) $options['x0xgsbn'] = Epey::answer($compass);

		// barometer
		$barometer = Epey::query('//strong[@class="ozellik1175"]/following::span[1]/span/text()');
		if($barometer) $options['x399jxz'] = Epey::answer($barometer);

		// uv sensor
		$uv_sensor = Epey::query('//strong[@class="ozellik1176"]/following::span[1]/span/text()');
		if($uv_sensor) $options['ywtcej1'] = Epey::answer($uv_sensor);

		// thermometer
		$thermometer = Epey::query('//strong[@class="ozellik1190"]/following::span[1]/span/text()');
		if($thermometer) $options['k626aelh'] = Epey::answer($thermometer);

		// proximity sensor
		$proxim_sensor = Epey::query('//strong[@class="ozellik1250"]/following::span[1]/span/text()');
		if($proxim_sensor) $options['h88pkmd1'] = Epey::answer($proxim_sensor);

		// pedometer
		$pedometer = Epey::query('//strong[@class="ozellik1141"]/following::span[1]/span/a/text()');
		if($pedometer) {
			$options['guoawdo'] = Epey::answer($pedometer);
		}

		// accelerometer
		$accelerometer = Epey::query('//strong[@class="ozellik1137"]/following::span[1]/span/a/text()');
		if($accelerometer) {
			$options['h1ddzrt'] = Epey::answer($accelerometer);
		}

		// gyroscope
		$gyroscope = Epey::query('//strong[@class="ozellik1138"]/following::span[1]/span/a/text()');
		if($gyroscope) {
			$options['ywtcejg'] = Epey::answer($gyroscope);
		}

		// compability os
		$compability_os = Epey::query('//strong[@class="ozellik1264"]/following::span[1]');
		if($compability_os) {
			$compability_syn = trim(str_replace(' ', '', $compability_os));
			$options['0v8w2sz'] = Epey::check($compability_syn, 'iOS'); // iOS
			$options['a5sj3l2'] = Epey::check($compability_syn, 'indow'); // windows
			$options['vxq3g1f'] = Epey::check($compability_syn, 'lackBerry'); // blackberry
			$options['llulwif'] = Epey::check($compability_syn, 'ndroid'); // android
		}

		// bluetooth version
		$bluetooth_version = Epey::query('//strong[@class="ozellik1114"]/following::span[1]');
		if($bluetooth_version) {
			$options['p4zld5l'] = trim($bluetooth_version);
		}

		// wifi
		$wifi = Epey::query('//strong[@class="ozellik1130"]/following::span[1]');
		if($wifi) $options['2pinrcv'] = str_replace(['Yazılım güncellemesi gerektirebilir','Konum Bilgisi için'], ['Software update may require','For location information'], trim($wifi));

		// nfc
		$nfc = Epey::query('//strong[@class="ozellik1134"]/following::span[1]');
		if($nfc) $options['9ee4viy'] = Epey::answer($nfc);

		// usb
		$usb = Epey::query('//strong[@class="ozellik1131"]/following::span[1]');
		if($usb) {
			$options['2q8o92fk'] = Epey::answer($usb);
		}

		// usb type
		$usb_type = Epey::query('//strong[@class="ozellik1132"]/following::span[1]');
		if($usb_type) {
			$options['p85t8s8z'] = Epey::check($usb_type, 'Micro-USB');
		} // micro-usb

		// release date
		$release_date = Epey::query('//strong[@class="ozellik1154"]/following::span[1]');
		if($release_date) {
			$options['2lbcv9f'] = trim($release_date);
		}

		// series
		$series = Epey::query('//strong[@class="ozellik2137"]/following::span[1]');
		if($series) {
			$options['34fksng'] = trim($series);
		}

		// chipset
		$chipset = Epey::query('//strong[@class="ozellik1136"]/following::span[1]');
		if($chipset) {
			$options['dkg7n4e'] = str_replace('Â','',trim($chipset));
		}

		// cpu info
		$cpu = Epey::query('//strong[@class="ozellik1110"]/following::span[1]');
		if($cpu) {
			preg_match('/GHz\s+(.+)/mui', $cpu, $out_cpu_wear);
			if(isset($out_cpu_wear[1]) && !empty($out_cpu_wear[1])) {
				$options['y5xo6x4'] = str_replace('ARM', '', trim($out_cpu_wear[1]));
			}
		}

		// cpu core
		$cpu_core = Epey::query('//strong[@class="ozellik1112"]/following::span[1]');
		if($cpu_core) $options['y5xo6x5'] = trim($cpu_core);

		// ram size
		$ram_size = Epey::query('//strong[@class="ozellik1106"]/following::span[1]');
		if($ram_size) $options['ej4wq1y'] = str_replace(' ', '', trim($ram_size));

		// internal storage
		$flash = Epey::query('//strong[@class="ozellik1105"]/following::span[1]');
		if($flash) $options['c8xo6x6'] = str_replace(' ', '', trim($flash));

		// GPS
		$gps = Epey::query('//strong[@class="ozellik1167"]/following::span[1]');
		if($gps) $options['yfvshn2'] = Epey::answer($gps);

		// sim support
		$sim = Epey::query('//strong[@class="ozellik1169"]/following::span[1]');
		if($sim) $options['mdmfh57'] = Epey::answer($sim);


		// get type
		$options['drbmx1r'] = 2;
	}

	foreach($options as $code=>$value){
		if(!is_string($value)) continue;
		if (!isset(Epey::$unification[$code][$value])) continue;
		$options[$code] = Epey::$unification[$code][$value];
	}

	$options = array_filter($options, function ($v, $k) {
		return !empty($v);
	}, ARRAY_FILTER_USE_BOTH);

	// get main product
	if(isset($options['t40i1m6'])) {
		$main_products = array_merge(array_values($options['t40i1m6']), (array)$url);
		usort($main_products, function ($item1, $item2) {
			if(strlen($item1) == strlen($item2)) {
				return 0;
			}
			return (strlen($item1) < strlen($item2)) ? -1 : 1;
		});
		$main_product = $main_products[0];
		$options['g55z4fn'] = $main_product;
		if($url === $main_product) {
			$options['rt6iqm1'] = 1;
		} else {
			$options['rt6iqm1'] = 0;
		}
	} else {
		$options['g55z4fn'] = $url;
		$options['rt6iqm1'] = 1;
	}

	$items[$url] = array_map(function ($item) {
		return (is_string($item)) ? (trim(strip_tags($item))) : $item;
	}, $options);

	// save data to cache
	if($webpage != null) {
		$webpage->version = Epey::$version;
		$webpage->desc = Json::encode($items[$url]);
		$webpage->save();
	} else {
		$webpage = new WebPage([
			'path_hash' => $path_hash,
			'source'    => 'epey',
			'url'       => $url,
			'version'   => Epey::$version,
			'desc'      => Json::encode($items[$url]),
			'format'    => 'json',
		]);
		$webpage->save();
	}
}
$codes = Sheet::rf('@config/product/specs.csv', ['indexFrom'=>'code']);
//H::print_r($items);
echo(Render::render($items, $codes,['category','group','title_ru', 'code', 'units_en']));
