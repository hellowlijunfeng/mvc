<?php
namespace admin\controller;
use admin\controller\D;
class Index extends D
{
	protected $obj;
	function _init()
	{
		
	}
	public function index()
	{
		$this->display();
	}
}