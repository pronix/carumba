<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
  <link rel="stylesheet" type="text/css" href="./<?=SEARCH_INCLUDES ?>/search.css" />
  <meta http-equiv="content-type" content="text/html; charset=windows-1251" />
  <title>W3Search :: Поиск</title>
 </head>
 <body>

  <!-- Здесь находится код заголовка страницы -->

  <form action="search.php" method="get" accept-charset="windows-1251">
   <nobr>
    <input type="text" name="q" value="<?=(isset($_GET['q'])?$_GET['q']:'') ?>" style="width: 90%;" />
    <input type="submit" value="Поиск!" />
   </nobr>
  </form>