<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 邮件、短信、微信、等相关发送类库
 * add by zhixiao476@gmail.com
 * 2013年8月25日 20:14:36
 */
Class Send {
	private $CI;
	
	function __construct(){
		$this->CI = &get_instance();
	}
	
function send_email($data = array(
		'email' => '',	//目标接受地址
		'title' => '',	//邮件标题
		'content' => '',	//邮件内容
		'reply_to' => '',	// 回复邮件地址
	)){
		//error_reporting(E_ALL);
		if (empty($data['email'])){return false;}
		if (empty($data['content'])){return false;}
		//一下信息省略
		$this->CI->load->library('email');
		$config = array(
				'smtp' => 'smtp-n.global-mail.cn',
				'from' => 'gitlab@woshop.cn',
				'user' => 'gitlab@woshop.cn',
				'pass' => 'GIT123lab',
		);
		$e_config = array();
		$e_config['protocol'] = 'smtp';
		$e_config['smtp_host'] = $config['smtp'];
		$e_config['smtp_user'] = $config['user'];
		$e_config['smtp_pass'] = $config['pass'];
		$e_config['smtp_port'] = 465;
		$e_config['smtp_crypto'] = 'ssl';
		$e_config['wordwrap'] = TRUE;
		$e_config['mailtype'] = 'html';
		$e_config['charset'] = 'utf-8';
		$e_config['smtp_timeout'] = '10';
		$e_config['newline'] = "\r\n";
		//$e_config['crlf'] = "\r\n";

		$this->CI->email->initialize($e_config);
		$this->CI->email->set_mailtype('html');
		$this->CI->email->subject($data['title']);	//标题
		$this->CI->email->from($config['from'], $data['title'] . ' - 达沃信息化');
		$this->CI->email->reply_to($data['reply_to']);
		
		$this->CI->email->to($data['email']);
		$this->CI->email->message($data['content']);
		$send_res = $this->CI->email->send();

		if($send_res === true){
			$result[] = array('target' => $data['email'], 'status'=>1);
		}else{
			$send_err = $data['email'];
			$result[] = array('target' => $data['email'], 'status'=>-1, 'message' => $this->CI->email->print_debugger());
		}
		return $result;
	}
	
}