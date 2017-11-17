<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 佣金规则管理（通过机型+时间+地区等方式进行规则制定)
 * add by yinyibin
 * 2016年08月05日09:20:01
 */
class Mvmanage extends  MY_Controller{
	
	/**
	 * 初始化相关
	 * add by zhixiao476@gmail.com
	 * 2016年08月05日09:20:17
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model("common_model");
	}
	
	/**
	 * 佣金规则管理首页
	 * add by yinyibin
	 * 2016年08月05日09:20:38
	 */
	public function index($offset = 0){
		$price = $this->input->get("price",true);		//价格过滤
		$buss_id = $this->input->get("buss_id",true);	//平台商过滤
		$dis = $this->input->get("dis",true);			//区县
		$pro = $this->input->get("pro",true);			//省份
		$city = $this->input->get("city",true);			//城市
		$starttime = $this->input->get("starttime",true);
		$endtime = $this->input->get("endtime",true);
		$area = $this->input->get("area", true);
		$keyword = $this->input->get("keyword",true);		// 搜索查询关键词
		$expload = $this->input->get("expload",true);		//将组合条件查询后的结果导出到Excel文件
		
		if (empty($expload)){
			$pageSize = 20;
		}else{
			$pageSize = -1;
		}
		
		$params = array(
				'table' => 'excation',
				'select' => 'id,buss_id,mach_type,price,starttime,endtime,province,district,city',
				'order' => 'id',
				'order_type' =>'desc',
				'offset' => $offset,
				'limit' => $pageSize,
		);
		
		// 条件过滤
		$start = strtotime($starttime);
		$end = strtotime($endtime);
		if ($start && $end){
			$params['where'] = array('starttime >=' => $start,'endtime <=' => $end);
		}
		
		if ($price){
			$params['where'] = array('price' => $price);
		}
		if ($buss_id){
			$params['where'] = array('buss_id' => $buss_id);
		}
		if ($dis){
			$params['where'] = array('district' => $dis);
		}
		if ($pro){
			$params['where'] = array('province' => $pro);
		}
		if ($city){
			$params['where'] = array('city' => $city);
		}
		// 地区查询
		if (!empty($area)){
			$_area = explode(',', $area);
			if ($_area[0] && $_area[1] && $_area[2]){$params['where'] = array('province' => $_area[0], 'city' => $_area[1], 'district' => $_area[2]);}
			if ($_area[0] && $_area[1] && empty($_area[2])){$params['where'] = array('province' => $_area[0], 'city' => $_area[1]);}
			if ($_area[0] && empty($_area[1]) && empty($_area[2])){$params['where'] = array('province' => $_area[0]);}
		}
		if ($keyword){
			$params['like_field'] = 'mach_type';
			$params['like_value'] = $keyword;
		}
		
		
		$list = $this->common_model->get_list($params);
		if ($expload == 1){
		$this->exportExcelExecaList($list);
		exit();
		}
		
		$params['total'] = true;
		$total = $this->common_model->get_list($params);
		
		//获取平台商
		$buss = $this->common_model->getBussList();
		foreach($buss as $key => $val){
			$bussList[$val['id']] = $val;
		}
		
		$this->load->library('pagination');
		$config['base_url'] = site_url("excation/index/{page}?city={$city}&pro={$pro}&dis={$dis}&price={$price}&buss_id={$buss_id}&area={$area}&keyword={$keyword}");
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
		$this->template['list'] = $list;
		$this->template['bussList'] = $bussList;
		$this->template['Page'] = '<ul class="pagination ">'.rtrim($pageStr,"/").'</ul>';
		$this->load->view("mvmanage/index", $this->template);
	}
	
	/**
	 * 按照时间方式查询
	 * add by yinyibin
	 * 2016年08月05日14:35:27
	 */
	public function searchType(){
		$this->load->view("mvmanage/searchType", $this->template);
	}
	
	/**
	 * 上传规则文件
	 * add by yinyibin
	 * 2016年08月05日14:49:19
	 */
	public function uploads(){
		$this->load->view("mvmanage/uploads", $this->template);
	}
	
	/**
	 * 编辑机型
	 * add by yinyibin
	 * 2016年08月05日11:22:07
	 */
	public function doAdd($ids = 0){
		if (!empty($ids)){
			$params = array(
					'table' => 'excation',
					'where' => array('id' => $ids),
					'limit' => 1
			);
			$info = $this->common_model->get_list($params);
			$this->template['info'] = $info;
			$pro = $this->common_model->getDqList('sf');
			$city = $this->common_model->getDqList('cs');
			$dis = $this->common_model->getDqList('qx');
			$this->template['_pro'] = $pro;
			$this->template['_city'] = $city;
			$this->template['_dis'] = $dis;
			
		}else{
			$pro = $this->common_model->getDqList('sf');
			$city = $this->common_model->getDqList('cs');
			$dis = $this->common_model->getDqList('qx');
			$this->template['_pro'] = $pro;
			$this->template['_city'] = $city;
			$this->template['_dis'] = $dis;
		}
		$this->template['busslist'] = $this->common_model->getBussList();
		$this->load->view("mvmanage/edit", $this->template);
	}
	
	/**
	 * 保存数据
	 * add by yinyibin
	 * 2016年08月05日13:12:11
	 */
	public function doSave($buss_id = 0){
		$data['buss_id'] = $this->input->post("buss_id",true);
		$data['mach_type'] = $this->input->post("mach_type",true);
		$data['price'] = $this->input->post("price",true);
		$data['starttime'] = $this->input->post("starttime",true);
		$data['endtime'] = $this->input->post("endtime",true);
		$area = $this->input->post("area",true);
		
		//拆分地区数据
		$_area = explode(',', $area);
		
		if (empty($data['buss_id']) || empty($data['mach_type']) || empty($data['price']) || empty($data['starttime']) || empty($data['endtime'])){
			$this->splitJson('请填写完成信息！',1);
		}
		
		if (empty($_area[0]) && empty($_area[1]) && empty($_area[2])){
			$this->splitJson('请至少选择省份！',1);
		}
		
		if ($data['starttime'] > $data['endtime']){
			$this->splitJson('开始时间必须小于结束时间！',1);
		}
		
		$data['starttime'] = strtotime($data['starttime']);
		$data['endtime'] = strtotime($data['endtime']);
		
		//检查当前机型是否存在
		$params = array(
				'table' => 'excation',
				'where' => array('mach_type' => $data['mach_type'], 'buss_id' => $data['buss_id'],'province' => $_area[0],'city' => $_area[0],'district' => $_darea[2]),
				'limit' => 1
		);
		$info = $this->common_model->get_list($params);
		if (!empty($info)){
			$this->splitJson('当前 平台商 下已经添加过该机型，请务重复添加！',1);
		}
		
		// 查询当前地区下，当前时间段内是否存在着一个已经在执行的标准
		$data['province'] = (int)$_area[0];
		$data['district'] = (int)$_area[2];
		$data['city'] = (int)$_area[1];
		$data['created'] = time();

		//入库
		$this->common_model->set_table("excation");
		$info_id = $this->common_model->save($data, $buss_id);
		if (empty($info_id)){
			$this->splitJson('保存失败！',1);
		}else{
			$this->splitJson('保存成功！',0);
		}
	}
	
	
	/**
	 * 删除奖励规则
	 * add by yinyibin
	 * 2016年08月05日14:09:14
	 */
	public function DelInfo($id = 0){
		if (empty($id)){$this->splitJson('无效的ID属性值！',1);}
		$this->common_model->set_table('excation');
		$info_id = $this->common_model->delById($id);
		if (empty($info_id)){
			$this->splitJson('删除失败！',1);
		}else{
			$this->splitJson('删除成功！',0);
		}
	}
    /**
     * 批量删除
     * add by yinyibin
     * 2017年4月12日15:28:14
     */
    public function DelAll(){
	   $ids = $this->input->get("ids",true);
        if($ids[0] == 'on'){
            //将全选时 on删除
            array_shift($ids);
        }

        $arrid = implode(',',$ids);
        $sql ="delete from dw_excation where id in({$arrid})";
        $info_id = $this->common_model->query($sql);
        if (empty($info_id)){
            $this->splitJson('删除失败！',1);
        }else{
            $this->splitJson('删除成功！',0);
        }
    }
	
	/**
	 * 将用户资料导出到文件中
	 * add by yinyibin
	 * 2016年08月06日14:32:49
	 */
	private function exportExcelExecaList($data = array()){
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
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '机型');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '奖励金额');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '开始时间');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '结束时间');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '省份');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '城市');
		$resultPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '区县');
	
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
			if ($val['starttime']){$start = date("Y-m-d H:i:s",$val['starttime']);}
			if ($val['endtime']){$endtime = date("Y-m-d H:i:s",$val['endtime']);}
			$province = $_provinceList[$val['province']]['ProvinceName'];
			$city = $_cityList[$val['city']]['CityName'];
			$district = $_districtList[$val['district']]['DistrictName'];
				
				
			$resultPHPExcel->getActiveSheet(0)->setCellValue('A'.$i, $val['mach_type']);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('B'.$i, $val['price']);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('C'.$i, $start);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('D'.$i, $endtime);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('E'.$i, $province);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('F'.$i, $city);
			$resultPHPExcel->getActiveSheet(0)->setCellValue('G'.$i, $district);
				
			$i++;
		}
		// 设置表头
		$resultPHPExcel->getActiveSheet()->setTitle('佣金规则导出列表');
		$resultPHPExcel->setActiveSheetIndex(0);
	
		$outputFileName = date("Y-m-d")."佣金规则明细";
	
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $outputFileName . '.xls"');
		header('Cache-Control: max-age=0');
	
		$objWriter = PHPExcel_IOFactory::createWriter($resultPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	
	}
	
}