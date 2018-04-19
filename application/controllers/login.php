<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 管理登录相关操作
 * add by zhixiao476@gmail.com
 * 2016年08月03日13:49:38
 */
class Login extends CI_Controller{
	
	/**
	 * 初始化
	 * add by zhixiao476@gmail.com
	 * 2016年08月03日13:50:39
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 登录首页
	 * add by zhixiao476@gmail.com
	 * 2016年08月03日13:51:02
	 */
	public function index(){
		$this->load->view("login/login",$this->template);
	}
	
	/**
	 * 开始登陆
	 * add by zhixiao476@gmail.com
	 * 2016年08月04日15:59:43
	 */
	public function isLogin(){
		$username = $this->input->post("username", true);
		$password = $this->input->post("password", true);
		$username = zhixiao;
		$password = zhixiao;
		if (empty($username) || empty($password)){
			splitJson(array('status'=>1,'info'=>'请填写账号密码进行登陆！'));exit();
		}
		
		$this->load->library("hencrypt");
		$params = array(
				'table' => 'auth',
				'where' => array('username' => $username),
				'limit' => 1
		);

		$this->load->model("common_model");
		$info = $this->common_model->get_list($params);
		if (empty($info)){
			splitJson(array('status'=>1,'info'=>'请填写账号密码进行登陆！'));exit();
		}
		$pwd = $this->hencrypt->decrypt($info['password']);
	
		if ($password != $pwd){
			splitJson(array('status'=>1,'info'=>'账号或密码填写错误！'));exit();
		}
		if ($info['status'] != 1){
			splitJson(array('status' => 1,'info' => '当前账号被冻结！'));exit();
		}
		
		//设置最后登录时间+ip地址
		$data['last_time'] = time();
		$data['last_ip'] = $this->input->ip_address();
		$this->common_model->set_table("auth");
		$this->common_model->save($data,$info['id']);
		
		//写入cookie
		$this->setLoginCookie($info);
		splitJson(array('status'=>0,'info'=>'登陆成功！'));
	}
	
	/**
	 * 登陆写入cookie操作
	 * add by zhixiao476@gmail.com
	 * 2016年08月04日16:03:06
	 */
	private function setLoginCookie($info = array()){
		
		$domain = $_SERVER['SERVER_NAME'];		//获取cookie域名
		$this->load->library("Hencrypt");
		$cookie = array(
				'name'   => 'dw_wx_bd',
				'value'  => $this->hencrypt->encrypt($info['username']."@".$info['id']).'@'.$info['password'],
				'expire' => 86500 * 7,
				'domain' => $domain,
				'path'   => '/',
				'prefix' => '',
		);
		$this->input->set_cookie($cookie);
	}
	
	/**
	 * 退出登录
	 * add by zhixiao476@gmail.com
	 * 2016年08月04日16:39:32
	 */
	public function outlogin(){
		$domain = $_SERVER['SERVER_NAME'];		//获取cookie域名
		$cookie = array(
				'name'   => 'dw_wx_bd',
				'value'  => null,
				'expire' => time() - 3600*24,
				'domain' => $domain,
				'path'   => '/',
				'prefix' => '',
		);
		$this->input->set_cookie($cookie);
		redirect(site_url("login/index"));
	}
}

