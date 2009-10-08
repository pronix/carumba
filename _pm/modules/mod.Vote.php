<?php

	class Vote extends AbstractModule
	{
		var $dNames;
		var $itemsCount = 0;

		function Vote()
		{
		    $this->name = 'Vote';
			$this->publicFunctions = array("getContent", "getBlock", "getSubItemType", "getItemType", "getItemDesc",
            "getSpecificDataForEditing", "getSpecificBlockDesc", "updateSpecificData", 'updateAdditionalColumns');

		}

		function getContent($args)
		{
			$aID = _post("vote");
			$qID = _post("qID");

			$cmd = _post("cmd");
			$res = "";

			switch ($cmd)
			{
				case "vote":
				{
					if(!$this->checkIfUserVoted($qID)) {
						$this->makeVote($qID, $aID);
						$res .= "<div class=\"podbor\"><p><strong>Внимание!</strong></p>
								<p>Вы не можете голосовать больше одного раза</p></div>
								";
					} else {
						$res .= "<div class=\"podbor\"><p><strong>Внимание!</strong></p>
								<p>Спасибо. Ваш голос учтен</p></div>";
					}
					break;
				}
			}
			$res .= $this->getResults($args);
			return $res;
		}

		function makeVote($qID, $aID)
		{
			$ip = $_SERVER['REMOTE_ADDR'];
			$query = "INSERT INTO pm_vote_results (qID, aID, ip, date)
					VALUES ('".$qID."', '".$aID."', '".$ip."', '".date("Y-m-d H:i:s")."')";
			if (!mysql_query($query))
			{
				trigger_error("Error while making vote page [$qID] - " . mysql_error(), PM_FATAL);
			}

			header("Location: ".$_SERVER['HTTP_REFERER']);
		}

		function getResults($args)
		{
			global $structureMgr, $templatesMgr;

			SetCfg("Vote.itemsPerPage", 10);
			SetCfg("Vote.itemsPerCol", 1);

			$pageID = $args[0];

			$pager = "";

			$content = "";

			$pNum = $structureMgr->getPageNumberByPageID($pageID);
            $URL = $structureMgr->getPathByPageID($pageID, false);

			$perPage = GetCfg("Vote.itemsPerPage");

            $startFrom = ($pNum - 1) * $perPage;
            $endAt = $startFrom + $perPage - 1;

			$voteresults = $this->getVoteResults($startFrom, $endAt);

            $cnt = $this->itemsCount;

            if ($endAt >= $cnt)
                $endAt = $cnt - 1;

            $pagesCount = ceil($cnt / $perPage);

            if ($pagesCount < $pNum)
            {
				//echo $pagesCount . " -- ". $pNum."<br>";
                trigger_error("Invalid pageNumber [$pNum of $pagesCount] - possibly hacking or serious change in DB", PM_ERROR);
            }
            else
            {
                if ($pagesCount > 1)
                {
                    $tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/" . "pager.html");
                    $purePager = "";

                    for ($i=1; $i <= $pagesCount; $i++)
                    {
                        if ($i > 1)
                        {
                            $purePager .= " - ";
                            $u = $URL . "/page" . $i;
                        }
                        else
                           $u = $URL;

                        if ($filter)
                            $u .= "?" . $filter;

                        if ($i == $pNum)
                        {
                            $purePager .= $i;
                        }
                        else
                        {
                            $purePager .= "<a href=\"$u\" class=\"levm\">" . $i . "</a>";
                        }
                    }

                    $pager = str_replace("%links%", $purePager, $tpl);
                }

			}

			$content .= "<div class=\"items\">
						<table cellpadding=\"0\" cellspacing=\"0\" class=\"items-table\">
						";

			if(count($voteresults)) {
				$i = 1;
				foreach($voteresults as $voteresult) {
					if($i == 1){
						$style = "up";
					} elseif($i == count($voteresults)) {
						$style = "dwn";
					} else {
						$style = "mid";
					}
					$content .="<tr>". $this->getResultsFilledTemplate($voteresult, $style)."</tr>";
					$i++;
				}
			}
			$content .= "</table></div>";
			return $pager.$content.$pager;
		}

		function getVote($qID)
		{
			$content = "";
			$vote = $this->getVoteData($qID);
			if(count($vote))
				$content = $this->getFilledTemplate($vote);

			return $content;
		}

		function getBlock()
		{
			$content = "";
			$vote = $this->getActiveVote();
			if(count($vote)) {
				$content .= $this->getFilledTemplate($vote);
			}

			return $content;
		}

		function getFilledTemplate($vote)
		{
			global $structureMgr, $templatesMgr;
			$URL = $structureMgr->getPathByPageID($vote["sID"], false);
			$tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Vote/vote.html");

			$tpl = str_replace("%link%", $URL, $tpl);
			$tpl = str_replace("%qID%", $vote["qID"], $tpl);
			//print_r($vote);
			$tpl = str_replace("%question%", $vote['Question'], $tpl);
			$answers = "";
			for($i = 1; $i < 11; $i++) {
				if($vote["Ans$i"])
					$answers .= "<input name=\"vote\" type=\"radio\" value=\"".$i."\" ".( $i==1 ? "checked=\"checked\"" : "")." /> &nbsp;".$vote["Ans$i"]."<br />\n";
			}
			$tpl = str_replace("%answers%", $answers."<br />", $tpl);

			return $tpl;

		}


		function getResultsFilledTemplate($voteresults, $style = "mid")
		{
			global $structureMgr, $templatesMgr;
			$tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Vote/voteresult.html");

			//$tpl = str_replace("%link%", $URL, $tpl);

			$tpl = str_replace("%question%", $voteresults['question'], $tpl);
			$results = "";
			$sum = 0;
			foreach($voteresults['answers'] as $key=>$value) {
				$sum += $value;
			}
			$i=1;
			foreach($voteresults['answers'] as $key=>$value) {
				//echo ($i-1).'<hr>';
				//$results .= $key." - ".round($value/$sum*100)."% <br>";
				$results .= "<img src=\"/small_image.php?color=".($i-1)."\" width=\"10\" height=\"10\"  alt=\"\" />
					<input name=\"vote\" type=\"radio\" value=\"".$i."\" ".($i==1 ? "checked=\"checked\"" : "")." /> ". $key ." ( ".$value." / ".round($value/$sum*100)."% )<br />";
				$i++;
			}
			$tpl = str_replace("%result%", $results, $tpl);
			$URL = $structureMgr->getPathByPageID($voteresults["sID"], false);
			$tpl = str_replace("%link%", $URL, $tpl);
			$tpl = str_replace("%sum%", $sum, $tpl);
			$tpl = str_replace("%style%", $style, $tpl);
			$tpl = str_replace("%qID%", $voteresults["qID"], $tpl);
			$tpl = str_replace("%picture%", "/image.php?qID=".$voteresults['qID'], $tpl);
			return $tpl;

		}

		function checkIfUserVoted($qID)
		{
			$ip = $_SERVER['REMOTE_ADDR'];
			$yesterday  = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m")  , date("d")-1, date("Y")));
			$query = "SELECT qId FROM pm_vote_results WHERE qID = '".$qID."' && ip = '".$ip."' && date > '".$yesterday."'";

			$result = mysql_query($query);
			if(mysql_num_rows($result)) {
				return true;
			} else {
				return false;
			}
		}

		function getVoteResults($startFrom, $endAt)
		{
			$voteResults = Array();

			$query = "SELECT SQL_CALC_FOUND_ROWS
						qID, sID, Question, Ans1, Ans2, Ans3,
						Ans4, Ans5, Ans6, Ans7, Ans8, Ans9, Ans10
						FROM pm_vote
						WHERE
							isActive = 1
						LIMIT ".$startFrom.",".GetCfg("Vote.itemsPerPage")."
					";
			$result = mysql_query($query);

			$query = "SELECT FOUND_ROWS() as itemsCount";
			$res = mysql_query($query);
			$row = mysql_fetch_assoc($res);
			$this->itemsCount = $row['itemsCount'];

			if(mysql_num_rows($result)) {
				$j = 0;
				while($vote = mysql_fetch_assoc($result)) {
					for($i = 1; $i < 11; $i++) {
						if($vote["Ans$i"]) {
							$qAns = "SELECT  qID FROM pm_vote_results WHERE qID = '".$vote['qID']."' && aID = '".$i."'";
							$rAns = mysql_query($qAns);
							$voteResults[$j]['answers'][$vote["Ans$i"]] = mysql_num_rows($rAns);
						}
					}
					$voteResults[$j]['question'] = $vote['Question'];
					$voteResults[$j]['qID'] = $vote['qID'];
					$voteResults[$j]['sID'] = $vote['sID'];
					$j++;
				}
			}

			return $voteResults;
		}

		function getActiveVote()
		{
			$vote = Array();

			$query = "SELECT qID, question, Ans1, Ans2, Ans3,
						Ans4, Ans5, Ans6, Ans7, Ans8, Ans9, Ans10, sID
						FROM pm_vote
						WHERE
							isActive = 1 && isdefault = 1
					";
			$result = mysql_query($query);

			if (mysql_num_rows($result)) {

				$question = mysql_fetch_assoc($result);
				$vote['qID'] = $question['qID'];
				$vote['sID'] = $question['sID'];
				$vote['Question'] = $question['question'];
				for($i = 1; $i < 11; $i++)
					$vote["Ans$i"] = $question["Ans$i"];
			}
			//print_r($question);
			return $vote;
		}

		function getVoteData($qID)
		{
			$vote = Array();

			$query = "SELECT qID, sID, Question, Ans1, Ans2, Ans3,
						Ans4, Ans5, Ans6, Ans7, Ans8, Ans9, Ans10
						FROM pm_vote
						WHERE
							isActive = 1 && qID = '".$qID."'
					";
			$result = mysql_query($query);

			if (mysql_num_rows($result)) {

				$question = mysql_fetch_assoc($result);
				$vote['qID'] = $question['qID'];
				$vote['sID'] = $question['sID'];
				$vote['Question'] = $question['Question'];
				for($i = 1; $i < 11; $i++)
					$vote["Ans$i"] = $question["Ans$i"];
			}

			return $vote;
		}

		function getSpecificDataForEditing($args)
        {
			return Array();
        }

        function updateSpecificData($args)
        {

            return true;
        }

        function getSpecificBlockDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Vote":
                    return "Параметры опроса";
            }

            return "";
        }

        function getItemDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Vote":
                    return "";
            }

            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Vote":
                    return array("голосование", "голосования", "голосованию"); //Именит, Род, Дат
            }

            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case "Catalogue":
                    return array("Vote" => "голосование");
				case "Article":
                    return array("Vote" => "голосование");
            }
            return array();
        }


        function updateAdditionalColumns($args)
        {
            return false;
        }

	}

?>