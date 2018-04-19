<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 佣金管理
 * add by zhixiao476@gmail.com
 * 2016年08月08日08:48:22
 */
class Company extends MY_Controller{
	
	/**
	 * 初始化
	 * add by zhixiao476@gmail.com
	 * 2016年08月08日08:48:42
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model("common_model");
	}
	
	/**
	 * 数据列表
	 * add by yinyibin
	 * 2018年04月19日00:11:09
	 */
	public function index($offset = 0){
		$pageSize = 20;
		$limit = "limit {$offset},{$pageSize}";
		$sql = "select * from dw_company where 1=1 order by score desc {$limit}";
		$list = $this->common_model->execute($sql);

		//条数
		$totalsql = "select count(*) as count from dw_company where 1=1 {$where} ";
		$totals = $this->common_model->execute($totalsql);
		$total = $totals[0]['count'];

		$this->load->library('pagination');
		$config['base_url'] = site_url("company/index/{page}");
		$config['total_rows'] = $total;
		$config['per_page'] = $pageSize;
		$config['uri_segment'] = 3;
		$config['num_links'] = 6;
		$config['cur_tag_open'] = '<li class="paginate_button active">';
		$config['cur_tag_close'] = '</li>';

		
		$this->pagination->initialize($config);
		$pageStr = $this->pagination->create_links();
		$this->template['Page'] = '<ul class="pagination ">'.rtrim($pageStr,"/").'</ul>';

		$this->template['list'] = $list;
		$this->load->view("company/index", $this->template);
	}
	
	/**
	 * 新增、编辑一条兑付资料（已经确认的）
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日11:16:50
	 */
	public function doAdd($id = 0){
		if (!empty($id)){
			$params = array(
					'table' => 'commis',
					'where' => array('id' => $id),
					'limit' => 1
			);
			$info = $this->common_model->get_list($params);
			$this->template['info'] = $info;
		}
		
		$pro = $this->common_model->getDqList('sf');
		$phoneTypeList = $this->common_model->getMachTypeList();
		
		$this->template['pro'] = $pro;
		$this->template['phonetype'] = $phoneTypeList;

		$this->load->view("commis/edit", $this->template);
	}
	
	/**
	 * 保存奖励数据
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日11:35:41
	 */
	public function doSave($id = 0){
		$data['unicode'] = $this->input->post("unicode",true);
		$data['unorderid'] = $this->input->post("order_id",true);
		$data['phonetypeid'] = $this->input->post("phonetype",true);
		$data['serialCode'] = $this->input->post("serialCode",true);
		$data['area'] = $this->input->post("area",true);
		$imptime = $this->input->post("imptime",true);
		$data['imptime'] = strtotime($imptime);
		$data['ordertype'] = $this->input->post("ordertype",true);
		
		// 检查数据
		if (empty($data['unicode'])){$this->splitJson('请填写发展人编码！',1);}
		if (empty($data['unorderid'])){$this->splitJson('请填写联通系统订单号！',1);}
		if (empty($data['phonetypeid'])){$this->splitJson('请选择机型！',1);}
		if (empty($data['serialCode'])){$this->splitJson('请填写串号！',1);}
		if (empty($data['area'])){$this->splitJson('请选择省份！',1);}
		if (empty($data['imptime'])){$this->splitJson('请填写竣工时间！',1);}
		if (empty($data['ordertype'])){$this->splitJson('请填写订单类型！',1);}
		
		// 类型继续判断
		// 11:北京，13：天津，17：山东，91：辽宁
		if (in_array(substr($data['unicode'],0,2), $this->config->config['sunicode']) === false){
			$this->splitJson('发展人编码填写错误，目前仅支持北京、天津、山东、辽宁');
		}
		
		// 获取机型详细信息
		$mach = $this->common_model->getMacInfo($data['phonetypeid']);
		if (empty($mach)){
			$this->splitJson('没有找到你所选择的机型，请刷新页面重新尝试！',1);
		}
		$data['price'] = $mach['price'];
		$data['buss_id'] = $mach['buss_id'];
		if (empty($id)){
			$data['datetime'] = time();
		}
		
		// 准备入库
		$this->common_model->set_table("commis");
		$info_id = $this->common_model->save($data,$id);
		
		if (empty($info_id)){
			$this->splitJson('信息保存失败！',1);
		}else{
			$this->splitJson('保存成功！',0);
		}
	}
	
	
	/**
	 * 查看详细信息
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日13:42:27
	 */
	public function LockInfo($id = 0){
		if (empty($id)){
			$this->showMessage('无效的ID属性值！');exit();
		}
		$params = array(
				'table' => 'commis',
				'where' => array('id' => $id),
				'limit' => 1
		);
		$info = $this->common_model->get_list($params);
		if (empty($info)){
			$this->showMessage('没有找到该条信息，请确认！',1);
		}
		
		// 查找省份
		$pro = $this->common_model->getDqByIdInfo($info['area'],'sf');
		// 查找机型
		$macthInfo = $this->common_model->getMacInfo($info['phonetypeid']);
		// 会员信息
		$userInfo = $this->common_model->getUserInfo($info['unicode']);
		if (empty($userInfo)){
			$this->showMessage("发展人编码{$info['unicode']}并没有找到该注册会员！");exit();
		}
		
		$this->template['pro'] = $pro;
		$this->template['mach'] = $macthInfo;
		$this->template['info'] = $info;
		$this->template['userInfo'] = $userInfo;
		$this->load->view("commis/info", $this->template);
	}
	
	
	/**
	 * ajax方式不断刷新获取导入文件中产生的错误信息
	 * add by zhixiao476@gmail.com
	 * 2016年10月21日10:43:42
	 */
	public function getImportMessage(){
		$mkey = $this->input->get("mkey", true);
		if (empty($mkey)){
			$this->splitJson('无效的key值！', 1);
		}
		$this->load->library("Memcache");
		
		$message = $this->memcache->get($mkey."_import");
		$status = $this->memcache->get($mkey."_status");
		if ($status == 1){
			if ($message){
				$this->splitJson($message, 1);
			}else{
				$this->splitJson('',1);
			}
		}else{
			$this->splitJson($message, 0);
		}
	}
	
	
	/**
	 * 结算操作页面
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日15:07:23
	 */
	public function startBill(){
		$params = array(
				'table' => 'funds',
				'select' => 'id,total,totalprice',
				'limit' => 1
		);
		$FundInfo = $this->common_model->get_list($params);			// 获取账户余额
		$this->template['fundinfo'] = $FundInfo;
		$this->load->view("commis/startbill", $this->template);
	}
	
	/**
	 * 结算金额以及结算有效条数计算
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日15:16:10
	 */
	public function compute($type_id = 0){
		if (empty($type_id)){$this->splitJson('结算方式选择错误，请重新选择！',1);}
		if ($type_id == 1){			// 结算全部没有结算的
			$sql = "select id,price,status,datetime from dw_commis where status = 0";
		}elseif ($type_id == 2){
			$starttime = strtotime(date("Y-m-d"));
			$sql = "select id,price,status,datetime from dw_commis where status=0 and datetime = {$starttime}";
		}
		$list = $this->common_model->execute($sql);
		$Total = count($list);
		if ($Total <= 0){
			$this->splitJson('没有需要进行结算的数据！',1);
		}
		$PriceTotal = 0;
		foreach($list as $key => $val){
			$PriceTotal += $val['price'];
		}
		$info['total'] = $Total;
		$info['price'] = $PriceTotal;
		$this->splitJson($info,0);
	}
	
	/**
	 * 开始真正的结算了
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日15:38:32
	 */

	public function startcompute($total_n = 0, $total = 0){
		$pageSize = 5;
		$type = $this->input->get("startbill",true);			// 结算方式
		
		if ($type == 1){			// 结算全部未结算
			$sql = "select id,unicode,unorderid,price,status,datetime from dw_commis where status = 0 limit {$pageSize}";
			$sql2 = "select count(*) as count from dw_commis where status = 0";
		}elseif ($type == 2){		// 结算当日导入
			$starttime = strtotime(date("Y-m-d"));
			$sql = "select id,unicode,unorderid,price,status,datetime from dw_commis where status=0 and datetime = {$starttime} limit {$pageSize}";
			$sql2 = "select count(*) as count from dw_commis where status = 0 and datetime = {$starttime}";
		}else{
			$this->showMessage('结算类型错误！');exit();
		}
		
		$list = $this->common_model->execute($sql);
		if (!$total){
			$totalCount = $this->common_model->execute($sql2);
			$total = $totalCount[0]['count'];
		}
		
		echo "<title>赢励宝结算进行中...</title><style>body{font-size:12px;}</style>";
		echo '正在结算中,总共 : <font color=red>'.$total.'</font> 条<br><br>';


		unset($sql,$sql2);
		
		foreach($list as $key => $val){
			$total_n += 1;
			// 这里不直接将资金存入用户账户中，让用户自行登录微信后进行操作
			//$sql = "update dw_users set totalprice=totalprice+{$val['price']},price={$val['price']},grandtotal=grandtotal+{$val['price']} where unicode='{$val['unicode']}' limit 1";
			//$info_id = $this->common_model->query($sql);
			//if (empty($info_id)){
			//	echo "<font color=red>订单号：".$val['unorderid']."结算失败</font><br>";
			//	continue;
			//}
			// 更新状态
			$sql = "update dw_commis set status=1 where id={$val['id']}";
			$this->common_model->query($sql);
			$sql2 = "update dw_funds set total = total - {$val['price']} where id = 1";
			$this->common_model->query($sql2);
			echo "订单".$val['unorderid']."结算完成！<br>";
		}
		
		if ($total_n < $total){
			echo '<meta http-equiv="refresh" content="0;url='.site_url("commis/startcompute/{$total_n}/{$total}?startbill={$type}").'">';
		}else{
			echo '<font color=green><b>结算完成，请返回查看列表</b></font>';
		}
	}
	
	/**
	 * 删除激励
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日14:04:51
	 */

	public function DelInfo($id = 0){
	    if (empty($id)|| !is_numeric($id)){$this->showMessage('无效的ID属性值！');}
	    $this->common_model->set_table("commis");
	    $info_id = $this->common_model->delById($id);
	    if (empty($info_id)){
	        $this->splitJson('删除失败！',1);
	    }else{
	        $this->splitJson('删除成功！',0);
	    }
	}
	
	/**
	 * 条件查询
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日14:08:13
	 */
	public function searchType(){
		// 获取所有的机型
		$machList = $this->common_model->getMachTypeList();
		
		$this->template['machlist'] = $machList;
		$this->load->view('commis/searchType', $this->template);
	}
	
	/**
	 * 导出佣金查询数据
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日14:31:25
	 */
	public function exportExcelCommisList($list = array()){
		if (empty($list)){$this->showMessage('没有任何数据需要导出！');exit();}

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
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '发展人ID');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '金额');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '订单号');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '机型');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '串号');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '地区');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '城市');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '区县');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '门店详情');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '时间');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', '支付状态');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', '订单状态');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', '操作者');
		
		
		// 组合数据列表
		$i = 2;
		foreach($list as $key => $val){
			$pro = $this->common_model->getDqByIdInfo($val['area'],'sf');
			$city = $this->common_model->getDqByIdInfo($val['city'],'cs');
			$dis = $this->common_model->getDqByIdInfo($val['dis'],'qx');
			
			$UserInfo = $this->common_model->getUserInfo($val['unicode']);
			$phonetype = $this->common_model->getMacInfo($val['phonetypeid']);
			
			if ($val['status'] == 0){
				$st = '未支付';
			}elseif ($val['status'] == 1){
				$st = '未领取';
			}elseif ($val['status'] == 2){
				$st = '已领取';
			}
			
			if (empty($val['cities'])){$val['cities']='';}
			if (empty($val['county'])){$val['county']='';}
			if (empty($val['hall'])){$val['hall']='';}
            //  在导出表格时对城市和门店字段存在的函数进行过滤。
			$arr = array('VLOOKUP','COUNTIF','LEFT','MID','RIGHT','LEN','SUM','IF');
			foreach ($arr as $k => $v) {
                if (stripos($val['county'],$v)!== false) {
                    $val['county'] = '城市存在Excel函数,不予显示';
                }
                if (stripos($val['hall'],$v)!== false) {
                    $val['hall'] = '门店存在Excel函数,不予显示';
                }
            }
			$resultPHPExcel->getActiveSheet(0)->setCellValue('A'.$i, $UserInfo['uname']);		// 姓名
			$resultPHPExcel->getActiveSheet(0)->setCellValue('B'.$i, $val['unicode']);		// 发展人
			$resultPHPExcel->getActiveSheet(0)->setCellValue('C'.$i, $val['price']);		// 金额
			$resultPHPExcel->getActiveSheet(0)->setCellValue('D'.$i, $val['unorderid']);		// 订单号
			$resultPHPExcel->getActiveSheet(0)->setCellValue('E'.$i, $phonetype['mach_type']);		// 机型
			$resultPHPExcel->getActiveSheet(0)->setCellValue('F'.$i, $val['serialCode']);		// 串号
			$resultPHPExcel->getActiveSheet(0)->setCellValue('G'.$i, $pro['ProvinceName']);		// 地区
			$resultPHPExcel->getActiveSheet(0)->setCellValue('H'.$i, $val['cities']); //城市
			$resultPHPExcel->getActiveSheet(0)->setCellValue('I'.$i, $val['county']);//区县
			$resultPHPExcel->getActiveSheet(0)->setCellValue('J'.$i, $val['hall']);//门店详情
			$resultPHPExcel->getActiveSheet(0)->setCellValue('K'.$i, date("Y-m-d H:i:s",$val['imptime']));		// 时间
			$resultPHPExcel->getActiveSheet(0)->setCellValue('L'.$i, $st);		// 支付状态
			$resultPHPExcel->getActiveSheet(0)->setCellValue('M'.$i, $val['ordertype']);		// 订单状态
			$resultPHPExcel->getActiveSheet(0)->setCellValue('N'.$i, $val['uname']);		// 操作者
			
			
			$i++;
		}

		// 设置表头
		$resultPHPExcel->getActiveSheet()->setTitle('佣金列表');
		$resultPHPExcel->setActiveSheetIndex(0);
		
		$outputFileName = time()."佣金明细表";
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $outputFileName . '.xls"');
		header('Cache-Control: max-age=0');
		try{
		$objWriter = PHPExcel_IOFactory::createWriter($resultPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		}catch (Exception $e){
			print_r($e->getMessage());die;
		}
	}
	
	/**
	 * 导入可以兑付的数据资料
	 * add by zhixiao476@gmail.com
	 * 2016年08月09日08:48:24
	 */
	public function importData(){
		$this->load->view("company/import", $this->template);
	}
}