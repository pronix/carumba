<?php

class VoteAdminHandler{

	function getContent($qID = 0)
	{
		global $templatesMgr;
		

		$tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/vote.html");
		
		$votes = $this->getVotesList();
		if(count($votes)) {
			$voteContent = "";
			foreach($votes as $vote) {
				$voteContent .= "
					<tr>
						<td>".$vote['qID']. "</td>
						<td>".$vote['Question']."</td>
						<td>".$vote['Ans1']."<br>".$vote['Ans2']."<br>".$vote['Ans2']."<br>".$vote['Ans3']."<br>".$vote['Ans4']."<br>".$vote['Ans5']."<br>".$vote['Ans6']."<br>".$vote['Ans7']."<br>".$vote['Ans8']."<br>".$vote['Ans9']."<br>".$vote['Ans10']."</td>
						<td>&nbsp;".$vote['isactive']."</td>
						<td>&nbsp;".$vote['isdefault']."</td>
						<td><a href=\"/admin?cmd=vote&qID=".$vote['qID']."\">Редактировать</a></td>
						<td><a href=\"/admin?cmd=vote&act=delete&qID=".$vote['qID']."\">Удалить</a></td>
					</tr>
					";
				if($qID == $vote['qID']) {
					$tpl = str_replace("%qID%", $vote['qID'], $tpl);
					$tpl = str_replace("%Question%", $vote['Question'], $tpl);
					$tpl = str_replace("%Ans1%", $vote['Ans1'], $tpl);
					$tpl = str_replace("%Ans2%", $vote['Ans2'], $tpl);
					$tpl = str_replace("%Ans3%", $vote['Ans3'], $tpl);
					$tpl = str_replace("%Ans4%", $vote['Ans4'], $tpl);
					$tpl = str_replace("%Ans5%", $vote['Ans5'], $tpl);
					$tpl = str_replace("%Ans6%", $vote['Ans6'], $tpl);
					$tpl = str_replace("%Ans7%", $vote['Ans7'], $tpl);
					$tpl = str_replace("%Ans8%", $vote['Ans8'], $tpl);
					$tpl = str_replace("%Ans9%", $vote['Ans9'], $tpl);
					$tpl = str_replace("%Ans10%", $vote['Ans10'], $tpl);
					$tpl = str_replace("%isactive%", ($vote['isactive'] == 1 ? "checked" : ""), $tpl);
					$tpl = str_replace("%isdefault%", ($vote['isdefault'] == 1 ? "checked" : ""), $tpl);

				}
			}
			$tpl = str_replace("%votes%", $voteContent, $tpl);
		} else {
			$tpl = str_replace("%votes%", "", $tpl);
		}
		
		if(!$qID) {
			$tpl = str_replace("%qID%", "", $tpl);
			$tpl = str_replace("%Question%", "", $tpl);
			$tpl = str_replace("%Ans1%", "", $tpl);
			$tpl = str_replace("%Ans2%", "", $tpl);
			$tpl = str_replace("%Ans3%", "", $tpl);
			$tpl = str_replace("%Ans4%", "", $tpl);
			$tpl = str_replace("%Ans5%", "", $tpl);
			$tpl = str_replace("%Ans6%", "", $tpl);
			$tpl = str_replace("%Ans7%", "", $tpl);
			$tpl = str_replace("%Ans8%", "", $tpl);
			$tpl = str_replace("%Ans9%", "", $tpl);
			$tpl = str_replace("%Ans10%", "", $tpl);
			$tpl = str_replace("%isactive%", "", $tpl);
			$tpl = str_replace("%isdefault%", "", $tpl);
		}
		

		return $tpl;

	}
	
	function saveVote() {
		$qID = _post("qID");
		$Question = _post("Question");
		$Ans1 = _post("Ans1");
		$Ans2 = _post("Ans2");
		$Ans3 = _post("Ans3");
		$Ans4 = _post("Ans4");
		$Ans5 = _post("Ans5");
		$Ans6 = _post("Ans6");
		$Ans7 = _post("Ans7");
		$Ans8 = _post("Ans8");
		$Ans9 = _post("Ans9");
		$Ans10 = _post("Ans10");
		$isactive = _post("isactive");
		$isdefault = _post("isdefault");
		
		if(!$qID) {
			$query = "INSERT INTO pm_vote (`Question`, `Ans1`, `Ans2`, `Ans3`, `Ans4`, `Ans5`, `Ans6`, `Ans7`, `Ans8`, `Ans9`, `Ans10`, `isdefault`, `isactive`) 
			VALUES ('".$Question."', '".$Ans1."', '".$Ans2."', '".$Ans3."', '".$Ans4."', '".$Ans5."', '".$Ans6."', '".$Ans7."', '".$Ans8."', '".$Ans9."', '".$Ans10."',  '".$isdefault."', '".$isactive."')
			";
		} else {
			$query = "UPDATE pm_vote SET 
				`Question` = '".$Question."',
				`Ans1` = '".$Ans1."',
				`Ans2` = '".$Ans2."',
				`Ans3` = '".$Ans3."',
				`Ans4` = '".$Ans4."',
				`Ans5` = '".$Ans5."',
				`Ans6` = '".$Ans6."',
				`Ans7` = '".$Ans7."',
				`Ans8` = '".$Ans8."',
				`Ans9` = '".$Ans9."',
				`Ans10` = '".$Ans10."',
				`isdefault` = '".$isdefault."',
				`isactive` = '".$isactive."'
			WHERE qID = '".$qID."'";
		}
		//echo $query;
		if (!mysql_query($query))
		{
			trigger_error("Error while updating vote [$query] - " . mysql_error(), PM_FATAL);
		}
		header("Location: /admin?cmd=vote");
	}

	function deleteVote($qID) {
		if(!$qID)
			trigger_error("No vote to delete [$qID] - " . mysql_error(), PM_FATAL);
		$query = "DELETE FROM pm_vote WHERE qID = '".$qID."'";
		if (!mysql_query($query))
		{
			trigger_error("Error while delete banner [$qID] - " . mysql_error(), PM_FATAL);
		}
		header("Location: /admin?cmd=vote");
	}

	function getVotesList() {
		$votes = Array();
		$query = "SELECT * FROM pm_vote";
		$result = mysql_query($query);
		if(mysql_num_rows($result)) {
			while($vote = mysql_fetch_assoc($result)) {
				$votes[] = $vote;
			}
		}

		return $votes;
	}
}
?>