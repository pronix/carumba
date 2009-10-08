<?php /* Smarty version 2.6.14, created on 2007-05-24 03:36:33
         compiled from menu.html */ ?>
<table class="tblup" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tbody><tr>
    <td width="202"><img src="/images/carumba2.gif" height="44" width="202"></td>
    <td><table class="tblin" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="5" height="44" width="100%">
        <tbody><tr bgcolor="#dbdbdb"> 
        <?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m']):
?>
            <td width="25%">
            <?php if ($this->_tpl_vars['topic']['name'] == $this->_tpl_vars['m']['url']): ?>
                <strong><?php echo $this->_tpl_vars['m']['name']; ?>
</strong><br>
            <?php else: ?>
                <a href="/task/<?php echo $this->_tpl_vars['m']['url']; ?>
/"><?php echo $this->_tpl_vars['m']['name']; ?>
</a><br>
            <?php endif; ?>
                <?php echo $this->_tpl_vars['m']['count']; ?>
 
            <?php if ($this->_tpl_vars['m']['newcount']): ?>    
                (<strong>+<?php echo $this->_tpl_vars['m']['newcount']; ?>
</strong>)
            <?php endif; ?>
            </td>
        <?php endforeach; endif; unset($_from); ?>        
         </tr>
      </tbody></table></td>
  </tr>
</tbody></table>
<br>
