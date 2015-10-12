<?php /* Smarty version 2.6.28, created on 2015-04-01 15:09:35
         compiled from login-page.html */ ?>
<div class="logIn-box">
    <header>
        <h3>Already got a JustGiving Account?<br />Sweet! Log in here.</h3>
    </header>     
	<form action="<?php echo $this->_tpl_vars['formurl']; ?>
" method="post" class="user-forms" id="login_form" name="loginForm">
	<div class="container login">
		<div class="col-xs-* col-xs-12">
            <div class="form-item input-row">
                <label for="user-name">Email address*</label>
                <input type="email" name="user-name" id="user-name" class="input-text<?php if ($this->_tpl_vars['usernameerror'] != ''): ?> error<?php endif; ?>" value="<?php echo $this->_tpl_vars['userName']; ?>
"  validate="required:true" /> 
                <span class="error"><?php if ($this->_tpl_vars['usernameerror'] != ''): ?><?php echo $this->_tpl_vars['usernameerror']; ?>
<?php endif; ?>  </span>
              
            </div>
            <div class="form-item input-row">
                <label for="password">Password*</label>
                <input type="password" name="password" id="password" class="input-text<?php if ($this->_tpl_vars['passworderror'] != ''): ?> error<?php endif; ?>"  validate="required:true" />      
<span class="error"><?php if ($this->_tpl_vars['passworderror'] != ''): ?><?php echo $this->_tpl_vars['passworderror']; ?>
<?php endif; ?></span>
            </div>        
        </div>
        <div class="container login">
            <div class="row">
                <div class="col-xs-* col-xs-12 fieldset-submit water-line-bottom"> 
                    <div class="form-item input-row centered">        
                        <button title="Login" class="button submit site_cta" type="submit">Login</button>
                        <input type="hidden" name="action" value="log-in" />
                        <input type="hidden" name="button" value="<?php echo $this->_tpl_vars['submit']; ?>
" />
                        <input type="hidden" name="formName" value="login" />
                        <?php echo $this->_tpl_vars['nonce']; ?>

                    </div>
                </div>
            </div>
        </div>        
        
        <?php if ($this->_tpl_vars['forgotURL'] != ''): ?>
        <div class="forgot-pwd"><a href="<?php echo $this->_tpl_vars['forgotURL']; ?>
">Oops! I forgot my password</a></div>
        <?php endif; ?>
        
        <?php if ($this->_tpl_vars['chooseURL'] != ''): ?>

        <!-- <div class="choose-account"><a href="<?php echo $this->_tpl_vars['chooseURL']; ?>
">go back</a></div> -->

        <?php endif; ?>        
    </div>    
	</form>
</div> 