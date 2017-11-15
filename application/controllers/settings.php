<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 系统设置页面
 * add by zhixiao476@gmail.com
 * 2016年08月03日15:01:11
 */
class Settings extends MY_Controller{
	
	/**
	 * 初始化
	 * add by zhixiao476@gmail.com
	 * 2016年08月03日15:01:31
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model("common_model");
	}
	
	/**
	 * 系统设置页面
	 * add by zhixiao476@gmail.com
	 * 2016年08月03日15:01:54
	 */
	public function index(){
		
		//从数据库中找出配置信息
		$params = array(
				'table' => 'setting',
				'limit' => 1
		);
		$_info = $this->common_model->get_list($params);
		
		$info = @unserialize($_info['content']);
		
		$this->template['info'] = $info;
		$this->template['infoid'] = $_info['id'];
		$this->load->view("settings/index", $this->template);
	}
	
	/**
	 * 保存配置信息
	 * add by zhixiao476@gmail.com
	 * 2016年08月03日15:47:26
	 */
	public function doSave($id = 0){
		$dt = $this->input->post();
		$check_array = array('webname','weburl','domain','isregister','email');
		foreach ($dt as $key => $val){
			if (!in_array($key,$check_array)){
				$this->splitJson('非法提交！',1);
			}
			if (empty($val) && $key != 'isregister'){
				$this->splitJson('信息填写不完整，请填写完成！',1);
			}
		}
		
		//保存到数据库中
		$data['content'] = serialize($dt);
		$this->common_model->set_table('setting');
		$info_id = $this->common_model->save($data,$id);
		if (empty($info_id)){
			$this->splitJson('信息未发生变动，或者信息保存失败！',1);
		}else{
			$this->splitJson('信息保存成功！',0);
		}
	}
}