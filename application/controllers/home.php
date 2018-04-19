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
		
		
		$this->load->view("main/right");
	}
	
}