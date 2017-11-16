<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 视频类型管理
 * add by yinyibin
 * 2017年11月16日22:41:12
 */
class Videotype extends MY_Controller{
	
	/**
	 * 初始化
	 * add by yinyibin
	 * 2016年08月05日16:49:14
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model("common_model");
	}
	
	/**
	 * 注册用户管理页面
	 * add by yinyibin
	 * 2016年08月05日16:50:30
	 * @param number $offset：分页
	 */
	public function index($offset = 0){
		$pageSize = 20;
		$this->load->library('pagination');					// 分页类库加载

		$sql = "select id,vname,status,created from dw_videotype where 1=1";
		$totalSql = "select count(id) as count from dw_videotype where 1=1";

		$list = $this->common_model->execute($sql);
		$totalInfo = $this->common_model->execute($totalSql);
		$total = $totalInfo[0]['count'];

		$config['base_url'] = site_url("dw_videotype/index/{page}");
		$config['total_rows'] = $total;
		$config['per_page'] = $pageSize;
		$config['uri_segment'] = 3;
		$config['num_links'] = 6;
		$config['cur_tag_open'] = '<li class="paginate_button active">';
		$config['cur_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);
		$pageStr = $this->pagination->create_links();


		$this->template['list'] = $list;
		$this->template['Page'] = '<ul class="pagination ">'.rtrim($pageStr,"/").'</ul>';
		$this->load->view("videotype/index", $this->template);
	}
	
	/**
	 * 编辑注册用户资料
	 * add by yinyibin
	 * 2016年08月06日11:12:21
	 */
	public function doAdd($user_id = 0){
		if (!empty($user_id)){
			$params = array(
					'table' => 'videotype',
					'where' => array('id' => $user_id),
					'limit' => 1
			);
			$info = $this->common_model->get_list($params);
			$this->template['info'] = $info;
		}
		$this->load->view("videotype/edit",$this->template);
	}
	
	/**
	 * 保存会员资料
	 * add by yinyibin
	 * 2016年08月06日11:36:37
	 */
	public function doSave($user_id = 0){
		$data['vname'] = $this->input->post('vname',true);
	    $data['status']  = $this->input->post('status',true);
	    $data['created']  = time();
	    //基本信息判断
	    if (empty($data['vname'])){$this->splitJson('请填写类型名称！',1);}

	    // 查询是否已经存在
	    $params = array(
	    		'table' => 'videotype',
	    		'select' => 'id',
	    		//'where' => array('uid' => $data['uid'], 'unicode' => $data['unicode']),
	    		'limit' => 1
	    );

	    if ($user_id){
	    	$params['where'] = array('id !=' => $user_id,'vname' => $data['vname']);
	    }else{
	    	$params['where'] = array('vname' => $data['vname']);
	    }

	    $userinfo = $this->common_model->get_list($params);
	    if (!empty($userinfo)){
	    	$this->splitJson('当前填写的视频类型存在重复！',1);
	    }
	    //准备入库
	    $this->common_model->set_table("videotype");
	    $info_id = $this->common_model->save($data,$user_id);
	    if (empty($info_id)){
	    	$this->splitJson('保存信息失败！',1);
	    }else{
	    	$this->splitJson('保存成功！',0);
	    }
	}
	
	/**
	 * 删除
	 * add by yinyibin
	 * 2016年08月06日11:59:18
	 */
	public function DelInfo($user_id = 0){
		if (empty($user_id)){$this->splitJson('无效的ID属性值！',1);}
		
		$this->common_model->set_table("videotype");
		$info_id = $this->common_model->delById($user_id);
		if (empty($info_id)){
			$this->splitJson('删除失败！',1);
		}else{
			$this->splitJson('删除成功！',0);
		}
	}
}