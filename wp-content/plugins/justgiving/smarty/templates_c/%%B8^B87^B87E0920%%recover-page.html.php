<?php /* Smarty version 2.6.28, created on 2015-08-27 12:23:35
         compiled from recover-page.html */ ?>
            <div class="main_content form_content" id="mainContent">       
                <section class="">
                    <div class="inner_content small_container">
            <h2>I forgot My password</h2>
            <p>No worries. Just give us your email and we'll send it to you.</p>
      
        <?php if ($this->_tpl_vars['message'] != ''): ?>
        <p class="warning"><?php echo $this->_tpl_vars['message']; ?>
</p>
        <?php endif; ?>
        <form enctype="multipart/form-data" method="post" id="recover_password" name="recoverPassword" class="user-forms" action="<?php echo $this->_tpl_vars['formurl']; ?>
">            
            <div class="form-item input-row">
                <label for="username_email">Email address*</label>
                <input name="username_email" type="email" id="username_email" value="<?php echo $this->_tpl_vars['username_email']; ?>
" class="input-text" validate="required:true" />
                <span class="error"><?php if ($this->_tpl_vars['Errors']['username_email']['message'] != ''): ?><?php echo $this->_tpl_vars['errorMark']; ?>
<?php endif; ?></span>
            </div>
            <div class="form-item input-row centered">
                <button class="button submit site_cta" title="Get New Password" type="submit" name="recover_password" id="recover_password">
                    Send
                </button>
                <input name="action" type="hidden" id="action" value="recover_password" />
                <?php echo $this->_tpl_vars['nonce']; ?>

            </div>

            <div class="forgot-links">
                <?php if ($this->_tpl_vars['loginurl'] != ''): ?>
                <a class="back-home" href="<?php echo $this->_tpl_vars['loginurl']; ?>
">Return to log in page</a>
                <?php endif; ?>
            </div>               
        </form>
        <?php if ($this->_tpl_vars['message2'] != ''): ?>
            <?php echo $this->_tpl_vars['message2']; ?>

        <?php endif; ?>
        <?php if ($this->_tpl_vars['message3'] != ''): ?>
            <?php echo $this->_tpl_vars['message3']; ?>

        <?php endif; ?>
                    </div>
                </section>

                               
            </div>