<?php
namespace lijunfeng\framework;
class Image
{
	protected $saveDir;				//保存路径
	protected $isUniqidName=false;	//是否随机文件名
	protected $path;				//加水印图片的路径
	/**
	 * 初始化属性
	 * @param [type]  $saveDir      保存图片的路径
	 * @param boolean $isUniqidName 是否随机产生文件名
	 */
	public function __construct($saveDir='./upload',$isUniqidName=false)
	{
		
		$this->saveDir = $this->checkDir($saveDir);
		$this->saveDir = $this->convertToSavedir();
		$this->isUniqidName = $isUniqidName;
		// $this->imageType = $this->getImageType($imageType);
	}
	/**
	 * 图片水印
	 * @param  [type] $destImage   原图片
	 * @param  [type] $sourceImage 水印图片
	 * @param  [type] $pos         位置 int 1-9
	 * @return [type]              成功返回加水印图片的路径
	 */
	public function watermark($destImage,$sourceImage,$pos)
	{
		 // - 1)、路径检测
		 if(!file_exists($destImage) || !file_exists($sourceImage))
		 {
		 	exit('图片资源不存在');
		 }
   		 // - 2)、计算图片尺寸
   		 list($desWidth,$desHeight)=getimagesize($destImage);
   		 list($souWidth,$souHeight)=getimagesize($sourceImage);
   		 // - 3)、计算水印位置
   		 //$this->location($pos);
    	 // - 4)、合并图片
    	 $dst_im = $this->createImage($destImage);
    	 $src_im = $this->createImage($sourceImage);
		 imagecopymerge($dst_im, $src_im, 100, 150, 0, 0, $souWidth, $souHeight, 30);
   		 // - 5)、保存图片
   		 $this->saveImage($dst_im,$destImage);
   		 // - 6)、释放资源 
   		 imagedestroy($dst_im);
   		 imagedestroy($src_im);
   		 return $this->path;

	}
	/**
	 * 保存图片
	 * @param  [type] $dst_im    图片资源
	 * @param  [type] $destImage [description]
	 * @return [type]            [description]
	 */
	protected function saveImage($dst_im,$destImage)
	{
		if($this->isUniqidName){
			$fileName = uniqid().'.'.$this->getExt($destImage);
		}else{
			$fileName = pathinfo($destImage)['filename'].'.'.$this->getExt($destImage);
		} 
		$ex = $this->getExt($destImage);
		$ext = $this->getImageType($ex);
		$funcname = 'image'.$ext;
		$path = $this->saveDir.$fileName;
		$funcname($dst_im,$path);
		$this->path = $path;
	}
	/**
	 * 获取文件后缀名
	 * @param  [type] $fileName 文件名
	 * @return [type]           后缀名
	 */
	protected function getExt($fileName)
	{
		return pathinfo($fileName)['extension'];
	}
	/**
	 * 创建图片资源
	 * @param  [type] $imagePath [description]
	 * @return [type]            [description]
	 */
	protected function createImage($imagePath)
	{
		$ext =  pathinfo($imagePath)['extension'];
		$ext =  $this->getImageType($ext);
		$func = 'imagecreatefrom'.$ext;
		return $func($imagePath);
	}
	/**
	 * 传一个后缀名 返回 对应的系统名  ex:  jpg  =>  jpeg
	 * @param  [type] $imageExt 文件后缀名
	 * @return [type]           [description]
	 */
	protected function getImageType($imageExt)
	{
		$arrayName = array(
			'jpg' =>'jpeg',
			'pjpeg'=>'jpeg',
			'bmp'=>'wbmp'
			 );
		if(array_key_exists($imageExt,$arrayName))
		{
			$Ext = $arrayName[$imageExt];
			return $Ext;
		}else{
			return $imageExt;
		}

	}
	/**
	 * 	转换目录的 \ to /
	 */
	protected function convertToSavedir()
	{
		$name = str_replace('\\','/',$this->saveDir);
		return rtrim($name,'/').'/';
	}
	/**
	 * 检查目录是否存在 不存在则创建一个目录
	 * @param  [type] $saveDir 目录
	 * @return [type]          [description]
	 */
	protected function checkDir($saveDir)
	{
		//判断文件目录是否存在 若不存在创建一个
		if(!is_dir($saveDir))
		{
			return mkdir($saveDir,0777);
		}
		if(!is_readable($saveDir) || !is_writable($saveDir))
		{
			chmod($saveDir,0777);
		}
		return $saveDir;
	}
}