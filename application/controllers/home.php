<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 后台管理首页
 * add by zhixiao476@gmail.com
 * 2016年08月03日14:00:52
 */
class Home extends MY_Controller{
	
	/**
	 * 初始化
	 * add by zhixiao476@gmail.com
	 * 2016年08月03日14:01:10
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model("common_model");
	}
	
	/**
	 * 管理首页面
	 * add by zhixiao476@gmail.com
	 * 2016年08月03日14:01:42
	 */
	public function index(){
		$this->load->view("main/index",$this->template);
	}
	
	/**
	 * 后台管理首页右侧默认页面
	 * add by zhixiao476@gmail.com
	 * 2016年08月03日14:22:04
	 */
	public function rightmain(){
		$tops15 = $this->getstatis(15);
		$total = $machtotal = array();
		$dtimes = array();
		for($i = 0; $i<= 14; $i++){
			$dtime = date("Y-m-d",strtotime("-{$i} days"));
			$total[$dtime]['total'] = 0;
			$dtimes[] = $dtime;
			foreach($tops15 as $key => $val){
				if ($val['datetime'] == $dtime){
					$total[$dtime]['total'] += $val['total'];
				}
			}
		}
		$this->machstatis($tops15);		// 获取机型销售统计
		$this->template['tops15'] = $total;
		$this->template['tops15_date'] = $dtimes;
		$this->load->view("main/right",$this->template);
	}
	
	/**
	 * 机型销售方式统计
	 * add by zhixiao476@gmail.com
	 * 2016年08月24日08:44:33
	 */
	private function machstatis($list = array()){
		$mach = $mach_ids = $total = array();
		foreach($list as $key => $val){
			if (in_array($val['mach'], $mach) === false){
				$mach[$val['mach']]['total'] += $val['total'];
				$mach_ids[] = $val['mach'];
			}
		}
		// 获取机型列表
		$params = array(
				'table' => 'excation',
				'select' => 'id,mach_type',
				'where_in_field' => 'id',
				'where_in_value' => $mach_ids,
				'limit' => -1
		);
		$machlist = $this->common_model->get_list($params);
		foreach((array)$machlist as $key => $val){
			$machName[$val['id']] = $val;
		}
		foreach($mach as $key => $val){
			$mach[$key]['machname'] = $machName[$key]['mach_type'];
		}
		uasort($mach, 'uasort_cmp');
		$this->template['mach'] = array_slice($mach,0,10);
	}
	
	/**
	 * 首页数据汇总 - 15天内销售数据汇总
	 * add by zhixiao476@gmail.com
	 * 2016年08月23日17:20:05
	 */
	private function getstatis($day = 15){
		if (empty($day)) return false;
		$startTime = date("Y-m-d",strtotime("-{$day} days"));
		$endTime = date("Y-m-d");
		$params = array(
				'table' => 'statistic_day',
				'select' => 'id,mach,pro,city,dis,buss,totalprice,total,datetime',
				'limit' => -1
		);
		if ($this->finance == 2){
			$params['where'] = array("datetime >=" => $startTime,"datetime <=" => $endTime, "pro" => $this->userinfo['province']);
		}else{
			$params['where'] = array("datetime >=" => $startTime,"datetime <=" => $endTime);
		}
		
		$list = $this->common_model->get_list($params);
		
		return $list;
		
	}
}