<?php
namespace index\controller;
use lijunfeng\framework\Template;
class D extends Template
{
	public function __construct()
	{
		parent::__construct('./cache/index','app/index/view');
		$this->_init();
	}
	public function _init()
	{
		
	}
	/**
	 * 初始化display
	 * @param  [type]  $viewFile [description]
	 * @param  boolean $isData   [description]
	 * @return [type]            [description]
	 */
	public function display($viewFile=null,$isData=true)
	{
		if(empty($viewFile)){
			$viewFile = $_GET['c'].'/'.$_GET['a'].'.html';
		}
		

		parent::display($viewFile,$isData=true);
	}
	/**
	 * 成功提示页面
	 * @return [type] [description]
	 */
	public function sucess()
	{

	}
	
}