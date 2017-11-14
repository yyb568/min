<?php

/**
* FILE_NAME : MY_MOdel.php
*	富商中国行业网站 前台 项目
* @package Cyning
* @author By Cyning <zhixiao476@gmail.com>
* @copyright Copyright (c) 2012, Cyning Inc.
* @link http://zhixiao.fsung.cn * @since Version 1.0
* @version 2012-6-18 * @filesource
*/

class MY_Model extends CI_Model{
	//master
	public $groupdatabase;			// 启用数据库分组
	public $db;
	private $table;	//数据库表
	private $primary_key = 'id'; //主键
	
	public function __construct() {
    	parent::__construct();
    }
    
    public function setDataBase($group = ''){
    	if (empty($group)) return false;
    	$this->groupdatabase = $group;
    }
    
    protected function getDb() {
    	$db = ENVIRONMENT !== 'product' ? 'test' : 'product';
    	return $db;
    }
    
    /*
     * 执行一条sql语句--返回影响行数，（INSERT/DELETE/UPDATE）
     * add by zhixiao476@gmail.com
     * 2014年7月16日21:11:22
     */
    public function query($sql){
    	if (empty($sql)) return false;
    	$db = $this->getDb();
    	$this->db = $this->load->database($db,true,true);
    	$this->db->query($sql);
    	return $this->db->affected_rows();
    }
    
    /**
     * 执行一条sql语句，返回结果集
     * add by zhixiao476@gmail.com
     * 2016年02月18日19:37:13
     */
    public function execute($sql, $limit = 0){
    	if (empty($sql)) return false;
    	$db = $this->getDb();
    	$this->db = $this->load->database($db,true,true);
    	$query = $this->db->query($sql);
    	$num = $this->db->affected_rows();
    	if ($num > 0){
    		if ($limit == 1){
    			$result = $query->row_array();
    		}else{
    			$result = $query->result_array();
    		}
    	}else{
    		$result = array();
    	}
    	return $result;
    }
    
   
    
    /**
     * 获取数据列表
     *
     * @see		get_lists()
     * @author	Cyning <zhixiao476@gmail.com>
     * @version 2013年9月24日 22:44:02
     * @param	('where'=>array(), 'offset'=>0, 'limit'=>20, 'order'=>'id', 'order_type'=>'DESC', 'total'=>false, 'table'=>'可选')
    */
    public function get_list($params) {
    	$result = array();
    	if (empty($params)) return $result;
    	$db = $this->getDb();
    	
    	if (!empty($params['table'])) {$this->set_table($params['table']);}
		$select = !empty($params['select']) ? $params['select'] : '';
		$order = !empty($params['order']) ? $params['order'] : '';
		$where = !empty($params['where']) ? $params['where'] : '';
		$or_where = !empty($params['or_where']) ? $params['or_where'] : '';
		$where_in_field = !empty($params['where_in_field']) ? $params['where_in_field'] : '';
		$where_in_value = !empty($params['where_in_value']) ? $params['where_in_value'] : '';
		$like_field = !empty($params['like_field']) ? $params['like_field'] : '';
		$like_value = !empty($params['like_value']) ? $params['like_value'] : '';
		$table = !empty($params['table']) ? $params['table'] : '';
		$offset = !empty($params['offset']) ? $params['offset'] : 0;
		$limit = !empty($params['limit']) ? $params['limit'] : 0;
    	$limit = intval($limit);
		
    	$limit = ($limit == 0) ? 20 : $limit;
    	$total = isset($params['total']) ? $params['total'] : false;
    	$order_type = !empty($params['order_type']) ? $params['order_type'] : 'DESC';
    	
    	if (!empty($table)) {
    		$this->set_table($table);
    	}
    	$this->db = $this->load->database($db,true,true);
    	if (!empty($select)) {
    		if (is_array($select)) {
    			$select = join(',', $select);
    		}
    		$this->db->select($select);
    	}
    	
    	if (!empty($where)) {
    		$this->db->where($where);
    	}
    	if (!empty($or_where)){
    		$this->db->or_where($or_where);
    	}
    	
    	if (!empty($where_in_field) && !empty($where_in_value) && is_array($where_in_value)) {
    		$this->db->where_in($where_in_field, $where_in_value);
    	}
    	if (!empty($like_field) && !empty($like_value)) {
    		if (is_array($like_field)){
    			foreach($like_field as $val){
    				$this->db->or_like($val,$like_value);
    			}
    		}else{
    			$this->db->like($like_field, $like_value);
    		}
    		/*
    		if (is_array($like_value)){
    			foreach($like_value as $val){
    				$this->db->like($like_field,$val);
    			}
    		}else{
    			$this->db->like($like_field, $like_value);
    		}*/
    	}
    	if (!$total) {
    		if (!empty($order)) {
    			$this->db->order_by($order, $order_type);
    		}
    		if ($limit != -1){
    			$this->db->limit($limit, $offset);
    		}
    		
    		$query = $this->db->get($this->table);
    		//echo $this->db->last_query().'<br>';
    		if ($limit == 1) {
    			$result = $query->row_array();
    		}else {
    			$result = $query->result_array();
    		}
    	}else{
    		$result = $this->db->count_all_results($this->table);
    	}

    	return $result;
    }
    
/**
     * 根据ID获取数据
     *
     * @see		get_data_by_id()
     * @author	Cyning <zhixiao476@gmail.com>
     * @version	2013年9月24日 22:43:27
     * @param	int $id
     * @param	string $select
    */
    public function get_data_by_id($id, $select='') {
    	$db = $this->getDb();
    	$this->db = $this->load->database($db,true,true);
    	if (!empty($select)) {
    		$this->db->select($select);
    	}
    	$this->db->where($this->primary_key, $id);
    	$result = $this->db->get($this->table, 1)->row_array();
    	
    	return $result;
    }
    
    /**
     * 设置当前操作表
     * @param string $table
     */
    public function set_table($table) {
    	$this->table = $table;
    }
    
    /**
     * 获取当前操作表
     * @return string
     */
    public function get_table() {
    	return $this->table;
    }
    
    /**
     * 保存、插入、更新数据
     *
     * @see		save()
     * @author	Cyning <zhixiao476@gmail.com>
     * @version	2013年9月24日 22:43:21
     * @param	int|null $id  主键，更新时需指定，新插入时留空
     * @param	array $db_array 具体的更新或者插入数据的字段
    */
    public function save($db_array,$id = 0, $bach = false) {
    	if (empty($db_array)) {
    		return false;
    	}
    	$db = $this->getDb();
    	
		$this->db = $this->load->database($db,true,true);
    	if (!$id) {
    		if ($bach == true){
    			$this->db->insert_batch($this->table,$db_array);
    		}else{
    			$this->db->insert($this->table, $db_array);
    		}
    		//echo $this->db->last_query();die;
    		return $this->db->insert_id();
    	}else {
    		$this->db->where_in($this->primary_key, $id);
    		$this->db->update($this->table, $db_array);
    		//echo $this->db->last_query();die;
    		return $this->db->affected_rows();
    	}
    }
    
    /**
     * 根据条件删除数据
     *
     * @see		delete()
     * @author	Cyning <zhixiao476@gmail.com>
     * @version	2013年9月24日 22:43:14
     * @param	('where'=>array(), 'table'=>'可选')
     * @return	int|boolean
     */
    public function delete($params) {
    	if (empty($params)) return false;
    	$db = $this->getDb();
    	
    	if (!empty($params['table'])) {
    		$this->set_table($params['table']);
    	}

    	if (!empty($params['where'])) {
    		$this->db = $this->load->database($db,true,true);
    		$this->db->where($params['where']);
    		$this->db->delete($this->table);

    		return $this->db->affected_rows();
    	}
    	
    	return false;
    }
    
    /**
     * 根据主键删除数据
     * @param	$id 主键值
     * @return	int|boolean
     */
    public function delById($id) {
    	$db = $this->getDb();
    	
    	if (!empty($id)) {
    		$this->db = $this->load->database($db,true,true);
	    	$this->db->where($this->primary_key, $id);
	    	$this->db->delete($this->table);
	    	
	    	return $this->db->affected_rows();
    	}
    	
    	return FALSE;
    }
    
    /**
     * 设置主键
     * @param string $colunm 主键名
     */
    public function set_primary_key($colunm) {
    	if (!empty($colunm)) {
    		$this->primary_key = $colunm;
    		
    		return true;
    	}
    	
    	return false;
    }
    //概率
    public function get_rand($proArr) {
    	$result = '';
    
    	//概率数组的总概率精度
    	$proSum = array_sum($proArr);
    
    	//概率数组循环
    	foreach ($proArr as $key => $proCur) {
    		$randNum = mt_rand(1, $proSum);
    		if ($randNum <= $proCur) {
    			$result = $key;
    			break;
    		} else {
    			$proSum -= $proCur;
    		}
    	}
    	unset($proArr);
    
    	return $result;
    }
    
}
