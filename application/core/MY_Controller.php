<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 控制器基类
 * add by yinyibin
 * 2015年12月10日11:33:26
 */
if (ENVIRONMENT != 'product'){
	ini_set('memory_limit', '256M');
}
class MY_Controller extends CI_Controller{
	
	/**
	 * 常量变量定义
	 */
	public $template = array();			//模板数据
	protected $user_id;					//用户登陆id
	protected $username;				//用户账号
	protected $uname;					//姓名
	protected $isLogin = 0;				//登陆状态，1表示已经登陆
	protected $mymach;					// 当前登录用户机型列表
	protected $finance;					// 当前用户权限类型  1:财务；2：管理员
	protected $userinfo;
	protected $parentmenuList;			// 当前用户权限下的父菜单
	protected $lastmenulist;			// 当前用户权限下的子菜单
	protected $Msg;						//错误信息
	protected $settings;				//系统配置
	
	
	/**
	 * 初始化操作
	 * add by yinyibin
	 * 2015年12月10日11:34:15
	 */
	public function __construct(){
		parent::__construct();
		// $this->isLogin();			// 检查登录状态
		//$this->role();				//检查权限
		//$this->getSetting();		// 获取配置信息
		$this->template['uname'] = $this->uname;
		$this->template['userinfo'] = $this->userinfo;
		$this->template['finance'] = $this->finance;
		$this->template['mymach'] = $this->mymach;
	}
	
	/**
	 * 判断用户是否登陆
	 * add by yinyibin
	 * 2015年12月10日11:34:44
	 */
	public function isLogin(){
		$this->load->model("common_model");
		$this->load->library("Hencrypt");
		$userinfo = $this->input->cookie("dw_wx_bd");
		if (empty($userinfo)){
			redirect(site_url("login/index"));
			exit();
		}
		if (stripos($userinfo,'@') === false){
			redirect(site_url("login/index"));
			exit();
		}
		
		$user_info = explode('@',$userinfo);
		
		$user = $this->hencrypt->decrypt($user_info[0]);
		unset($userinfo);
		$userinfo = explode("@",$user);
		if (empty($userinfo)){
			redirect(site_url("login/index"));
			exit();
		}
		
		//查询数据库是否存在
		$this->common_model->set_table('auth');
		$this->username = $userinfo[0];
		$this->user_id = $userinfo[1];
		$password = $user_info[1];
		
		$params = array(
			'select' => 'id,uname,username,password,email,finance,show_static,mach_ids,role,province,city,district,status',
			'where' => array('username' => $this->username),
			'limit' => 1
		);
		$info = $this->common_model->get_list($params);
		if (empty($info)){
			redirect(site_url("login/index"));
		}
		
		if ($this->hencrypt->decrypt($password) != $this->hencrypt->decrypt($info['password'])){
			redirect(site_url("login/index"));
		}
		if ($info['status'] == 0){
			redirect(site_url("login/index"));
		}
		$this->mymach = $info['mach_ids'];
		$this->finance = $info['finance'];
		$this->uname = $info['uname'];
		$this->userinfo = $info;
		return true;
	}
	
	/**
	 * 获取并检查权限
	 * add by yinyibin
	 * 2016年08月04日16:41:20
	 */
	private function role(){
		//根据user_id查询当前用户权限
		$params = array(
				'table' => 'auth',
				'select' => 'id,role,status',
				'where' => array('id' => $this->user_id),
				'limit' => 1
		);
		$this->load->model("common_model");
		$userRole = $this->common_model->get_list($params);
		if (empty($userRole)){
			redirect(site_url("login/index"));exit();
		}
		
		// 获取权限
		if ($userRole['role'] != -1){		//系统管理员
			$role = unserialize($userRole['role']);		//拿到当前用户的权限列表
		}
		unset($params);
		// 获取系统菜单
		$params = array(
				'table' => 'menu',
				'select' => 'id,menu_name,pid,url,icon,sortd',
				'order' => 'sortd',
				'order_type' => 'ASC',
				'where' => array('pid' =>0),
				'limit' => -1
		);
		$menuList = $this->common_model->get_list($params);
		
		//根据当前用户的权限，筛选出符合当前用户的系统菜单
		$menu = array();
		foreach($menuList as $key => $val){
			$menu[$val['id']] = $val;
		}

		$Menu = array();
		if ($userRole['role'] != -1){
			foreach($menu as $key => $val){
				if (in_array($val['id'],$role)){
					if ($val['pid'] == 0){
						$sql = "select menu_name,url from dw_menu where pid={$val['id']}";
						$nums = $this->common_model->execute($sql);
						$parentmenu[$val['id']] = $val;
						$lastmenu[$val['id']] = $nums;
					}
				}
			}
		}else{
			foreach($menu as $key => $val){
				if ($val['pid'] == 0){
					$sql = "select menu_name,url from dw_menu where pid={$val['id']}";
					$nums = $this->common_model->execute($sql);
					$parentmenu[$val['id']] = $val;
					$lastmenu[$val['id']] = $nums;
				}
			}
		}
		$this->parentmenuList = $parentmenu;
		$this->lastmenulist = $lastmenu;
		$this->template['parentmenuList'] = $parentmenu;
		$this->template['lastmenulist'] = $lastmenu;
	}
	
	/**
	 * 获取系统配置信息
	 * add by yinyibin
	 * 2016年08月03日16:01:36
	 */
	private function getSetting(){
		$params = array(
				'table' => 'setting',
				'select' => 'content',
				'limit' => 1
		);
		$this->load->model("common_model");
		$_info = $this->common_model->get_list($params);
		$this->settings = @unserialize($_info['content']);
		$this->template['settings'] = $this->settings;
	}
	
	
	/**
	 * 错误信息输出格式
	 * add by yinyibin
	 * 2015年12月25日21:33:39
	 */
	public function showMessage($info){
		$this->template['info'] = $info;
		$html = $this->load->view("error/error",$this->template,true);
		echo $html;die;
	}
	
	/**
	 * 统一的错误输出
	 * add by yinyibin
	 * 2015年12月10日15:49:53
	 */
	public function splitJson($json,$status = 0,$type = 0) {
		$array = array('status' => $status,'info' => $json);
		if (empty($type)){
			echo json_encode($array);exit();
		}else{
			return json_encode($array);
		}
	}
	
}