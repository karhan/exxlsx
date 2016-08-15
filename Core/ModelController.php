<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core{

/**
 * Description of ModelController
 *
 * @author itadmin
 */
    class ModelController {
    
        public $db; 

        static $instance;

        static function get_instance() {
            if(self::$instance instanceof self) {
                    return self::$instance;
            }
            return self::$instance = new self;
        }

        private function __construct() {  

            @$this->db = new mysqli(HOST,USER,PASSWORD,DB_NAME); 
            if($this->db->connect_error) { 
                throw new DbException("Connection error : ".$this->db->connect_errno."|".iconv("CP1251","UTF-8",$this->db->connect_error),'1');
            }

            $this->db->query("SET NAMES 'UTF8'");  
        }

        public function select(
                                $param, 
                                $table,
                                $where = array(),
                                $order = FALSE,
                                $napr = "ASC",
                                $limit = FALSE,
                                $operand = array('=') 

                                ) {

            $sql = "SELECT"; 

            $sql = rtrim($sql,',');

            $sql .= ' '.'FROM'.' '.$table;

            if(count($where) > 0) {

                    $ii = 0;
                    foreach($where as $key=>$val) {
                            if($ii == 0) {
                                    if($operand[$ii] == "IN") {
                                            $sql.= " WHERE ".strtolower($key)." ".$operand[$ii]."(".implode(',',$val).")";
                                    }
                                    else {
                                            $sql .= ' '.' WHERE '.strtolower($key).' '.$operand[$ii].' '."'".$this->db->real_escape_string($val)."'";
                                    }
                            }
                            if($ii > 0) {
                                    if($operand[$ii] == "IN") {
                                            $sql.= " AND ".strtolower($key)." ".$operand[$ii]."(".implode(',',$val).")";
                                    }
                                    else {
                                            $sql .= ' '.' AND '.strtolower($key).' '.$operand[$ii].' '."'".$this->db->real_escape_string($val)."'";
                                    }

                            }
                            $ii++;
                            if((count($operand) -1) < $ii) {
                                    $operand[$ii] = $operand[$ii-1];
                            }
                    }

            } 

            if($order) {
                    $sql .= ' ORDER BY '.$order." ".$napr.' ';
            }

            if($limit) {
                    $sql .= " LIMIT ".$limit;
            }  

            $result = $this->db->query($sql);

            if(!$result) { 
                throw new DbException('SQL error : '.$sql);
            } 

            if($result->num_rows == 0) {
                    return FALSE;
            }

            for($i = 0; $i < $result->num_rows; $i++) {
                    $row[] = $result->fetch_assoc();
            }

            return $row;				

        }

        public function query($sql) { 
            $result = $this->db->query($sql);
            if(!$result) { 
                throw new DbException('SQL error : '.$sql);
            } 

            if($result->num_rows == 0) {
                    return FALSE;
            }

            for($i = 0; $i < $result->num_rows; $i++) {
                    $row[] = $result->fetch_assoc();
            }

            return $row;				

        }

        public function insert($table, $data = array(),$values = arraY(),$id = FALSE) { 

            $sql = "INSERT INTO ".$table." (";

            $sql .= implode(",",$data).") ";

            $sql .= "VALUES (";

            foreach($values as $val) {
                $sql .= "'".$val."'".",";
            }

            $sql = rtrim($sql,',').")";

            $result = $this->db->query($sql);
            if(!$result) { 
                throw new DbException('SQL error : '.$sql);
            } 

            if($id) {
                return $this->db->insert_id;
            }

            return TRUE;
        }

        public function multyinsert($sql,$id = FALSE) {

            $result = $this->db->query($sql);
            if(!$result) { 
                throw new DbException('SQL error : '.$sql);
            }

            if($id) {
                return $this->db->insert_id;
            }

            return TRUE;
        }

        public function update($table,$data = array(),$values = array(),$where = array(),$us_if=FALSE) { 

            $data_res = array_combine($data,$values); 

            $sql = "UPDATE ".$table." SET ";

            if($us_if!=FALSE){
               $sql =$sql.$us_if."  ";
            }

            foreach($data_res as $key=>$val) {
                $sql .= $key."='".$val."',";
            }

            $sql = rtrim($sql,',');
            $sql=$sql.' WHERE ';	
            foreach($where as $k=>$v) {
                $sql .= $k."="."'".$v."' and ";
            }
            $sql = trim($sql);
            $sql = rtrim($sql,'and');
            $result = $this->db->query($sql);
            if(!$result) { 
                throw new DbException('SQL error : '.$sql);
            } 

            return TRUE;
        }

        public function delete($table,$where = array(),$operand = array('=')) { 

            $sql = "DELETE FROM ".$table." WHERE ";
            if(is_array($where)){
                $i = 0;
                foreach($where as $k=>$v) {
                    $sql .= $k.$operand[$i]."'".$v."' and ";
                    $i++; 
                    if((count($operand) -1) < $i) {
                        $operand[$i] = $operand[$i-1];
                    }
                } 
            }  
            $sql = trim($sql);            
            $sql=rtrim($sql,'and'); 
            $result = $this->db->query($sql);
            if(!$result) { 
                throw new DbException('SQL error : '.$sql);
            } 
            return TRUE;;
        }
}
}