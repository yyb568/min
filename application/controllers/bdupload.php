<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 百度文件上传服务端处理
 * add by zhixiao476@gmail.com
 * 2016年11月17日13:57:20
 */
@set_time_limit(5 * 60);
class Bdupload extends MY_Controller{
	
	private $cleanupTargetDir = true;		// 移除旧文件
	private $targetDir;
	private $uploadDir;
	private $maxFileAge;		// 最大执行上传时间
	
	/**
	 * 初始化
	 * add by zhixiao476@gmail.com
	 * 2016年11月17日13:57:38
	 */
	public function __construct(){
		parent::__construct();
		$this->targetDir = FCPATH.'uploads/upload_tmp';
		$this->uploadDir = FCPATH.'uploads/unicomcsv';
		$this->maxFileAge = 5 * 3600;
	}
	
	/**
	 * 准备上传
	 * add by zhixiao476@gmal.com
	 * 2016年11月17日13:58:11
	 */
	public function uploads(){
		
		if (!file_exists($this->targetDir)) {
			@mkdir($this->targetDir);
		}
		
		// Create target dir
		if (!file_exists($this->uploadDir)) {
			@mkdir($this->uploadDir);
		}
		$name = $this->input->post("name",true);
		if (isset($name)) {
			$fileName = $name;
		} elseif (!empty($_FILES)) {
			$fileName = $_FILES["file"]["name"];
		} else {
			$fileName = uniqid("file_");
		}
		
		$filePath = $this->targetDir . DIRECTORY_SEPARATOR . $fileName;
		$uploadPath = $this->uploadDir . DIRECTORY_SEPARATOR . $fileName;
		
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
		
		if ($this->cleanupTargetDir) {
			if (!is_dir($this->targetDir) || !$dir = opendir($this->targetDir)) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
			}
		
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $this->targetDir . DIRECTORY_SEPARATOR . $file;
		
				// If temp file is current file proceed to the next
				if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
					continue;
				}
		
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $this->maxFileAge)) {
					@unlink($tmpfilePath);
				}
			}
			closedir($dir);
		}
		
		if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
		
		if (!empty($_FILES)) {
			if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}
		
			// Read binary input stream and append it to temp file
			if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		} else {
			if (!$in = @fopen("php://input", "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		}
		
		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}
		
		@fclose($out);
		@fclose($in);
		
		rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
		
		$index = 0;
		$done = true;
		for( $index = 0; $index < $chunks; $index++ ) {
			if ( !file_exists("{$filePath}_{$index}.part") ) {
				$done = false;
				break;
			}
		}
		if ( $done ) {
			if (!$out = @fopen($uploadPath, "wb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			}
		
			if ( flock($out, LOCK_EX) ) {
				for( $index = 0; $index < $chunks; $index++ ) {
					if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
						break;
					}
		
					while ($buff = fread($in, 4096)) {
						fwrite($out, $buff);
					}
		
					@fclose($in);
					@unlink("{$filePath}_{$index}.part");
				}
		
				flock($out, LOCK_UN);
			}
			@fclose($out);
		}
		
		// Return Success JSON-RPC response
		echo json_encode(array(
				'jsonrpc' => '2.0',
				'result' => $fileName,
				'id' => 'id'
		));
		
	}
}