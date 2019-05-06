<? namespace app\helpers;

use DOMDocument;
use DOMXPath;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * @property string $id
 * @property string $hl #language code: en,ru
 * @property string $node #node name: Country/City/Course/School/Uni
 * @property integer $nid #node id from table
 * @property integer $version version of class
 * @property integer $source #source name: google/wikipedia/geonames
 * @property string $name #html title
 * @property string $path_hash
 * @property string $format #json,html or xml
 * @property string $url #remote url after redirection
 * @property string $path #url without ignore attributes
 * @property string $http_code #curl responce code
 * @property string $desc #responce
 */
class WebPage extends \yii\db\ActiveRecord
{
    public static function tableName(){return 'academic_webpage';}
    public $charset = 'utf-8';
    public static $default_charset = 'utf-8';
    public static $folder = [
    	'file'=>[
    		'public'=>'@cache/file/',
    		'private'=>'@-cache/file/',
	    ],
    	'page'=>[
    		'public'=>'@cache/page/',
    		'private'=>'@-cache/page/',
	    ]
    ];
    public static $cleaners = [
	    'head'  =>"/\<head([\s\S]+?)\<\/head\>/",
		'script'=>"/\<script([\s\S]+?)\<\/script\>/",
		'style' =>"/\<style([\s\S]+?)\<\/style\>/",
		'symbol'=>'/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u',
    ];
    public static $clean = [];
	public $queue = [];
	public $files = [];

    public function rules()
    {
        return [
            [['nid', 'redirect_count','version'], 'integer'],
            [['desc','path','url'], 'string'],
            [['source'], 'string', 'max' => 255],
            [['path_hash'], 'string', 'max' => 100],
            [['http_code','hl','format'], 'string', 'max' => 10],
        ];
    }

	public static function request($endpoint, $urlParams = [], $options=[])
	{
        $attributes   = $options['attributes']  ??[];
		$ignoreParams = $options['ignoreParams']??[];
		$headers      = $options['headers']     ??[];
		$format       = $options['format']      ??'html';

		$result = self::get($endpoint, $urlParams, $attributes, $m, $ignoreParams,$headers);

		if($format=='json'){
			return JSON::decode($result);
		}else{
			return $result;
		}
	}

	public static function get($endpoint, $urlParams=[], $attributes=[],&$m=null, $ignoreParams=[],$headers=[],$useCache=true)
	{
		$path = self::makeUrl($endpoint, $urlParams, $ignoreParams);
		$path_hash = hash('sha256', $path);
		if($useCache){
			$m = WebPage::find()->filterWhere(['path_hash'=>$path_hash,'format'=>'html'])->one();
			if($m!==null){
				return $m->desc;
			}
		}
		$url = self::makeUrl($endpoint,$urlParams);
		$m = new WebPage();
		$m->connect($url,$headers, true);
		foreach (self::$clean as $cleaner){
			$m->desc = preg_replace(self::$cleaners[$cleaner],    '',$m->desc);
		}

		if(!empty($m->desc)){
			$m->path_hash = $path_hash;
			$m->path      = $path;
			$m->url       = $url;
			$m->format    = 'html';
			$m->created_at = new Expression('NOW()');
			$m->attributes = $attributes;
			$m->save(false);
		}
		return $m->desc;
	}

	public static function clean($endpoint, $urlParams=[],$ignoreParams=[]){
		$path = self::makeUrl($endpoint, $urlParams, $ignoreParams);
		$path_hash = hash('sha256', $path);
		WebPage::deleteAll(['path_hash'=>$path_hash]);
	}

	public static function makeUrl($url, $urlParams=[], $ignoreParams=[])
	{
		foreach ($ignoreParams as $key){
			unset($urlParams[$key]);
		};
		if(!empty($urlParams)){
			$url .=  "?" . http_build_query($urlParams);
		}
		return $url;
	}

	public static function json($endpoint, $urlParams=[], $attributes=[],&$m=null,$ignoreParams=[],$useCache=true)
	{
		$content = self::get($endpoint, $urlParams, $attributes, $m, $ignoreParams,[], $useCache);
		return JSON::decode($content);
	}

	public function changeCharset($ch)
	{
		$header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
		$header_string = substr($this->desc, 0, $header_size);
		$this->desc = substr($this->desc, $header_size);
		$headers = [];
		foreach(explode("\n",$header_string) as $header)
		{
			$tmp = explode(":",trim($header),2);
			if (count($tmp)>1)
			{
				$headers[strtolower($tmp[0])] = trim(strtolower($tmp[1]));
			}
		}

		if (isset($headers['content-type']))
		{
			$tmp = explode("=", $headers['content-type']);
			if (count($tmp)>1) $this->charset = $tmp[1];
		}
		if ($this->charset != self::$default_charset) $this->desc = iconv($this->charset, self::$default_charset, $this->desc);
	}

	public function connect($url,$headers=[], $withProxy = false)
	{
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		// proxy options
		if($withProxy) {
			$try = true;
			$steps = 160;
			$step = 0;

			$proxy = file(Url::to('@-parsers/market/proxy.txt'));

			while ($try) {
				if(count($proxy) > 1) {
					shuffle($proxy);
					$http_code = self::checkProxy($proxy[$step]);
				} else {
					$http_code = self::checkProxy($proxy);
				}

				$step++;
				if($step == $steps && $http_code != 200) { throw new \RuntimeException("{$steps} попыток закончилось, попробуйте снова!") ;}
				$try = (($step < $steps) && ($http_code != 200));
			}

			$newProxy = trim((count($proxy) > 1) ? $proxy[$step-1] : implode('', $proxy));

			curl_setopt($ch, CURLOPT_PROXY, $newProxy);
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		}

		// useragent
		$userAgent = file(Url::to('@-parsers/market/user_agents_list.txt'));
		$userAgentsCount = count($userAgent) - 1;

		curl_setopt($ch, CURLOPT_USERAGENT, $code = $userAgent[rand(0, $userAgentsCount)]);
		curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE,  'cookie.txt');

		$headers_string = [];
		foreach ($headers as $key=>$header){
			$headers_string[] = "$key:$header";
		}
		if(!empty($headers)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_string);
		}
		$this->desc = curl_exec($ch);
		$this->changeCharset($ch);
		$this->path = $url;
		$this->http_code      = curl_getinfo($ch,CURLINFO_RESPONSE_CODE);
		$this->redirect_count = curl_getinfo($ch,CURLINFO_REDIRECT_COUNT);
		$this->direct         = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close ($ch);
	}

	/**
	 * @param $proxyUrl
	 * @return mixed
	 */
	public static function checkProxy($proxyUrl)
	{
		$userAgent = file(Url::to('@-parsers/market/user_agents_list.txt'));
		$userAgentsCount = count($userAgent) - 1;
		$checkUrl = 'https://m.ya.ru/robots.txt';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $checkUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, ($userAgent[rand(0, $userAgentsCount)]));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_PROXY, ( is_array($proxyUrl) ? (implode('', $proxyUrl))  :  $proxyUrl ));
		curl_exec($ch);

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $http_code;
	}

	public static function urlDom($url, $urlParams=[],$attributes=[])
	{
		$content = self::get($url, $urlParams,$attributes);
		return self::dom($content);
	}

	public static function dom($content)
	{
		$doc = new DOMDocument();
		@$doc->loadHTML($content);
		return new DOMXpath($doc);
	}

	public static function cacheFile($url_remote)
	{
		$path =  preg_replace("/(http(s)?)\:\/\/(www\.)?/",'', $url_remote);
		if(strlen($path)>150)
		{
			$path = substr($path_long = $path,0,140);
			$path.='---'.substr($path_long = $path,-10);
		}
		$path = preg_replace("~[^A-Za-z0-9\.]~",'-', $path);
		$hash = hash('sha256', $url_remote);
		$path = substr($hash,0,6).' '.$path;
		$url_local = Url::to(self::$folder['file']['private'].$path);
		self::loadFile($url_remote,$url_local);
		return Url::to(self::$folder['file']['public'].$path);
	}

	public static function cachePage($url)
	{
		$path =  preg_replace("/(http(s)?)\:\/\/(www\.)?/",'', $url);
		$path = preg_replace("~[^A-Za-z0-9]~",'-', $path);
		$hash = hash('sha256', $url);
		$cacheUrl = substr($hash,0,6).' '.$path;
		return Url::to(self::$folder['page']['private'].$cacheUrl);
	}

	public function addQueue($url)
	{
		$m = WebPage::find()->filterWhere(['path'=>$url,'format'=>'html'])->one();
		if($m===null){
			$this->queue[$url] = self::cachePage($url);
		}else{
			$this->files[$url] = $m->desc;
		}
	}

	public function flush($attributes=[])
	{
		self::loadFiles($this->queue);
		foreach ($this->queue as $url=>$path){
			$this->files[$url] = file_get_contents($path);
			$attributes['desc'] = $this->files[$url];
			$attributes['path'] = $url;
			if(!($m = new WebPage($attributes))->save()){
				var_dump($m->errors);
			}
		};
		return $this->files;
	}

	public static function loadFile($path_remote,$path_local)
	{
		$ch = curl_init ($path_remote);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FILE, $fp = fopen ($path_local, 'w'));
		curl_exec($ch);
		curl_close ($ch);
		fclose($fp);
	}

	public static function loadFiles($list)
	{
		$handles = curl_multi_init();
		$files = [];
		$queue = [];

		foreach ($list as $url => $file)
		{
			$queue[$url]  = curl_init($url);
			$files[$url] = fopen ($file, "w");
			curl_setopt ($queue[$url], CURLOPT_FILE,           $files[$url]);
			curl_setopt ($queue[$url], CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt ($queue[$url], CURLOPT_HEADER ,        0);
			curl_setopt ($queue[$url], CURLOPT_USERAGENT ,     "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0");
			curl_setopt ($queue[$url], CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($queue[$url], CURLOPT_CONNECTTIMEOUT, 60);
			curl_multi_add_handle ($handles,$queue[$url]);
		}

		do {
			curl_multi_exec($handles,$running);
		}
		while($running > 0);

		foreach ($list as $url => $file)
		{
			curl_multi_remove_handle($handles,$queue[$url]);
			curl_close($queue[$url]);
			fclose ($files[$url]);
		}
		curl_multi_close($handles);
	}

}
