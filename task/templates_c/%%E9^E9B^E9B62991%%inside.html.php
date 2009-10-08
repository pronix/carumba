<?php /* Smarty version 2.6.14, created on 2007-05-24 18:47:23
         compiled from inside.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'inside.html', 57, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>Конференция разработчиков проекта Карумба.Ру</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link href="/css/orders.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="/task/script.js"></script>
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

<br>
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr>
    <td><?php if ($this->_tpl_vars['pages'] != ''): ?>
            <?php echo $this->_tpl_vars['pages']; ?>

        <?php else: ?>
            &nbsp;
        <?php endif; ?></td>
    <td width="50"><a href="#">Обновить</a></td>
  </tr>
</tbody></table>
<br>
<table class="tbl" bgcolor="#c6c6c6" border="0" cellpadding="5" cellspacing="1" width="100%">
  <tbody><tr bgcolor="#dbdbdb">
    <td bgcolor="#dbdbdb" width="110"><a href="/task/<?php echo $this->_tpl_vars['topic']['name']; ?>
"><strong><?php echo $this->_tpl_vars['menu'][$this->_tpl_vars['topic']['imp']]['name']; ?>
</strong></a></td>
    <td colspan="3" bgcolor="#dbdbdb"><strong><?php echo $this->_tpl_vars['head']['title']; ?>
</strong></td>
  </tr>
  <tr bgcolor="#ffffff">
    <td> <?php echo $this->_tpl_vars['head']['date']; ?>
<br>
      <strong><?php echo $this->_tpl_vars['head']['Login']; ?>
</strong></td>
    <td id="content<?php echo $this->_tpl_vars['head']['id']; ?>
"><?php echo $this->_tpl_vars['head']['message']; ?>
</td>
    <?php if ($this->_tpl_vars['head']['Login'] == $this->_tpl_vars['userData']['Login']): ?>
    <td width="45"><a href="javascript:edit('<?php echo $this->_tpl_vars['head']['id']; ?>
')">Правка</a></td>
    <td width="50"><a href="?action=delete&id=<?php echo $this->_tpl_vars['head']['id']; ?>
">Удалить</a></td>
    <?php else: ?>
    <td>-</td>
    <td>-</td>
    <?php endif; ?>
  </tr>


  <?php $_from = $this->_tpl_vars['topic_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['t']):
?>
  <tr bgcolor="<?php if ($this->_tpl_vars['t']['new']): ?>#eeffee<?php else:  echo smarty_function_cycle(array('values' => "#f6f6f6,#ffffff"), $this); endif; ?>">
    <td><?php echo $this->_tpl_vars['t']['date']; ?>
<br><strong><?php echo $this->_tpl_vars['t']['Login']; ?>
</strong></td>
    <td id="content<?php echo $this->_tpl_vars['t']['id']; ?>
"><?php echo $this->_tpl_vars['t']['message']; ?>
</td>
    <?php if ($this->_tpl_vars['userData']['Login'] == $this->_tpl_vars['t']['Login']): ?>
    <td><a href="javascript:edit('<?php echo $this->_tpl_vars['t']['id']; ?>
')">Правка</a></td>
    <td><a href="?action=delete&id=<?php echo $this->_tpl_vars['t']['id']; ?>
">Удалить</a></td>
    <?php else: ?>
    <td>-</td>
    <td>-</td>
    <?php endif; ?>
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
        <?php endif; ?></td>
    <td width="50"><a href="#">Обновить</a></td>
  </tr>
</tbody></table>
<br>
<form method="POST" name="edit_form" id="edit_form">
<input type="hidden" name="action" id="action" value="addreply">
<input type="hidden" name="id" id="id" value="">
<table class="tbl" bgcolor="#c6c6c6" border="0" cellpadding="5" cellspacing="1" width="650">
  <tbody><tr bgcolor="#f6f6f6">
    <td bgcolor="#ffffff" width="120"> Текст:</td>
    <td> <textarea name="textarea" rows="7" type="text" id="input"></textarea></td>
  </tr>
  <tr bgcolor="#dbdbdb">
    <td><strong>&nbsp;</strong></td>
    <td><input type="submit" id="submit" value="Добавить"> |
    <input type="button" id="submit" value="Сохранить" onclick="saveReply(); submit();"></td>
  </tr>
</tbody></table>

<iframe style="display:none;" name="actionFrame" id="actionFrame" src="about:blank"></iframe>

</form>
</body></html>