<?php if (! defined('BASEPATH')) exit('Access Denied!');
/**
 * 公共函数库
 */


function uasort_cmp($a, $b) {
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? 1 : -1;		// 从大道小排序
	//return ($a < $b) ? -1 : 1;		// 从小到大排序
}

/**
 * 科学计数法转成正常数字
 * @param unknown $num	科学计数法字符串  如 2.1E-5
 * @param number $double小数点保留位数 默认5位 
 */
function NumToStr($num){
    if (stripos($num,'e')===false) return $num; 
    $num = trim(preg_replace('/[=\'"]/','',$num,1),'"');//出现科学计数法，还原成字符串 
    $result = ""; 
    while ($num > 0){ 
        $v = $num - floor($num / 10)*10; 
        $num = floor($num / 10); 
        $result   =   $v . $result; 
    }
    return $result; 
}

/**
 * 统一的错误输出
 * add by zhixiao476@gmail.com
 * 2015年12月10日15:49:53
 */
 function splitJson($json,$type = 0) {
	if (empty($type)){
		echo json_encode($json);
	}else{
		return json_encode($json);
	}
}


/**
 * md5加密处理
 * add by zhixiao476@gmail.com
 * 2015年12月10日20:13:15
 */
function mymd5($str){
	if (empty($str)) return false;
	$enStr = "E#&340DW#)&^$%SD";		//加密串
	return md5(md5($str).$enStr);
}


/**
 * 去掉数组中为空的元素
 * add by zhixiao476@gmail.com
 * 2015年12月25日21:40:35
 */
function RemoveArrayNull($array = array()){
	foreach((array)$array as $key => $val){
		if (empty($val)){unset($array[$key]);}
	}
	return $array;
}

/**
 * 将以个用“,”或其他方式分割的字符串切割成数组
 * add by zhixiao476@gmail.com
 * 2016年02月02日12:02:40
 */
function SplitStrToArray($str = '',$split = ','){
	if (empty($str)) return false;
	if (stripos($str, $split) !== false){
		$_str = explode($split,$str);
		return RemoveArrayNull($_str);
	}else{
		return array($str);
	}
}