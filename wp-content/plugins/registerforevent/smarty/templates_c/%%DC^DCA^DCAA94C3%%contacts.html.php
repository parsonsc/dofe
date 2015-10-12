<?php /* Smarty version 2.6.28, created on 2014-09-15 10:18:28
         compiled from contacts.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'contacts.html', 1, false),array('function', 'math', 'contacts.html', 2, false),)), $this); ?>
<?php $this->assign('listsum', count($this->_tpl_vars['teams'])); ?> 
<?php echo smarty_function_math(array('equation' => "x * y",'x' => $this->_tpl_vars['listsum'],'y' => ".5",'assign' => 'itemscol'), $this);?>

<div class="contact_column first_column">
    <ul>
    <?php 
        $y = 0;
     ?>     
        <?php $_from = $this->_tpl_vars['teams']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['myloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['myloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['v']):
        $this->_foreach['myloop']['iteration']++;
?> 
            <?php 
                $y++;
             ?>
            <?php if ((1 & ($this->_tpl_vars['i']++ / 1))): ?> 
                <?php $this->assign('CellCSS', ''); ?> 
            <?php else: ?> 
                <?php $this->assign('CellCSS', 'zebra'); ?> 
            <?php endif; ?>
        <li class="<?php echo $this->_tpl_vars['CellCSS']; ?>
"> 
            <p><?php echo $this->_tpl_vars['v']['teamname']; ?>
</p>
            <?php if ($this->_tpl_vars['v']['teamfbpage'] != ''): ?>
            <div class="social_contact facebook_contact">
               <a href="<?php echo $this->_tpl_vars['v']['teamfbpage']; ?>
">
                   <img src="<?php echo $this->_tpl_vars['templateurl']; ?>
/images/layout/social_box.png" alt="Facebook" title="Facebook" width="20">
               </a>
            </div>
            <?php endif; ?>
            
            <?php if ($this->_tpl_vars['v']['teamtwpage'] != ''): ?>
            <div class="social_contact twitter_contact">
               <a href="<?php echo $this->_tpl_vars['v']['teamtwpage']; ?>
" >
                   <img src="<?php echo $this->_tpl_vars['templateurl']; ?>
/images/layout/social_box.png" alt="Twitter" title="Twitter" width="20">
               </a>
            </div>
            <?php endif; ?>
   
        </li>       
            <?php 
                if($y == ceil(count($this->_tpl_vars['teams'])/2)){
                    echo '</ul></div><div class="contact_column last-column"><ul>';
                    $this->assign('i',0);
                }
             ?>
        <?php endforeach; endif; unset($_from); ?> 
    </ul>
</div>