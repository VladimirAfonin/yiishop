<? namespace app\helpers;

use Yii;
use app\models\Currency;
use yii\helpers\Html;
use yii\helpers\Url;

class H extends Html
{
	public static function tag($name, $content = '', $options_or_class = [])
	{
		$options = is_string($options_or_class)?['class'=>$options_or_class]:$options_or_class;
		return parent::tag($name, $content, $options);
	}

	public static function span($content = '', $options = [])
	{
		return self::tag('span', $content, $options);
	}

	public static function ico($class, $content=' ', $options=[])
	{
		return self::tag('i', "", ['class'=>$class]+$options).$content;
	}

	/* icon */
	public static function i($class, $content=' ', $options=[])
	{
		return self::tag('i', "", ['class'=>$class]+$options).$content;
	}

	/* fontAwesome icon */
	public static function fa($class, $content=' ', $options=[])
	{
		return self::tag('i', '', ['class'=>'fa fa-'.$class]+$options).$content;
	}

	public static function td($content=" ", $options=[])
	{
		return self::tag('td', $content, $options);
	}

	public static function div($content = '',$htmlOptions=[])
	{
		return self::tag('div',$content, $htmlOptions);
	}

	/* replace chars string to Url */
	public static function toUrl($url=null)
	{
		$url = strtolower($url);
		$url = strip_tags($url);
		$url = stripslashes($url);
		$url = html_entity_decode($url);
		$url = str_replace('\'', '', $url);
		$match = '/[^a-z0-9_]+/';
		$replace = '_';
		$url = str_replace('__', '_', $url);
		$url = str_replace('__', '_', $url);
		$url = preg_replace($match, $replace, $url);
		$url = trim($url, $replace);
		return $url;
	}

	public static function translit($string)
	{
		return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $string);
	}

	public static function userPower()
	{
		if(Yii::$app->user->isGuest){
			return 0;
		}
		return Yii::$app->user->identity->power;
	}

	/* flag image by country code */
	public static function flag($id, $params=[])
	{
		if(empty($id)){
			return null;
		}elseif ($id=='en'){
			$id = 'uk';
		}
		$id = strtolower($id);
		if(is_numeric($id)&&isset(Yii::$app->params['countries'][$id])){
			$id = strtolower(Yii::$app->params['countries'][$id]);
		}
		$defaults =['class'=>'flag-lang'];
		$params = array_merge($defaults, $params);
		$src = '@flag/'.$id.'.png';
		return self::img($src, $params);
	}

	public static function encodeNumber($number, $separator = ',', $round=null)
	{
		if($number!==null)
			return number_format($number , $decimals = 0 , '.' , $separator);
	}

	/* Finance: Amount+Currency*/
	public static function cost($value,$decimals=0)
	{
		if(!is_numeric($value)) return false;
		return number_format(Currency::convert($value),$decimals)."&nbsp;".H::span(H::currency(),'currency');
	}

	/* Finance: Amount only */
	public static function amount($value,$decimals=0)
	{
		if(!is_numeric($value)) false;
		return number_format(Currency::convert($value),$decimals);
	}

	/* Finance: Currency only*/
	public static function currency()
	{
		return Yii::$app->session->get('currency','USD');
	}

	public static function Ntext($text, $engine=null, $options=[])
	{
		$text= trim($text);
		if(empty($engine))
			return nl2br(($text));
		if($engine==1)
			return nl2br(($text));
		if((int)$engine==3)
			return $text;
		if((int)$engine==4)
			return Markup::parse($text, $options);
	}

	public static function a($text, $url = null, $options = [])
	{
		$options = is_string($options)?['class'=>$options]:$options;
		$nofollow =[
			'uni/index',
			'institution/index',
			'city/index',
			'request/create',
			'page/index',
			'course/index',
			'product/compare',
		];
		if(!isset($options['rel'])&&is_array($url)&&isset($url[0])&&array_search(trim($url[0],'/'), $nofollow)!==false){
			$options['page-url'] = (string)Url::to($url);
			$options['rel'] = 'nofollow';
			$url = '#';
		};

		return parent::a($text, $url, $options);
	}

	/* a + icon */
	public static function ai($class, $url = null, $options = [])
	{
		return self::a(self::i($class.' flat'), $url, $options);
	}

	/* a + Yii::t('main',$text)*/
	public static function am($text, $url = null, $options = [])
	{
		return self::a(Yii::m($text),$url,$options);
	}

	/* span + Yii::t('main',$text)*/
	public static function span_m($text, $options = [])
	{
		return self::span(Yii::m($text),$options);
	}

	/* international link TO article */
	public static function to($path,$language=null)
	{
		if(empty($language)){
			$language = Yii::$app->language;
		}
		return Url::to(['page/view', 'hl'=>$language, 'id'=>$path]);
	}

	/* Noindex link */
	public static function na($text, $url = null, $options = [])
	{
		if(is_string($options)){
			$options = ['class'=>$options];
		};
		$options['page-url'] = Url::to($url);
		$options['rel'] = 'nofollow';
		$url = '#';
		return self::a($text, $url, $options);
	}

	/* domain from url*/
	public static function domain($url,$part = '$4')
	{
		return (preg_replace("/((http(s)?\:\/\/)((www\.)?(.+?)))(\/(.+)?)?$/", $part, $url));
	}

	/* Default header */
	public static function header($content, $tag='h2', $options=[])
	{
		$h = self::tag($tag, $content, $options);
		return self::tag('div', "\n".$h."\n", ['class'=>'heading-title heading-border-bottom heading-color']);
	}

	public static function tipLink($url, $options_or_title=[])
	{
		$url = is_string($url)?H::to($url):$url;
		$options = is_string($options_or_title)?['title'=>$options_or_title]:$options_or_title;
		if(!isset($options['title'])){
			$options['title']=Yii::m('Details');
		}
		return self::a(self::i('fa fa-question-circle flat muted'), $url, $options);
	}
//		$options = is_string($options)?['class'=>$options]:$options;
//		if(is_string($url)){
//			$url = H::to($url);
//		};
//		if(is_string($options)){
//			$options = ['title'=>$options];
//		};

	/* Question icon with title */
	public static function question($comment, $options=[])
	{
		$options['title']=$comment;
		return self::span(self::i('fa fa-question-circle flat muted'), $options);
	}

	/* If media file does not exist return default image */
	public static function isMedia($path, $default='@media/uni_0.jpg')
	{
		if(file_exists(Url::to('@-media/'.$path))){
			return Url::to('@media/'.$path);
		}else{
			return Url::to($default);
		}
	}

	/* Check url or return default image */
	public static function check($localUrl,$publicUrl, $default='@media/uni_0.jpg')
	{
		if(file_exists(Url::to($localUrl))){
			return Url::to($publicUrl);
		}else{
			return Url::to($default);
		}
	}

	/* Path to media url */
	public static function media($path)
	{
		$path = str_replace('/','_',$path);
		return Url::to('@media/'.$path);
	}

	public static function checkJson($navigator)
	{
		$n = json_decode($navigator);
		return json_last_error() == JSON_ERROR_NONE;
	}

	/* Explode array from app parameters */
	public static function explodeParams($node)
	{
		$explode_path = explode('.', $node);
		$count_explode = count($explode_path);
		$items = Yii::$app->params;
		for ($i = 0; $i < $count_explode; $i++) {
			$items = $items[$explode_path[$i]];
		}
		return $items;
	}

	/* DropDown with labels */
	public static function labelsList($node, $values = null)
	{
		if (!is_array($values)) {
			$values = self::explodeParams($node);
		};
		$rows = [];
		foreach ($values as $value) {
			$rows[$value] = Yii::t(Yii::$app->params['phrase']['category'][$node], $value);;
		}

		return $rows;
	}

	/* DropDown with Discovery codes */
	public static function codesList($node, $values = [])
	{
		$list = [];
		$items = self::explodeParams($node);

		foreach ($items as $id => $code) {
			if (empty($values) || array_search($id, $values) !== false) {
				$list[$id] = $code . ' ' . Yii::t('discovery', $id);
			}
		}
		return $list;
	}

	public static function br($values=[])
	{
		$values = (array)$values;
		echo implode(' ■ ',$values).'<br/>';
	}

	public static function print_r($value)
	{
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}


	/*	public static function cut($string, $length=15)
		{
			return mb_strimwidth($string ,0,$length,'...','utf-8');
	}
	public static function translitUrl($string)
	{
		$string = self::translitUrl($string);
		return self::toUrl($string);
	}
	public static function myTime($time)
	{
		return $time;
	}
	public static function latin($text)
	{
		$cyr = [
			'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
			'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
			'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
			'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
		];
		$lat = [
			'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
			'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
			'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
			'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
		];
		return str_replace($cyr, $lat, $text);
	}

	public static function li($content = '', $options = [])
	{
		return self::tag('li', $content, $options);
	}
	*/
}