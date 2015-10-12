<?php /* Smarty version 2.6.28, created on 2015-07-17 15:36:49
         compiled from page-notcomplete.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_R', 'page-notcomplete.html', 3, false),)), $this); ?>
<div class="thank_you">
     <h2>Not Complete</h2>   
    <?php echo print_R($this->_tpl_vars['nonce']); ?>

    <?php echo print_R($this->_tpl_vars['page']); ?>

    <?php echo print_R($this->_tpl_vars['donations']); ?>

    <?php echo print_R($this->_tpl_vars['templateurl']); ?>
 
    
    <?php echo print_R($this->_tpl_vars['Get']); ?>
  
    <?php echo print_R($this->_tpl_vars['Post']); ?>

    <?php echo print_R($this->_tpl_vars['Errors']); ?>

    <?php echo print_R($this->_tpl_vars['Session']); ?>


</div>    