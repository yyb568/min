<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 通用model类
 * add by zhixiao476@gmail.com
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
	 * 获取指定条数的用户列表
	 * add by zhixiao476@gmail.com
	 * 2016年08月12日14:56:03
	 */
	public function getUserListP($user_id = 0, $limit = 5, $select = "id,unicode"){
		if (empty($limit) || empty($select)){return false;}
		$params = array(
				'table' => 'users',
				'select' => $select,
				'where' => array('id >'=> $user_id),
				'order_type' => 'asc',
				'order' => 'id',
				'limit' => $limit
		);
		$result = $this->get_list($params);
		return $result;
	}
	
	/**
	 * 通过发展人id查询会员信息
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日13:54:29
	 */
	public function getUserInfo($unicode = 0){
		if (empty($unicode)){return false;}
		$params = array(
			'table' => 'users',
			'select' => 'id,phone,unicode,nice,sex,uname,status,created,last_time,created',
			'where' => array('unicode' => $unicode, 'status' => 1),
			'limit' => 1
		);
		return $this->get_list($params);
	}
	
	/**
	 * 查询所有的注册用户
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日10:31:40
	 */
	public function getUserList($type = 0 , $select = "*"){
		$params = array(
				'table' => 'users',
				'select' => $select,
				'limit' => -1
		);
		$list = $this->get_list($params);
		if ($type == 1){
			foreach($list as $key => $val){
				$unList[$val['unicode']] = $val;
			}
			return $unList;
		}elseif ($type == 2){
			foreach($list as $key => $val){
				$unList[$val['id']] = $val;
			}
			return $unList;
		}else{
			return $list;
		}
	}
	
	/**
	 * 获取所有的机型
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日08:53:56
	 */
	public function getMachTypeList($type = 0){
		$params = array(
				'table' => 'excation',
				'select' => 'id,buss_id,mach_type,price,city,district,province,starttime,endtime,created',
				'limit' => -1
		);
		$list = $this->get_list($params);
		if (empty($type)){
			foreach($list as $key => $val){
				$machList[$val['id']] = $val;
			}
			return $machList;
		}else{
			return $list;
		}
		
	}
	
	/**
	 * 通过机型查询详细信息
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日13:34:56
	 */
	public function getMacInfo($macth_id = ''){
		if (empty($macth_id)){return false;}
		$params = array(
				'table' => 'excation',
				'select' => 'id,buss_id,mach_type,price,city,district,province,starttime,endtime,created',
				'where' => array('id' => $macth_id),
				'limit' => 1
		);
		$info = $this->get_list($params);
		return $info;
	}
	
	/**
	 * 获取平台商
	 * add by zhixiao476@gmail.com
	 * 2016年08月05日11:26:50
	 */
	public function getBussList(){
		$params = array(
				'table' => 'buss',
				'limit' => -1
		);
		$list = $this->get_list($params);
		return $list;
	}
	
	/**
	 * 获取地区全部数据
	 * add by zhixiao476@gmail.com
	 * 2016年08月05日13:54:04
	 */
	public function getDqList($type = 'sf'){
		if ($type == 'sf'){		//省
			$params = array(
					'table' => 'province',
					'limit' => -1
			);
		}elseif ($type == 'qx'){		//区县
			$params = array(
					'table' => 'district',
					'limit' => -1
			);
		}elseif ($type == 'cs'){	//城市
			$params = array(
					'table' => 'city',
					'limit' => -1
			);
		}
		$list = $this->get_list($params);
		return $list;
	}
	
	/**
	 * 查找省份
	 * add by zhixiao476@gmail.com
	 * 2016年08月05日13:48:19
	 */
	public function getDqByIdInfo($id = 0,$type = 'sf'){
		if ($type == 'sf'){		//省
			$params = array(
					'table' => 'province',
					'where' => array('ProvinceID' => $id),
					'limit' => 1
			);
		}elseif ($type == 'qx'){		//区县
			$params = array(
					'table' => 'district',
					'where' => array('DistrictID' => $id),
					'limit' => 1
			);
		}elseif ($type == 'cs'){	//城市
			$params = array(
					'table' => 'city',
					'where' => array('CityID' => $id),
					'limit' => 1
			);
		}
		$info = $this->get_list($params);
		return $info;
	}
	
	/**
	 * 根据提供的平台商中文名称，匹配查找平台商id
	 * add by zhixiao476@gmail.com
	 * 2016年08月05日16:00:41
	 */
	public function getSearchBussForId($info = array(), $searchTxt = ''){
		if (empty($info) || empty($searchTxt)){return false;}
		foreach($info as $key => $val){
			if (stripos($val['buss_name'],$searchTxt) !== false){
				return $val['id'];
			}
		}
	}
	
	/**
	 * 根据提供的省份、城市、区县的中文名称，找出对应的id来
	 * add by zhixiao476@gmail.com
	 * 2016年08月05日15:52:23
	 */
	public function getSearchDqForId($type = 'sf', $searchTxt = '', $info = array()){
		if (empty($info) || empty($searchTxt)){return false;}
		foreach($info as $key => $val){
			if ($type == 'sf'){
				if (stripos($val['ProvinceName'],$searchTxt) !== false){
					return $val['ProvinceID'];
				}
			}elseif ($type == 'cs'){
				if (stripos($val['CityName'], $searchTxt) !== false){
					return $val['CityID'];
				}
			}elseif ($type == 'qx'){
				if (stripos($val['DistrictName'], $searchTxt) !== false){
					return $val['DistrictID'];
				}
			}
		}
	}
	
	
	/**
	 * 通过给定的机型列表数据，查找相关机型信息 - area只表示省分
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日09:15:43
	 */
	public function getSearchMachForInfo($searchTxt = '', $machList = array() , $area, $dtime){		//不用系统时间，用excel文件导入的竣工时间
		foreach($machList as $key => $val){
			if ($val['mach_type'] == $searchTxt && $area == $val['province']){
				//$dtime = time();
				if ($dtime >= $val['starttime'] && $dtime <= $val['endtime']){
					return $val;
				}
			}
		}
	}
	
	/**
	 * 通过给定的机型、省、市、区三级关系进行查找机型
	 * 特别注意：这里的时间范围检查不用系统时间，用excel导入佣金时里面的竣工时间
	 * add by zhixiao476@gmail.com
	 * 2016年09月30日13:52:10
	 */
	public function getSearchMachForAreaInfo($search = '',$dtime =0, $machlist = array(), $pro = array(), $city = array(), $dis = array()){
		if (empty($search) || empty($machlist)) return false;
		$dtime = strtotime($dtime);
		if ($pro && empty($city) && empty($dis)){		// 只有省份的时候
			return $this->getSearchMachForInfo($search, $machlist, $pro, $dtime);
		}elseif ($pro && $city && empty($dis)){			// 有省份城市的时候
			foreach($machlist as $key => $val){
				if ($val['mach_type'] == $search && $pro == $val['province'] && $city == $val['city']){
					//$dtime = time();
					if ($dtime >= $val['starttime'] && $dtime <= $val['endtime']){
						return $val;
					}
				}
			}
		}elseif ($pro && $city && $dis){				// 三者都有的时候 
			foreach ($machlist as $key => $val){
				if ($val['mach_type'] == $search && $pro == $val['province'] && $city == $val['city'] && $dis == $val['district']){
					//$dtime = time();
					if ($dtime >= $val['starttime'] && $dtime <= $val['endtime']){
						return $val;
					}
				}
			}
		}
	}
	
	function __destruct(){
		if (is_object($this->db)){
			$this->db->close();
		}
	}
	
}