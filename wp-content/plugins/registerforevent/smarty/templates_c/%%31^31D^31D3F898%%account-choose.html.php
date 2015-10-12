<?php /* Smarty version 2.6.28, created on 2015-03-18 09:27:31
         compiled from account-choose.html */ ?>
<div class="logIn-box"> 
    <header class="sign_up_banner">
        <h3>Already got a JustGiving Account? - Login <br/>Not got one yet? - Register</h3>
    </header>
    <div class="inner_content">   
    	<form action="<?php echo $this->_tpl_vars['formurl']; ?>
" method="post" class="user-forms" id="login_form" name="loginForm">
        	<div class="login">
        		<div class="col-xs-* col-xs-12">
                    <div class="form-item input-row">
                        <label for="choose">Choose</label>
                            <!-- <label><input type="radio" name="choose" id="choose"  value="login" validate="required:true">Login</label>
                            <label><input type="radio" name="choose" id="choose"  value="register">Register</label> -->


                            <input type="radio" name="choose" id="login"  value="login" validate="required:true"/>
                            <label for="login"><span class="hint" data-title=""></span>Login</label>

                            <input type="radio" name="choose" id="register"  value="register"/>
                            <label for="register"><span data-title=""></span>Register</label>
                    </div>
                </div>
                <div class="container login">
                    <div class="row">
                        <div class="col-xs-* col-xs-12 fieldset-submit water-line-bottom"> 
                            <div class="form-item input-row centered">        
                                <button title="Go" class="button submit site_cta" type="submit">Go</button>
                                <input type="hidden" name="action" value="account-choose" />
                                <input type="hidden" name="button" value="<?php echo $this->_tpl_vars['submit']; ?>
" />
                                <input type="hidden" name="formName" value="login" />
                                <?php echo $this->_tpl_vars['nonce']; ?>

                            </div>
                        </div>
                    </div>
                </div>        
            </div>    
    	</form>
    </div>
</div> 