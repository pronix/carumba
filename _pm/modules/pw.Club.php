<?

include_once('mod.Cart.php');

class ClubHandler{

	function ClubHandler(){
	
	}

	function getContent(){
		global $authenticationMgr;
		$userID = $authenticationMgr->getUserID();

		$ret = '';
		$isCardInCart = Cart::isItemInCart(GetCfg('Club.GoodID'));

		if(isset($_GET['act'])){
			$handler = new ClubGetCardHandler();
			$ret.=$handler->getContent();
		}else{
			$userData = array();
			if($userID!=1){
				$userData = $authenticationMgr->getUserData($userID,'');
				$userName = $userData['FirstName']." ".$userData['LastName'];

				$ret.='Здравствуйте, '.$userName.'<br/><br/>';

				if($isCardInCart || (isset($userData['cardID']) && $userData['cardID']!='0')){
					$ret.= $isCardInCart ? 'Ваша клубная карта будет доступна после оплаты<br/>'
						: 'Номер вашей карты: '.$userData['cardID'];
				}else{
					$ret.='

<table style="width:100%;margin:0 0 20px;" cellspacing="0" cellpadding="0">
	<tr>
		<td style="width:100px;">
		<div style="padding:20px 0 0 20px;"><img src="http://www.carumba.ru/images/minime.gif" border="0" />
<!-- <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="65" height="90">
 <param name="movie" value="/images/carumbych.swf"/>
 <param name="quality" value="high"/>
 <embed src="/images/carumbych.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="65" height="90"></embed>
</object> -->
</div>
		</td>
		<td style="vertical-align:middle;width:195px;">
		<div style="padding:10px;">
<a href="/main/club"><img src="/images/club.gif" alt="Автоклуб КАРУМБА (скидки, бонусы)" width="175" height="98" border="0" align="absmiddle"/></a>
		</div>

		</td>
		<td><br/><br/>
			<p>Для вступления в клуб Вам необходимо положить карту в корзину.<br/>Как карта появится в корзине Ваше цена станет "Цена по карте".<br/>Цена: 500 руб.<br/>
				<form action="/" method="post" style="margin: 0;">
                  <input type="image" src="/images/buy.gif" alt="Купить" />
                  <input type=hidden name=module value=Cart>
                  <input type=hidden name=function value=tocart>
                  <input type=hidden name=goodID value="'.GetCfg('Club.GoodID').'">
                  </form>
            </p>
		</td>
	</tr>
</table>
					';
				}
			
			}else{
				$ret.='Пожалуйста, <a href="/registration">зарегистрируйтесь</a>';
			}

			if(!(sizeof($userData) && $userData['cardID']!='0')){
				$ret.='<div style="margin:10px 0;">
<p>Цена карты до 1 января 2008 года <strong>всего 500 рублей</strong>! </p>
<strong>5 За! Для вступления в автоклуб &quot;Карумба&quot;:</strong>
<ol>
  <li>Скидки на весь ассортимент on-line каталога</li>
  <li> Уникальные низкие цены на широкий ассортимент товаров Спецпредложений</li>
  <li> Индивидуальный подход к каждому члену клуба</li>
  <li> Дополнительные удобства при заказе товаров</li>
  <li> Номера карт участвуют в розыгрыше призов</li>
</ol>
<p><em>Приобретая карту автоклуба вы экономите время при заказе товара!</em></p>
<p><strong>Для новых покупателей:</strong></p>
<ul>
  <li> для вступления в автоклуб вам сначала нужно пройти регистрацию</li>
</ul>
<p><strong>Для зарегистрированных пользователей:</strong></p>
<ul>
  <li> если вы уже за регистрированы то вам нужно просто положить карту в корзину</li>
  <li> номер карты будет присвоен Вам нашим оператором, после проверки анкеты</li>
</ul>
<em>Карта которую приобретете сейчас будет с вами долгие годы!</em>
				</div>';
			}
		}

		return $ret;
	}

}

class ClubGetCardHandler{
	function getContent(){
		global $authenticationMgr;
		$userID = $authenticationMgr->getUserID();
		$ret='';
		if($userID==1){
			$ret.='Пожалуйста, <a href="/registration">зарегистрируйтесь</a>';
		}else{
			$userData = $authenticationMgr->getUserData($userID,'');
			$userName = $userData['FirstName']." ".$userData['LastName'];
			if($userData['cardID']!='0'){
				$ret.= 'У Вас уже есть карта';
				$ret.='Номер Вашей карты: '.$card->itemData['cardID'];
			}else{
				$cards = new Cards(array('userID'=>'0'));
				$card = current($cards->items);
				if($card->assignUserID($userID) && $authenticationMgr->setUserData($userID,'cardID',$cardID)){
					$ret.='Номер Вашей карты: '.$card->itemData['cardID'];
				}else{
					$ret.='Ошибка присвоения карты';
				}
			}
		
		}
		return $ret;
	}
}

?>