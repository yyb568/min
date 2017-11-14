<?php if (! defined('BASEPATH')) exit('Access Denied!');

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
/*if ( ! function_exists('site_url')) {
	function site_url($uri = '',$domain = '') {
		//判断是否是以http开头的
		if(substr($uri, 0, 7) == 'http://') {
			return $uri;
		}

		//do the rewrite rule
		if(function_exists('rewrite')){
			$uri = rewrite($uri);
		}
		
		$CI =& get_instance();
		if ($domain){
			$url = "http://".str_replace("http://{$domain}", "www",$CI->config->site_url($uri));
			return $url;
		}else{
			return $CI->config->site_url($uri);
		}
	}
}*/
/*
 * 会员企业站路径设置
 * uri:直接填写会员的域名即可 domain_name
 */
if (!function_exists('site_member')){
function site_member($uri = ''){
	if (empty($uri)){return false;}
	$CI =& get_instance();
	$url = "http://".str_replace("http://www", $uri, $CI->config->config['web']['url']);
	return $url;
}
}
//重写url，用于router中优化url
//在url辅助函数site_url中调用
if ( ! function_exists('rewrite')) {
	function rewrite($url){
		$CI = &get_instance();
		$CI->config->load('rewrite', TRUE);
		$rewrite = $CI->config->item('rewrite');

		ksort($rewrite['pattern']);
		ksort($rewrite['replace']);

		$url = preg_replace($rewrite['pattern'], $rewrite['replace'], $url, 1);
		return $url;
	}
}

/**
 * 取全路径url
 *
 * @see		full_url()
 * @author	Cyning <haohailuo@163.com>
 * @version	Tue May 17 17:34:42 CST 2011
 * @param	<string> $url 路径
 * @return	<string>
*/
function full_url($url = '') {
	$url = site_url($url);
	if (substr($url, 0, 7) != 'http://') {
		if (!empty($_SERVER["HTTP_HOST"])) {
			$url = 'http://'.$_SERVER["HTTP_HOST"].$url;
		}else {
			$CI =& get_instance();
			
			$url = $CI->config->item('full_url').$url;
		}
		
	}
	return $url;
}

/**
 * 获取静态文件，images、js、css等的路径
 *
 * @see		static_url()
 * @access	public
 * @since	$Id: common_helper.php 354 2011-03-09 05:39:46Z xuhaoyou $
 * @author	Cyning <haohailuo@163.com>
 * @version	Wed Jan 20 11:26:19 CST 2010
 * @param 	<string> $type	文件类型
 * @return	<string> $staticurl 静态文件的路径
*/
function static_url($type = 'images') {
	if (! in_array($type, array('images', 'js', 'css', 'img','uploads') ) ) {
		$type = 'images';
	}
	if ($type == 'javascript') {
		$type = 'js';
		$staticurl = base_url().'static/'.$type.'/';
	}elseif ($type == "other"){
		$staticurl = base_url().'static/';
	}elseif ($type == 'uploads'){
		$staticurl = base_url()."uploads/";
	}else{
		$staticurl = base_url().'static/'.$type.'/';
	}
	
	return $staticurl;
}

/**
 * 由长连接生成短链接操作
 *
 * 算法描述：使用6或者7个字符来表示短链接，我们使用ASCII字符中的'a'-'z','0'-'9','A'-'Z'，共计62个字符做为集合。
 * 每个字符有62种状态，六个字符就可以表示62^6（56800235584）或者62^7（3521614606208），那么如何得到这六个字符，
 * 具体描述如下：
 *  1. 对传入的长URL+设置key值 进行Md5，得到一个32位的字符串(32 字符十六进制数)，即16的32次方；
 *  2. 将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1)与操作, 即超过30位的忽略处理；
 *	3. 这30位分成6段, 每5个一组，算出其整数值，然后映射到我们准备的62个字符中, 依次进行获得一个6位的短链接地址。
 *
 */
function shortUrl( $long_url, $length = 6 ) {
	$key = 'CyningORzhixiao';
	$base32 = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

	if($length == 7) {
		$yu = 0x7FFFFFFFF;
	}else {
		$yu = 0x3FFFFFFF;
	}

	// 利用md5算法方式生成hash值
	$hex = hash('md5', $long_url.$key);
	$hexLen = strlen($hex);
	$subHexLen = $hexLen / 8;

	$output = array();
	for( $i = 0; $i < $subHexLen; $i++ ) {
		//将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1或者35位1)进行与操作
		$subHex = substr($hex, $i*8, 8);
		$idx = $yu & (1 * ('0x' . $subHex));
		 
		// 这30位分成6段, 35位分为7段, 每5个一组，算出其整数值，然后映射到我们准备的62个字符
		$out = '';
		for( $j = 0; $j < $length; $j++ ) {
			$val = 0x0000003D & $idx;
			$out .= $base32[$val];
			$idx = $idx >> 5;
		}
		$output[$i] = $out;
	}

	return $output;
}

/* End of file url_helper.php */
/* Location: ./applicaion/helpers/url_helper.php */