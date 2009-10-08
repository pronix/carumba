<?php /* Smarty version 2.6.14, created on 2007-05-18 00:06:28
         compiled from main.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'main.html', 77, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Комментарии к товарам проекта Карумба.Ру</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link href="/css/orders.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/comments/script.js"></script>
</head>
<body bgcolor="#f6f6f7">
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr> 
    <td><strong>Комментарии к товарам проекта Карумба.Ру</strong></td>
    <td width="25"><a href="/?cmd=logout">Выход</a></td>
  </tr>
</tbody></table><br>
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
    <td width="202"><img src="/images/carumba2.gif" height="44" width="202"></td>
    <td align="right">
            <form action="" method="GET">
            <table cellpadding="0" cellspacing="0" border="0">
            <tr>
            <td>Поиск по ID товара:<br>
            <a href="/comments/">Очистить фильтр</a></td>
            <td><input type="text" name="id" id="id" value="<?php echo $this->_tpl_vars['id']; ?>
">
            <input type="submit" value="Искать"></td>
            </tr>
            </table>
            </form>
        </td>
  </tr>
</tbody></table>
<br>


<form action="" method="POST">
<input type="hidden" name="action" id="action" value="">
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr> 
    <td align="left">
        Отмеченные:
        <a href="javascript:send('p');">Опубликовать</a> |
        <a href="javascript:send('h');">Отменить</a> |
        <a href="javascript:send('d');">Удалить</a>
        </td>
  </tr>
</table>
<br>

<?php if ($this->_tpl_vars['pages'] != ''): ?>
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr> 
    <td><?php echo $this->_tpl_vars['pages']; ?>
</td>
  </tr>
</table>
<br>
<?php endif; ?>

<table class="tbl" bgcolor="#c6c6c6" border="0" cellpadding="5" cellspacing="1" width="100%">
  <tbody><tr bgcolor="#dbdbdb"> 
    <td width="20"></td>
    <td width="110"><strong>Дата</strong></td>
    <td><strong>Краткий текст</strong></td>
    <td width="100"><strong>Редактировать</strong></td>
    <td width="100"><strong>Статус</strong></td>
  </tr>
  <?php $_from = $this->_tpl_vars['com_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['t']):
?>
  <tr bgcolor="<?php if ($this->_tpl_vars['t']->public == 0): ?>#ddffdd<?php elseif ($this->_tpl_vars['t']->public == 2): ?>#ffdddd<?php else:  echo smarty_function_cycle(array('values' => "#ffffff,#eeeeee"), $this); endif; ?>"> 
    <td><input type="checkbox" name="change[<?php echo $this->_tpl_vars['t']->cID; ?>
]" id="change[<?php echo $this->_tpl_vars['t']->cID; ?>
]" value="<?php echo $this->_tpl_vars['t']->cID; ?>
"></td>
    <td><label for="change[<?php echo $this->_tpl_vars['t']->cID; ?>
]"><?php echo $this->_tpl_vars['t']->date; ?>
<br><b><?php echo $this->_tpl_vars['t']->name; ?>
</b><br><?php echo $this->_tpl_vars['t']->email; ?>
</label></td>
    <td><p><a href="<?php echo $this->_tpl_vars['t']->path; ?>
" target="_blank"><?php echo $this->_tpl_vars['t']->Title; ?>
</a></p><?php echo $this->_tpl_vars['t']->comment; ?>
</td>
    <td><a href="javascript:edit(<?php echo $this->_tpl_vars['t']->cID; ?>
);">Правка</a></td>
    <td>
    <b><?php if ($this->_tpl_vars['t']->public == 1): ?>Опубликован<?php endif; ?>
    <?php if ($this->_tpl_vars['t']->public == 2): ?>Отменен<?php endif; ?>
    <?php if ($this->_tpl_vars['t']->public == 0): ?>Новый<?php endif; ?></b>
    </td>
  </tr>
  <?php endforeach; endif; unset($_from); ?>
</tbody></table>
<br>


<?php if ($this->_tpl_vars['pages'] != ''): ?>
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr> 
    <td><?php echo $this->_tpl_vars['pages']; ?>
</td>
  </tr>
</table>
<br>
<?php endif; ?>

<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr> 
    <td align="left">
        Отмеченные:
        <a href="javascript:send('p');">Опубликовать</a> |
        <a href="javascript:send('h');">Отменить</a> |
        <a href="javascript:send('d');">Удалить</a>
        </td>
  </tr>
</table>
<br>
</form>
<br>

</body></html>