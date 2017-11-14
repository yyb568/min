<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * JS防机器人
 * add by zhixiao476@gmail.com
 * 2014年11月21日14:22:31
 */
class Seed_Validate{
	
	/**
	 * 获取验证种子
	 */
	static public function getSeed($usr, $name){
		//存储一个种子，用于页面验证，防止恶意刷签到
		$a = rand(1, 30);
		$b = substr(md5("fs_as_{$usr}_{$name}"),8,3);
		$time = floor(time()/3600);
		$seed = "{$a}|{$b}|{$name}|{$time}";
		return $seed;
	}
	
	/**
	 * 服务端计算验证码
	 * @param unknown_type $seed
	 */
	static public function getHashCode($seed){
		$data = explode('|', $seed);
		$a = $data[0];
		$b = $data[1];
		$name = $data[2];
		$time = $data[3];
		//超过个1小时的无效
		if(floor(time()/3600) - $time > 1)return null;
		$hashCode = substr($b, self::getStartIndex($b, $a), 3).'zY.'.substr($time, self::getStartIndex($time, $a), 3)
		.substr($name, self::getStartIndex($name, $a),3);
		$hashCode = substr($hashCode, self::getStartIndex($hashCode, $a), 8);
		return $hashCode;
	}
	
	/**
	 * 计算截取字符串的起始位置
	 * @param unknown_type $str
	 * @param unknown_type $a
	 */
	static protected function getStartIndex($str, $a) {
		$length = strlen($str);
		$start = $a % $length;
		$start = $start < $length/2 ? $start : -$start;
		return $start;
	}
	/**
	 * 服务端检查验证码(同时检查b参数和hashCode)
	 * @param unknown_type $seed
	 * @param unknown_type $hashCode
	 * @param unknown_type $usr
	 */
	static public function checkHashCode($seed, $hashCode, $usr, $name){
		if(!$hashCode) {
			return false;
		}
		$data = explode('|', $seed);
		$clientB = $data[1];//客户端的种子所包含的b参数
		$serverB = substr(md5("fs_as_{$usr}_{$name}"),8,3);//根据用户名、模块名计算的b参数
		if($clientB != $serverB) {
			return false;
		}
		$hashCodeInServer = self::getHashCode($seed);
		return $hashCodeInServer == $hashCode;
	}
	
	/**
	 * 下发种子同时生成js
	 * @param string $usr 用户名
	 * @param string $name 模块标志
	 */
	static public function writeJs($usr, $name){
		$seed = self::getSeed($usr, $name);
		echo <<<DOC
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(36)};if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[4-79c-r]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\\b'+e(c)+'\\\b','g'),k[c]);return p}('7 getRebuildUrl(h,c){4 6=k(c);h=h+\'&_s_3d=\'+c+\'&_s_3c=\'+6;9 h}7 k(c){4 d=c.split(\'|\');4 a=d[0];4 b=d[1];4 e=d[2];4 f=d[3];4 l=m(b,a);4 n=o(f,a);4 p=q(e,a);4 6=l+\'zY.\'+n+p;6=6.i(g(6,a),8);9 6}7 m(b,a){4 5=g(b,a);9 b.i(5,3)}7 o(f,a){4 5=g(f,a);9 f.i(5,3)}7 q(e,a){4 5=g(e,a);9 e.i(5,3)}7 g(r,a){4 j=r.j;4 5=a%j;5=5<j/2?5:-5;9 5}',[],28,'||||var|start|hashCode|function||return|||seed|data|name|time|getStartIndex|url|substr|length|getHashCode|param1|getPart1|param2|getPart2|param3|getPart3|str'.split('|'),0,{}))
function gotoUrlWithSeedValidate(url){
	var seed = '{$seed}';
	url = getRebuildUrl(url, seed);
	location.href = url;
}
function rebuildUrlWithSeed(url){return getRebuildUrl(url, '{$seed}');}
DOC;
	}
	
	/**
	 * 不下发种子,只生成相关js
	 * 此时种子需要另外使用ajax方法获得
	 */
	static public function writeJsWithoutSeed(){
		echo <<<DOC
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(36)};if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[4-79c-r]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\\b'+e(c)+'\\\b','g'),k[c]);return p}('7 getRebuildUrl(h,c){4 6=k(c);h=h+\'&_s_3d=\'+c+\'&_s_3c=\'+6;9 h}7 k(c){4 d=c.split(\'|\');4 a=d[0];4 b=d[1];4 e=d[2];4 f=d[3];4 l=m(b,a);4 n=o(f,a);4 p=q(e,a);4 6=l+\'zY.\'+n+p;6=6.i(g(6,a),8);9 6}7 m(b,a){4 5=g(b,a);9 b.i(5,3)}7 o(f,a){4 5=g(f,a);9 f.i(5,3)}7 q(e,a){4 5=g(e,a);9 e.i(5,3)}7 g(r,a){4 j=r.j;4 5=a%j;5=5<j/2?5:-5;9 5}',[],28,'||||var|start|hashCode|function||return|||seed|data|name|time|getStartIndex|url|substr|length|getHashCode|param1|getPart1|param2|getPart2|param3|getPart3|str'.split('|'),0,{}))
function rebuildUrlWithSeed(url, seed){return getRebuildUrl(url, seed);}
DOC;
	}
}