<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class HEncrypt
{
	private static $enkey = '$%#ZYDJL@';
	//62位加密码
	private static $base62 = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	//执行加密
	public static function encrypt($str, $enkey='') {
		$enkey = empty($enkey) ? self::$enkey : $enkey;
		$str = $enkey.$str;
		
		$bin = self::strToBin($str);
		$arr = explode(' ', $bin);
		
		$encode = '';
		foreach($arr as $num) {
			$num = bindec($num);
			if (!empty($encode)) {
				$encode .= self::getSplitChar();
			}

			$encode .= self::numEncode($num);
		}
		
		return $encode;
	}
	
	//执行解密
	public static function decrypt($str, $enkey='') {
		$enkey = empty($enkey) ? self::$enkey : $enkey;
		
		$encode = preg_split('/[$\.,_\-;\*]/', $str);
		$decode = array();
		foreach($encode as $code) {
			$num = self::numDecode($code);
			$decode[] = decbin($num);
		}
		
		$string = join(' ', $decode);
		
		$decode = self::binToStr($string);
		
		return substr($decode, strlen($enkey));
	}
	
	private static function getSplitChar() {
		$chars = array('$', '.', ',', '_', '-', ';', '*');
		
		return $chars[array_rand($chars)];
	}
	
	//十进制数转换为62位加密
	public static function numEncode($number) {
		if ($number <= 0) return false;
		$encode = '';
		
		while($number > 0){
			$_mod = bcmod($number, 62);
			$encode .= self::$base62[$_mod];
			$number = bcdiv(bcsub($number, $_mod), 62);
		}
		
		return strrev($encode);
	}
	
	//62为字段串解密为十进制数
	public static function numDecode($encode) {
		if ($encode == '') return false;
		
		$number = 0;
		$len = strlen($encode);
		
		$arr = array_flip(str_split(self::$base62));
		for($i = 0; $i < $len; $i++){
			$number = bcadd($number, bcmul($arr[$encode[$i]],  bcpow(62, $len - $i - 1)));
		}
		return $number;
	}
	
	//字段串转换为二进制
	public static function strToBin($str){
		//1.列出每个字符
		$arr = preg_split('/(?<!^)(?!$)/u', $str);
		//2.unpack字符
		foreach($arr as &$v){
			$temp = unpack('H*', $v);
			$v = base_convert($temp[1], 16, 2);
			unset($temp);
		}
	
		return join(' ',$arr);
	}
	
	//二进制转换为字段串
	public static function binToStr($str){
		$arr = explode(' ', $str);
		foreach($arr as &$v){
			$v = pack("H".strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
		}
	
		return join('', $arr);
	}
}

?>