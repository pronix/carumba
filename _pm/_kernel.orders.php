<?

require('sd.class.Order.php');
require('sd.class.vinorder.php');
require('sd.class.vinordergem.php');

function processOrdersCommand($cmd)
{ 
    global $modulesMgr, $structureMgr, $authenticationMgr, $permissionsMgr, $cacheMgr, $templatesMgr;
    
	$tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/top.html');
	
    $content = '';
	
	$cmd = (!_get('cmd') ? _post('cmd') : _get('cmd') );
	$act = (!_get('act') ? _post('act') : _get('act') );

    switch ($cmd)
    {
		case 'vinorders':
        {   
			$tpl = str_replace('%orders%','<a href="/carorders?cmd=orders&status=1">Заказы общие</a>',$tpl);
			$tpl = str_replace('%vinorders%','Заказ на иномарки (VIN)',$tpl);
			$tpl = str_replace('%vinordersgem%','<a href="/carorders?cmd=vinordersgem&status=1">Заказ на иномарки Германии (VIN)</a>',$tpl);

			$handler = new VinOrdersHandler();
			$status = _get('status');
			if(!$status) $status = 1;
			$page = _get('page');
			$orderID = _get('orderID');
			

			if(!$page)
				$page = 1;

            switch($act) {
				case 'refresh': {
					$handler->updateOrders();
					break;
				}
				case 'setDeliveryDate': {
					$handler->setDeliveryDate();
					break;
				}
				case 'setPrePayment': {
					$handler->setPrePayment();
					break;
				}
				case 'recalc': {
					$handler->recalcOrders();
					break;
				}
				case 'addPart': {
					$handler->addPart();
					break;
				}
				case 'sendPart': {
					$handler->sendPart();
					break;
				}
				case 'setMessage': {
					$handler->setComment();
					break;
				}
				case 'sendReply': {
					$handler->sendReply();
					break;
				}
				case 'viewReplies': {
					$tpl = $handler->viewReplies($orderID);
					break;
				}
				case 'anket': {
					$tpl = $handler->getAnket($orderID);
					break;
				}
				case 'order': {
					$tpl = $handler->getLookOrder($orderID);
					break;
				}
				case 'data': {
					$tpl = $handler->getDataHistory($orderID);
					break;
				}
				case 'delivery': {
					$tpl = $handler->getDeliveryDate($orderID);
					break;
				}
				case 'send': {
					$tpl = $handler->getSend($orderID);
					break;
				}
				case 'prepayment': {
					$tpl = $handler->getPrepayment($orderID);
					break;
				}
				default: {
					$content .= $handler->getContent($status, $page);
					$tpl = str_replace('%content%', $content, $tpl);
					break;
				}
			}
			break;
        }
		case 'vinordersgem':
        {   
			$tpl = str_replace('%orders%','<a href="/carorders?cmd=orders&status=1">Заказы общие</a>',$tpl);
			$tpl = str_replace('%vinorders%','<a href="/carorders?cmd=vinorders&status=1">Заказ на иномарки (VIN)</a>',$tpl);
			$tpl = str_replace('%vinordersgem%','Заказ на иномарки Германии (VIN)',$tpl);

			$handler = new VinOrdersGemHandler();
			$status = _get('status');
			if(!$status) $status = 1;
			$page = _get('page');
			$orderID = _get('orderID');
			

			if(!$page)
				$page = 1;

            switch($act) {
				case 'refresh': {
					$handler->updateOrders();
					break;
				}
				case 'setDeliveryDate': {
					$handler->setDeliveryDate();
					break;
				}
				case 'setPrePayment': {
					$handler->setPrePayment();
					break;
				}
				case 'recalc': {
					$handler->recalcOrders();
					break;
				}
				case 'addPart': {
					$handler->addPart();
					break;
				}
				case 'sendPart': {
					$handler->sendPart();
					break;
				}
				case 'setMessage': {
					$handler->setComment();
					break;
				}
				case 'sendReply': {
					$handler->sendReply();
					break;
				}
				case 'viewReplies': {
					$tpl = $handler->viewReplies($orderID);
					break;
				}
				case 'anket': {
					$tpl = $handler->getAnket($orderID);
					break;
				}
				case 'order': {
					$tpl = $handler->getLookOrder($orderID);
					break;
				}
				case 'data': {
					$tpl = $handler->getDataHistory($orderID);
					break;
				}
				case 'delivery': {
					$tpl = $handler->getDeliveryDate($orderID);
					break;
				}
				case 'send': {
					$tpl = $handler->getSend($orderID);
					break;
				}
				case 'prepayment': {
					$tpl = $handler->getPrepayment($orderID);
					break;
				}
				default: {
					$content .= $handler->getContent($status, $page);
					$tpl = str_replace('%content%', $content, $tpl);
					break;
				}
			}
			break;
        }
        default:
        {      
			$tpl = str_replace('%orders%','Заказы общие',$tpl);
			$tpl = str_replace('%vinorders%','<a href="/carorders?cmd=vinorders&status=1">Заказ на иномарки (VIN)</a>',$tpl);
			$tpl = str_replace('%vinordersgem%','<a href="/carorders?cmd=vinordersgem&status=1">Заказ на иномарки Германии (VIN)</a>',$tpl);

			$handler = new OrdersHandler();
			$status = _get('status');
			if(!$status) $status = 1;
			$page = _get('page');
			$orderID = _get('orderID');

			if(!$page)
				$page = 1;

		    //print 'act='.$act;
            switch($act) {
				case 'refresh': {
                    //print '<br>in';
					$handler->updateOrders();                            
					break;
				}
				case 'setDeliveryDate': {
					$handler->setDeliveryDate();
					break;
				}
				case 'setPrePayment': {
					$handler->setPrePayment();
					break;
				}
				case 'recalc': {
					$handler->recalcOrders();
					break;
				}
				case 'addPart': {
					$handler->addPart();
					break;
				}
				case 'setMessage': {
					$handler->setComment();
					break;
				}
				case 'sendReply': {
					$handler->sendReply();
					break;
				}
				case 'viewReplies': {
					$tpl = $handler->viewReplies($orderID);
					break;
				}
				case 'anket': {
					$tpl = $handler->getAnket($orderID);
					break;
				}
				case 'order': {
					$tpl = $handler->getLookOrder($orderID);
					break;
				}
				case 'data': {
					$tpl = $handler->getDataHistory($orderID);
					break;
				}
				case 'delivery': {
					$tpl = $handler->getDeliveryDate($orderID);
					break;
				}
				case 'send': {
					$tpl = $handler->getSend($orderID);
					break;
				}
				case 'prepayment': {
					$tpl = $handler->getPrepayment($orderID);
					break;
				}
				default: {
				    //print 'status='.$status.' page='.$page;
					$content .= $handler->getContent($status, $page);
					$tpl = str_replace('%content%', $content, $tpl);
					break;
				}
			}
			break;
        }

    }
    return $tpl; 
}?>  