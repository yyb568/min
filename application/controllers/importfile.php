<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 将文件导入到数据库中
 * add by zhixiao476@gmail.com
 * 2016年08月05日15:20:00
 */
ini_set("max_input_time",120000);
set_time_limit(0);
ini_set("memory_limit",'2048M');
class ImportFile extends MY_Controller{
	
	/**
	 * 初始化操作
	 * add by zhixiao476@gmail.com
	 * 2016年08月05日15:20:19
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 社会奖励数据导入
	 * add by zhixiao476@gmail.com
	 * 2016年09月29日13:09:03
	 */
	public function ImportSoCashExcel(){
		$filename = $this->input->get("file",true);
		
		$file_name = FCPATH.$filename;
		if (!file_exists($file_name)){
			$this->splitJson('未找到上传的文件，请确认是否正确上传！',1);
		}
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		$fileExt = substr(strrchr($file_name, '.'), 1);
		if ($fileExt == 'xlsx'){		//2007以上版本
			$reader = PHPExcel_IOFactory::createReader('Excel2007');
		}elseif ($fileExt == 'xls'){	//2007一下版本
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		}
		$PHPExcel = $reader->load($file_name);
		$sheet = $PHPExcel->getSheet(0);
		
		$highestRow = $sheet->getHighestRow();		//读取总行数
		$colsNum = $sheet->getHighestColumn();	//读取总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
		
		$this->load->model("common_model");
		
		$errorMsg = '';
		for ($row = 2;$row <= $highestRow;$row++){
			$data = array();
			for ($column = 0;$column < $highestColumm;$column++){					//循环得到列
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);		//获取列名（A/B/C/D）
					
				$value = $sheet->getCellByColumnAndRow($column,$row)->getValue();
		
				if ($columnName == 'A'){				// 发展人编码
					$data['unicode'] = $value;
				}elseif ($columnName == 'B'){			// 手机号
					$data['phone'] = $value;
				}elseif ($columnName == 'C'){			// 金额
					$data['price'] = $value;
				}elseif ($columnName == 'D'){			// 竣工时间
					$data['created'] = gmdate("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($value));
				}elseif ($columnName == 'E'){			// 所属客户
					$data['customer'] = $value;
				}elseif ($columnName == 'F'){			// 串码
					$data['imei'] = $value;
				}
		
			}
			// 对数据进行整理
			if (!empty($data)){
				// 判断发展人id是否合格
				if (empty($data['unicode'])){		// 正常10位
					//$errorMsg .= "第".$row."行中的 发展人ID 必须填写！<br>";
					//continue;
				}
				if (empty($data['phone'])){
					$errorMsg .= "第".$row."行中的 手机号 必须填写！<br>";
					continue;
				}
			
				if (empty($data['customer'])){
					$errorMsg .= "第".$row."行中的所属客户必须填写！<br>";
					continue;
				}
				$_customer = $this->common_model->getCustomerName($data['customer']);
				
				//如果是海口联通 则将串码改为 0
				if ($_customer['id'] == 4){
					$data['imei'] = 0;
				}
				
				if ($_customer['receiptime'] == 1){
					#获取客户下设置的领取时间
					$receiptime = $this->common_model->getCustomerById($_customer['id']);
					$data['receiptime'] = $receiptime['receiptime'];
				}
				//如果是移动侧判断串码是否正确
				if ($_customer['id'] != 4){
					if ($data['imei'] == "" && strlen($data['imei']) <= 5){
						$errorMsg .= "第".$row."行中的串码位数似乎不对，才5位，请确认！<br>";
						continue;
					}
				}
				
				if ($_customer === false){
					$errorMsg .= "第".$row."行中没有找到该客户名称《{$data['customer']}》<br>";
					continue;
				}else{
					$data['customer'] = $_customer['id'];
				}
				
				$data['user_id'] = $this->user_id;
				$data['uname'] = $this->uname;
				// 重新整理数据
				if (empty($data['created'])){
					$data['created'] = time();
				}else{
					$data['created'] = strtotime($data['created']);
				}
				$data['imptime'] = time();
				$data['status'] = 0;			// 修改成 已经确认未发放状态
				//准备入库
				$this->common_model->set_table("socommis");
				$info_id = $this->common_model->save($data);
				if (empty($info_id)){
					$errorMsg .= "第".$row."行写入数据库失败，可能的原因是当前这条数据已经存在，请确认！<br>";
				}
					
			}
		
			unset($data);
		}
		$this->splitJson($errorMsg,0);
	}
	
	
	/**
	 * 将联通订单原始输入导入到系统中
	 * add by zhixiao476@gmail.com
	 * 2016年08月08日16:25:21
	 */
	public function ImportCashExcel(){
		$filename = $this->input->get("file",true);
		// $memcacheKey = $this->input->get("mkey", true);÷
		$file_name = FCPATH.$filename;
		if (!file_exists($file_name)){
			$this->splitJson('未找到上传的文件，请确认是否正确上传！',1);
		}
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		$fileExt = substr(strrchr($file_name, '.'), 1);
		if ($fileExt == 'xlsx'){		//2007以上版本
			$reader = PHPExcel_IOFactory::createReader('Excel2007');
		}elseif ($fileExt == 'xls'){	//2007一下版本
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		}
		$PHPExcel = $reader->load($file_name);
		$sheet = $PHPExcel->getSheet(0);
		
		$highestRow = $sheet->getHighestRow();		//读取总行数
		$colsNum = $sheet->getHighestColumn();	//读取总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
		
		$this->load->model("common_model");
		
		$errorMsg = '';
		for ($row = 2;$row <= $highestRow;$row++){
			$data = array();
			for ($column = 0;$column < $highestColumm;$column++){					//循环得到列
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);		//获取列名（A/B/C/D）
				$value = $sheet->getCellByColumnAndRow($column,$row)->getValue();
				
				if ($columnName == 'A'){				// 证券代码
					$data['stockcode'] = $value;
				}elseif ($columnName == 'B'){			// 证券简称
					$data['cname'] = $value;
				}elseif ($columnName == 'C'){			// 净利润
					$data['net_profit'] = $value;
				}elseif ($columnName == 'D'){			// 利润差
					$data['net_profit_difference'] = $value;
				}elseif ($columnName == 'E'){			// 每股现金流量净额
					$data['single_cash_flow'] = $value;				
				}elseif ($columnName == 'F'){			// 现金流量
					$data['cash_flow'] = $value;
				}elseif ($columnName == 'G'){			// 净债务差
					$data['company_debt'] = $value;
				}elseif ($columnName == 'H'){			// 流动比率
					$data['current_ratio'] = $value;
				}elseif ($columnName == 'I'){			// 总股本差
					$data['total_equity'] = $value;
				}elseif ($columnName == 'J'){			// 销售利润率
					$data['sales_rate'] = $value;
				}elseif ($columnName == 'K'){			// 总资产周准率
					$data['total_turnover'] = $value;
				}
								
			}
			$data['score'] = 0;
			// 对数据进行整理
			if (!empty($data)){
				if ($data['net_profit'] > 0){//净利润
					$data['score'] +=  1;
				}
				if ($data['net_profit_difference'] > 0){//净利润变化差值
					$data['score'] +=  1;
				}
				if ($data['single_cash_flow'] > 0){// 每股现金流量净额
					$data['score'] +=  1;
				}
				if ($data['cash_flow'] > 0){// 现金流量
					$data['score'] +=  1;
				}
				if ($data['company_debt'] < 0){// 净债务差
					$data['score'] +=  1;
				}
				if ($data['current_ratio'] > 2){// 流动比率
					$data['score'] +=  1;
				}
				// 流动比率
				if ($data['current_ratio'] > 1 && $data['current_ratio'] < 2){
					$data['score'] +=  0.5;
				}
				// 总股本差
				if ($data['total_equity'] < 0){
					$data['score'] +=  1;
				}
				// 销售利润率
				if ($data['sales_rate'] > 0.1){
					$data['score'] +=  1;
				}
				// 销售利润率
				if ($data['sales_rate'] > 0 && $data['sales_rate'] < 0.1){
					$data['score'] +=  0.5;
				}
				//总资产周准率
				if ($data['total_turnover'] > 0){
					$data['score'] +=  1;
				}
				if ($data['stockcode'] == '数据来源：Wind资讯'){
					continue;
				}
				$data['created'] = time();		

               	//准备入库
				$this->common_model->set_table("company");
				$info_id = $this->common_model->save($data);
			}
				
			unset($data);
		}
		$this->splitJson('导入完成！',0);
	}
	
	/**
	 * 将失败的订单写入到数据库
	 * add by zhixiao476@gmail.com
	 * 2016年10月11日10:42:57
	 */
	public function FaillOrderSave($data = array()){
		if (empty($data)) return false;
		$this->common_model->set_table("fail_commis");
		$this->common_model->save($data);
	}
	
	/**
	 * 将不能进行兑付的数据导入到系统中
	 * add by zhixiao476@gmail.com
	 * 2016年08月10日10:28:00
	 */
	public function ImportNotCashExcel(){
		$filename = $this->input->get("file",true);
	
		$file_name = FCPATH.$filename;
		if (!file_exists($file_name)){
			$this->splitJson('未找到上传的文件，请确认是否正确上传！',1);
		}
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		$fileExt = substr(strrchr($file_name, '.'), 1);
		if ($fileExt == 'xlsx'){		//2007以上版本
			$reader = PHPExcel_IOFactory::createReader('Excel2007');
		}elseif ($fileExt == 'xls'){	//2007一下版本
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		}
		$PHPExcel = $reader->load($file_name);
		$sheet = $PHPExcel->getSheet(0);
	
		$highestRow = $sheet->getHighestRow();		//读取总行数
		$colsNum = $sheet->getHighestColumn();	//读取总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
	
		$this->load->model("common_model");
		//获取所有的地区+城市+区县
		$proList = $this->common_model->getDqList('sf');
		//$cityList = $this->common_model->getDqList('cs');
		//$distList = $this->common_model->getDqList('qx');
	
		// 获取机型数据
		$machType = $this->common_model->getMachTypeList();
		if (empty($machType)){
			$this->splitJson('请先将机型数据导入再操作兑付！',1);
		}
	
		// 获取注册用户--找出所有的发展人编码
		// 先不做发展人编码查询
		/*$userList = $this->common_model->getUserList(1);
		if (!empty($userList)){
			foreach($userList as $key => $val){
				$_userList[] = $val['unicode'];
			}
		}*/
	
	
		$errorMsg = '';
		for ($row = 2;$row <= $highestRow;$row++){
			$data = array();
			for ($column = 0;$column < $highestColumm;$column++){					//循环得到列
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);		//获取列名（A/B/C/D）
					
				$value = $sheet->getCellByColumnAndRow($column,$row)->getValue();
	
				if ($columnName == 'A'){				// 联通订单号
					$data['unorderid'] = $value;
				}elseif ($columnName == 'B'){			// 手机型号
					$data['mach'] = $value;
				}elseif ($columnName == 'C'){			// 串号
					$data['serialCode'] = $value;
				}elseif ($columnName == 'D'){			// 发展人编码
					$data['unicode'] = $value;
				}elseif ($columnName == 'E'){			// 地区
					$data['area'] = $value;
				}elseif ($columnName == 'F'){			// 竣工时间
					$data['endTime'] = gmdate("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($value));
				}elseif ($columnName == 'G'){			// 订单类型
					$data['orderType'] = $value;
				}elseif ($columnName == 'H'){
					$data['comment'] = $value;
				}
	
			}
				
				
			// 对数据进行整理
			if (!empty($data)){
				if (empty($data['mach'])){
					$errorMsg .= "第".$row."行中的 机型 数据为空，请检查！<br>";
					continue;
				}
				// 获取到省份的id
				$province = (int)$this->common_model->getSearchDqForId('sf',$data['area'],$proList);
				//通过机型查询相关信息
				$mach = $this->common_model->getSearchMachForInfo($data['mach'],$machType,$province);
				if (!empty($mach)){
					$data['phonetypeid'] = $mach['id'];				// 手机型号id
					$data['price'] = $mach['price'];
					$data['buss_id'] = $mach['buss_id'];
				}else{
					$errorMsg .= "第".$row."行 由于机型、地区、时间的原因未能匹配到奖励数据，请查看相关原因！<br>";
					continue;
				}
				
				// 判断发展人id是否合格
				if (strlen($data['unicode']) == 10){		// 正常10位
					$unicode = $data['unicode']{0}.$data['unicode']{1};
					if (empty($data['unicode']) || !in_array($unicode,$this->config->config['sunicode'])){
						$errorMsg .= "第".$row."行中的 发展人ID错误！<br>";
					}
				}elseif (strlen($data['unicode']) == 7){	//7位的是北京老id
					// ...
				}else{
					$errorMsg .= "第".$row."行中的 发展人ID错误！<br>";
				}
	
				// 查找当前用户是否存在
				// 先不做发展人编码排查
				/*$unicodeFlag = false;
				foreach($userList as $key => $val){
					if ($key == $data['serialCode']){
						$unicodeFlag = true;
						continue;
					}
				}
	
				if ($unicodeFlag === false){
					$errorMsg .= "第".$row."行中的发展人编码在系统找没有找到，该用户没有注册！<br>";
				}*/
					
				// 重新整理数据
				$data['area'] = $province;
				$data['imptime'] = strtotime($data['endTime']);
				$data['datetime'] = strtotime(date("Y-m-d"));
				unset($data['mach'],$data['endTime']);
					
				//准备入库
				$this->common_model->set_table("notcommis");
				$info_id = $this->common_model->save($data);
				if (empty($info_id)){
					$errorMsg .= "第".$row."行写入数据库失败，可能的原因是当前这条数据已经存在，请确认！<br>";
				}
					
			}
	
			unset($data);
		}
		$this->splitJson($errorMsg,0);
	}
	
	/**
	 * 将规则文件写入到数据库中
	 * add by zhixiao476@gmail.com
	 * 2016年08月05日15:20:45
	 */
	public function ImportExcel(){
		$filename = $this->input->get("file",true);
		
		$file_name = FCPATH.$filename;
		if (!file_exists($file_name)){
			$this->splitJson('未找到上传的文件，请确认是否正确上传！',1);
		}
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		$fileExt = substr(strrchr($file_name, '.'), 1);
		if ($fileExt == 'xlsx'){		//2007以上版本
			$reader = PHPExcel_IOFactory::createReader('Excel2007');
		}elseif ($fileExt == 'xls'){	//2007一下版本
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		}
		$PHPExcel = $reader->load($file_name);
		$sheet = $PHPExcel->getSheet(0);
		
		$highestRow = $sheet->getHighestRow();		//读取总行数
		$colsNum = $sheet->getHighestColumn();	//读取总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
		
		$this->load->model("common_model");
		//获取所有的地区+城市+区县
		$proList = $this->common_model->getDqList('sf');
		$cityList = $this->common_model->getDqList('cs');
		$distList = $this->common_model->getDqList('qx');
		
		//获取所有的平台商
		$bussList = $this->common_model->getBussList();

		
		$errorMsg = '';
		for ($row = 2;$row <= $highestRow;$row++){
			$data = array();
			for ($column = 0;$column < $highestColumm;$column++){					//循环得到列
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);		//获取列名（A/B/C/D）
			
				$value = $sheet->getCellByColumnAndRow($column,$row)->getValue();
				//根据不同的列组合不同的元素
				if ($columnName == 'A' && !empty($value)){				//省份
					$data['pro'] = $value;
				}elseif ($columnName == 'B'){							//城市
					$data['cit'] = $value;
				}elseif ($columnName == 'C'){							//区县
					$data['dis'] = $value;
				}elseif ($columnName == 'D'  && !empty($value)){		//机型
					$data['mach_type'] = $value;
				}elseif ($columnName == 'E'  && !empty($value)){		//奖金
					$data['price'] = $value;
				}elseif ($columnName == 'F'  && !empty($value)){		//开始时间
					//echo gmdate("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($value));die;
					$data['starttime'] = gmdate("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($value));
				}elseif ($columnName == 'G'  && !empty($value)){		//结束时间
					$data['endtime'] = gmdate("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($value));
				}elseif ($columnName == 'H'  && !empty($value)){		//平台商
					$data['buss'] = $value;
				}
			}
			
			if (!empty($data)){		//对数据进行重新整理
				if ($data['price'] < 1){
					$errorMsg .= "第".$row."行的佣金不能小于1元,必须是大于1的整数<br>";
					continue;
				}
				if (stripos($data['price'],'.')){
					$errorMsg .= "第".$row."行的佣金不能有小数存在，必须是整数<br>";
					continue;
				}
				if (empty($data['pro']) && empty($data['cit']) && empty($data['dis'])){
					$errorMsg .= '第 '.$row.' 行中缺少地区，自动跳过不导入<br>';
					continue;
				}
				// 将地区进行转换
				$data['province'] = (int)$this->common_model->getSearchDqForId('sf',$data['pro'],$proList);
				$data['city'] = (int)$this->common_model->getSearchDqForId('cs',$data['cit'],$cityList);
				$data['district'] = (int)$this->common_model->getSearchDqForId('qx',$data['dis'],$distList);
				
				$buss_id = $this->common_model->getSearchBussForId($bussList, $data['buss']);
				if (empty($buss_id)){
					$errorMsg .= '第'.$row.'行中的平台商('.$data['buss'].')在系统中未找到，已经跳过处理，请在系统中添加!<br>';
					continue;
				}
				$data['buss_id'] = $buss_id;
			
				//格式化时间
				$start = strtotime($data['starttime']);
				$end = strtotime($data['endtime']);
				if (empty($start) || empty($end)){
					$errorMsg .= '第'.$row.'行中的时间格式不正确，已经跳过处理！<br>';
					continue;
				}
				$data['starttime'] = $start;
				$data['endtime'] = $end;
				unset($data['pro'],$data['cit'],$data['dis'],$data['buss']);
				
				// 判断是否存在
				if ($this->common_model->checkInfo($data, 'excation') != false){
					$errorMsg .= "第".$row."行已经存在！<br>";
					continue;
				}

				//入库
				$data['created'] = time();
				$this->common_model->set_table("excation");
				$info_id = $this->common_model->save($data);
				if (empty($info_id)){
					$errorMsg .= '第'.$row.'行入库失败，很可能已经存在了！<br>';
				}
			}
			unset($data);
		}
		$this->splitJson($errorMsg,0);
	}
	
	
	/**
	 * 将原始系统中的用户导入到新系统中
	 * add by zhixiao476@gmail.com
	 * 2016年08月06日09:23:26
	 */
	public function ImportUserExcel(){
		$filename = $this->input->get("file",true);
		
		$file_name = FCPATH.$filename;
		if (!file_exists($file_name)){
			$this->splitJson('未找到上传的文件，请确认是否正确上传！',1);
		}
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		$fileExt = substr(strrchr($file_name, '.'), 1);
		if ($fileExt == 'xlsx'){		//2007以上版本
			$reader = PHPExcel_IOFactory::createReader('Excel2007');
		}elseif ($fileExt == 'xls'){	//2007一下版本
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		}
		$PHPExcel = $reader->load($file_name);
		$sheet = $PHPExcel->getSheet(0);
		
		$highestRow = $sheet->getHighestRow();		//读取总行数
		$colsNum = $sheet->getHighestColumn();	//读取总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
		
		$this->load->model("common_model");

		//获取所有的地区+城市+区县
		$proList = $this->common_model->getDqList('sf');
		$cityList = $this->common_model->getDqList('cs');
		$distList = $this->common_model->getDqList('qx');
		
		$errorMsg = '';
		for ($row = 2;$row <= $highestRow;$row++){
			$data = array();
			for ($column = 0;$column < $highestColumm;$column++){					//循环得到列
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);		//获取列名（A/B/C/D）
					
				$value = $sheet->getCellByColumnAndRow($column,$row)->getValue();
				//根据不同的列组合不同的元素
				if ($columnName == 'A' && !empty($value)){				//手机号/账号
					$data['phone'] = $value;
				}elseif ($columnName == 'B'){							//姓名
					$data['uname'] = $value;
				}elseif ($columnName == 'C'){							//发展人编码
					$data['unicode'] = $value;
				}elseif ($columnName == 'D'){							//省
					$province = $value;
				}elseif ($columnName == 'E'){							//市
					$city = $value;
				}elseif ($columnName == 'F'){							//详细地址
					$data['address'] = $value;
				}elseif ($columnName == 'G'){							//用户昵称
					$data['nice'] = $value;
				}elseif ($columnName == 'K'){							//用户状态
					$data['status'] = ($value == 0 || $value == '0')  ? 1 : 0;
				}elseif ($columnName == 'L'  && !empty($value)){		//创建时间
					$data['created'] = $value;//gmdate("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($value));
				}elseif ($columnName == 'M'  && !empty($value)){		//最后更新时间
					$data['last_update'] = $value;//gmdate("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($value));
				}
			}

			//判断导入用户的手机号是否重复
			if(!empty($data['phone'])){
				$sql = "select phone from dw_users where phone={$data['phone']}";
				$repeatlist = $this->common_model->query($sql);
				if ($repeatlist > 0){
					$_hd['unicode'] = $data['unicode'];
					$_hd['uname'] = $data['uname'];
					$_hd['phone'] = $data['phone'];
					$_hd['dif'] = 0;
					$_hd['created'] = $data['created'];
					$_hd['content'] = "该用户手机号已被注册，无法导入";
					$this->FaillUserSave($_hd);
					$errorMsg .= "第".$row."行 该用户手机号已被注册过，不在导入！<br>";
				}
			}
			//重新组合数据
			if (!empty($data)){
				
				// 判断发展人id是否合格
				if (strlen($data['unicode']) == 10){		// 正常10位
					$unicode = $data['unicode']{0}.$data['unicode']{1};
					if (empty($data['unicode']) || !in_array($unicode,$this->config->config['sunicode'])){
						$errorMsg .= "第".$row."行中的 发展人ID错误！<br>";
					}
				}elseif (strlen($data['unicode']) == 7){	//7位的是北京老id
					// ...
				}else{
					$errorMsg .= "第".$row."行中的 发展人ID错误！<br>";
				}
				
				// 将地区进行转换
				if (!empty($province)){
					$data['province'] = (int)$this->common_model->getSearchDqForId('sf',$province,$proList);
				}else{
					$data['province'] = 0;
				}
				if (!empty($city)){
					$data['city'] = (int)$this->common_model->getSearchDqForId('cs',$city,$cityList);
				}else{
					$data['city'] = 0;
				}
				
				$data['district'] = 0;
				//格式化时间
				$start = strtotime($data['created']);
				$end = strtotime($data['last_update']);
				if (empty($start) || empty($end)){
					$errorMsg .= '第'.$row.'行中的时间格式不正确，已经跳过处理！<br>';
					//continue;
				}
				$data['created'] = $start;
				$data['last_update'] = $end;

				$this->common_model->set_table("users");
				$info_id = $this->common_model->save($data);
				if (empty($info_id)){
					$errorMsg .= '第'.$row.'行入库失败，很可能已经存在了！<br>';
				}
				
			}
			unset($data);
		}
		$this->splitJson($errorMsg,0);
	}
	/**
	 * 将失败的用户写入到数据库
	 * add by yinyibin@woshop.cn
	 * 2016年12月1日16:58:38
	 */
	public function FaillUserSave($data = array()){
		if (empty($data)) return false;
		$this->common_model->set_table("fail_users");
		$this->common_model->save($data);
	
	}
	
	/**
	 * 导入社会奖励用户
	 * add by zhixiao476@gmail.com
	 * 2016年09月29日12:02:54
	 */
	public function ImportSoUserExcel(){
		$filename = $this->input->get("file",true);
		
		$file_name = FCPATH.$filename;
		if (!file_exists($file_name)){
			$this->splitJson('未找到上传的文件，请确认是否正确上传！',1);
		}
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		$fileExt = substr(strrchr($file_name, '.'), 1);
		if ($fileExt == 'xlsx'){		//2007以上版本
			$reader = PHPExcel_IOFactory::createReader('Excel2007');
		}elseif ($fileExt == 'xls'){	//2007一下版本
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		}
		$PHPExcel = $reader->load($file_name);
		$sheet = $PHPExcel->getSheet(0);
		
		$highestRow = $sheet->getHighestRow();		//读取总行数
		$colsNum = $sheet->getHighestColumn();	//读取总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
		
		$this->load->model("common_model");
		
		//获取所有的地区+城市+区县
		$proList = $this->common_model->getDqList('sf');
		$cityList = $this->common_model->getDqList('cs');
		$distList = $this->common_model->getDqList('qx');
		
		$errorMsg = '';
		for ($row = 2;$row <= $highestRow;$row++){
			$data = array();
			for ($column = 0;$column < $highestColumm;$column++){					//循环得到列
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);		//获取列名（A/B/C/D）
					
				$value = $sheet->getCellByColumnAndRow($column,$row)->getValue();
				//根据不同的列组合不同的元素
				if ($columnName == 'A' && !empty($value)){				//手机号/账号
					$data['phone'] = $value;
				}elseif ($columnName == 'B'){							//姓名
					$data['uname'] = $value;
				}elseif ($columnName == 'C'){							//发展人编码
					$data['unicode'] = $value;
				}elseif ($columnName == 'D'){							//身份证号
					$data['uid'] = $value;
				}elseif ($columnName == 'E'){							//省
					$province = $value;
				}elseif ($columnName == 'F'){							//市
					$city = $value;
				}elseif ($columnName == 'G'){							// 区
					$district = $value;
				} elseif ($columnName == 'H'){							// 营业厅地址
                    $address = $value;
                }elseif ($columnName == 'I'){							// 次月兑付日期
					$data['month_day'] = $value;
				}elseif ($columnName == 'J'){							// 所属客户
					$_kehu = $value;
				}

			}
			//判断导入用户的手机号是否重复
			if(!empty($data['phone'])){
				$sql = "select phone from dw_sousers where phone={$data['phone']}";
				$repeatlist = $this->common_model->query($sql);
				if ($repeatlist > 0){
					$_hd['unicode'] = $data['unicode'];
					$_hd['uname'] = $data['uname'];
					$_hd['phone'] = $data['phone'];
					$_hd['created'] = $data['created'];
					$_hd['dif'] = 1;
					$_hd['content'] = "该用户手机号已被注册，无法导入";
					$this->FaillUserSave($_hd);
					$errorMsg .= "第".$row."行 该用户手机号已被注册过，不在导入！<br>";
				}
			}

			//重新组合数据
			if (!empty($data)){

				// 检查下是否有重复的
				$_soUserInfo = $this->common_model->checkInfo(array('phone' => $data['phone']), 'sousers', 1);
				if (!empty($_soUserInfo)){
					$errorMsg .= "第".$row."行 {$data['phone']} 已经存在，跳过<br>";
					continue;
				}

				// 检查客户是否存在，并进行转换
				$kehu = $this->common_model->getCustomerName($_kehu);
				if ($kehu === false){
					$errorMsg .= "第".$row."行 {$data['phone']} 客户名称不存在，跳过<br>";
					continue;
				}

				//根据客户是否为山东移动来导入营业厅地址。
				if($kehu['names'] == "山东移动"){
				    $data['address']= $address;
                }else{
                    $data['address']='';
                }
				$data['customer'] = $kehu['id'];
				// 将地区进行转换
				if (!empty($province)){
					$data['province'] = (int)$this->common_model->getSearchDqForId('sf',$province,$proList);
				}else{
					$data['province'] = 0;
				}
				if (!empty($city)){
					$data['city'] = (int)$this->common_model->getSearchDqForId('cs',$city,$cityList);
				}else{
					$data['city'] = 0;
				}
				if (!empty($district)){
					$data['district'] = (int)$this->common_model->getSearchDqForId('qx',$district,$distList);
				}
				
				$data['created'] = time();

				$this->common_model->set_table("sousers");
				$info_id = $this->common_model->save($data);
				if (empty($info_id)){
					$errorMsg .= '第'.$row.'行入库失败，很可能已经存在了！<br>';
				}

			}
			unset($data);
		}
		$this->splitJson($errorMsg,0);
	}

	
	
	/**
	 * 给地市开通管理权限
	 * add by zhixiao476@gmail.com
	 * 2016年10月17日14:33:55
	 */
	public function arearole(){
		$file_name = './111.xlsx';
		
		//$file_name = FCPATH.$filename;
		if (!file_exists($file_name)){
			$this->splitJson('未找到上传的文件，请确认是否正确上传！',1);
		}
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		$fileExt = substr(strrchr($file_name, '.'), 1);
		if ($fileExt == 'xlsx'){		//2007以上版本
			$reader = PHPExcel_IOFactory::createReader('Excel2007');
		}elseif ($fileExt == 'xls'){	//2007一下版本
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		}
		$PHPExcel = $reader->load($file_name);
		$sheet = $PHPExcel->getSheet(0);
		
		$highestRow = $sheet->getHighestRow();		//读取总行数
		$colsNum = $sheet->getHighestColumn();	//读取总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
		
		$this->load->model("common_model");
		
		//获取所有的地区+城市+区县
		$proList = $this->common_model->getDqList('sf');
		$cityList = $this->common_model->getDqList('cs');
		$distList = $this->common_model->getDqList('qx');
		
		$this->load->library("hencrypt");
		$pass_word = '';
		$errorMsg = '';
		for ($row = 2;$row <= $highestRow;$row++){
			$data = array();
			for ($column = 0;$column < $highestColumm;$column++){					//循环得到列
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);		//获取列名（A/B/C/D）
					
				$value = $sheet->getCellByColumnAndRow($column,$row)->getValue();
				//根据不同的列组合不同的元素
				if ($columnName == 'A' && !empty($value)){				//姓名
					$data['uname'] = $value;
				}elseif ($columnName == 'B'){							//账号
					$data['username'] = $value;
				}elseif ($columnName == 'C'){							//密码
					$password = $value;
				}elseif ($columnName == 'D'){							//省
					$province = $value;
				}elseif ($columnName == 'E'){							//市
					$city = $value;
				}elseif ($columnName == 'F'){							//区
					$district = $value;
				}elseif ($columnName == 'G'){							// 邮箱
					$data['email'] = $value;
				}elseif ($columnName == 'H'){							// 权限列表
					$data['role'] = $value;
				}
			}
		
			//重新组合数据
			if (!empty($data)){
		
				// 检查下是否有重复的
				$_soUserInfo = $this->common_model->checkInfo(array('username' => $data['username']), 'auth', 1);
				if (!empty($_soUserInfo)){
					$errorMsg .= "第".$row."行 {$data['phone']} 已经存在，跳过<br>";
					continue;
				}
		
				// 将地区进行转换
				if (!empty($province)){
					$data['province'] = (int)$this->common_model->getSearchDqForId('sf',$province,$proList);
				}else{
					$data['province'] = 0;
				}
				if (!empty($city)){
					$data['city'] = (int)$this->common_model->getSearchDqForId('cs',$city,$cityList);
				}else{
					$data['city'] = 0;
				}
				if (!empty($district)){
					$data['district'] = (int)$this->common_model->getSearchDqForId('qx',$district,$distList);
				}
				
				// 随机生成密码
				
				$data['password'] = $this->hencrypt->encrypt($password);
				$data['status'] = 1;
				$data['mach_ids'] = 0;
				$data['created'] = time();

				$this->common_model->set_table("auth");
				$info_id = $this->common_model->save($data);
				if (empty($info_id)){
					$errorMsg .= '第'.$row.'行入库失败，很可能已经存在了！<br>';
				}
		
			}
			unset($data);
		}
		
		echo $errorMsg;
	}
	
	/**
	 * 将串码导入串码池中
	 * add by yinyibin@woshop.cn
	 * 2016年12月19日15:28:40
	 */
	public function ImportImeiExcel(){
		$filename = $this->input->get("file",true);
		$memcacheKey = $this->input->get("mkey", true);
	
		$file_name = FCPATH.$filename;
		if (!file_exists($file_name)){
			$this->splitJson('未找到上传的文件，请确认是否正确上传！',1);
		}
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		$fileExt = substr(strrchr($file_name, '.'), 1);
		if ($fileExt == 'xlsx'){		//2007以上版本
			$reader = PHPExcel_IOFactory::createReader('Excel2007');
		}elseif ($fileExt == 'xls'){	//2007一下版本
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		}
		$PHPExcel = $reader->load($file_name);
		$sheet = $PHPExcel->getSheet(0);
	
		$highestRow = $sheet->getHighestRow();		//读取总行数
		$colsNum = $sheet->getHighestColumn();	//读取总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
	
		$this->load->model("common_model");
	
		$errorMsg = '';
		for ($row = 2;$row <= $highestRow;$row++){
			$data = array();
			for ($column = 0;$column < $highestColumm;$column++){					//循环得到列
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);		//获取列名（A/B/C/D）
	
				$value = $sheet->getCellByColumnAndRow($column,$row)->getValue();
	
				if ($columnName == 'A'){				// imei
					$data['imei'] = $value;
				}elseif ($columnName == 'B'){			// 手机型号
					$data['machname'] = $value;
				}
			}
			//获取操作者
			
			// 对数据进行整理
			if (!empty($data)){
	
				if(empty($data['imei'])){
					$errorMsg .= "第 ".$row." 行A列串码为空，不在继续导入！<br>";
					continue;
				}
				if(empty($data['machname'])){
					$errorMsg .= "第 ".$row." 行B列手机型号为空，不在继续导入！<br>";
					continue;
				}
				//判断该发展人编码是否被多次注册
				$repeatsql ="select count(*) as count from dw_imei where imei={$data['imei']} ";
				$count = $this->common_model->execute($repeatsql);
					if ($count[0][count] >= 1){
						$errorMsg .= "第".$row."行 该串码以存在，不在继续导入！<br>";
					}
				unset($repeatsql, $count);
					
				//准备入库
				$data['auth_id'] = $this->user_id;
				$this->common_model->set_table("imei");
				$info_id = $this->common_model->save($data);
				if (empty($info_id)){
					$errorMsg .= "第".$row."行写入数据库失败，可能的原因是当前这条数据已经存在，请确认！<br>";
				}
	
			}
	
		}
		if ($errorMsg){
			$this->splitJson($errorMsg, 1);
		}else{
			$this->splitJson('导入完成！',0);
		}
	}
	
}