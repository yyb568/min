<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * memache缓存类
 *
 * @package		haohailuo
 * @author		By Cyning <haohailuo@163.com>
 * @copyright	Copyright (c) 2011, Haohailuo, Inc.
 * @link		http://www.haohailuo.com
 * @since		Version 3.0 $Id$
 * @version		Wed Jan 05 11:30:49 CST 2011
 * @filesource
*/

class CI_Memcache {
	
	public $mc;
	private $mkey;
	private $memcached_lib = 'memcache';
	
	function CI_Memcache()
	{
		if (class_exists('Memcached')) {
			$this->mc = new Memcached();
			$this->mc->addServer(MEMCACHE_HOST, MEMCACHE_PORT);
			$this->memcached_lib = 'memcached';
		} else {
			$this->mc = new Memcache();
			$this->mc->connect(MEMCACHE_HOST, MEMCACHE_PORT);
			$this->memcached_lib = 'memcache';
		}
	}
	
	/**
	 * 获取缓存 
	 *
	 * @param string $key
	 */
	function get($key)
	{
		$this->mkey = $key;
		return $this->mc->get($key);
	}
	
	/**
	 * 设置缓存
	 *
	 * @param cache_key $key
	 * @param unknown_type $data
	 * @param unknown_type $expiretime
	 */
	function set($key, $data, $expiretime=0) 
	{
		if( 'memcached' == $this->memcached_lib ) 
		{
			return $this->mc->set($key, $data, $expiretime);
		} else {
			return $this->mc->set($key, $data, 0, $expiretime);
		}
	}
	
	/**
	 * 删除缓存
	 *
	 * @param cache_key $key
	 * @return unknown
	 */
	function delete($key) {
		return $this->mc->delete($key);
	}
	
	function del($key) {
		return $this->delete($key);
	}		
}


?>