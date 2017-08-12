<?php
namespace admin\model;
use lijunfeng\framework\Model;

class M extends Model
{

	public function __construct()
	{
		$config=include('config/config.php');
		parent::__construct($config);
	}

}