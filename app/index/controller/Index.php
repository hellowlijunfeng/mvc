<?php
namespace index\controller;
use index\controller;
use index\model\LinkModel;
class Index extends D
{
	protected $obj;
	/**
	 * 实例化model
	 * @return [type] [description]
	 */
	public function _init()
	{
		$this->obj=new LinkModel();
	}
	public function index()
	{
		
		$data = $this->obj->userinfo();
		$this->assign('cate',$data);
		$this->display();
	} 
}
