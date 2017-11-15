<?php defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * 系统权限相关操作
 * add by yinyibin
 * 2016年08月04日08:40:21
 */
class Auth extends MY_Controller{
	
	/**
	 * 初始化
	 * add by zhixiao476@gmail.com
	 * 2016年08月04日08:40:38
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model("common_model");
	}
	
	/************************管理员(start)************************************************/
	
	/**
	 * 管理员管理
	 * add by yinyibin
	 * 2016年08月04日14:57:10
	 */
	public function adminlist($offset = 0){
		$pageSize = 20;
		$params = array(
				'table' => 'auth',
				'offset' => $offset,
				'limit' => $pageSize
		);
		$list = $this->common_model->get_list($params);
		$params['total'] = true;
		$total = $this->common_model->get_list($params);
		
		$this->load->library('pagination');
		$config['base_url'] = site_url("auth/adminlist/{page}");
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
		
		$this->load->view("auth/adminlist",$this->template);
	}
	
	/**
	 * 添加管理员
	 * add by yinyibin
	 * 2016年08月04日15:07:42
	 */
	public function adminAdd($admin_id = 0){
		if (!empty($admin_id)){
			$params = array(
					'table' => 'auth',
					'where' => array('id' => $admin_id),
					'limit' => 1
			);
			$info = $this->common_model->get_list($params);
			$this->load->library("hencrypt");
			
			$info['password'] = $this->hencrypt->decrypt($info['password']);
			
			
			$this->template['pro'] = $this->common_model->getDqList('sf');
			$this->template['city'] = $this->common_model->getDqList('cs');
			$this->template['dis'] = $this->common_model->getDqList('qx');
			$this->template['info'] = $info;
		}
		
		//获取系统菜单
		$params = array(
				'table' => 'menu',
				'limit' => -1
		);
		$menu_list = $this->common_model->get_list($params);
		foreach($menu_list as $key => $val){
			$menuList[$val['id']] = $val;
		}
		
		$this->template['menuList'] = $menuList;
		$this->load->view("auth/adminadd", $this->template);
	}
	
	/**
	 * 添加管理员的时候选择管理的机型
	 * add by yinyibin
	 * 2016年09月27日09:17:29
	 */
	public function getMachList(){
		$pro = $this->input->get("pro",true);
		$city = $this->input->get("city",true);
		$dis = $this->input->get("dis",true);
		if (empty($pro)){$this->splitJson('您需要先选择管理员所在地区！',1);}
		
		$params = array(
				'table' => 'excation',
				'select' => 'id,buss_id,mach_type,province,starttime,endtime',
				'where' => array('province' => $pro),
				'limit' => -1
		);
		$list = $this->common_model->get_list($params);
		if (empty($list)){
			$this->splitJson('该省份下并未找到任何可用的机型，请先添加机型！',1);
		}
		/*$timed = time();
		foreach($list as $key => $val){
			if ($val['endtime'] >= $timed){
				continue;
			}else{
				unset($list[$key]);
			}
		}*/
		foreach($list as $key => $val){
			$list[$key]['mach_type'] = $val['mach_type'].' - '.date("Y-m-d H:i:s",$val['endtime']);
		}
		sort($list);
		if (empty($list)){
			$this->splitJson('该省份下的机型中兑换时间已过期，无法使用！',1);
		}
		$this->splitJson($list,0);
	}
	
	/**
	 * 保存管理员
	 * add by yinyibin
	 * 2016年08月04日15:18:10
	 */
	public function doAdminSave($admin_id = 0){
		if (empty($admin_id)){	//如果是新增，则接受账号修改
			$data['username'] = $this->input->post("username",true);
		}
		$data['password'] = $this->input->post("password",true);
		$data['uname'] = $this->input->post("uname",true);
		$data['email'] = $this->input->post("email",true);
		$data['finance'] = $this->input->post("finance",true);
		$data['show_static'] = $this->input->post("show_static", true);
		$area = $this->input->post("area",true);
		$role_list = $this->input->post("role_list",true);
		$mach_ids = $this->input->post("mach_ids",true);
		
		
		if (empty($admin_id) && (empty($data['username']) || empty($data['password']) || empty($data['uname']))){
			$this->splitJson('请填写基本信息！',1);
		}
		
		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			$this->splitJson('请填写管理员的邮箱地址！',1);
		}
		
		//判断账号是否添加过
		if (!$admin_id){
			$params = array(
					'table' => 'auth',
					'where' => array('username' => $data['username']),
					'limit' => 1
			);
			$dInfo = $this->common_model->get_list($params);
			if (!empty($dInfo)){
				$this->splitJson('该账号已经存在，请更换账号！',1);
			}
		}
		
		$this->load->library("hencrypt");
		$data['password'] = $this->hencrypt->encrypt($data['password']);
		
		if ($role_list == -1){
			$data['role'] = -1;
		}else{
			$data['role'] = serialize($role_list);
		}
		
		// 将地区拆分成三份
		$_area = explode(",", $area);
		$data['province'] = $_area[0]; 
		$data['district'] = (int)$_area[2]; 
		$data['city'] = (int)$_area[1]; 
		
		if (empty($data['province']) && empty($data['city']) && empty($data['district'])){
			$this->splitJson('请选择地区',1);
		}
		if (is_array($mach_ids)){
			foreach($mach_ids as $key => $val){
				$machIds .= $val.",";
			}
		}else{
			$machIds = $mach_ids;
		}
		$data['mach_ids'] = $machIds;
		
		//入库
		$this->common_model->set_table("auth");
		$info_id = $this->common_model->save($data,$admin_id);
		if (empty($info_id)){
			$this->splitJson('保存失败！',1);
		}else{
			$this->splitJson('保存成功！',0);
		}
	}
	
	/**
	 * 删除管理员
	 * add by yinyibin
	 * 2016年08月04日15:34:49
	 */
	public function delAdmin($admin_id = 0){
		if (empty($admin_id)){$this->splitJson('无效的ID属性值！',1);}
		$this->common_model->set_table("auth");
		$info_id = $this->common_model->delById($admin_id);
		if (empty($info_id)){
			$this->splitJson('删除失败！',1);
		}else{
			$this->splitJson('删除成功',0);
		}
	}
	
	/************************管理员分组(end)************************************************/
	
	
	/************************管理员分组(start)************************************************/
	/**
	 * 分组管理
	 * add by yinyibin
	 * 2016年08月04日08:41:25
	 */
	public function group_index($offset = 0){
		$pageSize = 20;
		
		$params = array(
				'table' => 'group',
				'select' => 'id,group_name,menu_ids,status',
				'offset' => $offset,
				'limit' => $pageSize
		);
		$list = $this->common_model->get_list($params);
		$params['total'] = true;
		$total = $this->common_model->get_list($params);
		
		$this->load->library('pagination');
		$config['base_url'] = site_url("auth/group_index/{page}");
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
		$this->load->view("auth/group_index", $this->template);
	}
	
	/**
	 * 用户分组编辑
	 * add by yinyibin
	 * 2016年08月04日09:24:34
	 */
	public function group_add($group_id = 0){
		if (empty($group_id)){
			$this->load->view("auth/group_add", $this->template);
		}else{
			$params = array(
					'table' => 'group',
					'select' => 'id,group_name,menu_ids,status',
					'where' => array('id' => $group_id),
					'limit' => 1
			);
			$info = $this->common_model->get_list($params);
			$this->template['info'] = $info;
			$this->load->view("auth/group_add",$this->template);
		}
	}
	/**
	 * 保存分组信息
	 * add by yinyibin
	 * 2016年08月04日09:27:57
	 */
	public function doGroupSave($group_id = 0){
		$data['group_name'] = $this->input->post("group_name",true);
		$data['status'] = $this->input->post("isshow",true);
		if (empty($data['group_name'])){$this->splitJson('请输入分组名称！',1);}
		
		$this->common_model->set_table("group");
		$info_id = $this->common_model->save($data,$group_id);
		if (empty($info_id)){
			$this->splitJson('保存失败！',1);
		}else{
			$this->splitJson('保存成功！',0);
		}
	}
	
	/**
	 * 删除管理员分组
	 * add by yinyibin
	 * 2016年08月04日09:39:58
	 */
	public function del_group($group_id = 0){
		if (empty($group_id)){$this->splitJson('无效的ID属性值，请刷新页面重新尝试！',1);}
		$this->common_model->set_table("group");
		$info_id = $this->common_model->delById($group_id);
		if (empty($info_id)){
			$this->splitJson('删除失败！',1);
		}else{
			$this->splitJson('删除成功',0);
		}
	}
	/************************管理员分组(end)************************************************/
	
	/************************权限列表管理(start)************************************************/
	
	/**
	 * 权限列表管理
	 * add by yinyibin
	 * 2016年08月04日09:57:33
	 */
	public function acllist($offset = 0){
		$pageSize = 20;
		$params = array(
				'table' => 'acl',
				'limit' => $pageSize,
				'offset' => $offset
		);
		$list = $this->common_model->get_list($params);
		$params['total'] = true;
		$total = $this->common_model->get_list($params);
		
		$this->load->library('pagination');
		$config['base_url'] = site_url("auth/acllist/{page}");
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
		$this->load->view("auth/acl_index", $this->template);
	}
	
	
	/**
	 * 系统权限添加、修改
	 * add by yinyibin
	 * 2016年08月04日10:04:48
	 */
	public function acl_add($acl_id = 0){
		
		//获取导航菜单
		$params = array(
				'table' => 'menu',
				'select' => 'id,menu_name,pid',
				'where' => array('pid' => 0),
				'limit' => 999999, 
		);
		$menuList = $this->common_model->get_list($params);
		$this->template['menuList'] = $menuList;
		
		if (!empty($acl_id)){
			$params = array(
					'table' => 'acl',
					'where' => array('id' => $acl_id),
					'limit' => 1
			);
			$info = $this->common_model->get_list($params);
			$this->template['info'] = $info;
		}
		$this->load->view("auth/acl_add", $this->template);
	}
	
	/**
	 * ajax方式获取菜单的二级分类
	 * add by yinyibin
	 * 2016年08月04日13:41:44
	 */
	public function acl_menu($menu_id = 0){
		if (empty($menu_id)){return false;}
		$params = array(
				'table' => 'menu',
				'where' => array('pid' => $menu_id),
				'limit' => 9999999
		);
		$list = $this->common_model->get_list($params);
		$this->splitJson($list,0);
	}
	
	/************************权限列表管理(end)************************************************/
	
	
	/************************系统菜单管理(start)************************************************/
	
	/**
	 * 系统菜单管理
	 * add by yinyibin
	 * 2016年08月04日14:26:04
	 */
	public function menuList($offset = 0){
		$pageSize = 20;
		$params = array(
				'table' => 'menu',
				'order' =>'sortd',
				'order_type' =>'ASC',
				'offset' => $offset,
				'limit' => $pageSize,
		);
		$list = $this->common_model->get_list($params);
		$params['total'] = true;
		$total = $this->common_model->get_list($params);
		
		$_list = array();
		foreach((array)$list as $key => $val){
			$_list[$val['id']] = $val;
		}
		
		$this->load->library('pagination');
		$config['base_url'] = site_url("auth/menuList/{page}");
		$config['total_rows'] = $total;
		$config['per_page'] = $pageSize;
		$config['uri_segment'] = 3;
		$config['num_links'] = 6;
		$config['cur_tag_open'] = '<li class="paginate_button active">';
		$config['cur_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);
		$pageStr = $this->pagination->create_links();
		
		$this->template['list'] = $_list;
		$this->template['Page'] = '<ul class="pagination ">'.rtrim($pageStr,"/").'</ul>';
		$this->load->view("auth/menuList", $this->template);
	}
	
	/**
	 * 添加系统菜单
	 * add by yinyibin
	 * 2016年08月04日14:34:09
	 */
	public function menuAdd($menu_id = 0){
		
		//获取一级菜单
		$params = array(
				'table' => 'menu',
				'where' => array('pid' => 0),
				'limit' => 9999999
		);
		$ParentMenuList = $this->common_model->get_list($params);
		
		if (!empty($menu_id)){
			$params = array(
					'table' => 'menu',
					'where' => array('id' => $menu_id),
					'limit' => 1
			);
			$info = $this->common_model->get_list($params);
			$this->template['info'] = $info;
		}
		
		$this->template['ParentMenuList'] = $ParentMenuList;
		$this->template['menu_id'] = $menu_id;
		$this->load->view("auth/menuAdd", $this->template);
	}
	
	
	/**
	 * 保存菜单
	 * add by yinyibin
	 * 2016年08月04日14:44:25
	 */
	public function doMenuSave($menu_id = 0){
		$data['sortd'] = $this->input->post("sortd",true);
		$data['pid'] = $this->input->post("ParentId",true);
		$data['menu_name'] = $this->input->post("lastName",true);
		$data['url'] = $this->input->post("url",true);
		$data['icon'] = $this->input->post("icon",true);
		
		if (empty($data['menu_name'])){$this->splitJson('请填写菜单名称！',1);}
		
		if (empty($data['pid'])){$data['pid'] = 0;$data['url'] = '';}else{$data['icon'] = '';}
		
		$this->common_model->set_table("menu");
		$info_id = $this->common_model->save($data,$menu_id);
		if (empty($info_id)){
			$this->splitJson('保存失败！',1);
		}else{
			$this->splitJson('保存成功！',0);
		}
	}
	
	/**
	 * 删除系统菜单
	 * add by yinyibin
	 * 2016年08月04日14:53:19
	 */
	public function delMenu($menu_id = 0){
		$this->common_model->set_table("menu");
		$info_id = $this->common_model->delById($menu_id);
		if (empty($info_id)){
			$this->splitJson('删除失败！',1);
		}else{
			$this->splitJson('删除成功！',0);
		}
	}
	/************************系统菜单管理(end)************************************************/
	
}