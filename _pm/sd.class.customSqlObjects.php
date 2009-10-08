<?
class objectDataField{
	var $itemData;
	var $objectData = array('style'=>'admin/objects/fields');
	
	function objectDataField($itemData){
		$this->itemData = $itemData;
	}

	function assign($itemData){
		$this->itemData = $itemData;
	}
}

class objectData{
	var $itemData = array();
	var $objectId;
	var $objectDataId;
	var $xz;

	function objectData($className){
#		$cond = is_numeric($className) ? " pw_objects_data.pw_object_data_id = $className " : "pw_objects.class_name='$className'";
		$this->xz=$className;
		$res = is_numeric($className) ? mysql_query("SELECT * FROM pw_objects_data WHERE pw_object_data_id='$className'")
				: mysql_query("SELECT * FROM pw_objects LEFT JOIN pw_objects_data USING(pw_object_data_id) WHERE pw_objects.class_name='$className'");
		if(mysql_num_rows($res)>0){
			$this->itemData = mysql_fetch_assoc($res);
			$this->itemData['fields'] = array();
			$this->initFields();
		}
	}

	function initFields(){
		$res = mysql_query("SELECT * FROM pw_objects_data_fields WHERE pw_object_data_id = {$this->itemData['pw_object_data_id']}");
		$items = array();
#		print 'xx'.$this->itemData['pw_object_data_id'].'-'.$this->xz.'<br/>';
		if(mysql_num_rows($res)>0){
			while($row = mysql_fetch_assoc($res)){
				$this->itemData['fields'][$row['field']] = new objectDataField($row);
				$this->itemData['edit_fields'][] = $row['field'];
			}
		}
	}
}

class customTableObject{
	var $initData,$sqlData,$items,$itemsCount,$objectData;

	function customTableObject($initData=array(),$sqlData=array()){
		$this->initData = $initData;
		$this->sqlData = $sqlData;
		$this->initObjectData();
		$this->initItems();
	}

	function asHTML($style=''){
		if(count($this->items)==0){
			new template('msg','No items in this category');
		}else{
			new template(isset($style)&&$style!='' ? $style : $this->objectData['style'].'/list',$this);
		}
	}

	function initObjectData(){
		$objectData = new objectData(get_class($this));
		if(count($objectData->itemData) > 0) $this->objectData = $objectData->itemData;
	}

	function initItems(){
		$itemClass = $this->objectData['item_class'];
		$cond = $this->getCond();
		$res = $this->getSqlResult($cond);
		$hashKey = isset($this->objectData['hash_key'])&&$this->objectData['hash_key']!='' ? $this->objectData['hash_key'] : $this->objectData['key'];
		while($row = mysql_fetch_assoc($res)){
			if($this->objectData['item_class']=='_dinamic'){
				$itemClass = $row['item_class'];
			}
			if(isset($this->objectData['item_id_field'])){
				$this->items[$row[$hashKey]] = new $itemClass($row[$this->objectData['item_id_field']]);
			}else{
				$this->items[$row[$hashKey]] = new $itemClass();
				$this->items[$row[$hashKey]]->assign($row);
			}
		}
	}

	function getCond(){
		$initData = $this->initData;
		$cond=array();
		foreach($this->initData as $var=>$val){
			switch($var){
				case 'price_from' :	if(is_numeric($val)) array_push($cond,"price > $val");break;
				case 'price_to' :	if(is_numeric($val)) array_push($cond,"price < $val");break;
				case 'date_from' :	array_push($cond,"date > '".preg_replace('/\:[^\d]/','-',$val)." 00:00:00'");break;
				case 'date_to' :	array_push($cond,"date < '".preg_replace('/\:[^\d]/','-',$val)." 00:00:00'");break;
				case 'sql_cond' :	array_push($cond,$val);break;
				default : array_push($cond,$this->_formatCond($var,$val));break;
			}
		}
		return $cond;
	}

	function itemsCount(){
		return $this->itemsCount;
	}

	function getInitDataFields(){
		return isset($this->sqlData['sql_fields']) && count($this->sqlData['sql_fields'])>0 ? join(',',$this->sqlData['sql_fields']) : '*';
	}

	function getSqlResult($cond){
		$sqlData = $this->sqlData;
		$sqlcond = count($cond)>0 ? 'WHERE '.join(' AND ',$cond) : '';
		$sqlTable = (isset($sqlData['table']) ? $sqlData['table'] : $this->objectData['table']);
		$sqlFields = $this->getInitDataFields();
		$sqlSelect= (isset($sqlData['select']) ? $sqlData['select'] : "SELECT $sqlFields FROM $sqlTable");
		$sqllimit = "LIMIT ".(isset($sqlData['limit_from']) ? $sqlData['limit_from'] : 0).",".(isset($sqlData['limit_count']) ? $sqlData['limit_count'] : 50);
		$sqlOrderDirect = isset($sqlData['order_direct']) ? $sqlData['order_direct'] : '';
		$sqlOrder = isset($sqlData['order']) ? " ORDER BY ".$sqlData['order']." ".$sqlOrderDirect : "";
		$countRow = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as cnt FROM $sqlTable $sqlcond"));
		$this->itemsCount = $countRow['cnt'];
#		print $this->itemsCount;
#		print '<h1>'."$sqlSelect $sqlcond $sqlOrder $sqllimit".'</h1>';
		return mysql_query("$sqlSelect $sqlcond $sqlOrder $sqllimit");
	}

	function _formatCond($var,$val){
		$var = addslashes($var);
		$val = addslashes($val);
		return is_array($val) ? "(".join(" OR ","`$val` = '$val'").")" : "`$var`='$val'";
	}

	function deleteItem($itemId){
		return mysql_query("DELETE FROM `{$this->objectData['table']}` WHERE `{$this->objectData['key']}`='$itemId'");
	}

	function asSelect($selId=0,$initData=array()){
		$initData['name'] = isset($initData['name']) ? $initData['name'] : $this->objectData['key'];
		$onChange = isset($initData['on_change']) ? ' onchange="'.$initData['on_change'].'"' : '';
		$ret='<select name="'.$initData['name'].'"'.$onChange.'>';
		$i=0;
		foreach($this->items as $itemId=>$item){
			$selected = $selId==$itemId ? ' selected="selected"' : '';
			if($i==0 && isset($initData['first_item'])){
				$ret .= '<option'.$selected.' value="'.$initData['first_item']['value'].'">'.$initData['first_item']['title'].'</option>';
				$i++;
			}
			$ret .= '<option'.$selected.' value="'.$itemId.'">'.$item->itemData[$this->objectData['select_title']].'</option>';
		}
		$ret .= '</select>';
		return $ret;
	}
}

class customTableItem{
	var $id,$itemData,$objectData,$title;

	function customTableItem($id=0){
		$this->initObjectData();
		$this->init($id);
#		if(isset($this->objectData['select_title'])) $this->title = $this->itemData[$this->objectData['select_title']];
	}

	function getItemData($var = ''){
		return isset($this->itemData[$var]) ? $this->itemData[$var] : false;
	}

	function _initTitle(){
		if(isset($this->objectData['select_title'])) $this->title = $this->itemData[$this->objectData['select_title']];
	}

	function asHTML(){
		new template($this->objectData['style'].'/item',$this);
	}

	function init($id=0,$initData=array()){
		$this->initData = $initData;
		$fields = $this->getInitDataFields();
		if($id){
			$this->id = $id;
			$this->itemData = mysql_fetch_assoc(mysql_query("SELECT * FROM `{$this->objectData['table']}` WHERE `{$this->objectData['key']}`='{$this->id}'"));
			$this->_initTitle();
		}
	}

	function initObjectData(){
		$objectData = new objectData(get_class($this));
		if(count($objectData->itemData) > 0) $this->objectData = $objectData->itemData;
	}

	function getInitDataFields(){
		return isset($this->initData['sql_fields'])&&count($this->initData['sql_fields'])>0 ? join(',',$this->initData['sql_fields']) : '*';
	}

	function assign($data){
		$this->itemData = $data;
		$this->id = isset($this->id)&&$this->id!=0 ? $this->id : $data[$this->objectData['key']];
#		if(isset($this->objectData['select_title'])) $this->title = $this->itemData[$this->objectData['select_title']];
		$this->_initTitle();
	}

	function insert(){
		$values = $this->getValuesArray();
		$setVals = join(',',$values);
		return mysql_query("INSERT INTO `{$this->objectData['table']}` SET $setVals") ? mysql_insert_id() : false;
	}

	function save(){
		$values = $this->getValuesArray();
		$setVals = join(',',$values);
		return mysql_query("UPDATE `{$this->objectData['table']}` SET $setVals WHERE `{$this->objectData['key']}`='{$this->id}'");
	}

	function getValuesArray(){
		$values = array();
		foreach($this->objectData['edit_fields'] as $field){
			if(preg_match('/date|Date/',$field) && $this->itemData[$field]=='NOW()'){
				$values[] = "`$field`=NOW()";
			}else{
				$values[] = "`$field`='{$this->itemData[$field]}'";
			}
		}
		return $values;
	}

}

?>