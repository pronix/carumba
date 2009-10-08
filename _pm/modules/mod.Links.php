<?php

    class Links extends AbstractModule
	{
	    private $per_page;
	    public $name;
	    private $pageID;

        function __construct()
        {
            $this->publicFunctions = array('getContent', 'getSubItemType', 'getItemType', 'getItemDesc', 'getSpecificDataForEditing', 'getSpecificBlockDesc', 'updateSpecificData', 'updateAdditionalColumns');
            $this->per_page = 6;
            $this->name = 'Links';
        }

        public function getContent($args)
        {
            global $structureMgr;

			$pageID = $args[0];

            $parentPageId = $structureMgr->getParentPageID($pageID);

			$this->pageID = $pageID;

			$page = $structureMgr->getPageNumberByPageID($pageID);
            $URL = $structureMgr->getPathByPageID($pageID, false);

            $content = '<div class="podbor">';


            $oResult = mysql_query("SELECT DataType FROM pm_structure WHERE sID='$pageID' LIMIT 1");
            if ($oResult) {
                list($DataType) = mysql_fetch_array($oResult);
            } else {
                $DataType = '';
            }

            switch ($DataType) {
                case 'Links':
                    $content.=$this->getLinksCatalogue();
                    break;
                case 'Add':
                    $content.=$this->getAddForm($parentPageId);
                    break;
                case 'Link':
                    $oResult = mysql_query('SELECT COUNT(*) FROM pm_links WHERE public=1 AND cid='.$pageID);
                    if ($oResult) {
                        list($count) = mysql_fetch_array($oResult);
                    } else {
                        $count = 0;
                    }

                    $from = ($page-1)*$this->per_page;

                    $pages = ceil($count / $this->per_page);

                    $content.=$this->getLinksList($pageID, $from);
                    break;

                default:
                    $content .= 'Ошибка';
            }

            $content .= '</div>';

            if ($pages>1) {
                $content.='<div class="podbor">Страницы:';
                for ($i=1; $i<=$pages; $i++) {
                    $content .= ' - ';
                    if ($i == $page) {
                        $content .= $i;
                    } else {
                        $content .= '<a href="'.$URL.'/page'.$i.'">'.$i.'</a>';
                    }
                }
                $content .= '</div>';
            }

            return $content;
        }


        /**
         * Возвращает HTML-код общей страницы каталога ссылок
         *
         * @return string
         */
        private function getLinksCatalogue()
        {
            global $structureMgr;
            $oResult = mysql_query('SELECT DISTINCT s.sID, s.Title, COUNT(l.cid) as count
                                    FROM pm_structure s
                                    LEFT JOIN pm_links l ON (s.sID = l.cid AND l.public = 1)
                                    WHERE s.isHidden = 0 AND s.pms_sID='.$this->pageID.' AND s.DataType = "Link"
                                    GROUP BY s.sID
                                    ORDER BY s.Title');

            $aCategories = array();
            if ($oResult) {
                while ($aCat = mysql_fetch_assoc($oResult)) {
                    $aCategories[] = $aCat;
                }
            }

            $textContent = $structureMgr->getData($this->pageID);

            $iOfColoumn = ceil( sizeof($aCategories) / 2 );
            $aCat2 = array_chunk($aCategories, $iOfColoumn);

            $content = '<table border="0" width="100%"><tr>';
            foreach ($aCat2 as $aCat1) {
                $content.= '<td width="50%">';
                foreach ($aCat1 as $v) {
                    $content.= "<a href='/main/links/{$v['sID']}'>".$v['Title'].'</a> ('.$v['count'].')'.'<br>';
                }
                $content.= '</td>';
            }
            $content.= '</tr></table><br />';

            $content.= '<p><img src="/images/arr_gray2.gif" width="7" height="9" alt="" />
                <a href="/main/links/add">Добавить сайт</a></p>';

            $content .= $textContent;

            return $content;

        }


        /**
         * Возвращает HTML формы для добавления сайтов
         *
         * @return string
         */
        private function getAddForm($parentPageId)
        {
            $tpl_file = '_pm/templates/Links/add_form.html';
            if (file_exists($tpl_file)) {
                $tpl = file_get_contents($tpl_file);
            } else {
                trigger_error("File '$tpl_file' not exists", E_USER_ERROR);
            }

            $oResult = mysql_query('SELECT sID, Title FROM pm_structure WHERE pms_sID='.$parentPageId.' AND DataType = "Link" ORDER BY Title');
            $aCategories = array();
            if ($oResult)
                while ($aCat = mysql_fetch_assoc($oResult)) {
                    $aCat['Title'] = stripslashes($aCat['Title']);
                    $aCategories[] = $aCat;
                }

            $email_pattern = '/([a-zA-Z0-9_-]+@([a-zA-Z0-9_-]+\.)+[a-zA-Z0-9_-]+)/';
            $url_pattern = '/http:\/\/(([a-z0-9_-]+\.)+[a-z0-9_-]+)([\/]+[a-z0-9_-]+([a-z0-9_-]+\.[a-z0-9_-]+)*)*/i';


            $message = '';
            $send = _post('links_send');
            $url = _post('links_url') ? _post('links_url') : 'http://';
            $title = _post('links_title') ? _post('links_title') : '';
            $cat = _post('links_cat') ? _post('links_cat') : 0;
            $options = '';
            foreach ($aCategories as $v) {
                $selected = (!empty($cat) && is_numeric($cat) && $cat==$v['sID']) ? ' selected="true"' : '';
                $options.="<option value='{$v['sID']}'{$selected}>".$v['Title'].'</option>';
            }
            $referer = _post('links_referer') ? _post('links_referer') : 'http://';
            $email = _post('links_email') ? _post('links_email') : '';
            $code = _post('links_code') ? _post('links_code') : '';
            $text = _post('links_text') ? _post('links_text') : '';

            $oResult = mysql_query("SELECT id FROM pm_comments_codes WHERE code = '{$code}' LIMIT 1");
            if ($send) {
                if ($oResult && mysql_num_rows($oResult)) {
                    list($code_id) = mysql_fetch_array($oResult);
                    mysql_query("DELETE FROM pm_comments_codes WHERE id = '$code_id' LIMIT 1");
                } else {
                    $message = '<p><b>Введенный код неверен.</b></p>';
                }
            }

            if ($send && sizeof($text) > 1000) {
                $message = '<p><b>Длина описания не должна превышать 1000 символов.</b></p>';
            }
            if ($send && !preg_match($email_pattern, $email)) {
                $message = '<p><b>Адрес Email задан неверно.</b></p>';
            }
            if ($send && !preg_match($url_pattern, $referer)) {
                $message = '<p><b>Адрес обратной ссылки задан неверно.</b></p>';
            }
            if ($send && !preg_match($url_pattern, $url)) {
                $message = '<p><b>Адрес сайта задан неверно.</b></p>';
            }

            if ($send && (empty($code) || empty($url) || empty($title) || empty($cat) || empty($referer) || empty($email) || empty($text))) {
                $message = '<p><b>(*) - Поля, обязательные для заполнения</b></p>';
            }

            if ($send && $message=='') {
                mysql_query("INSERT INTO `pm_links`
                                (`id`, `cid`, `date`, `url`, `title`, `text`, `referer`, `email`, `public`)
                                    VALUES ('',
                                    '{$cat}',
                                    UNIX_TIMESTAMP() ,
                                    '{$url}',
                                    '{$title}',
                                    '{$text}',
                                    '{$referer}',
                                    '{$email}', '2')");
                if (mysql_insert_id()) {
                    $message = '<p><b>Сайт успешно добавлен и появится в каталоге после просмотра модератором.</b></p>';
                } else {
                    $message = '<p><b>Произошла ошибка.</b></p>';
                }
            }

            // Защитный код
            $sCode = '0000'.rand(0, 9999);
            $sCode = substr($sCode, strlen($sCode)-4, 4);
            mysql_query("INSERT INTO pm_comments_codes (id, date, code) VALUES ('', ".time().", '$sCode')");
            $iCode = mysql_insert_id();
            if (empty($iCode)) die('bad arguments');

            mysql_query("DELETE FROM pm_comments_codes WHERE date < '".(time()-600)."'");

            $tpl = str_replace('{url}', $url, $tpl);
            $tpl = str_replace('{title}', $title, $tpl);
            $tpl = str_replace('{options}', $options, $tpl);
            $tpl = str_replace('{referer}', $referer, $tpl);
            $tpl = str_replace('{email}', $email, $tpl);
            $tpl = str_replace('{text}', $text, $tpl);
            $tpl = str_replace('{message}', $message, $tpl);
            $tpl = str_replace('{code}', $iCode, $tpl);
            return $tpl;
        }

        /**
         * Возвращает HTML-код списка ссылок
         *
         * $cid - поле cid в таблице pm_links
         * $from - начиная с какой записи выводить
         *
         * @param int $cid
         * @param int $from
         * @return string
         */
        private function getLinksList($pageID, $from)
        {
            if ($pageID) {
                $oResult = mysql_query('SELECT * FROM pm_links
                                        WHERE public=1 AND cid='.$pageID.'
                                        LIMIT '.$from.','.$this->per_page);
                if ($oResult) {
                    $aLinks = array();
                    while ($aLink = mysql_fetch_assoc($oResult)) {
                        foreach ($aLink as &$v) $v = stripcslashes($v);
                        $aLink['date'] = date('d-m-Y (H:i)', $aLink['date']);
                        $aLinks[] = $aLink;
                    }
                }
            } else {
                //$sCategory = 'Категория не определена';
            }

            $content = '';
            $size = sizeof($aLinks);
            $i = 0;
            foreach ($aLinks as $v) {
                $i++;
                $content .= '<div>';
                $content .= '<a href="'.$v['url'].'" target="_blank"><b>'.$v['title'].'</b></a><br />';
                $content .= '<div>'.$v['text'].'</div>';
                $content .= '<div style="color:#999; font-size:10px;">'.$v['url'].'</div>';
                $content .= '</div>';
                if ($i != $size)
                    $content .= '<div style="padding:5px 0 0 0; margin:0 0 5px 0; border-bottom: 1px dashed #DDD;"></div>';
            }

            return $content;
        }




        /**
         * Функции, реализующие API для админки
         */


        function getItemDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Links":
                    return "Текст каталога";
            }

            return "";
        }

        function getSpecificDataForEditing($args)
        {
            return array();
        }

        function getSpecificBlockDesc($args)
        {
            return "";
        }

        function updateSpecificData($args)
        {
            return false;
        }


        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case 'Links':
                    return array('ссылки',
                                 'ссылок',
                                 'ссылкам'
                                ); //Именит, Род, Дат
            }

            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case 'Catalogue':
                    return array('Links' => 'ссылки');
				case 'Article':
                    return array('Links' => 'ссылки');
            }
            return array();
        }


        function updateAdditionalColumns($args)
        {
            return false;
        }
	}
?>