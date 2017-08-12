<?php
include 'boot/AutoLoad.php';
class Route
{
	protected static $load;
	public static function load()
	{
		$config = include('config/namespace.php');
		self::$load=new AutoLoad($config);
	}
	public static function router()
	{

		//获取路由
		$_GET['c'] = empty($_GET['c'])?'index':$_GET['c'];
		$_GET['a'] = empty($_GET['a'])?'index':$_GET['a'];
		$_GET['m'] = empty($_GET['m'])?'index':$_GET['m'];
		$c = '\\'.$_GET['m'].'\\'.'controller'.'\\'.ucfirst($_GET['c']);
		
		call_user_func([new $c(),$_GET['a']]);

	}
}