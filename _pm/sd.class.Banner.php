<?php

class BannerAdminHandler{

	function getContent($banID = 0)
	{
		global $templatesMgr;
		

		$tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/banner.html");
		
		$banners = $this->getBannersList();
		if(count($banners)) {
			$bannerContent = "";
			foreach($banners as $banner) {
				$bannerContent .= "
					<tr>
						<td>".$banner['param']. "</td>
						<td>".$banner['text']."</td>
						<td>".$banner['link']."</td>
						<td>".$banner['url']."</td>
						<td>".$banner['show']."</td>
						<td>".$banner['click']."</td>
						<td>".$banner['isactive']."</td>
						<td><a href=\"/admin?cmd=banner&banID=".$banner['banID']."\">Редактировать</a></td>
						<td><a href=\"/admin?cmd=banner&act=delete&banID=".$banner['banID']."\">Удалить</a></td>
					</tr>
					";
				if($banID == $banner['banID']) {
					$tpl = str_replace("%banID%", $banner['banID'], $tpl);
					$tpl = str_replace("%param%", $banner['param'], $tpl);
					$tpl = str_replace("%url%", $banner['url'], $tpl);
					$tpl = str_replace("%link%", $banner['link'], $tpl);
					$tpl = str_replace("%text%", $banner['text'], $tpl);
					$tpl = str_replace("%isactive%", ($banner['isactive'] == 1 ? "checked" : ""), $tpl);
				}
			}
			$tpl = str_replace("%banners%", $bannerContent, $tpl);
		} else {
			$tpl = str_replace("%banners%", "", $tpl);
		}
		
		if(!$banID) {
			$tpl = str_replace("%banID%", "", $tpl);
			$tpl = str_replace("%param%", "", $tpl);
			$tpl = str_replace("%url%", "", $tpl);
			$tpl = str_replace("%link%", "", $tpl);
			$tpl = str_replace("%text%", "", $tpl);
			$tpl = str_replace("%isactive%", "", $tpl);
		}
		

		return $tpl;

	}
	
	function saveBanner() {
		$banID = _post("banID");
		$url = _post("url");
		$link = _post("link");
		$text = _post("text");
		$param = _post("param");
		$isactive = _post("isactive");
		
		$fParts = split("\.", $url);
		//echo '<hr><pre>'; print_r($fParts);echo '</pre><hr>';
		if(count($fParts ) == 1) {
			$type = $fParts[0];
		} else {
			$type = $fParts[1];
		}

		if(!$banID) {
			$query = "INSERT INTO pm_banners (`url`, `type`, `link`,`text` ,`param`, `isactive`) 
			VALUES ('".$url."', '".$type."', '".$link."', '".$text."', '".$param."', '".$isactive."')
			";
		} else {
			$query = "UPDATE pm_banners SET 
				`url` = '".$url."',
				`type` = '".$type."',
				`link` = '".$link."',
				`text` = '".$text."',
				`isactive` = '".$isactive."',
				`param` = '".$param."'
			WHERE banID = '".$banID."'";
		}
		//echo $query;
		if (!mysql_query($query))
		{
			trigger_error("Error while updating banner [$query] - " . mysql_error(), PM_FATAL);
		}
		header("Location: /admin?cmd=banner");
	}

	function deleteBanner($banID) {
		if(!$banID)
			trigger_error("No banner to delete [$banID] - " . mysql_error(), PM_FATAL);
		$query = "DELETE FROM pm_banners WHERE banID = '".$banID."'";
		if (!mysql_query($query))
		{
			trigger_error("Error while delete banner [$banID] - " . mysql_error(), PM_FATAL);
		}
		header("Location: /admin?cmd=banner");
	}

	function getBannersList() {
		$banners = Array();
		$query = "SELECT banID, `show`, click, url, link, text, `param`, isactive FROM pm_banners";
		$result = mysql_query($query);
		if(mysql_num_rows($result)) {
			while($banner = mysql_fetch_assoc($result)) {
				$banners[] = $banner;
			}
		}

		return $banners;
	}
}
?>
