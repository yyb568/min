<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 通用model类
 * add by yinyibin
 * 2015年12月10日20:08:11
 */
class Common_model extends MY_Model{

	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 根据串号查询当前串号是否已经兑付过
	 * add by zhixiao476@gmail.com
	 * 2016年10月18日17:47:39
	 */
	public function getserialCodeByInfo($id = 0){
		$params = array(
				'table' => 'commis',
				'select' => 'id,unicode,serialCode,price',
				'where' => array("serialCode" => $id),
				'limit' => 1
		);
		$info = $this->get_list($params);
		return $info;
	}
	
	/**
	 * 判断给定的所有信息中是否存在
	 * add by zhixiao476@gmail.com
	 * 2016年09月20日13:25:15
	 */
	public function checkInfo($where = array(), $table = '', $limit = 1){
		$params = array(
				'table' => $table,
				'where' => $where,
				'limit' => $limit
		);
		return $this->get_list($params);
	}

	/**
	 * 查询申报可用机型
	 * add by zhixiao476@gmail.com
	 * 2016年08月23日10:52:10
	 */
	public function getReportedMachList(){
		$params = array(
				'table' => 'reportedmach',
				'limit' => -1
		);
		$list = $this->get_list($params);
		return $list;
	}
	
	/**
	 * 获取视频类型
	 * add by yinyibin
	 * 2017年11月20日21:45:10
	 */
	public function getMvTypeList(){
		$params = array(
			'table' => 'videotype',
			'select' => 'id,vname,status,created',
			'where' => array('status' => 1),
			'limit' => -1
		);
		return $this->get_list($params);
	}

	
	function __destruct(){
		if (is_object($this->db)){
			$this->db->close();
		}
	}
	
}