<?php /* Smarty version 2.6.28, created on 2014-09-17 08:59:14
         compiled from top5teamsbymembers.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'ucfirst', 'top5teamsbymembers.html', 10, false),)), $this); ?>
<table class="top5teams"> 
    <thead> 
        <tr> 
            <th colspan="2"><span>Simply the Best:Today&rsquo;s Top 5 Teams</span></th>  
        </tr> 
    </thead> 
    <tbody>
<?php $_from = $this->_tpl_vars['teams']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
        <tr>
            <td class="teamname"><?php echo ucfirst($this->_tpl_vars['v']['teamname']); ?>
</td>
            <td class="members"><?php if ($this->_tpl_vars['v']['numMembers'] > 0): ?><?php echo $this->_tpl_vars['v']['numMembers']; ?>
<?php else: ?>0<?php endif; ?> <?php if ($this->_tpl_vars['v']['numMembers'] == 1): ?><?php else: ?><?php endif; ?></td>
        </tr>
<?php endforeach; endif; unset($_from); ?> 
    </tbody> 
</table>                        