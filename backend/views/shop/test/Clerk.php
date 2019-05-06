<?php

namespace backend\views\shop\test;
use yii\helpers\Url;

class Clerk
{
	public $file;
	public $last;
	public $symbols = [
		'empty'=>'⬜',
		'full'=>'⬛',
	];
	public $lines = [];
	public $text='';
	public $cells = 70;
	public $data = [
		'time'=>'',
		'spend'=>'',
		'progress'=>0,
		'left'=>'',
		'current'=>0,
		'total'=>0
	];
	public function __construct($file,$data)
	{
		$this->file = Url::to($file);
		$this->data = array_merge($this->data,$data);
		$this->last = time();
	}
	public function tick($data=[])
	{
		$spend = (time()-$this->last);
		if(!isset($data['current'])){
			$this->data['current']++;
		}
		$this->data = array_merge($this->data,$data);

		if(!isset($data['progress'])){
			$this->data['progress'] = number_format(100*$this->data['current']/$this->data['total'],2).'%';
		}

		$line = $data = $this->data;

		$this->last = time();
		$line['current'] = $line['current'].'/'.$line['total'];
		unset($line['total']);
		$line['time'] = date("H:i:s");
		$line['spend'] = $spend. ' sec.';

		$this->lines[] = implode("\t".'⬛ ',array_filter($line));

		$left = ($data['total']-$data['current'])*$spend;
		$data['left'] = gmdate("H:i:s", $left); //round((($data['total']-$data['current'])*$spend/60),2).' min.';

		$state = array_filter($data);

		$state = array_map(function($a,$b){
			return $b.' => '.$a;
		} , $state, array_flip($state));


		$graph = '';
		for ($i = 1; $i <= $this->cells; $i++) {
			$graph.= (($data['current']/$data['total'])>=($i/$this->cells))? $this->symbols['full']:$this->symbols['empty'];
		};

		$text = implode("\n", $state)."\n\n".$graph."\n\n";


		$text.= implode("\n",array_reverse($this->lines));

		file_put_contents($this->file,$text);

	}
}