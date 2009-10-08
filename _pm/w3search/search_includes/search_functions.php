<?
	function w3s_mdate($timestamp) {
		$m[1]='января';
		$m[2]='февраля';
		$m[3]='марта';
		$m[4]='апреля';
		$m[5]='мая';
		$m[6]='июня';
		$m[7]='июля';
		$m[8]='августа';
		$m[9]='сентября';
		$m[10]='октября';
		$m[11]='ноября';
		$m[12]='декабря';

		return date('j', $timestamp) . ' ' . $m[date('n', $timestamp)] . ' ' . date('Y') . ' года, ' . date('G:i:s', $timestamp);
	}

	function w3s_addToBlacklist($url) {
		global $doNotUploadURLs;

		if (file_exists(SEARCH_INCLUDES . '/blacklist.conf')) @chmod('blacklist.conf', 0777);
		$f=@fopen('blacklist.conf', 'a+');
		@fwrite($f, $url . "\r\n"); @fclose($f);

		@chmod('blacklist.conf', 0777);

		return TRUE;
	}

	function w3s_exclude(&$item1, $key) {
		$x=0;
		if (strlen($item1)<3) { $x=1; $item1=''; }
		if (strval(intval($item1))==$item1) { $x=1; $item1=''; }
		if (in_array($item1, $GLOBALS['stopWords'])) { $x=1; $item=''; }
		if ($x!=1) $item1=' ' . trim($item1) . ' ';
	}

	function w3s_getExistURLsList() {
		if (isset($GLOBALS['existURLs'])) return $GLOBALS['existURLs'];

		$GLOBALS['existURLs']=array();
		$res=@mysql_query("SELECT `url` FROM `" . TABLE . "` WHERE 1");
		while ($row=@mysql_fetch_row($res))
			$GLOBALS['existURLs'][]=$row[0];
		@mysql_fetch_array($res);

		return $GLOBALS['existURLs'];
	}

	function w3s_getDoNotUploadURLsList() {
		if (isset($GLOBALS['doNotUploadURLs'])) return $GLOBALS['doNotUploadURLs'];

		$GLOBALS['doNotUploadURLs']=@file(SEARCH_INCLUDES . '/blacklist.conf');
		array_walk($GLOBALS['doNotUploadURLs'], create_function('&$item', '$item=str_replace("\r", \'\', $item); $item=str_replace("\n", \'\', $item);'));

		return $GLOBALS['doNotUploadURLs'];
	}

	function w3s_indexURL($url, $ref='', $md5='') {
		$existURLs=w3s_getExistURLsList();
		$doNotUploadURLs=w3s_getDoNotUploadURLsList();

		$ret=w3s_index($url, $ref, $md5);

		if ($ret['number']==200 and $ret['action']=='update') {
			$retval='added';
			$res=@mysql_query("SELECT `text` FROM `" . TABLE . "` WHERE `url`='" . @mysql_real_escape_string($url) . "'");
			$p=@mysql_fetch_row($res); @mysql_free_result($res);
			if (isset($p[0])) {
				$res=@mysql_query("UPDATE `" . TABLE . "` SET `text`='" . @mysql_real_escape_string($ret['string']) . "', `md5`='" . $ret['md5'] . "', `lastupdate`='" . gmdate('U') . "', `title`='" . @mysql_real_escape_string($ret['title']) . "' WHERE `url`='" . @mysql_real_escape_string($url) . "'");
			} else {
				$res=@mysql_query("INSERT INTO `" . TABLE . "` (`text`, `md5`, `lastupdate`, `title`, `url`) VALUES ('" . @mysql_real_escape_string($ret['string']) . "', '" . $ret['md5'] . "', '" . gmdate('U') . "', '" . @mysql_real_escape_string($ret['title']) . "', '" . @mysql_real_escape_string($url) . "');");
			}
			@mysql_free_result($res);

			$query='INSERT INTO `' . TABLE . '` ( `url`, `referrer`, `md5`, `lastupdate` ) VALUES '; $insertedRows=0;
			foreach($ret['links'] as $k) {
				if (!@in_array($k, $existURLs) and !@in_array($k, $doNotUploadURLs)) {
					$query.="('" . @mysql_real_escape_string($k) . "', '" . @mysql_real_escape_string($url) . "', '', '0'), ";
					$existURLs[]=$k;
					$insertedRows++;
				}
			}

			if ($insertedRows>0) {
				$res=@mysql_query(substr($query, 0, -2));
				@mysql_free_result($res);
			}
		} else {
			$retval='not added';
			if ($ret['action']=='delete') {
				$res=@mysql_query("DELETE FROM `" . TABLE . "` WHERE `url`='" . @mysql_real_escape_string($url) . "'"); @mysql_free_result($res);

				w3s_addToBlacklist($url);
				$retval='deleted';
			}
		}

		return $retval;
	}

	function w3s_index($url, $ref, $md5) {
		global $acceptMIMETypes, $deniedExtensions, $replacementArrays;
		$rn="\r\n";

		$doNotUploadURLs=w3s_getDoNotUploadURLsList();

		$query='GET ' . $url . ' HTTP/' . HTTP . $rn . 'Referer: ' . STARTURL . $rn . 'Content-Type: application/x-www-form-urlencoded' . $rn . 'Host: ' . URLHOST . $rn . 'Accept: ' . implode(', ', $acceptMIMETypes) . $rn . 'Accept-Encoding: gzip, deflate' . $rn . 'Connection: Keep-Alive' . $rn . 'User-Agent: ' . UA . $rn . $rn;

		$errno=0; $errstr='';
		$f=fsockopen(URLHOST, 80, $errno, $errstr, 50);
		if (!$f) {
			$retVal=array(
				'number'=>$errno,
				'string'=>$errstr
			);
		} else {
			@fwrite($f, $query);
			$inputData=FALSE; $fileData=NULL; $status=NULL; $newLocation=NULL;
			$contentType=NULL; $charset=NULL;
			while(!@feof($f)) {
				$line=fgets($f, 1024*1024);
					// Читаем строку 1Mb из файла…

				$line=str_replace("\r", '', $line);
				$line=str_replace("\n", '', $line);

				if ($inputData) {
					if (!$fileData) $fileData=$line;
					else		$fileData.=$rn . $line;
				} else {
					if (strtolower(substr(strtok($line, ' '), 0, 4))=='http')
						$status=strtok(' ');

					if (strtolower(substr($line, 0, 7))=='status:')
						$status=substr($line, 7);

					if (strtolower(substr($line, 0, 9))=='location:')
						$newLocation=substr($line, 9);

					if (strtolower(substr($line, 0, 17))=='content-encoding:')
						$encoding=substr($line, 18);

					if (strtolower(strtok($line, ' '))=='content-type:') {
						$contentType=strtolower(trim(strtok(';')));
						if (strtolower(trim(strtok('=')))=='charset') {
							$charset=strtolower(strtok(';'));
						}
					}

					if (!$line) {
						$inputData=TRUE;

						if ($status==400) {
							$retVal=array(
								'number'=>400,
								'string'=>'Ошибка запроса'
							);
							break;
						}
						if ($status==401) {
							$retVal=array(
								'number'=>401,
								'string'=>'Требуется авторизация',
								'action'=>'delete'
							);
							break;
						}
						if ($status==403) {
							$retVal=array(
								'number'=>403,
								'string'=>'Доступ закрыт',
								'action'=>'delete'
							);
							break;
						}
						if ($status==404) {
							$retVal=array(
								'number'=>404,
								'string'=>'Файл не найден',
								'action'=>'delete'
							);
							break;
						}
						if ($status==406) {
							$retVal=array(
								'number'=>406,
								'string'=>'Тип документа не соответствует разрешённым для индексации типам',
								'action'=>'delete'
							);
							break;
						}
						if ($status==410) {
							$retVal=array(
								'number'=>410,
								'string'=>'Файл навсегда удалён с сервера',
								'action'=>'delete'
							);
							break;
						}
						if ($status>500) {
							$retVal=array(
								'number'=>$status,
								'string'=>'Ошибка на стороне сервера'
							);
							if ($status!=503) $retVal['action']='delete';
							break;
						}

						if (!@in_array($contentType, $acceptMIMETypes)) {
							$retVal=array(
								'number'=>406,
								'string'=>'Тип документа не соответствует разрешённым для индексации типам',
								'action'=>'delete'
							);
							break;
						}

						if (!empty($newLocation)) {
							if (strtolower(substr($newLocation, 0, 4))=='http') {
								$retVal=array(
									'number'=>601,
									'string'=>'Перенаправление на другой домен',
									'action'=>'delete'
								);
							} else {
								if (substr($newLocation, 0, 1)!='/') $newLocation=dirname($url) . '/' . $newLocation;

								return w3s_index($newLocation, $url, $md5);
							}
							break;
						}
					}
				}
			}
			@fclose($f);

			if (isset($encoding) and $encoding=='deflate' and function_exists('gzinflate'))
				$fileData=@gzinflate($fileData);

			$retVal=array(
				'number'=>200,
				'md5'=>md5(@$fileData)
			);

			if ($md5!=$retVal['md5']) {
				$retVal['action']='update';

				// Ищем Charset в тегах <meta>
				$ret=''; $mask='#<meta http-equiv="content-type" content="[a-zA-Z0-9\/]+;[\s]*charset=([-\w]+)#i';
				preg_match($mask, $fileData, $ret);
				if (isset($ret[1]) and !empty($ret[1])) $charset=$ret[1];

				// Перекодировываем результат ;)
				if ($charset!='windows-1251' and $charset!='win-1251') {
					if (DECODE_METHOD=='iconv') {
						$fileData=iconv(strtoupper($charset), 'WINDOWS-1251', $fileData);
					} else {
						if ($charset=='cp866') $charset='a';
						if ($charset=='koi8-r') $charset='k';
						if ($charset=='koi-8r') $charset='k';
						if ($charset=='cp-866') $charset='a';
						if ($charset=='x-cp866') $charset='a';
						if ($charset=='x-cp-866') $charset='a';
						if ($charset=='alt-866') $charset='a';
						if ($charset=='iso8859-5') $charset='i';
						if ($charset=='x-mac-cyrillic') $charset='m';

						$fileData=convert_cyr_string($charset, 'w', $fileData);
					}
				}

				// Находим заголовок страницы
				$ts=split('title>', $fileData);
				if (isset($ts[1]) and !empty($ts[1])) $title=substr($ts[1], 0, -2);

				if (USE_NOINDEX_TAG)
					$fileData=preg_replace('#<noindex>.*<\/noindex>#i', ' ', $fileData);
				$fileData=str_replace($replacementArrays['from'], $replacementArrays['to'], $fileData);

				// Вырезаем все УРЛы
				$mask='#href=(["\']{0,1})([a-zA-Zа-яА-Я0-9\.\/,\-_\?\&\=\:; ]+)\1#i';
				$ret=''; preg_match_all($mask, $fileData, $ret);
				$ret=$ret[2];

				// Удаляем повторяющиеся ссылки
				$ret=array_unique($ret);

				foreach($ret as $i=>$k) {
					if ($ret[$i]==$url) unset($ret[$i]);

					$ext=substr(@$ret[$i], strrpos(@$ret[$i], '.')+1);
					if (@in_array($ext, $deniedExtensions)) unset($ret[$i]);

					if (@in_array(@$ret[$i], $doNotUploadURLs)) unset($ret[$i]);

					// Удаляем все полные УРЛ-ы (которые содержат ://)
					if (@strpos(' ' . $ret[$i], '://')>0) unset($ret[$i]);
					if (isset($ret[$i]) and substr($ret[$i], 0, 1)!='/') $ret[$i]=dirname($url) . $ret[$i];
					if (isset($ret[$i])) $ret[$i]=str_replace('\\', '/', $ret[$i]);
				}

				// Избавляемся от всего лишнего в тексте
				$fileData=strtolower($fileData);
				$fileData=str_replace(array('<br />', '<br>', '<br/>', '<p>', '</p>'), ' ', $fileData);
				$fileData=str_replace(array('<td', '<tr', '<th', '<li'), array(' <td', ' <tr', ' <th', ' <li'), $fileData);
				$fileData=strip_tags($fileData);
				$fileData=str_replace(array('nbsp', 'laquo', 'raquo', "\r", "\n"), ' ', $fileData);
				$fileData=preg_replace('([[:punct:]]+)', ' ', $fileData);
				$fileData=preg_replace('([[:space:]]+)', ' ', $fileData);

				$wordArray=explode(' ', $fileData);

				array_walk($wordArray, 'w3s_exclude');
				$wordArray=array_unique($wordArray);
/*
				$wordArray=array_count_values($wordArray);

				foreach($wordArray as $word=>$count)
					if (trim($word))
						$words[]=trim($word) . ' ' . $count;
*/
				$text=@implode(' ', $wordArray); $text=str_replace('  ', ' ', $text);

				$retVal['string']=@$text;
				$retVal['title']=@$title;
				$retVal['links']=@$ret;
				$retVal['action']='update';
			} else {
				$retVal['action']=FALSE;
			}
		}

		if (empty($retVal['string'])) $retVal['action']='delete';

		return @$retVal;
	}
?>