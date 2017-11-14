<?php

/**
 * Redis 操作类库
 * add by zhixiao476@gmail.com
 * 2016年09月12日14:02:16
 */
class CI_Redis{
	
	private $db;
	private $redis;			// 对象资源池
	
	public function __construct($host = REDIS_HOST, $port = REDIS_PORT, $db = REDIS_DB){
		$this->connect($host, $port, $db);
	}
	
	/**
	 * 写入缓存数据到redis中
	 * add by zhixiao476@gmail.com
	 * 2016年09月12日14:08:13
	 */
	public function set($name, $value, $expire = 3600){
		if($expire == 0){
			$ret = $this->redis->set($name, $value);
		}else{
			$ret = $this->redis->setex($name, $expire, $value);
		}
		return $ret;
	}
	
	/**
	 * 读取缓存信息
	 * add by zhixiao476@gmail.com
	 * 2016年09月12日14:09:42
	 */
	public function get($name){
		// 是否一次取多个值
        $func = is_array($name) ? 'mGet' : 'get';
        return $this->redis->{$func}($name);
	}
	
	/**
	 * 删除缓存
	 * add by zhixiao476@gmail.com
	 * 2016年09月12日14:11:10
	 */
	public function remove($key){
		return $this->redis->delete($key);
	}
	 
	/**
	 * 值加加操作,类似 ++$i ,如果 key 不存在时自动设置为 0 后进行加加操作
	 * add by zhixiao476@gmail.com
	 * 2016年09月12日14:11:28
	 * @param string $key 缓存KEY
	 * @param int $default 操作时的默认值
	 * @return int　操作后的值
	 */
	public function incr($key,$default=1){
		if($default == 1){
			return $this->redis->incr($key);
		}else{
			return $this->redis->incrBy($key, $default);
		}
	}
	
	/**
	 * 值减减操作,类似 --$i ,如果 key 不存在时自动设置为 0 后进行减减操作
	 * add by zhixiao476@gmail.com
	 * 2016年09月12日14:12:04
	 * @param string $key 缓存KEY
	 * @param int $default 操作时的默认值
	 * @return int　操作后的值
	 */
	public function decr($key,$default=1){
		if($default == 1){
			return $this->redis->decr($key);
		}else{
			return $this->redis->decrBy($key, $default);
		}
	}
	 
	/**
	 * 添空当前数据库
	 * add by zhixiao476@gmail.com
	 * @return boolean
	 */
	public function clear(){
		return $this->redis->flushDB();
	}
	
	
	
	/**
	 * 连接redis
	 * add by zhixiao476@gmail.com
	 * 2016年09月12日14:04:18
	 */
	private function connect($host, $port, $db){
		$redis = new Redis();
		$this->redis = $redis->pconnect($host, $port);
	}
	
	
	/**
	 * 关闭Redis连接资源
	 * add by zhixiao476@gmail.com
	 * 2016年09月12日14:07:11
	 */
	public function close(){
		$this->redis->close();
	}
}