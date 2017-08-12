<?php
namespace index\model;
use index\model;
class LinkModel extends M
{
	public function userinfo()
	{

		return $this->select();
		
	}
}