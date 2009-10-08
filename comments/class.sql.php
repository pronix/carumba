<?php
#-----------------------------------------------------
#---- updated and optimized by Alexey Reshetnikov ----
#---- v.2.1 24.07.2004 13:07:28                   ----
#-----------------------------------------------------

  class sql {

    var $db;
    var $data;
    var $result;
    var $connect;

    function sql($dbs,$dbu,$dbp,$dbn){
      $this->connect=@mysql_connect($dbs,$dbu,$dbp);
      $this->result=@mysql_select_db($dbn,$this->connect);
    }

    function destroy(){@mysql_close($this->connect);}

    function query($query){
      if($this->connect){
        @mysql_query("SET NAMES cp1251",$this->connect);
        $this->result=@mysql_query($query,$this->connect);
        return true;
      }
      return false;
    }

    function get($query){
      if($this->connect){
        @mysql_query("SET NAMES cp1251",$this->connect);
        if($query){$this->result=@mysql_query($query,$this->connect);}
        if($this->result){
          if($this->data=@mysql_fetch_row($this->result)){
            return $this->data[0];
          }
        }
        return false;
      }
      return false;
    }

    function fetch(){
      if($this->result){
        if($this->data=@mysql_fetch_object($this->result)){
          return true;
        }
      }
      return false;
    }

    function seek($position=0){
      if($this->result){
        @mysql_data_seek($this->result,$position);
      }
    }

    function count($query=""){
      if($query){$this->query($query);}
      if($this->result){
        return @mysql_num_rows($this->result);
      }
      return false;
    }

    function getid(){
      if($this->connect){
        return @mysql_insert_id($this->connect);
      }
      return false;
    }

    function is_table($table){
      global $dbn;
      $this->result=@mysql_list_tables($dbn);
      while($this->data=@mysql_fetch_row($this->result)){
        if($this->data[0]==$table){return true;}
      }
      return false;
    }
  }

?>