<?php
namespace lijunfeng\framework;
/**
*  数据库操作类
*/
class Model
{
	protected $host;		//mysql地址
	protected $user;		//用户名
	protected $password;	//密码
	protected $dbName;		//数据库名
	protected $charset;		//字符集
	protected $link;		//连接数据库返回
	protected $cacheField;	//表记录缓存 文件目录
	protected $cacheData;   //缓存内容
	protected $sql;			//SQL语句
	protected $tabe;	//表名
	protected $union=array(
			'FIELD'=>'*',
			'TABLE'=>'',
			'WHERE'=>'',
			'GROUP'=>'',
			'HAVING'=>'',
			'ORDER'=>'',
			'LIMIT'=>'',
			'SET'=>'',
			'VALUE'=>''
		);

	public function __construct($config)
	{

		$this->host = $config['db_host'];
		$this->user = $config['db_user'];
		$this->password = $config['password'];
		$this->dbName = $config['db_name'];
		$this->charset = $config['charset'];
		$this->connect();
		$this->tabe=$this->getTable();
		$this->union['TABLE']=$this->tabe;
		$this->cacheField = $config['db_cache'];
		//生成缓存字段
		$this->cacheData = $this->getCache();
		
	}
	/**
	 * 缓存数据库字段
	 * @return [type] [description]
	 */
	protected function getCache()
	{
		
		$cacheFilename = rtrim($this->cacheField,'/').'/'.$this->tabe.'.php';
		
		if(file_exists($cacheFilename)){
			return include $cacheFilename;
		}else{
			//不存在创建
			$sql = 'desc '.$this->tabe;
			echo $sql;
			$result = mysqli_query($this->link,$sql);
			$field = [];

			//将字段添加到数组
			while($row = mysqli_fetch_assoc($result))
			{
				//把主键也添加到数组
				if ($row['Key'] == 'PRI') {
					$field['PRI'] = $row['Field'];
				}
				$field[] = $row['Field'];
			}
			

			//写入文件
			$str ="<?php \n return ".var_export($field,true).';';
			file_put_contents($cacheFilename, $str);

			return $field;
		}

		

	}
	/**
	 * 连接数据库
	 * @return [type] 
	 */
	protected function connect()
	{
		//连接数据库
		$link = mysqli_connect($this->host,$this->user,$this->password);
		if (!$link) {
			exit('数据库连接失败');
		}
		//选择数据库
		if(!mysqli_select_db($link,$this->dbName)){
			exit('数据库选择失败');
		}
		if (!mysqli_set_charset($link,$this->charset)) {
			exit('字符集设置失败');
		}
		$this->link = $link;

		
	}
	/**
	 *	获取表名
	 * @return [type] [description]
	 */
	protected function getTable()
	{
		//从类名获得表名
		//获取当前对象的类名，并且转换为小写
		$className = strtolower(get_class($this));
		//使用反斜线分割类名  'app\index\model\usermodel'
		$className = explode('\\',$className);
		//获取类名
		$className = array_pop($className);
		//获取类名model前的部分，例如usermodel,得到user
		if (stripos($className, 'model') === false) {
			//类名中不包含model
			return $this->prefix .$className;
		}
		$className = substr($className, 0,-5);
		
		return $className;
		
	}
	//WHER 条件 
	//['id'=>5,'name'=>'34']
	public function where($where)
	{
		if(is_string($where)){

			$this->union['WHERE']=' where '.$where;
		}
		if(is_array($where)){
			foreach ($where as $key => $value) {
				if(is_string($value)){
					$value = "'$value'";
				}
				$array[] = $key.'='.$value;
			}
			$where = implode(' and ', $array);
			$this->union['WHERE']=' where '.$where;

		}
		
		return $this;
	}
	//字段
	public function field($field)
	{
		$this->union['FIELD']=$field;
		return $this;
	}
	//分组
	public function group($group)
	{
		$this->union['GROUP']=' group by '.$group;
		return $this;
	}
	public function having($having)
	{
		$this->union['HAVING']=' having '.$having;
		return $this;
	}
	public function order($order)
	{
		$this->union['ORDER']=' order by '.$order;
		return $this;
	}
	public function limit($limit)
	{
		$this->union['LIMIT']=' limit '.$limit;
		return $this;
	}
	/**
	 * 查询语句
	 * @return [type] [description]
	 */
	public function select()
	{
		

		$sql = "select %FIELD% from %TABLE% %WHERE% %GROUP% %HAVING% %ORDER% %LIMIT%";
		$sql =str_replace(
				[
					"%FIELD%",
					"%TABLE%",
					"%WHERE%",
					"%GROUP%",
					"%HAVING%",
					"%ORDER%",
					"%LIMIT%"
				], 
				[
					$this->union['FIELD'],
					$this->union['TABLE'],
					$this->union['WHERE'],
					$this->union['GROUP'],
					$this->union['HAVING'],
					$this->union['ORDER'],
					$this->union['LIMIT']

				], $sql);
		
		return $this->query($sql);
		
	}
	public function insert($data,$isreturnId=false)
	{
		//插入数据 前  先分析  字段是否存在表中 不存在就过滤掉
		//交换缓存的键和值
		$cach = array_flip($this->cacheData);
		//过滤字段
		$data = array_intersect_key($data,$cach);
		//处理数据成字符串
		$this->settoString($data);
		$sql = "insert into %TABLE%(%SET%) VALUES(%VALUE%)";
		$sql =str_replace(
				[
					"%TABLE%",
					"%SET%",
					"%VALUE%"
				], 
				[
					$this->union['TABLE'],
					$this->union['SET'],
					$this->union['VALUE'],

				], $sql);
		return $this->carry($sql,$isreturnId);
		
	}
	protected function carry($sql,$isreturnId)
	{
		$result = mysqli_query($this->link,$sql);
		$this->emptyarray();
		if($result && $isreturnId){
			return mysqli_insert_id($this->link);
		}
		return false;
	}
	/**
	 * 删除表数据
	 * @return [type] [description]
	 */
	public function delete()
	{
		$sql = "delete FROM %TABLE% %WHERE%";
		$sql =str_replace([
					'%TABLE%',
					'%WHERE%'
					],
					[
					$this->union['TABLE'],
					$this->union['WHERE']
					],$sql);
			
			return $this->queryResult($sql);
	}

	/**
	 * 修改数据 传一个数组
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function update($data)
	{
		
		//交换缓存的键和值
		$cach = array_flip($this->cacheData);
		//过滤字段
		$data = array_intersect_key($data,$cach);
		//处理
		$this->updataToString($data);
		
		$sql = "update %TABLE% SET %SET% %WHERE% %ORDER% %LIMIT%";
		$sql =str_replace(
				[
					"%TABLE%",
					"%SET%",
					"%WHERE%",
					"%ORDER%",
					"%LIMIT%"
				], 
				[
					$this->union['TABLE'],
					$this->union['SET'],
					$this->union['WHERE'],
					$this->union['ORDER'],
					$this->union['LIMIT']

				], $sql);
			return $this->queryResult($sql);
	}
	protected function queryResult($sql)
	{
		$result = mysqli_query($this->link,$sql);
		$this->emptyarray();
		if($result && mysqli_affected_rows($this->link)){

			return true;
			
		}else{
			
			return false;
		}
	}
	/**
	 * update  信息处理
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	protected function updataToString($data)
	{
		//['name'=>'lijunfneg']
		foreach ($data as $key => $value) 
		{
			if(is_string($value)){
				$updata[] = $key.'='."'$value'";
			}else{
				$updata[] = $key.'='.$value;
			}
			
		}
		$this->union['SET']=implode(',',$updata);
	}
	/**
	 * 将insert 的数据 处理 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	protected function settoString($data)
	{
	
		$zhi = array_values($data);
		foreach($zhi as $key=>$value)
		{

			if(is_string($value))
			{
				$value= "'$value'";
			}
			$come[]= $value;
		}
		
		;
		$this->union['SET'] = implode(',',array_keys($data));
		$this->union['VALUE'] = implode(',',$come);

	}
	/**
	 * query执行sql语句
	 * @param  [type] $sql      sql语句
	 * @param  [type] $dataType 查询结果返回的 类型
	 * @return [type]           [description]
	 */
	public function query($sql,$dataType=MYSQLI_BOTH)
	{

		$result = mysqli_query($this->link,$sql);
		$this->emptyarray();
		if($result && mysqli_affected_rows($this->link)){

			return mysqli_fetch_all($result,$dataType);
			
		}else{
			
			return false;
		}

	}
	//清空数据
	protected function emptyarray()
	{

		$this->union=array(
			'FIELD'=>'*',
			'TABLE'=>'link',
			'WHERE'=>'',
			'GROUP'=>'',
			'HAVING'=>'',
			'ORDER'=>'',
			'LIMIT'=>'',
			'SET'=>'',
			'VALUE'=>''
		);
	}
	
}






