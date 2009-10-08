<?

class UsersAdminHandler{
	var $location;

	function getGroupTitle($groupID){
		$groups = array('5'=>'Админ','6'=>'Юзер');
		return isset($groups[$groupID]) ? $groups[$groupID] : 'гость';
	}

	function editUser(){
		$ret='
		&raquo; <a href="/admin/?cmd=users">Список пользователей</a><br/><br/>
		<table class="list left-align" cellspacing="0" cellpadding="0">';
		$user = new pmUser(_get('userID'));
		foreach($user->itemData as $itemVar=>$itemVal){
			$ret.='<tr'.($i++%2==0?" class=\"even\"":'').'>
				<td  style="width:130px;"><b>'.$itemVar.'</b></td>
				<td>'.$itemVal.'</td>
			</tr>';
		}
		$ret.='</table>';
		return $ret;
	}

	function getList(){
		$users = new pmUsers(array('isUserGroup'=>'0'));
		$n = $users->itemsCount; // Количество зарегиных пользователей
		
		/**
		 * Следущий код формирует линейку с номерами страниц.
		 * Пока, чтобы не искать, есть ли такая функция, сделую вывод вручную
		 */
		$ret='';

	    $ret='';
		$pageCount = ceil($n / $users->perPage);
		$ret.='<div style="padding:3px; margin-bottom:5px; background-color:#FFFFFF;">';
		$ret.='Зарегистрировано: '.$n.'. ';
		$ret.='Страницы: ';
		for ($i = 1; $i <= $pageCount; $i++ ) {
		    if ($users->page == $i) {
              $ret.=' '.$i.' ';
		    } else {
		      $ret.=' <a href="?cmd=users&page='.$i.'">'.$i.'</a> ';
		    }
		}
		$ret.='</div>';
		
		$i = 0;
		$ret.='<table class="list" cellspacing="0" cellpadding="0">
			<tr>
				<th>ID</th>
				<th>Группа</th>
				<th>Логин</th>
				<th>Имя</th>
				<th>Фамилия</th>
				<th>Отчество</th>
				<th>Мыло</th>
				<th>Карта</th>
				<th>Телефон</th>
				<th>Регион</th>
				<th>Город</th>
				<th>Адрес</th>
				<th>Авто</th>
				<th>-</th>
			</tr>
		';
	
		foreach($users->items as $itemID=>$item){
			$ret.= $item->getItemData('isUserGroup')=='0' ? '
				<tr'.($i++%2==0?" class=\"even\"":'').'>
					<td>'.$itemID.'</td>
					<td>'.$this->getGroupTitle($item->getItemData('groupID')).'</td>
					<td><a href="/admin/?cmd=users&amp;act=edit&amp;userID='.$itemID.'">'.$item->getItemData('Login').'</a></td>
					<td>'.$item->getItemData('FirstName').'</td>
					<td>'.$item->getItemData('LastName').'</td>
					<td>'.$item->getItemData('SurName').'</td>
					<td><a href="maito:'.$item->getItemData('Email').'">'.$item->getItemData('Email').'</a></td>
					<td>'.$item->getItemData('cardID').'</td>
					<td>'.$item->getItemData('phone').'</td>
					<td>'.$item->getItemData('region').'</td>
					<td>'.$item->getItemData('city').'</td>
					<td>'.$item->getItemData('address').'</td>
					<td>Auto ['.$item->getItemData('carID').']</td>
					<td><a title="Удалить пользователя &laquo;'.$item->getItemData('Login').'&raquo;" href="/admin/?cmd=users&amp;act=del&userID='.$itemID.'"><img src="/images/icon/del.gif" width="13" height="13" alt="delete"/></a></td>
				</tr>
			' : '';
		}
		$ret.='</table>';
		return $ret;
	}

	function delUser(){
		$userID = _get('userID');
		$sure = _get('sure');
		if($sure == 'yes'){
			$users = new pmUsers();
			$users->deleteItem($userID);
			$this->location = '/admin/?cmd=users';
		}else{
			return 'Удалить пользователя с userID: '.$userID.'?<br/><a href="/admin/?cmd=users&amp;act=del&amp;userID='.$userID.'&amp;sure=yes">Да</a>&nbsp;&bull;&nbsp;<a href="/admin/?cmd=users">Нет</a>';
		}
	}
	
	function getContent(){
		switch(_get('act')){
			case 'edit' : return $this->editUser(); break;
			case 'del' : return $this->delUser(); break;
			default : return $this->getList();
		}

		return "xzzxz";
	}
}

class pmUsers extends customTableObject {
    var $perPage, $page;
    
	function pmUsers() {
		// Значения следующих двух переменных не определены, поэтому они закоментированы и переопределены
		/*
		$this->initData = $initData;
		$this->sqlData = $sqlData;
		*/
		
		$this->perPage = 50;
		$page = _get('page');
		$page = $page ? $page : 1;
		$this->page = $page;
		
		// свойства класса customTableObject
		$this->initData = array();
		$this->sqlData = array('limit_from' => ($page-1)*$this->perPage,
		                       'limit_count' => $this->perPage
		                       );
		$this->objectData = array('table'=>'pm_users',
		                          'item_class'=>'pmUser',
		                          'select_field'=>'userID',
		                          'key'=>'userID',
		                          'edit_fields'=>array('userID',
		                                               'groupID',
		                                               'isUserGroup',
		                                               'Login',
		                                               'Password',
		                                               'FirstName',
		                                               'LastName',
		                                               'SurName',
		                                               'Email',
		                                               'cardID',
		                                               'BirthDate',
		                                               'LockDate',
		                                               'SessionTimeout',
		                                               'MustChangePsw',
		                                               'NextPswChangeDate',
		                                               'DiskQuota',
		                                               'uDeleted',
		                                               'LoginDate',
		                                               'sex',
		                                               'phone',
		                                               'region',
		                                               'city',
		                                               'address',
		                                               'carID',
		                                               'carType',
		                                               'subscribe'
		                                              )
		                          );
		$this->initItems();
	}

	function asHTML(){
#		new template('Users/list',$this);

	}
}

class pmUser extends customTableItem{
	function pmUser($id=0){
		$this->objectData = array('table'=>'pm_users','item_class'=>'pmUser','key'=>'userID','edit_fields'=>array('userID','groupID','isUserGroup','Login','Password','FirstName','LastName','SurName','Email','cardID','BirthDate','LockDate','SessionTimeout','MustChangePsw','NextPswChangeDate','DiskQuota','uDeleted','LoginDate','sex','phone','region','city','address','carID','carType','subscribe'));
		$this->init($id);
		$this->title = $this->itemData['FirstName'];
	}

	function asHTML(){
#		new template('Users/item',$this);

	}
}

?>