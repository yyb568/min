<?php


/**
 * 轻量级 SlasticSearch PHP 操作客户端
 * @author Cyning
 * 2016年09月09日10:20:55
 */
class ElasticSearchClient
{
    private $_ch;
    
    private $_index;
    private $_type;
    private $_server, $_port;
    /**
     * Create a new ElasticSearchClient
     * 
     * @param string $index  The index to be used
     * @param string $type   The type to be used
     * @param string $server The host to connect to
     * @param int    $port   The server port to connect to
     * 
     * @return void
     */
    public function __construct($index = '', $type = '', $server = 'localhost', $port = '9200')
    {
        $this->_ch = curl_init();
        $this->setIndex($index)->setType($type);
        $this->_server  = $server;
        $this->_port    = $port;
    }
    /**
     * Get a document
     * 
     * @param string $id The id of the document to retrieve
     * 
     * @return void
     */
    public function get($id)
    {
        $url = $this->buildURL(
            array(
            $this->_type
            , $id
            )
        );
        return $this->_call($url, 'GET');
    }
    /**
     * Set a document into the index
     * 
     * @param mixed  $document Array or string to be indexed
     * @param string $id       The id of the document to retrieve
     * 
     * @return mixed The indexed document, if found
     */
    public function set($document, $id = null)
    {
        if($id)
        {
            $method = 'PUT';
        }
        else
        {
            $method = 'POST';
        }
        $url = $this->buildURL(
            array(
                $this->_type
                , $id
            )
        );
        return $this->_call($url, $method, $document);
    }
    /**
     * Search across the index
     * 
     * @param string $query   The string to search for
     * @param array  $options Modifiers for the search operation
     * 
     * @return void
     */
    public function search($query, $options = array())
    {
        // For the moment, just string queries
        $url = $this->buildUrl(
            array(
                $this->_type
                , "_search?q=" . urlencode($query)
            )
        );
        return $this->_call($url, 'GET', $options);
    }
    /**
     * Build the URL path for a request, given the type, id, etc.
     * 
     * @param array $path   Elements of the path, to be joined
     * @param array $params Any param to be attached to the URL, after ?
     * 
     * @return string The path
     */
    public function buildURL($path = array(), $params = array())
    {
        $url = $this->_index;
        if ($path 
            && is_array($path) 
            && sizeof($path) > 0
        )
        {
            // Self-defense: Get rid of null/empty items
            $url .= "/" . implode("/", array_filter($path));
        }
        if (count($params) > 0)
        {
            $url .= "?" . http_build_query($params, '', '&');
        }
        return $url;
    }
    /**
     * Perform a cURL request agains the ElasticSearch server
     * 
     * @param string $path   The path for the request
     * @param string $method GET, POST, PUT, DELETE
     * @param array  $params Fields to be sent within the request
     * 
     * @return mixed The result of the request or _source array if present
     */
    
    private function _call($path, $method = 'GET', $params = array())
    {
        $url = 'http://' . $this->_server .':'. $this->_port .'/'. $path;
        curl_setopt($this->_ch, CURLOPT_URL, $url);
        curl_setopt($this->_ch, CURLOPT_PORT, $this->port);
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if (is_array($params) 
            && sizeof($params) > 0
        )
        {
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        else
        {
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, null);
        }
        $res = curl_exec($this->_ch);
        if ($res !== false)
        {
            $data = json_decode($res, true);
        }
        else
        {
            // Throw exception
            return 0;
        }
        if(isset($data['_source']))
        {
            return $data['_source'];
        }
        if(isset($data['hits']))
        {
            return $data['hits'];
        }
        return $data;
    }
    /**
     * Set the current index 
     *
     * @param string $index Index to use
     * 
     * @return void
     */
    public function setIndex($index)
    {
        $this->_index = $index;
        return $this;
    }
    /**
     * Set the type to use
     *
     * @param string $type Type
     * 
     * @return void
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }
}