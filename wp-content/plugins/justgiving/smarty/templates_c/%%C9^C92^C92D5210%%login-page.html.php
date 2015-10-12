<?php /* Smarty version 2.6.28, created on 2015-08-27 12:17:41
         compiled from login-page.html */ ?>
            <div class="main_content form_content" id="mainContent">       
                <section class="">
                    <div class="inner_content small_container">
                        <form action="<?php echo $this->_tpl_vars['formurl']; ?>
" method="post" class="user-forms" id="login_form" name="loginForm">
                            <div class="form-item">
                                <label for="user-name">Email address*</label>
                                <input type="email" name="user-name" id="user-name" class="input-text<?php if ($this->_tpl_vars['usernameerror'] != ''): ?> error<?php endif; ?>" value="<?php echo $this->_tpl_vars['userName']; ?>
"  validate="required:true" /> 
                                <span class="error"><?php if ($this->_tpl_vars['usernameerror'] != ''): ?><?php echo $this->_tpl_vars['usernameerror']; ?>
<?php endif; ?>  </span>
                            </div>

                             <div class="form-item">
                                <label for="password">Password*</label>
                                <input type="password" name="password" id="password" class="input-text<?php if ($this->_tpl_vars['passworderror'] != ''): ?> error<?php endif; ?>"  validate="required:true" />      
                                <span class="error"><?php if ($this->_tpl_vars['passworderror'] != ''): ?><?php echo $this->_tpl_vars['passworderror']; ?>
<?php endif; ?></span>
                            </div>
                            
                             <div class="form-item">
                                <button title="Login" class="button submit site_cta" type="submit">Login</button>
                                <input type="hidden" name="action" value="log-in" />
                                <input type="hidden" name="button" value="<?php echo $this->_tpl_vars['submit']; ?>
" />
                                <input type="hidden" name="formName" value="login" />
                                <?php echo $this->_tpl_vars['nonce']; ?>

                            </div>
                        </form>
                        <?php if ($this->_tpl_vars['forgotURL'] != ''): ?>
                        <div class="forgot-pwd"><a href="<?php echo $this->_tpl_vars['forgotURL']; ?>
">Oops! I forgot my password</a></div>
                        <?php endif; ?>
                        
                        <?php if ($this->_tpl_vars['chooseURL'] != ''): ?>

                        <div class="choose-account"><a href="<?php echo $this->_tpl_vars['chooseURL']; ?>
">go back</a></div>

                        <?php endif; ?>                            
                    </div>
                </section>        
            </div>

