<?php
namespace lijunfeng\framework;
class Template
{
	protected $tplDir;  //模板文件目录
	protected $cacheDir; //缓存路径
	protected $vars=[];

	public function __construct($cache='./cache',$tplDir='./view')
	{
		//检查 目录是否存在
		
		$this->tplDir = $this->checkDir($tplDir);
		$this->cacheDir = $this->checkDir($cache);
	}
	protected function checkDir($dir)
	{
		$dir = str_replace('\\','/', $dir);
		$dir = rtrim($dir,'/') . '/';
		$flag = true;
		if (!is_dir($dir)) {
			$flag =  mkdir($dir,0777,true);
		} else if (!is_readable($dir) || !is_writable($dir)) {
			$flag =  chmod($dir, 0777);
		}
		if (!$flag) {
			exit('目录不存在或不可写');
		}
		return $dir;
	}
	public function assign($name,$value)
	{
		
		$this->vars[$name]=$value;
		
	}
	/**
	 * 将html文件缓存为php文件 并加载
	 * @param  [type]  $viewFile index.html  模板文件名
	 * @param  boolean $isData   是否加载数据   TRUE or false
	 * @return [type]            [description]
	 */
	public function display($viewFile,$isData=true)
	{

		//1、拼接模板文件和 缓存文件
		$view = trim($this->tplDir,'/').'/'.$viewFile;
		$cache = explode('/',$viewFile);
		$cache = array_pop($cache);
		$cache = trim($this->cacheDir,'/').'/'.str_replace('.','_',$cache).'.php';
		
		// 2、检测文件是否存在
		if(!file_exists($view)){
			exit("模板文件不存在");
		}
		//3、编译文件 file_put_contents()
		$data = $this->compile($view);
		file_put_contents($cache,$data);
		//处理数据 加载文件
		if($isData){
			//处理数据
			extract($this->vars);
			include $cache;
		}
	}
	/**
	 * 编译文件成php文件 并加载
	 * @return [type] [description]
	 */
	protected function compile($view)
	{
		$content = file_get_contents($view);
		$refer=array(
				'{$%%}'=>'<?php echo $\1; ?>',
				'{switch %%}'=>'<?php switch(\1):?>',
				'{case %%}'=>'<?php case \1:?>',
				'{break}'=>'<?php break;?>',
				'{default}'=>'<?php default:?>',
				'{/switch}'=>'<?php endswitch;?>',
				'{if %%}' => '<?php if(\1): ?>',
				'{str_repeat %%,%%}' => '<?php echo str_repeat(\1,\2);?>',
				'{else}' => '<?php else:?>',
				'{/if}' => '<?php endif;?>',
				'{foreach $%%=$%%}'=>'<?php foreach($\1 as $\2):?>',
				'{/foreach}'=>'<?php endforeach; ?>',
				'{include(%%)}'=>'<?php include (\1);?>',
				'{substr($%%,%%,%%)}'=>'<?php echo substr($\1,\2,\3);?>',
				'{str_replace($%%,%%.$%%.%%,$%%)}'=>'<?php echo str_replace($\1,\2.$\3.\4,$\5);?>',
				'{str_replace($%%,%%.$%%.%%,%%)}'=>'<?php echo str_replace($\1,\2.$\3.\4,\5));?>',
				'{is_array($%%)}'=>'<?php is_array($\1); ?>',
				'{in_array($%%,$%%)}'=>'<?php in_array($\1,$\2); ?>',
				'{exploade(%%,$%%)}'=>'<?php explode(\1,$\2);?>',
				'{floor($%%)}'=>'<?php echo floor($\1);?>'
			);
		foreach ($refer as $key=>$value) {
			$key = preg_quote($key,'/');
			$pattern = '/'.str_replace('%%', '(.+)', $key) . '/U';
			if (stripos($key,'include')) {
				$content = preg_replace_callback($pattern,[$this,'includeManage'], $content);
			} else {
				$content = preg_replace($pattern, $value, $content);
			}
			
		}
		return $content;
	}
	/**
	 * 处理模板中加载的模板文件
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	protected function includeManage($data)
	{
		$this->display($data[1],false);
		$cacheFile =$this->cacheDir.str_replace('.', '_',$data[1]).'.php';
		return "<?php include '$cacheFile';?>";
	}
	
}