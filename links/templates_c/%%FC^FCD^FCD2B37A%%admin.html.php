<?php /* Smarty version 2.6.14, created on 2007-05-29 01:00:10
         compiled from admin.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'admin.html', 69, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Каталог ссылок проекта Карумба.Ру</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link href="/css/links_admin.css" rel="stylesheet" type="text/css">
<script src="/links/admin.js" type="text/javascript"></script>
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

<form method="POST" action="">
<input type="hidden" name="editName" id="editName" value="">
<table class="tbl" bgcolor="#c6c6c6" border="0" cellpadding="5" cellspacing="1" width="650">
  <tbody>
  <tr bgcolor="#f6f6f6">
    <td width="60"> Название:</td>
    <td>
      <input name="inputo" id="input" type="text">
      </td>
    <td width="120">
    <input type="submit" value="Добавить/Изменить" class="button">
    </td>
    <td width="120">
    <input type="button" value="Очистить" id="clear" class="button">
    </td>
  </tr>
</tbody></table>
</form><br />

<?php if ($this->_tpl_vars['message'] != ''): ?>
<table class="tblup" bgcolor="White" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td colspan="2"><strong><?php echo $this->_tpl_vars['message']; ?>
</strong></td>
  </tr>
</table>
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

<form action="" method="post">
<input type="hidden" name="action" id="action" value="">
<table class="tbl" bgcolor="#c6c6c6" border="0" cellpadding="5" cellspacing="1" width="100%">
  <tbody><tr class="thead">
    <td width="20">&nbsp;</td>
    <td width="40">id</td>
    <td>Название раздела</td>
    <td width="100">Статус</td>
    <td width="100">Редактировать</td>
  </tr>
  <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['c']):
?>
  <tr bgcolor="<?php if ($this->_tpl_vars['c']['count_new'] > 0): ?>#eeffee<?php else:  echo smarty_function_cycle(array('values' => "#ffffff,#f6f6f6"), $this); endif; ?>">
    <td><input type="checkbox" name="change[]" value="<?php echo $this->_tpl_vars['c']['sID']; ?>
"></td>
    <td><?php echo $this->_tpl_vars['c']['sID']; ?>
</td>
    <td><a href="links.php?<?php echo $this->_tpl_vars['c']['sID']; ?>
" id="name<?php echo $this->_tpl_vars['c']['sID']; ?>
"><?php echo $this->_tpl_vars['c']['Title']; ?>
</a> (<?php echo $this->_tpl_vars['c']['count']; ?>
)</td>
    <td><?php if ($this->_tpl_vars['c']['isHidden'] == 0): ?>Опубликован<?php else: ?><font color="Red">Скрыт</font><?php endif; ?></td>
    <td><a href="javascript:edit(<?php echo $this->_tpl_vars['c']['sID']; ?>
)">Правка</a></td>
  </tr>
  <?php endforeach; endif; unset($_from); ?>
</tbody></table>
</form>
<br>

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

</body></html>