<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 统一的文件上传
 * 如果需要上传大文件，需要这对.htaccess文件做修改，详情见该文件
 * add by zhixiao476@gmail.com
 * 2014年11月6日20:09:07
 */
class _up extends MY_Controller{
	
	private $php_path;
	private $save_path;		//文件保存路径
	private $save_url;
	//允许上传的文件扩展名
	private $ext_arr;
	private $max_size	=	1014416637;	//允许最大上传大小

	public function __construct(){
		parent::__construct();
		$this->php_path = dirname(__FILE__) . '/';
		$this->save_url = '/uploads/';
		$this->save_path =  ROOTPATH . 'uploads/';
		$this->ext_arr = array(
							'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
							'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','mp4','flv'),
						);
	}

	/*
	 * 开始准备上传文件
	 * add by zhixiao476@gmail.com
	 * 2014年11月6日20:13:04
	 */
	public function upload(){
		$this->ImageError();
		if (empty($_FILES) === false) {
			//原文件名
			$file_name = $_FILES['imgFile']['name'];
			//服务器上临时文件名
			$tmp_name = $_FILES['imgFile']['tmp_name'];
			//文件大小
			$file_size = $_FILES['imgFile']['size'];
			//检查文件名
			if (!$file_name) {
				$this->alert("请选择文件。");
			}
			//检查目录
			if (@is_dir($this->save_path) === false) {
				$this->alert("上传目录不存在。");
			}
			//检查目录写权限
			if (@is_writable($this->save_path) === false) {
				$this->alert("上传目录没有写权限。");
			}
			//检查是否已上传
			if (@is_uploaded_file($tmp_name) === false) {
				$this->alert("上传失败。");
			}
			//检查文件大小
			//if ($file_size > $this->max_size) {
			//	$this->alert("上传文件大小超过限制。");
			//}
			//检查目录名
			$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
			$stype = $_GET['stype'];
		//	$dir_name = empty($this->input->get('dir',true)) ? 'image' : trim($this->input->get('dir',true));
			if (empty($this->ext_arr[$dir_name])) {
				$this->alert("目录名不正确。");
			}
			
			//组合新的文件路径
			if ($dir_name == 'image' || $stype == 'image'){			//图片上传
				
				$this->save_path .= 'picture/'.date("Ym");
				$this->save_url .= "picture/".date("Ym");
				$dir_name = $stype;
				
			}elseif ($dir_name == 'file' && $stype == 'video'){				// 视频上传
				
				$this->save_path .= 'videos/'.date("Ym");
				$this->save_url .= "videos/".date("Ym");
				
			}elseif ($dir_name == 'file'){				//文件上传
				
				$this->save_path .= 'files/'.date("Ym");
				$this->save_url .= "files/".date("Ym");
				
			}else{
				exit("无法上传此类型的文件！");
			}
			
			//获得文件扩展名
			$temp_arr = explode(".", $file_name);
			$file_ext = array_pop($temp_arr);
			$file_ext = trim($file_ext);
			$file_ext = strtolower($file_ext);
			//检查扩展名
			if (in_array($file_ext, $this->ext_arr[$dir_name]) === false) {
				$this->alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $this->ext_arr[$dir_name]) . "格式。");
			}
			//创建文件夹
			if ($dir_name !== '') {
				if (!file_exists($this->save_path)) {
					mkdir($this->save_path);
				}
			}

			//chmod($this->save_path, 755);

			//新文件名
			$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
			//移动文件
			$file_path = $this->save_path.'/' . $new_file_name;
			if (move_uploaded_file($tmp_name, $file_path) === false) {
				$this->alert("上传文件失败。");
			}
			@chmod($file_path, 0755);
			$file_url = $this->save_url .'/'. $new_file_name;
		
			header('Content-type: text/html; charset=UTF-8');
			echo json_encode(array('error' => 0, 'url' => $file_url));
			exit;
		}
	}
	
	/*
	 * php代码部分上传失败的错误跑出
	 * add by zhixiao476@gmail.com
	 * 2014年11月6日20:13:50
	 */
	private function ImageError(){
		if (!empty($_FILES['imgFile']['error'])) {
			switch($_FILES['imgFile']['error']){
				case '1':
					$error = '超过php.ini允许的大小。';
					break;
				case '2':
					$error = '超过表单允许的大小。';
					break;
				case '3':
					$error = '图片只有部分被上传。';
					break;
				case '4':
					$error = '请选择图片。';
					break;
				case '6':
					$error = '找不到临时目录。';
					break;
				case '7':
					$error = '写文件到硬盘出错。';
					break;
				case '8':
					$error = 'File upload stopped by extension。';
					break;
				case '999':
				default:
					$error = '未知错误。';
			}
			$this->alert($error);
		}
	}
	
	function alert($msg) {
		header('Content-type: text/html; charset=UTF-8');
		echo json_encode(array('error' => 1, 'message' => $msg));
		exit;
	}
}
