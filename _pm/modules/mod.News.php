<?
    class News extends AbstractModule
    {
        function News()
        {
            $this->name = "News";
            $this->desc = "Provides a news system with comments";
            $this->publicFunctions = array("getContent", "getSubItemType", "getItemType", "getItemDesc",
            "getSpecificDataForEditing", "getSpecificBlockDesc", "updateSpecificData", "newsBlock", 'updateAdditionalColumns');
            $this->cmdFunctions = array();

            SetCfg("News.perPage", 10);
        }

        function getSpecificDataForEditing($args)
        {
            return array();
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
                case "Newsline":
                    return "Параметры";
                case "News":
                    return "Параметры";
            }

            return "";
        }

        function getItemDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Newsline":
                    return "";
                case "News":
                    return "Текст новости";
            }

            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Newsline":
                    return array("новостная лента", "новостную ленту", "новостной ленты"); //Именит, Род, Дат
                case "News":
                    return array("новость", "новость", "новости");
            }

            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case "Article":
                    return array("Newsline" => "новостную ленту");

                case "Newsline":
                    return array("News" => "новость");
            }

            return array();
        }

        function getContent($args)
        {
            global $structureMgr;
            $metaData = $structureMgr->getMetaData($args[0]);
            switch ($metaData["DataType"])
            {
                case "Newsline":
                    return $this->getNewsList($args[0], "newslist.xml", -1);
                case "News":
                    return $this->getNews($args[0]);
            }
        }

        function getNews($pageID)
        {
            global $structureMgr;
            $md = $structureMgr->getMetaData($pageID);
            return "<div class=\"podbor\">" . $structureMgr->getData($pageID)."</div>";
        }

        function getNewsList($pageID, $tplFilename, $count)
        {
            global $structureMgr, $templatesMgr;

            $res = "";

            $branch = $structureMgr->getStructureForPageID($pageID, 1);
            if (count($branch) == 0)
                return "Новостей нет.";

            $tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/News/" . $tplFilename);

            //$tpl = set_safe_newlines($tpl);

            $blocks = $templatesMgr->getValidTags(&$tpl, array("container", "item", "separator"));

            $from = count($branch);
            if (($count == -1) || ($count > $from))
                $to = 0;
            else
                $to = $from - $count;

            for ($i = $from - 1; $i >= $to; $i--)
            {
                $news = $branch[$i];
                $URL = $structureMgr->getPathByPageID($news["sID"], true);
                $bl = $blocks["item"];
                $bl = str_replace("%short_title%", $news["ShortTitle"], $bl);
                $bl = str_replace("%date%", $news["CreateDate"], $bl);
                $bl = str_replace("%news_link%", $URL, $bl);
                $bl = str_replace("%summary%", $structureMgr->getData($news["sID"]), $bl);

                if ($res && isset($blocks["separator"]))
                    $res .= $blocks["separator"];
                $res .= $bl;
            }
            $blocks["container"] = str_replace("%items%", $res, $blocks["container"]);

            return $blocks["container"];
        }

        function newsBlock($args)
        {
            if (!isset($args["TEMPLATE"]) || ($args["TEMPLATE"] == ""))
                trigger_error("Template for newsBlock must be specified", PM_FATAL);

            if (!isset($args["NEWSLINEID"]) || ($args["NEWSLINEID"] == ""))
                trigger_error("NewsLineID for newsBlock must be specified", PM_FATAL);

            if (!isset($args["NEWSCOUNT"]))
                $args["NEWSCOUNT"] = -1;

            return $this->getNewsList($args["NEWSLINEID"], $args["TEMPLATE"], $args["NEWSCOUNT"]);
        }

        function updateAdditionalColumns($args)
        {
            return false;
        }
    }
?>
