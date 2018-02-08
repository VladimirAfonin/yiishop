<?
namespace backend\entities;

use DOMDocument;
use DOMXPath;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property string $hl #language code: en,ru
 * @property string $node #node name: Country/City/Course/School/Uni
 * @property integer $nid #node id from table
 * @property integer $source #source name: google/wikipedia/geonames
 * @property string $name #html title
 * @property string $path #url without ignore attributes
 * @property string $http_code #curl response code
 * @property string $desc #responce
 * @property string $proxy_list
 */
class WebPage extends ActiveRecord
{
    public static function tableName()
    {
        return 'academic_webpage';
    }

    const CHECK_TIME = 1200; // sec.
    const GIMMEPROXY_API_URL = 'http://gimmeproxy.com/api/getProxy';
    const GETPROXYLIST_API_URL = 'https://api.getproxylist.com/proxy';
    const PUBPROXY_API_URL = 'http://pubproxy.com/api/proxy';

    public static $folder = [
        'file' => [
            'public' => '@cache/file/',
            'private' => '@-cache/file/',
        ],
        'page' => [
            'public' => '@cache/page/',
            'private' => '@-cache/page/',
        ]
    ];

    public $queue = [];
    public $files = [];
    public $proxyList = [];
    public $filename = 'proxy.txt';


    public function rules()
    {
        return [
            [['nid', 'redirect_count'], 'integer'],
            [['desc', 'path', 'url'], 'string'],
            [['source'], 'string', 'max' => 255],
            [['path_hash'], 'string', 'max' => 100],
            [['http_code', 'hl'], 'string', 'max' => 10],
        ];
    }

    /**
     * @param $endpoint
     * @param array $urlParams
     * @param array $attributes
     * @param bool $withProxy
     * @param null $m
     * @param array $ignoreParams
     * @return string
     */
    public static function get($endpoint, $urlParams = [], $attributes = [], $withProxy = false,  &$m = null, $ignoreParams = []): string
    {
        $path = self::makeUrl($endpoint, $urlParams, $ignoreParams);
        $path_hash = hash('sha256', $path);
        $m = WebPage::find()->filterWhere(['path_hash' => $path_hash])->one();
        if ($m !== null) {
            return $m->desc;
        }
        $url = self::makeUrl($endpoint, $urlParams);
        $m = new WebPage();

        $proxy = $m->connect($url, $withProxy);

        if ( ! empty($m->desc)) {
            self::saveWorkingProxy($proxy);
            $m->path_hash = $path_hash;
            $m->path = $path;
            $m->url = $url;
            $m->attributes = $attributes;
            $m->save(false);
        }
        return $m->desc;
    }

    /**
     * get ip&port in response from api.
     *
     * @param string $nameOfService
     * @return mixed
     */
    public static function getProxyResponse($nameOfService)
    {
        $params = [
            self::GIMMEPROXY_API_URL => [
                'supportsHttps' => true,
                'user-agent' => true,
                'anonymityLevel' => 1,
                'country' => 'US'
            ],
            self::GETPROXYLIST_API_URL => [
                'allowsHttps' => true,
                'allowsCustomHeaders' => true,
                'allowsUserAgentHeader' => true,
//                'anonymity' => 1,
            ],
            self::PUBPROXY_API_URL => [
                'level' => 'anonymous',
                'google' => 1,
                'user_agent' => true,
                'cookies' => true,
                'https' => 1,
                'limit' => 20, // from 0 to 20 for this API
                'country' => 'US',
            ],
        ];

        $urlProxyApi = self::makeUrl($nameOfService, $params[$nameOfService]);
        $ch = curl_init($urlProxyApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if($nameOfService == self::PUBPROXY_API_URL) {
            $result = $response['data'][0]['ipPort'];
        } else {
            $result = $response['ip'] . ':' . $response['port'];
        }
        return $result;
    }

    /**
     * @param $url
     * @param array $urlParams
     * @param array $ignoreParams
     * @return string
     */
    public static function makeUrl($url, $urlParams = [], $ignoreParams = []): string
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
     * @param $endpoint
     * @param array $urlParams
     * @param array $attributes
     * @param bool $withProxy
     * @param null $m
     * @param array $ignoreParams
     * @return mixed
     */
    public static function json($endpoint, $urlParams = [], $attributes = [], $withProxy = false, &$m = null, $ignoreParams = [])
    {
        $content = self::get($endpoint, $urlParams, $attributes, $withProxy, $m, $ignoreParams);
        return JSON::decode($content);
    }

    /**
     * check last save proxy for availability
     *
     * @return bool|mixed
     */
    public static function checkProxyValidation()
    {
        $proxy = ProxyList::find()->orderBy(['created_at' => SORT_DESC])->one();
        $proxyTimeCreate = $proxy->created_at;
        $currentTime = time();
        if(($currentTime - $proxyTimeCreate) > self::CHECK_TIME) {
            return false;
        }
        return $proxy->address;
    }

    /**
     * @param $url
     * @param $withProxy
     *
     * @return mixed
     */
    public function connect($url, $withProxy = false)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1); // 1
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

        if($withProxy) {
            $try = true;
            $steps = 20; // api response 20 request
            $step = 0;

            if(($answer = self::checkProxyValidation()) == false) {
                $proxy = file('../entities/proxy.txt');
                // for api call
//                $proxy = (array) self::getProxyResponse(self::GIMMEPROXY_API_URL); // self::GETPROXYLIST_API_URL | self::GIMMEPROXY_API_URL | self::PUBPROXY_API_URL
            } else {
                $proxy = (array) $answer;
            }

            $userAgent = file('../entities/user_agents_list.txt');
            $userAgentsCount = count($userAgent) - 1;

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

            $newProxy = ((count($proxy) > 1) ? $proxy[$step-1] : implode('', $proxy));

            curl_setopt($ch, CURLOPT_PROXY, $newProxy);
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.ru/');
            curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
            curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt');
            curl_setopt($ch, CURLOPT_USERAGENT, $code = $userAgent[rand(0, $userAgentsCount)]);

        }

        $response = iconv('CP1251', 'UTF-8', curl_exec($ch));
        $http_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        if(preg_match('/302\s[a-z]{5}|blue coat/i', $response)) {
            throw new \RuntimeException('we got 302 answer and bad proxy, try again.');
        }

        $this->desc = $response; //(count($response) > 5000) ? $response : '';
        $this->path = $url;
        $this->http_code = $http_code;
        $this->redirect_count = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        $this->direct = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        return ((!empty($newProxy)) ? $newProxy : null);
    }

    /**
     * @param null $proxy
     */
    public static function saveWorkingProxy($proxy = null)
    {
        if(!is_null($proxy)) {
            $m = new ProxyList();
            $m->address = $proxy;
            $m->created_at = time();
            $m->save(false);
        }
    }

    /**
     * @param $proxyUrl
     * @return mixed
     */
    public static function checkProxy($proxyUrl)
    {
        $userAgent = file('../entities/user_agents_list.txt');
        $userAgentsCount = count($userAgent) - 1;
        $checkUrl = 'https://m.google.com/robots.txt';
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

    /**
     * @param $url
     * @param array $urlParams
     * @param array $attributes
     * @param bool $withProxy
     * @return DOMXPath
     */
    public static function urlDom($url, $urlParams = [], $attributes = [], $withProxy = false)
    {
        $content = self::get($url, $urlParams, $attributes, $withProxy);
        return self::dom($content);
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

    /**
     * @param $url_remote
     * @return string
     */
    public static function cacheFile($url_remote)
    {
        $path = preg_replace("/(http(s)?)\:\/\/(www\.)?/", '', $url_remote);
        if (strlen($path) > 150) {
            $path = substr($path_long = $path, 0, 140);
            $path .= '---' . substr($path_long = $path, -10);
        }
        $path = preg_replace("~[^A-Za-z0-9\.]~", '-', $path);
        $hash = hash('sha256', $url_remote);
        $path = substr($hash, 0, 6) . ' ' . $path;
        $url_local = Url::to(self::$folder['file']['private'] . $path);
        self::loadFile($url_remote, $url_local);
        return Url::to(self::$folder['file']['public'] . $path);
    }

    /**
     * @param $url
     * @return string
     */
    public static function cachePage($url)
    {
        $path = preg_replace("/(http(s)?)\:\/\/(www\.)?/", '', $url);
        $path = preg_replace("~[^A-Za-z0-9]~", '-', $path);
        $hash = hash('sha256', $url);
        $cacheUrl = substr($hash, 0, 6) . ' ' . $path;
        return Url::to(self::$folder['page']['private'] . $cacheUrl);
    }

    /**
     * @param $url
     */
    public function addQueue($url)
    {
        $m = WebPage::find()->filterWhere(['path' => $url])->one();
        if ($m === null) {
            $this->queue[$url] = self::cachePage($url);
        } else {
            $this->files[$url] = $m->desc;
        }
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function flush($attributes = [])
    {
        self::loadFiles($this->queue);
        foreach ($this->queue as $url => $path) {
            $this->files[$url] = file_get_contents($path);
            $attributes['desc'] = $this->files[$url];
            $attributes['path'] = $url;
            if ( ! ($m = new WebPage($attributes))->save()) {
                var_dump($m->errors);
            }
        };
        return $this->files;
    }

    /**
     * @param $path_remote
     * @param $path_local
     */
    public static function loadFile($path_remote, $path_local)
    {
        $ch = curl_init($path_remote);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FILE, $fp = fopen($path_local, 'w'));
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    /**
     * @param $list
     */
    public static function loadFiles($list)
    {
        $handles = curl_multi_init();
        $files = [];
        $queue = [];

        foreach ($list as $url => $file) {
            $queue[$url] = curl_init($url);
            $files[$url] = fopen($file, "w");
            curl_setopt($queue[$url], CURLOPT_FILE, $files[$url]);
            curl_setopt($queue[$url], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($queue[$url], CURLOPT_HEADER, 0);
            curl_setopt($queue[$url], CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0");
            curl_setopt($queue[$url], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($queue[$url], CURLOPT_CONNECTTIMEOUT, 60);
            curl_multi_add_handle($handles, $queue[$url]);
        }

        do {
            curl_multi_exec($handles, $running);
        } while ($running > 0);

        foreach ($list as $url => $file) {
            curl_multi_remove_handle($handles, $queue[$url]);
            curl_close($queue[$url]);
            fclose($files[$url]);
        }
        curl_multi_close($handles);
    }

}
