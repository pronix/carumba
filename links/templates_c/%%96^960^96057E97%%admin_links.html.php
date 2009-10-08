<?php /* Smarty version 2.6.14, created on 2007-05-23 17:14:48
         compiled from admin_links.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'admin_links.html', 69, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Каталог ссылок проекта Карумба.Ру</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link href="/css/links_admin.css" rel="stylesheet" type="text/css">
<script src="/links/admin_links.js" type="text/javascript"></script>
</head>
<body bgcolor="#f6f6f7">
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr>
    <td><strong>Каталог ссылок проекта Карумба.Ру</strong></td>
    <td width="25"><a href="/?cmd=logout">Выход</a></td>
  </tr>
</tbody></table><br>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin_menu.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['message'] != ''): ?>
<table class="tblup" bgcolor="White" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td colspan="2"><strong><?php echo $this->_tpl_vars['message']; ?>
</strong></td>
  </tr>
</table>
<br>
<?php endif; ?>

<table class="tblup" bgcolor="White" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td colspan="2">
    <a href="/links/">Каталог ссылок</a> /
    <?php echo $this->_tpl_vars['sCategory']; ?>

    </td>
  </tr>
</table>
<br>

<form action="" method="post">
<input type="hidden" name="action" id="action" value="">
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr>
    <td>Отмеченные:
    <a href="javascript:pub();">Показать</a> |
    <a href="javascript:hide();">Скрыть</a> |
    <a href="javascript:del();">Удалить</a> |
    <a href="javascript:move();">Перенести в</a>:
    <select name="catid" class="input03">
    <?php $_from = $this->_tpl_vars['aCategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?>
        <option value="<?php echo $this->_tpl_vars['c']['sID']; ?>
"<?php if ($this->_tpl_vars['c']['sID'] == $this->_tpl_vars['cid']): ?> selected="true"<?php endif; ?>><?php echo $this->_tpl_vars['c']['Title']; ?>
</option>
    <?php endforeach; endif; unset($_from); ?></select>
    </td>
  </tr>
</tbody></table>
<br>

<table class="tbl" bgcolor="#c6c6c6" border="0" cellpadding="5" cellspacing="1" width="100%">
  <tbody><tr class="thead">
    <td width="20">&nbsp;</td>
    <td width="50">Дата</td>
    <td>URL</td>
    <td>Название</td>
    <td>Описание</td>
    <td>Страница со ссылкой</td>
    <td>E-mail</td>
    <td width="70">Статус</td>
    <td width="100">Редактировать</td>
  </tr>
  <?php $_from = $this->_tpl_vars['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['l']):
?>
  <tr bgcolor="<?php echo smarty_function_cycle(array('values' => "#ffffff,#f6f6f6"), $this);?>
">
    <td><input type="checkbox" name="change[]" value="<?php echo $this->_tpl_vars['l']['id']; ?>
"></td>
    <td><?php echo $this->_tpl_vars['l']['date']; ?>
</td>
    <td><a href="<?php echo $this->_tpl_vars['l']['url']; ?>
" target="_blank" id="url<?php echo $this->_tpl_vars['l']['id']; ?>
"><?php echo $this->_tpl_vars['l']['url']; ?>
</a></td>
    <td id="title<?php echo $this->_tpl_vars['l']['id']; ?>
"><?php echo $this->_tpl_vars['l']['title']; ?>
</td>
    <td id="text<?php echo $this->_tpl_vars['l']['id']; ?>
"><?php echo $this->_tpl_vars['l']['text']; ?>
</td>
    <td><a href="<?php echo $this->_tpl_vars['l']['referer']; ?>
" target="_blank" id="referer<?php echo $this->_tpl_vars['l']['id']; ?>
">Проверить</a></td>
    <td id="email<?php echo $this->_tpl_vars['l']['id']; ?>
"><?php echo $this->_tpl_vars['l']['email']; ?>
</td>
    <td><?php if ($this->_tpl_vars['l']['public'] == 1): ?>Опубликован
        <?php elseif ($this->_tpl_vars['l']['public'] == 2): ?><font color="Green">Новый</font>
        <?php else: ?><font color="Red">Скрыт</font><?php endif; ?></td>
    <td><a href="javascript:edit(<?php echo $this->_tpl_vars['l']['id']; ?>
)">Правка</a></td>
  </tr>
  <?php endforeach; endif; unset($_from); ?>
</tbody></table>
</form>
<br>

<?php if ($this->_tpl_vars['pageCont'] != ''): ?>
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr>
    <td>Страницы: <?php echo $this->_tpl_vars['pageCont']; ?>
</td>
  </tr>
</tbody></table>
<br>
<?php endif; ?>

<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr>
    <td>Отмеченные:
    <a href="javascript:pub();">Показать</a> |
    <a href="javascript:hide();">Скрыть</a> |
    <a href="javascript:del();">Удалить</a>
    </td>
  </tr>
</tbody></table>
<br>

<form method="POST" action="">
<input type="hidden" name="action" id="actionForm" value="">
<table>
<tr>
<td>
<table class="tbl" bgcolor="#c6c6c6" border="0" cellpadding="5" cellspacing="1" width="500">
  <tbody>
  <tr bgcolor="#ffffff">
    <td> URL*:<br />
         (вместе с http://)</td>
    <td>
      <input name="url" id="url" class="input" type="text" value="<?php if ($this->_tpl_vars['form']['url']):  echo $this->_tpl_vars['form']['url'];  else: ?>http://<?php endif; ?>">
      </td>
  </tr>
  <tr bgcolor="#f6f6f6">
    <td> Название*:</td>
    <td>
      <input name="title" id="title" class="input" type="text" value="<?php echo $this->_tpl_vars['form']['title']; ?>
">
      </td>
  </tr>
  <tr bgcolor="#ffffff">
    <td width="120"> Категория*:</td>
    <td>
      <select name="cat" id="cat" class="widesel">
        <?php $_from = $this->_tpl_vars['aCategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?>
            <option value="<?php echo $this->_tpl_vars['c']['sID']; ?>
"<?php if ($this->_tpl_vars['c']['sID'] == $this->_tpl_vars['cid']): ?> selected="true"<?php endif; ?>><?php echo $this->_tpl_vars['c']['Title']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
      </select>
      </td>
  </tr>
  <tr bgcolor="#f6f6f6">
    <td> Страница со ссылкой*:<br />
         (вместе с http://)</td>
    <td>
      <input name="referer" id="referer" class="input" type="text" value="<?php if ($this->_tpl_vars['form']['referer']):  echo $this->_tpl_vars['form']['referer'];  else: ?>http://<?php endif; ?>">
      </td>
  </tr>
  <tr bgcolor="#ffffff">
    <td> Email:</td>
    <td>
      <input name="email" id="email" class="input" type="text" value="<?php echo $this->_tpl_vars['form']['email']; ?>
">
      </td>
  </tr>
  <tr bgcolor="#f6f6f6">
    <td> Описание*:</td>
    <td><textarea name="text" rows="10" type="text" id="text" class="textarea"><?php echo $this->_tpl_vars['form']['text']; ?>
</textarea></td>
  </tr>
  <tr bgcolor="#dbdbdb">
    <td><strong>&nbsp;</strong></td>
    <td><input type="submit" value="Добавить/Изменить" id="submit" class="input03">
    <input type="button" id="clear" value="Очистить" class="input03">
    </td>
  </tr>
</tbody></table>
</td>
<td valign="top">
* - поля, обязательные для заполнения.<br />
На правах администратора Email можно не указывать.
</td>
</tr>
</table>
</form>
<br>

</body></html>