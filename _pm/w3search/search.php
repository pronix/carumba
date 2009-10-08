<?
	define('SEARCH_INCLUDES', 'search_includes');
	require_once SEARCH_INCLUDES . '/search.conf';

	require_once HEADER;

	$w3s_query=strtolower(@$_GET['q']);
	$w3s_query=str_replace(array('nbsp', 'laquo', 'raquo', "\r", "\n"), ' ', $w3s_query);
	$w3s_query=str_replace('!', '	', $w3s_query);
	$w3s_query=str_replace('-', '	', $w3s_query);
	$w3s_query=preg_replace('([[:punct:]]+)', ' ', $w3s_query);
	$w3s_query=str_replace('	', '!', $w3s_query);
	$w3s_query=preg_replace('([[:space:]]+)', ' ', $w3s_query);
	$w3s_wordArray=explode(' ', $w3s_query);
	array_walk($w3s_wordArray, 'w3s_exclude');

	if (trim(implode('', $w3s_wordArray))!='') {
		$w3s_db_query='SELECT `url`, `title`, `lastupdate` FROM `' . TABLE . '` WHERE ';
		$w3s_where_cond='';
		foreach($w3s_wordArray as $w3s_i) {
			$w3s_i=trim($w3s_i); $w3s_where_cond.='`text` ';
			if (substr($w3s_i, 0, 1)=='!') { $w3s_where_cond.='NOT '; $w3s_i=substr($w3s_i, 1); }
			$w3s_where_cond.='LIKE \'%' . @mysql_real_escape_string($w3s_i) . '%\' AND ';
		}
		$w3s_where_cond=substr($w3s_where_cond, 0, -4);
		$w3s_count_query=$w3s_db_query . $w3s_where_cond;

		if (!isset($_GET['page'])) {
			$w3s_page=0;
		} else	$w3s_page=intval($_GET['page'])-1;

		$w3s_db_query.=$w3s_where_cond . 'LIMIT ' . ($w3s_page * RESULTS_PER_PAGE) . ', ' . RESULTS_PER_PAGE;

		$w3s_count_query=str_replace('`url`, `title`, `lastupdate`', 'count(`url`) as `count`', $w3s_count_query);

		$w3s_res=@mysql_query($w3s_count_query); $w3s_results=@mysql_fetch_row($w3s_res); @mysql_free_result($w3s_res);

		$w3s_res=@mysql_query($w3s_db_query);
		while($w3s_item=@mysql_fetch_assoc($w3s_res)) {
			require SEARCH_INCLUDES . '/search_item.php';
		}
		@mysql_free_result($w3s_res);

		if (!$w3s_results[0]) echo '<p>Результатов по Вашему запросу не найдено</p>';
		else require_once SEARCH_INCLUDES . '/search_pages.php';
	} else echo '<p>Запрос пуст</p>';

	echo '<div class="copyright">Поиск по сайту &copy;2006 — <a href="http://evgeny.neverov.name/" target="_blank">Неверов Евгений</a></div>';

	require_once FOOTER;
?>