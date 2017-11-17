<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 注册用户管理
 * add by yinyibin
 * 2016年08月05日16:48:58
 */
class User extends MY_Controller{
	
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
		$phone = $this->input->get("phone",true);			//找出相同的手机号
		$expload = $this->input->get("expload",true);		//将组合条件查询后的结果导出到Excel文件	
		$searchType = $this->input->get("searchtype",true);	//是否是搜索类型
		$keyword = $this->input->get("keyword",true);		// 搜索查询关键词
		
		$this->load->library('pagination');					// 分页类库加载

		
		$sql = "select id,uname,totalprice,phone,nice,level,sex,email,status,last_time,open_id from dw_users where 1=1";
		$totalSql = "select count(id) as count from dw_users where 1=1";
		// 搜索关键词
		if (!empty($keyword) && is_numeric($keyword)){
			$where .=" and (phone='{$keyword})";
		}elseif (!empty($keyword)){		//如果不是数字，则搜索其他
			$where .=" and (nice like '%{$keyword}%' or uname like '%{$keyword}%') ";
		}elseif (filter_var($keyword, FILTER_VALIDATE_EMAIL)){		//判断是否是邮箱
			$where .= " and email='{$keyword}'";
		}
		
		// 时间
		if (!empty($start) && !empty($end)){
			$starttime = strtotime($start);
			$endtime = strtotime($end);
			$where .= " and (created >= {$starttime} and created <={$endtime})";
		}
		
		if ($phone){$where .= " and phone ={$phone}";}
		
		//组合sql语句
		$sql .= $where;
		if (empty($expload)){			// 只有当不是导出文件的时候才加上分页
			$sql .= " limit {$offset},{$pageSize}";
		}

		$totalSql .= $where;
		$list = $this->common_model->execute($sql);
		if (empty($expload)){		//  只有当不是导出的时候才查询总条数
			$totalInfo = $this->common_model->execute($totalSql);
			$total = $totalInfo[0]['count'];
		}
		

		/**
		 * 调用导出方法，将数据导出到文件
		 */
		if ($expload == 1){
			if (count($list) > 1000){
				$this->explodeExcel($list);
			}else{
				$this->exportExcelUserList($list);
			}
			exit();
		}
		$config['base_url'] = site_url("user/index/{page}?phone={$phone}&unicode={$unicode}&dis={$dis}&city={$city}&pro={$pro}&keyword={$keyword}&area={$area}&starttime={$start}&endtime={$end}&st={$st}");
		$config['total_rows'] = $total;
		$config['per_page'] = $pageSize;
		$config['uri_segment'] = 3;
		$config['num_links'] = 6;
		$config['cur_tag_open'] = '<li class="paginate_button active">';
		$config['cur_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);
		$pageStr = $this->pagination->create_links();
		
		$this->template['cityList'] = $this->common_model->getDqList('cs');		//城市
		$this->template['districtList'] = $this->common_model->getDqList('qx');		//区县
		$this->template['provinceList'] = $this->common_model->getDqList('sf');		//省份
		
		//查询条件
		$this->template['sex'] = $sex;
		$this->template['phone'] = $phone;
		$this->template['st'] = $st;
		$this->template['keyword'] = $keyword;
		$this->template['start'] = $start;
		$this->template['end'] = $end;
		$this->template['list'] = $list;
		$this->template['Page'] = '<ul class="pagination ">'.rtrim($pageStr,"/").'</ul>';
		$this->load->view("user/index", $this->template);
	}
	
	/**
	 * 编辑注册用户资料
	 * add by yinyibin
	 * 2016年08月06日11:12:21
	 */
	public function doAdd($user_id = 0){
		if (!empty($user_id)){
			$params = array(
					'table' => 'users',
					'where' => array('id' => $user_id),
					'limit' => 1
			);
			$info = $this->common_model->get_list($params);
			$this->template['info'] = $info;
		}
		
		$this->template['cityList'] = $this->common_model->getDqList('cs');		//城市
		$this->template['districtList'] = $this->common_model->getDqList('qx');		//区县
		$this->template['provinceList'] = $this->common_model->getDqList('sf');		//省份
		$this->load->view("user/edit",$this->template);
	}
	
	/**
	 * 保存会员资料
	 * add by yinyibin
	 * 2016年08月06日11:36:37
	 */
	public function doSave($user_id = 0){
		$data['uname'] = $this->input->post('uname',true);
		$data['password'] = $this->input->post("password",true);
	    $data['phone']  = $this->input->post('phone',true);
	    $data['sex'] = $this->input->post('sex',true);
	    $data['status']  = $this->input->post('status',true);
	    
	    
	    //基本信息判断
	    if (empty($data['uname'])){$this->splitJson('请填写登录账号！',1);}
	    if (empty($data['password'])){$this->splitJson('请填写密码！',1);}

	    // 查询是否已经存在
	    $params = array(
	    		'table' => 'users',
	    		'select' => 'id',
	    		//'where' => array('uid' => $data['uid'], 'unicode' => $data['unicode']),
	    		'limit' => 1
	    );
	    if ($user_id){
	    	$params['where'] = array('id !=' => $user_id,'uname' => $data['uname'], 'phone' => $data['phone']);
	    }else{
	    	$params['where'] = array('uname' => $data['uname'], 'phone' => $data['phone']);
	    }
	    $userinfo = $this->common_model->get_list($params);
	    if (!empty($userinfo)){
	    	$this->splitJson('当前填写的 姓名、手机号至少有一项存在重复！',1);
	    }

	    $this->load->library("hencrypt");
		$data['password'] = $this->hencrypt->encrypt($data['password']);

	    //准备入库
	    $this->common_model->set_table("users");
	    $info_id = $this->common_model->save($data,$user_id);
	    if (empty($info_id)){
	    	$this->splitJson('保存信息失败！',1);
	    }else{
	    	$this->splitJson('保存成功！',0);
	    }
	}
	
	/**
	 * 查看会员详细信息
	 * add by yinyibin
	 * 2016年08月06日10:59:24
	 */
	public function lockInfo($user_id = 0){
		if (empty($user_id)){$this->showMessage('无效的会员ID属性！');exit();}
		
		$params = array(
				'table' => 'users',
				'where' => array('id' => $user_id),
				'limit' => 1
		);
		$info = $this->common_model->get_list($params);
		if (empty($info)){
			$this->showMessage('没有找到当前会员请重新刷新页面操作！');exit();
		}
		
		$this->template['cityList'] = $this->common_model->getDqList('cs');		//城市
		$this->template['districtList'] = $this->common_model->getDqList('qx');		//区县
		$this->template['provinceList'] = $this->common_model->getDqList('sf');		//省份
		$this->template['info'] = $info;
		$this->load->view("user/info",$this->template);
		
	}
	
	/**
	 * 删除会员信息
	 * add by yinyibin
	 * 2016年08月06日11:59:18
	 */
	public function DelInfo($user_id = 0){
		if (empty($user_id)){$this->splitJson('无效的ID属性值！',1);}
		
		$this->common_model->set_table("users");
		$info_id = $this->common_model->delById($user_id);
		if (empty($info_id)){
			$this->splitJson('删除失败！',1);
		}else{
			$this->splitJson('删除成功！',0);
		}
	}
	
	/**
	 * 重置用户登录
	 * add by yinyibin@woshop.cn
	 * 2016年11月08日13:02:05
	 */
	public function Rest($id = 0){
		if (empty($id)){$this->splitJson('无效的ID属性值！',1);}
		
		$params = array(
				'table' => 'users',
				'select' => 'open_id',
				'where' => array('id' => $id),
				'limit' => 1
		);
		$info = $this->common_model->get_list($params);
		if (!empty($info['open_id'])){
			$data['open_id'] = null;
		}else{
			$data['open_id'] = null;
			$this->splitJson('该用户还未登录！',0);
		}
		//入库
		$this->common_model->set_table("users");
		$info_id = $this->common_model->save($data,$id);
	if (empty($info_id)){
	    	$this->splitJson('重置失败！',1);
	    }else{
	    	$this->splitJson('重置成功！',0);
	    }
	}
	
	/**
	 * 查询条件
	 * add by yinyibin
	 * 2016年08月06日13:10:19
	 */
	public function searchType(){
		$this->load->view("user/searchtype", $this->template);
	}
	
	
	/**
	 * 手动导入原始系统老用户资料
	 * add by yinyibin
	 * 2016年08月05日17:21:49
	 */
	public function importData(){
		$this->load->view("user/import",$this->template);
	}
	
	
	/**
	 * 将用户资料导出到文件中
	 * add by yinyibin
	 * 2016年08月06日14:32:49
	 */
	private function exportExcelUserList($data = array()){
		if (empty($data)){
			$this->showMessage('没有任何数据需要导出！');
			exit();
		}
		
		require_once APPPATH."libraries/PHPExcel.php";
		
		$resultPHPExcel = new PHPExcel();
		
		$resultPHPExcel->getProperties()->setCreator("ctos")
		->setLastModifiedBy("ctos")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");
		
		// 设置标题
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '姓名');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '性别');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '昵称');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '手机号');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '发展人编码');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '身份证号');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'QQ');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', 'Email');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '省份');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '城市');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '区县');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', '详细地址');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', '状态');
		//$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', '注册时间');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('O1', '最后登录时间');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('P1', '最后更新时间');
		
		//获取区县数据
		$cityList = $this->common_model->getDqList('cs');		//城市
		$districtList = $this->common_model->getDqList('qx');		//区县
		$provinceList = $this->common_model->getDqList('sf');		//省份
		
		$_provinceList = $_districtList = $_cityList = array();
		foreach($provinceList as $key => $val){
			$_provinceList[$val['ProvinceID']] = $val;
		}
		foreach($districtList as $key => $val){
			$_districtList[$val['DistrictID']] = $val;
		}
		foreach($cityList as $key => $val){
			$_cityList[$val['CityID']] = $val;
		}
		
		// 循环数据写入到对象中
		$i = 2;
		foreach($data as $key => $val){
			if ($val['sex'] == 1){$sex = '男';}elseif($val['sex'] == 2){$sex='女';}
			if ($val['status'] == 1){$status = '正常';}elseif ($val['status'] == 0){$status = '冻结';}
			if ($val['created']){$created = date("Y-m-d H:i:s",$val['created']);}
			if ($val['last_time']){$last_time = date("Y-m-d H:i:s",$val['last_time']);}
			if ($val['last_update']){$last_update = date("Y-m-d H:i:s",$val['last_update']);}
			$province = $_provinceList[$val['province']]['ProvinceName'];
			$city = $_cityList[$val['city']]['CityName'];
			$district = $_districtList[$val['district']]['DistrictName'];
			
			// 身份证号后三位替换
			$uid = str_replace(substr($val['uid'],-3),'***',$val['uid']);
			
			$resultPHPExcel->getActiveSheet(0)->setCellValue('A'.$i, $val['uname']);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('B'.$i, $sex);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('C'.$i, $val['nice']);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('D'.$i, $val['phone']);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('E'.$i, $val['unicode']);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('F'.$i, $uid);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('G'.$i, $val['qq']);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('H'.$i, $val['email']);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('I'.$i, $province);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('J'.$i, $city);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('K'.$i, $district);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('L'.$i, $val['address']);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('M'.$i, $status);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('N'.$i, $created);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('O'.$i, $last_time);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('P'.$i, $last_update);
			
			$i++;
		}
		// 设置表头
		$resultPHPExcel->getActiveSheet()->setTitle('注册会员导出列表');
		$resultPHPExcel->setActiveSheetIndex(0);
		
		$outputFileName = date("Y-m-d")."联通注册用户明细";
		
		header('Content-Type: application/vnd.ms-excel');  
    	header('Content-Disposition: attachment;filename="' . $outputFileName . '.xls"');  
    	header('Cache-Control: max-age=0');  
		
    	$objWriter = PHPExcel_IOFactory::createWriter($resultPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		
	}
	
	
	/**
	 * 导出简单方式
	 * add by yinyibin
	 * 2016年10月10日14:30:37
	 */
	public function explodeExcel($list){
		
		header("Content-type:application/octet-stream");
		header("Accept-Ranges:bytes");
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=".date("Y-m-d")."联通注册用户明细.xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		
		echo iconv("UTF-8", "GB2312","姓名").chr(9);
		echo iconv("UTF-8", "GB2312","手机号").chr(9);
		echo iconv("UTF-8", "GB2312","发展人编码").chr(9);
		echo iconv("UTF-8", "GB2312","身份证号").chr(9);
		echo iconv("UTF-8", "GB2312","QQ").chr(9);
		echo iconv("UTF-8", "GB2312","Email").chr(9);
		echo iconv("UTF-8", "GB2312","省份").chr(9);
		echo iconv("UTF-8", "GB2312","城市").chr(9);
		echo iconv("UTF-8", "GB2312","区县").chr(9);
		echo iconv("UTF-8", "GB2312","营业厅").chr(9);
		echo iconv("UTF-8", "GB2312","状态").chr(9);
		echo iconv("UTF-8", "GB2312","最后登录时间").chr(9);
		echo iconv("UTF-8", "GB2312","最后更新时间").chr(9);
		echo chr(13);
		
		//获取区县数据
		$cityList = $this->common_model->getDqList('cs');		//城市
		$districtList = $this->common_model->getDqList('qx');		//区县
		$provinceList = $this->common_model->getDqList('sf');		//省份
		
		$_provinceList = $_districtList = $_cityList = array();
		foreach($provinceList as $key => $val){
			$_provinceList[$val['ProvinceID']] = $val;
		}
		foreach($districtList as $key => $val){
			$_districtList[$val['DistrictID']] = $val;
		}
		foreach($cityList as $key => $val){
			$_cityList[$val['CityID']] = $val;
		}
		$i = 1;
		foreach($list as $key => $val){
			$i ++;
			if ($val['status'] == 1){$status = iconv("UTF-8", "GB2312",'正常');}elseif ($val['status'] == 0){$status = iconv("UTF-8", "GB2312",'冻结');}
			if ($val['created']){$created = date("Y-m-d H:i:s",$val['created']);}
			if ($val['last_time']){$last_time = date("Y-m-d H:i:s",$val['last_time']);}
			if ($val['last_update']){$last_update = date("Y-m-d H:i:s",$val['last_update']);}
			$province = $_provinceList[$val['province']]['ProvinceName'];
			$city = $_cityList[$val['city']]['CityName'];
			$district = $_districtList[$val['district']]['DistrictName'];
				
			// 身份证号后三位替换
			if ($this->finance == 1){
				$uid = $val['uid'];
			}else{
				$uid = str_replace(substr($val['uid'],-3),'***',$val['uid']);
			}
			
			echo !empty($val['uname']) ? iconv("UTF-8", "GB2312",$val['uname']).chr(9) : '-'.chr(9);
			echo !empty($val['phone']) ? $val['phone'].chr(9) : '-'.chr(9);
			echo !empty($val['unicode']) ? $val['unicode'].chr(9) : '-'.chr(9);
			echo !empty($uid) ? $uid.chr(9) : '-'.chr(9);
			echo !empty($val['qq']) ? $val['qq'].chr(9) : '-'.chr(9);
			echo !empty($val['email']) ? $val['email'].chr(9) : '-'.chr(9);
			echo !empty($province) ? iconv("UTF-8", "GB2312",$province).chr(9) : '-'.chr(9);
			echo !empty($city) ? iconv("UTF-8", "GB2312",$city).chr(9) : '-'.chr(9);
			echo !empty($district) ? iconv("UTF-8", "GB2312",$district).chr(9) : '-'.chr(9);
			echo !empty($val['address']) ? iconv("UTF-8", "GB2312",$val['address']).chr(9) : '-'.chr(9);
			echo !empty($status) ? $status.chr(9) : '-'.chr(9);
			echo !empty($last_time) ? $last_time.chr(9) : '-'.chr(9);
			echo !empty($last_update) ? $last_update.chr(9) : '-'.chr(9);
			echo chr(13);
			if ($i == 100){sleep(50);}
		}
	}
}