<?php

namespace app\helpers;
use yii\helpers\Json;
use yii\helpers\Url;

class Clerk
{
	public $file;
	public $last;
	public $symbols = [
		'empty' => '⬜',
		'full'  => '⬛',
	];
	public $lines = [];
	public $text='';
	public $cells = 70;
	public $data = [
		'time'     => '',
		'spend'    => '',
		'progress' => 0,
		'left'     => '',
		'current'  => 0,
		'total'    => 0
	];
	public static function time()
	{
		return microtime(true);
	}
	public function __construct($file,$data)
	{
		$this->file = Url::to($file);
		$this->data = array_merge($this->data,$data);
		$this->last = self::time();
	}
	public function update($data=[])
	{
		$current = $this->data['current'];
		$this->data = array_merge($this->data,$data);

		if(!isset($data['progress'])){
			$this->data['progress'] = number_format(100*$current/$this->data['total'],2).'%';
			$this->data['progress'] = str_pad($this->data['progress'], 6, ' ', STR_PAD_LEFT);
		};

		$line = $data = $this->data;
		$line = array_map(function($a){
			return is_array($a)?JSON::encode($a):$a;
		},$line);
		$this->last = self::time();
		$line['current'] = $current.'/'.$line['total'];
		$line['current'] = str_pad($line['current'], strlen($this->data['total'])*2+1, ' ', STR_PAD_LEFT);
		unset($line['total']);
		unset($line['left']);
		$line['time'] = date("H:i:s");
		$line['spend'] = str_pad($data['spend'], 8, ' ', STR_PAD_LEFT). ' sec.';

		$this->lines[$current] = implode("\t".'⬛ ',array_filter($line));

		$state = array_filter($data);

		unset($state['spend']);
		$state = array_map(function($a,$b){
			return $b.' => '.$a;
		} , $state, array_flip($state));


		$graph = '';
		for ($i = 1; $i <= $this->cells; $i++) {
			$graph.= (($current/$data['total'])>=($i/$this->cells))? $this->symbols['full']:$this->symbols['empty'];
		};

		$text = implode("\n", $state)."\n\n".$graph."\n\n";


		$text.= implode("\n",array_reverse($this->lines));

		@file_put_contents($this->file,$text);
	}
	public function tick($data=[])
	{
		$this->data['spend'] = number_format(self::time()-$this->last,3);
		$this->data['current']++;// = isset($data['current'])?$data['current']:++$this->data['current'];

		$left = ($this->data['total']-$this->data['current'])*$this->data['spend'];
		$this->data['left'] = gmdate("H:i:s", $left);

		$this->update($data);
	}
}