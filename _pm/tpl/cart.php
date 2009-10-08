<?


?>

<?if(!$object->getItemData('isClubMember')){?>
<div class="podbor">

<table cellspacing="0" cellpadding="0" class="both">
  <tr>
    <td class="leftupbask">
			<!--<object type="application/x-shockwave-flash" data="http://www.carumba.ru/images/carumbych.swf" width="65" height="90">
			<param name="movie" value="http://www.carumba.ru/images/carumbych.swf" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
	</object>--><img src="/images/minime_s.gif" alt="" width="75" height="110" border="0" />
	
	</td>
    <td class="midupbask"><a href="/main/club"><img src="/images/club.gif" width="175" height="98"  alt="Автоклуб КАРУМБА (скидки, бонусы)" /></a></td>
    <td class="rightupbask"> <p><strong>Внимание</strong>:</p>  
	<p>Для вашего удобства предлагаем Вам:</p>
<?if($object->getItemData('userID')==1){?>
<a href="/registration"><img src="/images/arr_gray2.gif" width="7" height="9"  alt="" />Зарегистрироваться</a>
<br />
<?}?>
<?if($object->getItemData('userID')>1 && !$object->getItemData('isClubMember')){?>
	<a href="/main/club"><img src="/images/arr_gray2.gif" width="7" height="9"  alt="" />Стать членом клуба</a>
<?}?>

      </td>
  </tr>
</table>
</div>

<?}?>
				

<?
if(count($object->goods)>0){?>
	<form action="/" method="post">
	<div>
        <input type="hidden" name="module" value="Cart" />
        <input type="hidden" name="cmd" value="recalc" />
	</div>
<div class="basket">
<table cellspacing="1" cellpadding="0" class="tab">
  <tr>
    <td class="up ac wide">Товары в корзине</td>
<?if($object->getItemData('isClubMember')){?>
    <td class="up actprice">Цена по карте</td>
<?}else{?>
    <td class="up ac">Цена по карте</td>
    <td class="up actprice">Ваша цена</td>
<?}?>
    <td class="up ac">Цена</td>
    <td class="up center ac">Кол-во</td>
	<td class="up center ac">Удалить</td>
  </tr>

<?
	foreach($object->goods as $goodID=>$good){?>
	<tr>
		<td>
		<div class="ditem">
			<div class="thumb">
			<?if($good->getItemData('smallPicture')){?>
				<a href="<?=$good->getItemData('good_link')?>"><img src="<?=$good->getItemData('smallPicture')?>" width="70" height="70"  alt="<?=$good->getItemData('good_name')?> (Подробнее)" /></a>
			<?}?>
			</div>
			<div class="descr">
				<div class="in">
					<a href="<?=$good->getItemData('good_link')?>"><strong><?=$good->getItemData('good_name')?></strong></a><br />
					<?if($good->getItemData('accPlantName')!='неизвестный'){?>
						<strong>Производитель: </strong><?=$good->getItemData('accPlantName')?><br />
					<?}?>
					<?if($good->getItemData('ptID')!=8){
						foreach($good->getItemData('props') as $propID=>$prop){
						if($prop[3]){?>
						<strong><?=$prop[1]?>:</strong> <?=$prop[3]?> <?=$prop[2]?><br/>
					<?	}
						}
					}else{
						print $good->getItemData('MetaDesc');
					}?>
					<?if($good->getItemData('ptID') == 2 || $good->getItemData('ptID') == 3 || $good->getItemData('ptID') == 4){?>
						<strong>Спецпредложение (<a href="/main/club">по карте</a>):</strong> - <span class="t_bonus"><?=$good->getItemData('ptPercent')?>%</span><br />
					<?}?>
					<?if($good->getItemData('ptID') == 5 || $good->getItemData('ptID') == 6 || $good->getItemData('ptID') == 7){?>
						<strong>Распродажа :</strong> - <span class="t_sale"><?=$good->getItemData('ptPercent')?>%</span><br />
					<?}?>
				</div>
			</div>
		</div>
		<?

#		print '<pre>';
#		print_r($good);
#		print '</pre>';
		?>
		</td>
		<?if($object->getItemData('isClubMember')){?>
			<td class="ac"><span class="<?=$good->getItemData('priceClass')?>"><?=$good->getItemData('card_price')?></span></td>
		<?}else{?>
			<td><span class="<?=$good->getItemData('priceClass')?>"><?=$good->getItemData('card_price')?></span></td>
			<td class="ac"><span <?= ($good->getItemData('priceClass') == "t_salepr" ?'class="'.$good->getItemData('priceClass').'"' : '') ?>><?=$good->getItemData('cur_price')?></span></td>
		<?}?>
			<td><span class="t_old"><?=$good->getItemData('price')?></span></td>

			<td class="center">
			 <?=($good->getItemData('ptID')==8?'<input name="gc'.$good->getItemData('accID').'" type="hidden" class="input02" value="'.$good->getItemData('good_count').'" />':'')?>
				<input name="gc<?=$good->getItemData('accID')?>" type="text" class="input02<?=($good->getItemData('ptID')==8?' disabled':'')?>" value="<?=$good->getItemData('good_count')?>" <?=($good->getItemData('ptID')==8?' disabled="disabled"':'')?>/>
			</td>
			<td class="center"><input type="checkbox" name="del<?=$good->getItemData('accID')?>" value="<?=$good->getItemData('goodID')?>" /></td>
          </tr>
<?}?>
		<tr>
		    <td class="ac"><strong>Сумма:</strong></td>
		<?if($object->getItemData('isClubMember')){?>
		    <td class="actprice"><strong><?=$object->getItemData('cardsum')?></strong></td>
		<?}else{?>
		    <td class="ac"><strong><?=$object->getItemData('cardsum')?></strong></td>
		    <td class="actprice"><?=$object->getItemData('cursum')?></td>
		<?}?>
    		<td class="ac"><span class="t_old"><?=$object->getItemData('sum')?></span></td>
    		<td class="ac center"><?=$object->getItemData('total')?></td>
		    <td class="ac center"><input type="image" src="/images/recalc.gif" class="ok_butt" alt="Пересчитать"  /></td>
		</tr>
      </table>
	  </div>
      </form>
<?}else if ($_GET['orderID']){?>
<div class="podbor">
                               Ваш заказ успешно обработан и ему присвоен номер <strong><?=$_GET['orderID']?>.</strong>
</div>
<?}else{?>
<div class="podbor">
                                Ваша корзина пуста.
</div>
<?}?>
<?=$object->getItemData('orderForm')?>

<?=$object->getItemData('blockdost')?>
