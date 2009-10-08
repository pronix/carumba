<?

class CardsAdminHandler{

	function addCards($count){
		$cards = new Cards();
		return "<p>Добавлено ".$cards->addItems($count)." карт</p>";
	}

	function handleRequest($request, $param = 0){
		return $this->$request($param);
	}

	function setCardToUser(){
		$ret = '';
		if(count($_POST)>0){
			$cardID = $_POST['cardID'] or trigger_error('Undefined userID',PM_FATAL);
			print '<pre>';
			$card = new Card($cardID);
			print_r($_POST);
#			$res
			print '</pre>';
#			new template('dump',$_POST);
			$ret .= 'no yet';
		}else{
			$action = "/admin/?cmd=cards&amp;act=setCardToUser&amp;cardID=".$item->getItemData('cardID');
			$ret.='
			<style type="text/css">
				.h16str	{margin-top:10px;}
				.h16str, .h16str *	{line-height:16px;vertical-align:middle;}
			</style>
			<form action="'.$action.'" method="post">';
			$pmUsers = new pmUsers(array('isUserGroup'=>'0'));
			$ret.='<select name="userID">';
			foreach($pmUsers->items as $itemID=>$item){
				$cardStr = $item->getItemData('cardID')=='0' 
					? '' 
					: ' #cardID: '.$item->getItemData('cardID');
				$ret.='<option value="'.$itemID.'">'.$item->getItemData('FirstName').' '.$item->getItemData('LastName').' ['.$item->getItemData('Login').' - '.$itemID.']'.$cardStr.'</option>';
			}
			$ret.='</select>';
			$ret.='<div class="h16str"><b>№ карты:</b> <input type="text" name="cardID" value="'._get('cardID').'"/></div>';
			$ret.='<div class="h16str">
				<input type="submit" name="submit" value="Назначить"/>
			</div>';
			$ret.='<form>';
		}
		return $ret;
	}

	function getList(){
		$ret = '';
		$cards = new Cards();

		$ret.='
		<style type="text/css">
		table.list	{width:100%;}
		table.list th	{padding:3px;background:silver;color:#fff;font-weight:bold;font-size:12px;}
		table.list td	{padding:3px;text-align:center;}
		table.list tr.even td	{background:#eee;}
		</style>
		';

		$ret.='<table class="list" cellspacing="0" cellpadding="0"><tr>
		   <th>cardID</th>
		   <th>userID</th>
		   <th>Дата создания</th>
		   <th>Дата регистрации</th>
		   <th>Используется</th>
		    </tr>';
		$i=0;
		foreach($cards->items as $itemId=>$item){
			$userID = $item->getItemData('userID');
			$userName = "&mdash;";
			if($userID){
				$user = new pmUser($userID);
				$userName = '<a href="/admin/?cmd=users&amp;act=edit&amp;userID='.$userID.'">'.$user->getItemData('FirstName')." ".$user->getItemData('LastName').'</a>';
				#$userID!='0' ? $userID : '&mdash;'
			}
			
			$ret.='<tr'.($i++%2==0?" class=\"even\"":'').'>
		   <td>'.$item->getItemData('cardID').'</td>
		   <td>'.$userName.'</td>
		   <td>'.$item->getItemData('createDate').'</td>
		   <td>'.($item->getItemData('registerDate')!='0000-00-00 00:00:00' ? $item->getItemData('registerDate') : '&mdash;').'</td>
		   <td>'.($item->getItemData('inUse')!='' ? 'используется' : '<a href="/admin/?cmd=cards&amp;act=setCardToUser&amp;cardID='.$item->getItemData('cardID').'">назначить</a>').'</td>
		    </tr>';
		}

		$ret.='</table>
		<br/><br/>
		<a href="/admin/?cmd=cards&amp;act=addCards">Добавить пачку</a><br/>
		';
		return $ret;
	}
}

class Cards extends customTableObject{
	function Cards($initData=array(),$sqlData=array()){
		$this->initData = $initData;
		$this->sqlData = $sqlData;
		$this->objectData = array('table'=>'pm_cards','item_class'=>'Card','key'=>'cardID','select_title'=>'CardId');
		$this->initItems();
	}

	function addItems($count = 200){
		$query  = "INSERT INTO pm_cards VALUES(0, 0, NOW(), 0, 0)";
		$inc = 0;
		$count = 199;
		for($i=1;$i<=$count;$i++){
			$inc += mysql_query($query);
		}
		return $inc;
	}


	function asHTML($style=''){
#		new template(isset($style)&&$style!='' ? $style : 'cards/list',$this);
	}
}

class Card extends customTableItem{
	function Card($id=0){
		$this->objectData = array('table'=>'pm_cards','item_class'=>'Card','key'=>'cardID','edit_fields'=>array('userID','createDate','registerDate','inUse'));
		$this->init($id);
		$this->title = $this->itemData['CardID'];
	}

	function assignUserID($userID){
		if(sizeof($this->itemData) && isset($this->itemData['cardID'])){
			$this->itemData['userID'] = $userID;
			$this->itemData['registerDate'] = 'NOW()';
			return $this->save();
		}else{
			return false;
		}
	}

	function asHTML(){
#		new template('Cards/book',$this);

	}
}

?>