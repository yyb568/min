<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 浏览服务器图片
 * add by zhixiao476@gmail.com
 * 2014年12月09日15:48:34 
 */
class manager extends MY_Controller{
	private $php_path;
	private $save_path;		//文件保存路径
	private $save_url;
	//允许上传的文件扩展名
	private $ext_arr;

	/*
	 * 初始化相关资源
	 * add by zhixiao476@gmail.com
	 * 2014年12月09日15:48:56
	 */
	public function __construct(){
		parent::__construct();
		$this->php_path = dirname(__FILE__) . '/';
		$this->save_url = '/uploads';
		$this->save_path =  ROOTPATH . 'uploads/';
		$this->ext_arr = array(
				'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
				'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'txt', 'zip', 'rar', 'gz', 'bz2','mp4','mov'),
		);
	}

	/*
	 * 获取指定目录下的文件列表
	 * add by zhixiao476@gmail.com
	 * 2014年12月09日15:50:42
	 */
	public function fileList(){
		$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
		$stype = $_GET['stype'];				//当前参数是用来区分是文件还是视频的
		$order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);	//排序方式
		//判断指定的目录是否存在
		if ($dir_name == 'image'){									//图片

			$_path = $this->save_path.'picture/';
			$this->save_url .= '/picture/';
			$this->save_path .= '/picture/';
		}elseif($dir_name == 'file' && $stype == 'video'){		//视频

			$_path = $this->save_path.'videos/';
			$this->save_url .= '/videos/';
			$this->save_path .= '/videos/';
		}elseif ($dir_name == 'file'){							// 文件
			$_path = $this->save_path.'files/';
			$this->save_url .= '/files/';
			$this->save_path .= '/files/';
		}
		if (!file_exists($_path)) {
			mkdir($_path);
		}


		//根据path参数，设置各路径和URL
		if (empty($_GET['path'])) {
			$current_path = realpath($_path) . '/';		//realpath变成相对地址
			$current_url = $this->save_url;
			$current_dir_path = '';
			$moveup_dir_path = '';
		} else {
			$current_path = ($_path) . '/' . $_GET['path'];
			$current_url = $this->save_url . $_GET['path'];
			$current_dir_path = $_GET['path'];
			$moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
		}

		//不允许使用..移动到上一级目录
		if (preg_match('/\.\./', $current_path)) {
			echo 'Access is not allowed.';
			exit;
		}
		//最后一个字符不是/
		if (!preg_match('/\/$/', $current_path)) {
			echo 'Parameter is not valid.';
			exit;
		}
		//目录不存在或不是目录
		if (!file_exists($current_path) || !is_dir($current_path)) {
			echo 'Directory does not exist.';
			exit;
		}

		//遍历目录取得文件信息
		$file_list = array();
		if ($handle = opendir($current_path)) {
			$i = 0;
			while (false !== ($filename = readdir($handle))) {
				if ($filename{0} == '.') continue;
				$file = $current_path . $filename;
				if (is_dir($file)) {
					$file_list[$i]['is_dir'] = true; //是否文件夹
					$file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
					$file_list[$i]['filesize'] = 0; //文件大小
					$file_list[$i]['is_photo'] = false; //是否图片
					$file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
				} else {
					$file_list[$i]['is_dir'] = false;
					$file_list[$i]['has_file'] = false;
					$file_list[$i]['filesize'] = filesize($file);
					$file_list[$i]['dir_path'] = '';
					$file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
					$file_list[$i]['is_photo'] = in_array($file_ext, $this->ext_arr[$dir_name]);
					$file_list[$i]['filetype'] = $file_ext;

				}
				$file_list[$i]['filename'] = $filename; //文件名，包含扩展名
				$file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
				$i++;
			}
			closedir($handle);
		}

		usort($file_list, array('Manager','cmp_func'));

		$result = array();
		//相对于根目录的上一级目录
		$result['moveup_dir_path'] = $moveup_dir_path;
		//相对于根目录的当前目录
		$result['current_dir_path'] = $current_dir_path;
		//当前目录的URL
		$result['current_url'] = $current_url;
		//文件数
		$result['total_count'] = count($file_list);
		//文件列表数组
		$result['file_list'] = $file_list;

		//输出JSON字符串
		header('Content-type: application/json; charset=UTF-8');
		$this->load->library("JSON");
		//print_r($result);die;
		echo $this->json->encode($result);
	}

	//排序
	function cmp_func($a, $b) {
		global $order;
		if ($a['is_dir'] && !$b['is_dir']) {
			return -1;
		} else if (!$a['is_dir'] && $b['is_dir']) {
			return 1;
		} else {
			if ($order == 'size') {
				if ($a['filesize'] > $b['filesize']) {
					return 1;
				} else if ($a['filesize'] < $b['filesize']) {
					return -1;
				} else {
					return 0;
				}
			} else if ($order == 'type') {
				return strcmp($a['filetype'], $b['filetype']);
			} else {
				return strcmp($a['filename'], $b['filename']);
			}
		}
	}
}