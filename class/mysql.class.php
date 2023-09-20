<?php

/****************************************
MySQL class by 3ventic
Email: 3ventic@gmail.com
Website: 3ventic.eu

This class uses MySQLi and prepared
statements to allow the user to run
queries with user-input without worrying
about it. 
****************************************/

class mysql {
    private $handle,
            $result,
            $data = array(
                'insert_id' => NULL,
                'error' => array('query' => '', 'errno' => -1, 'error' => ''),
                'num_rows' => -1,
            ),
            $connection;
    /*
     * Params: host, user, pass, database, port, socket
     * NULL skips parameter
     */
    public function __construct(mysqli $handle)
    {
        $this->handle = $handle;
        if($this->handle->connect_errno)
        {
            $this->data['error'] = array('errno' => $this->handle->connect_errno, 'error' => $this->handle->connect_error);
            $this->connection = FALSE;
        }
        else
        {
            $this->connection = TRUE;
        }
    }
    public function conStatus()
    {
        return $this->connection;
    }
    public function __destruct()
    {
        if($this->handle->close()) return TRUE;
        else return FALSE;
    }
    // Slightly modified from http://www.php.net/manual/en/language.oop5.overloading.php
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error('Undefined property via __get(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);
        return NULL;
    }
    public function __set($name, $value)
    {
        return;
    }
    /*
     * Params:
     * query - query itself, replace variables with ?
     * types - i for integer, s for string, d for double
     * ... - variables that replace the question marks in query, in correct order
     * Returns: TRUE, results or FALSE
     */
    public function query($query, $vartypes = NULL) {
        $this->resetQueryData();
        $type = $this->getQueryType($query);
        $this->data['error']['query'] = $query;
        
        $vars = func_get_args();
        
        /*// Debug
        echo "<pre><code>";
        var_dump($vars);
        echo "</code></pre>";
        //*/
        array_shift($vars);
        array_shift($vars);
        $args = array();
        $stmt = $this->handle->prepare($query);
        if(!$stmt)
        {
            return $this->setQueryErrors();
        }
        foreach($vars as $k => &$var)
        {
            $args[$k] = &$var;
        }
        $variables = array_merge(((array)$vartypes), $args);
        if($vartypes !== NULL) call_user_func_array(array($stmt, "bind_param"), $variables);
        if(!$stmt->execute())
        {
            return $this->setQueryErrors($stmt);
        }
        
        if($type == 'select')
        {
            $this->result = $stmt->get_result();
            if($this->result === FALSE)
            {
                $stmt->close();
                return $this->setQueryErrors($stmt);
            }
            else
            {
                $this->data['num_rows'] = $this->result->num_rows;
                if($this->result->num_rows == 0)
                {
                    return TRUE;
                }
                $resultset = array();
                while($row = $this->result->fetch_array())
                {
                    $resultset[] = $row;
                }
                $stmt->close();
                return $resultset;
            }
        }
        else
        {
            if($type == 'insert' || $type == 'update' || $type == 'delete') $this->data['num_rows'] = $stmt->affected_rows;
            if($type == 'insert') $this->data['insert_id'] = $stmt->insert_id;
            $stmt->close();
            return TRUE;
        }
    }
    
    // Private functions used by query
    private function setQueryErrors($stmt = NULL)
    {
        if($stmt === NULL)
        {
            $this->data['error']['errno'] = $this->handle->errno;
            $this->data['error']['error'] = $this->handle->error;
        }
        else
        {
            $this->data['error']['errno'] = $stmt->errno;
            $this->data['error']['error'] = $stmt->error;
        }
        return FALSE;
    }
    private function resetQueryData()
    {
        $this->data['insert_id'] = NULL;
        $this->data['error'] = array('errno' => -1, 'error' => '');
        $this->data['num_rows'] = -1;
    }
    private function getQueryType($query)
    {
        if(stripos($query, 'select') === 0) return "select";
        if(stripos($query, 'insert') === 0) return "insert";
        if(stripos($query, 'update') === 0) return "update";
        if(stripos($query, 'alter') === 0) return "alter";
        if(stripos($query, 'delete') === 0) return "delete";
        return NULL;
    }
}