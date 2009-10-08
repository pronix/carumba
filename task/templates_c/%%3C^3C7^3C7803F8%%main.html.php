<?php /* Smarty version 2.6.14, created on 2007-05-24 18:47:34
         compiled from main.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'main.html', 48, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Конференция разработчиков проекта Карумба.Ру</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link href="/css/orders.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#f6f6f7">
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr>
    <td><strong>Конференция разработчиков проекта Карумба.Ру</strong></td>
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

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "menu.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr>
    <td><?php if ($this->_tpl_vars['pages'] != ''): ?>
            <?php echo $this->_tpl_vars['pages']; ?>

        <?php else: ?>
            &nbsp;
        <?php endif; ?>
    </td>
    <td width="50"></td>
  </tr>
</tbody></table>
<br>


<table class="tbl" bgcolor="#c6c6c6" border="0" cellpadding="5" cellspacing="1" width="100%">
  <tbody><tr bgcolor="#dbdbdb">
    <td width="110"><strong>Дата последнего сообщения</strong></td>
    <td><strong>Название темы</strong></td>
    <td width="50"><strong>Ответы</strong></td>
    <td width="100"> <strong>Статус темы</strong></td>
  </tr>
  <?php $_from = $this->_tpl_vars['topic_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['t']):
?>
  <tr bgcolor="<?php if ($this->_tpl_vars['t']['new']): ?>#eeffee<?php else:  echo smarty_function_cycle(array('values' => "#f6f6f6,#ffffff"), $this); endif; ?>">
    <td><?php echo $this->_tpl_vars['t']['date']; ?>
<br><b><?php echo $this->_tpl_vars['t']['Login']; ?>
</b></td>
    <td> <a href="/task/<?php echo $this->_tpl_vars['topic']['name']; ?>
/<?php echo $this->_tpl_vars['t']['id']; ?>
"><strong><?php echo $this->_tpl_vars['t']['title']; ?>
</strong></a></td>
    <td><?php echo $this->_tpl_vars['t']['count']; ?>
 <?php if ($this->_tpl_vars['t']['newcount']): ?>(<strong>+<?php echo $this->_tpl_vars['t']['newcount']; ?>
</strong>)<?php endif; ?></td>
    <td>
    <form method="POST">
    <input type="hidden" name="action" value="select_state">
    <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['t']['id']; ?>
">
    <select name="select" class="widesel" onchange="submit(); return true;">
    <?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['m']):
?>
        <option value="<?php echo $this->_tpl_vars['k']; ?>
"<?php if ($this->_tpl_vars['k'] == $this->_tpl_vars['topic']['imp']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['m']['name']; ?>
</option>
    <?php endforeach; endif; unset($_from); ?>
      </select>
    </form>
    </td>
  </tr>
  <?php endforeach; endif; unset($_from); ?>
</tbody></table>
<br>


<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr>
    <td><?php if ($this->_tpl_vars['pages'] != ''): ?>
            <?php echo $this->_tpl_vars['pages']; ?>

        <?php else: ?>
            &nbsp;
        <?php endif; ?>
    </td>
    <td width="50"></td>
  </tr>
</tbody></table>
<br>

<form method="POST" action="">
<input type="hidden" name="action" value="addtopic">
<table class="tbl" bgcolor="#c6c6c6" border="0" cellpadding="5" cellspacing="1" width="650">
  <tbody><tr bgcolor="#f6f6f6">
    <td bgcolor="#ffffff" width="120"> Статус:</td>
    <td bgcolor="#f6f6f6">
      <select name="selecto" class="widesel">
        <?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['m']):
?>
            <option value="<?php echo $this->_tpl_vars['k']; ?>
"<?php if ($this->_tpl_vars['k'] == $this->_tpl_vars['topic']['imp']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['m']['name']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
      </select>
      </td>
  </tr>
  <tr bgcolor="#ffffff">
    <td> Заголовок:</td>
    <td>
      <input name="inputo" id="input" type="text">
      </td>
  </tr>
  <tr bgcolor="#f6f6f6">
    <td bgcolor="#ffffff"> Текст:</td>
    <td><textarea name="textarea" rows="10" type="text" id="input"></textarea></td>
  </tr>
  <tr bgcolor="#dbdbdb">
    <td><strong>&nbsp;</strong></td>
    <td><input type="submit" value="Добавить" id="submit"></td>
  </tr>
</tbody></table>
</form>
</body></html>