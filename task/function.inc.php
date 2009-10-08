<?php
    /**
     * ¬ыбирает параметр $var из cookie или возвращает false
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
     * ¬ернет отображение массива
     *
     * @param array $array
     * @return string
     */
    function get_ar(&$array) {
        ob_start();
        ob_clean();
        print_r($array);
        $msg = ob_get_contents();
        $msg = str_replace(' ', '&nbsp;', $msg);
        $msg = nl2br($msg);
        ob_end_clean();
        return $msg;
    }

    /**
     * »з строки массива, образованного из URL определ€ет параметры,
     * которые определ€ют отображаемую страницу
     *
     * @param array $aPar
     * @return array
     */
    function get_page_info_from_path($aPar)
    {
        global $aImportant;
        if (in_array($aPar[2], $aImportant)) {
            $aUImp = array_flip($aImportant);
            $aTopic['imp'] = $aUImp[$aPar[2]];
            $aTopic['name'] = $aPar[2];
        } else {
            $aTopic['imp'] = 0;
            $aTopic['name'] = 'hot';
        }

        if (is_numeric($aPar[3]) ) {
            $aTopic['id'] = $aPar[3];
            $page_param_key = 4;
        } else {
            $aTopic['id'] = 0;
            $page_param_key = 3;
        }

        if (preg_match('|page(\d+)|', $aPar[$page_param_key], $m )) {
            $aTopic['page'] = $m[1];
        } else {
            $aTopic['page'] = 1;
        }
        return $aTopic;
    }

    /**
     * ¬ернет массив дл€ шаблона меню
     *
     * @return unknown
     */
    function get_menu()
    {
        global $aImportant, $menu_count;

        $menu_count = array(0,0,0,0);
        $res = mysql_query('SELECT important, COUNT(`important`) as count FROM `task` WHERE sid=0 GROUP BY `important`');
        if ($res && mysql_num_rows($res)) {
            while ($data = mysql_fetch_assoc($res)) {
                $menu_count[$data['important']] = $data['count'];
            }
        } else {
            trigger_error('error select menu', E_USER_ERROR);
        }

        $last_date = time() - 86400*7;
        $menu_new = array(0,0,0,0);
        $res = mysql_query('SELECT important, COUNT(`important`) as count FROM `task` WHERE date > '.$last_date.' GROUP BY `important`');
        if ($res && mysql_num_rows($res)) {
            while ($data = mysql_fetch_assoc($res)) {
                $menu_new[$data['important']] = $data['count'];
            }
        }

        $namedImp = array('—рочные', '“екучка', '»деи', '«акрытые');
        // такой перебор дл€ того, чтобы установить все индексы в массиве
        $out = array();
        for ($i = 0; $i <= 3; $i++) {
            $menu_count[$i] = $menu_count[$i] ? $menu_count[$i] : 0;
            $menu_new[$i] = $menu_new[$i] ? $menu_new[$i] : 0;
            $out[] = array('url' => $aImportant[$i], 'name' => $namedImp[$i], 'count' => $menu_count[$i], 'newcount' => $menu_new[$i]);
        }
        return $out;
    }

    /**
     * ¬ернет HTML-код с номерами страниц
     *
     * ѕараметр $url должен заканчиватьс€ на слэшем '/'
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
        if ($pageCount > 1) {
            $ret = '—траницы: ';
            $pages = array();
            for ($i = 1; $i <= $pageCount; $i++) {
                if ($i != $page) {
                    $pages[] = '<a href="'.$url.'page'.$i.'">'.$i.'</a>';
                } else {
                    $pages[] = ''.$i;
                }
            }

            return $ret.implode(' - ', $pages);
        } else {
            return '';
        }
    }

    /**
     * ¬ соответствии с параметрами, полученными из форм производит действи€ над таблицей task
     *
     * addtopic - добавить сообщение в топик
     * addreply - добавить ответ в топик
     *
     */
    function do_action()
    {
        global $aTopic, $userID;
        switch ($_POST['action']) {

            case 'addtopic':
            // добавить топик
                $selecto = (isset($_POST['selecto']) && is_numeric($_POST['selecto'])) ? $_POST['selecto'] : $aTopic['imp'];
                $inputo = (isset($_POST['inputo'])) ? $_POST['inputo'] : '';
                $inputo = trim($inputo) ? $inputo : 'untitled';
                $textarea = (isset($_POST['textarea'])) ? $_POST['textarea'] : '';
                $inputo = mysql_escape_string($inputo);
                $textarea = mysql_escape_string($textarea);

                $now = time();
                mysql_query("INSERT INTO `task` ( `id` , `sid` , `date` , `title` , `message` , `important` , `userID` )
                                VALUES (
                                '', '0', '$now', '$inputo', '$textarea', '$selecto', '$userID')");
                break;

            case 'addreply':
            // добавить ответ в топик
                $textarea = (isset($_POST['textarea'])) ? $_POST['textarea'] : '';
                $textarea = mysql_escape_string($textarea);
                $sid = (isset($_POST['sid']) && is_numeric($_POST['sid'])) ? $_POST['sid'] : 0;
                $now = time();
                mysql_query("INSERT INTO `task` ( `id` , `sid` , `date` , `title` , `message` , `important` , `userID` )
                                VALUES (
                                '', '{$aTopic['id']}', '$now', '', '$textarea', '{$aTopic['imp']}', '$userID')");
                break;

            case 'select_state':
            // изменить важность топика
                $id = (isset($_POST['id']) && is_numeric($_POST['id'])) ? $_POST['id'] : -1;
                $select = (isset($_POST['select']) && is_numeric($_POST['select'])) ? $_POST['select'] : -1;

                if ($id!=-1 && $select!=-1) {
                    mysql_query("UPDATE task SET important = '$select' WHERE id = '$id' LIMIT 1");
                }
                break;

            case 'save_reply':
            // сохранить изменени€
                $id = (isset($_POST['id']) && is_numeric($_POST['id'])) ? $_POST['id'] : -1;
                $textarea = isset($_POST['textarea']) ? mysql_escape_string($_POST['textarea']) : '';
                if ($id != -1) {
                    mysql_query("UPDATE task SET message = '$textarea' WHERE id='$id' LIMIT 1");
                }
                break;
        }

        switch ($_GET['action']) {
            case 'delete':
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    $id = $_GET['id'];
                    $res = mysql_query("SELECT COUNT(id) FROM task WHERE sid='$id'");
                    if ($res) {
                        list($count) = mysql_fetch_array($res);
                    }
                    if ($count) {
                        mysql_query("DELETE FROM task WHERE sid='$id' LIMIT $count");
                    }
                    mysql_query("DELETE FROM task WHERE id='$id' LIMIT 1");

                    if (!$count) {
                        header('location: '.$_SERVER['HTTP_REFERER']);
                    } else {
                        header('location: /task/'.$aTopic['name']);
                    }
                    exit();
                }
                break;
        }
    }
?>