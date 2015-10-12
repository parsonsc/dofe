<?php /* Smarty version 2.6.28, created on 2015-07-20 15:28:30
         compiled from event-date-thanks.html */ ?>
    <section class="create_account">
        <header class="sign_up_banner">
            <div class="inner_content">

            </div>
        </header>
        <div class="inner_content">
            <!-- <p>You're logged in to JustGiving as <?php echo $this->_tpl_vars['Session']['email']; ?>
. Now create your page</p> -->
            <p class="log_out">Not <?php echo $this->_tpl_vars['Session']['email']; ?>
? Click <a href="<?php  echo get_permalink($this->get_template_vars('logout')); ?>">here</a> to logout.</p>

            <h2>Thanks</h2>
        </div>
    </section>