<?php
namespace lijunfeng\framework;
// $obj = new Page();
// //echo $obj->first().'<br/>';
// //echo $obj->last().'<br/>';
// //echo $obj->next().'<br/>';
// echo $obj->paging().'<br/>';
class Page
{
	protected $totleNum;	//总记录数
	protected $page;		//当期页
	protected $totlePage;	//总页数
	protected $pageOfNum=10;//每页显示条数
	protected $url;			//页面url

	public function __construct($totleNum=100,$pageOfNum=10)
	{
		$this->totleNum=$totleNum;
		$this->pageOfNum=$pageOfNum;
		$this->getPage();
		$this->totlePage=ceil($this->totleNum/$this->pageOfNum);
		$this->url=$this->getUrl();
		
	}
	/**
	 * 返回分页
	 * @return [type] [description]
	 */
	public function paging()
	{
		$a='';
		$pag = $this->page;
		if($this->totlePage - $pag >=5){
			$count = 5;
		}else{
			$count = $this->totlePage - $pag;
		}	
		for ($i=0; $i <$count ; $i++) { 
			$a.="<a  href='".$this->setUrl($pag)."'>".$pag."</a>";
			$pag++;
		}
		// $aa="<a  href='".$this->pre()."'>上一页</a>".";
		echo "<div class='page'>
				<a  href='".$this->pre()."'>上一页</a>"."
				$a
				<a  href='".$this->next()."'>下一页</a>"."共".$this->totlePage."页"."
			</div>";
		//设置样式
		echo "<style>
			.page {line-height:25px;}
			.page a{display:block; text-decoration:none; height:25px; line-height:25px; margin-left:5px; padding:0px 8px; border:1px solid #ddd; float:left;}
			.page a:hover{background-color:#26c486; color:#fff;}
			.page .hover{background-color:#26c486; color:#fff;}
		</style>";
	}
	/**
	 * 首页
	 * @return [type] [description]
	 */
	public function first()
	{
		return $this->setUrl(1);
	}
	/**
	 * 尾页
	 * @return [type] [description]
	 */
	public function last()
	{
		return $this->setUrl($this->totlePage);
	}
	/**
	 * 下一页
	 * @return [type] [description]
	 */
	public function next()
	{
		if($this->page >= $this->totlePage){
			return $this->setUrl($this->totlePage);
		}
		return $this->setUrl($this->page+1);
	}
	/**
	 * 上一页
	 * @return [type] [description]
	 */
	public function pre()
	{
		if($this->page <= 1){
			return $this->setUrl(1);
		}
		return $this->setUrl($this->page-1);
	}
	protected function setUrl($page)
	{
		if(stripos($this->url,"?")){
			return $this->url.'&Page='.$Page;
		}
		return $this->url.'?page='.$page;
	}
	/**
	 * 获取当前页
	 * @return [type] [description]
	 */
	protected function getPage()
	{
		if (empty($_GET['page'])) {
			$this->page=1; 
		}else{
			$this->page=$_GET['page'];
		}
		
	}
	/**
	 * 获取页面url
	 * @return [type] [description]
	 */
	public function getUrl()
	{
		$url  = $_SERVER['REQUEST_SCHEME'] . '://'; //拼接协议 http  /  https
		$url .= $_SERVER['SERVER_NAME'].':';	//拼接主机地址
		$url .= $_SERVER['SERVER_PORT'];		//拼接端口号
		//处理url的参数 去除page参数
		$data = parse_url($_SERVER['REQUEST_URI']);
		$url .= $data['path'];		//文件
		//处理掉 page参数
		if(!empty($data['query'])){

			parse_str($data['query'],$array);
			unset($array['page']);
			$url .='?'.http_build_query($array);
		}
		return rtrim($url,'?');
		
		
	}
}