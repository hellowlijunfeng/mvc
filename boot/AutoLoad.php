<?php
class AutoLoad
{
	protected $map=[];
	public function __construct($config)
	{
		$this->map=$config;
		//加载自定义的 autoload方法
		spl_autoload_register([$this,'loadClass']);
	}
	public function loadClass($className)
	{
		//echo $className."<br/>";
		//取出类名
		
		$arr = explode('\\',$className);
		$class = array_pop($arr);
		
		//取出命名空间名
		$namespace = implode('\\',$arr);
	
		//对照map 整改命名空间
		$this->map($namespace,$class);
		

	}
	protected function map($namespace,$class)
	{
		if(array_key_exists($namespace,$this->map)){
			$path=$this->map[$namespace];
		}else{
			$path = str_replace('\\', '/', $namespace);
		}

		include rtrim($path,'/').'/'.$class.'.php';
	}
}