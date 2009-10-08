<?php
    function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)((float)$usec + (float)$sec);
    }

    function userError($errno, $errstr, $errfile, $errline) {
        print '<div style="padding:5px; margin:5px; border:1px solid black; background:white;">';
        print '<b>ERROR</b> '.$errstr.'<br />';
        print $errfile.':'.$errline.'</div>';
    }

        /**
     * Выбирает параметр $var из cookie или возвращает false
     *
     * @param mixed $var
     * @return mixed
     */
    function _cookie($var) {
        if (isset($_COOKIE[$var])) {
            return $_COOKIE[$var];
        } else {
            return false;
        }
    }

    /**
     * Вернет HTML-код с номерами страниц
     * 
     * Параметр $url должен заканчиваться на слэшем '/'
     *
     * @param integer $page
     * @param integer $perPage
     * @param integer $count
     * @param string $url
     * @return string
     */
    function get_page_line($page, $perPage, $count, $url)
    {
        $pageCount = ceil($count/$perPage);
        if (strpos($url,'?')!==false) {
            $d = '&';
        } else {
            $d = '?';
        }
        if ($pageCount > 1) {
            $ret = 'Страницы: ';
            $pages = array();
            for ($i = 1; $i <= $pageCount; $i++) {
                if ($i != $page) {
                    $pages[] = '<a href="'.$url.$d.'page='.$i.'">'.$i.'</a>';
                } else {
                    $pages[] = ''.$i;
                }
            }
                       
            return $ret.implode(' - ', $pages);
        } else {
            return '';
        }
    }

    function action() {
        global $db;
        if (is_array($_POST['change']))
            foreach ($_POST['change'] as $i => $v) {
                if (is_numeric($i))
                switch ($_POST['action']) {
                    case 'p':
                        $db->query("UPDATE pm_comments SET public = 1 WHERE cID = $i LIMIT 1");
                        break;
                    case 'h':
                        $db->query("UPDATE pm_comments SET public = 2 WHERE cID = $i LIMIT 1");
                        break;
                    case 'd':
                        $db->query("DELETE FROM pm_comments WHERE cID = $i LIMIT 1");
                        break;
                }
            }
    }


?>