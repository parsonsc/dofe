<?php /* Smarty version 2.6.28, created on 2015-08-27 13:59:12
         compiled from register.html */ ?>

            <div class="main_content form_content" id="mainContent">       
                <section class="">
                    <div class="inner_content small_container">
  

    <form enctype="multipart/form-data" method="post" name="registerUser"
        id="adduser" class="user-forms" action="<?php echo $this->_tpl_vars['formurl']; ?>
">
       
        <div class="form-item input-row oterh-row">
            <label for="title" class="inline">Title</label>
            <div class="selectStyle">
                <select name="title" id="title" class="input-text styled2 border <?php if ($this->_tpl_vars['Errors']['title']['message'] != ''): ?>error<?php endif; ?>"  validate="" >
                    <option value=""></option>
                    <option value="Mr" <?php if ($this->_tpl_vars['Post']['title'] == 'Mr'): ?> selected="selected"<?php endif; ?>>Mr</option>
                    <option value="Mrs" <?php if ($this->_tpl_vars['Post']['title'] == 'Mrs'): ?> selected="selected"<?php endif; ?>>Mrs</option>
                    <option value="Miss" <?php if ($this->_tpl_vars['Post']['title'] == 'Miss'): ?> selected="selected"<?php endif; ?>>Miss</option>
                    <option value="Ms" <?php if ($this->_tpl_vars['Post']['title'] == 'Ms'): ?> selected="selected"<?php endif; ?>>Ms</option>
                    <option value="Dr" <?php if ($this->_tpl_vars['Post']['title'] == 'Dr'): ?> selected="selected"<?php endif; ?>>Dr</option>
                    <option value="Other" <?php if ($this->_tpl_vars['Post']['title'] == 'Other'): ?> selected="selected"<?php endif; ?>>Other</option>
                </select>
            </div>
            <span class="other"><input type="text" name="other_title" id="other_title"  value="<?php echo $this->_tpl_vars['Post']['other_title']; ?>
" class="input-text <?php if ($this->_tpl_vars['Errors']['other_title']['message'] != ''): ?>error<?php endif; ?>" /></span>            
            <span class="error"><?php if ($this->_tpl_vars['Errors']['title']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['title']['message']; ?>
<?php endif; ?></span>                
        </div>
        <div class="form-item input-row">
            <label for="firstname" class="inline">First name</label>
            <input type="text" name="firstname" id="firstname" value="<?php echo $this->_tpl_vars['Post']['firstname']; ?>
" class="input-text <?php if ($this->_tpl_vars['Errors']['firstname']['message'] != ''): ?>error<?php endif; ?>"  validate=""  />
                 
                <span class="error"><?php if ($this->_tpl_vars['Errors']['firstname']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['firstname']['message']; ?>
<?php endif; ?></span>
                            
        </div>
        <div class="form-item input-row">
            <label for="lastname" class="inline">Last name</label>
            <input type="text" name="lastname" id="lastname" value="<?php echo $this->_tpl_vars['Post']['lastname']; ?>
" class="input-text <?php if ($this->_tpl_vars['Errors']['lastname']['message'] != ''): ?>error<?php endif; ?>"  validate="" />
                 
                <span class="error"><?php if ($this->_tpl_vars['Errors']['lastname']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['lastname']['message']; ?>
<?php endif; ?></span>
                             
        </div>

        <div class="form-item input-row">
            <label for="address" class="inline">Address line 1</label>
            <input type="text" name="address" id="address" value="<?php echo $this->_tpl_vars['Post']['address']; ?>
" class="input-text <?php if ($this->_tpl_vars['Errors']['address']['message'] != ''): ?>error<?php endif; ?>"  validate=""  />
                 
                <span class="error"><?php if ($this->_tpl_vars['Errors']['address']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['address']['message']; ?>
<?php endif; ?></span>
                            
        </div>
        <!--div class="form-item input-row">
            <label for="address2" class="inline">Address line 2</label>
            <input type="text" name="address2" id="address2" value="<?php echo $this->_tpl_vars['Post']['address2']; ?>
" class="input-text <?php if ($this->_tpl_vars['Errors']['address2']['message'] != ''): ?>error<?php endif; ?>"  />
        </div -->
        <div class="form-item input-row">
            <label for="town" class="inline">Town or City</label>
            <input type="text" name="town" id="town" value="<?php echo $this->_tpl_vars['Post']['town']; ?>
" class="input-text <?php if ($this->_tpl_vars['Errors']['town']['message'] != ''): ?>error<?php endif; ?>"  validate=""  />
                
                <span class="error"><?php if ($this->_tpl_vars['Errors']['town']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['town']['message']; ?>
<?php endif; ?></span>
                              
        </div>
        <div class="form-item input-row">
            <label for="country" class="inline">Country</label>  
            <div class="selectStyle">      
                <select id="country" name="country" class="input-text styled2 border <?php if ($this->_tpl_vars['Errors']['country']['message'] != ''): ?>error<?php endif; ?>"  validate="">
            <?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['country']):
?>
                <option value="<?php echo $this->_tpl_vars['country']->name; ?>
" 
                    <?php if ($this->_tpl_vars['country']->name == $this->_tpl_vars['Post']['country']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['country']->name; ?>
</option>
            <?php endforeach; endif; unset($_from); ?> 
                </select>
            </div>
                
                <span class="error"><?php if ($this->_tpl_vars['Errors']['country']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['country']['message']; ?>
<?php endif; ?> </span>
                          
        </div>         
        <div class="form-item input-row">
            <label for="postcode" class="inline">Postcode</label>
            <input type="text" name="postcode" id="postcode" value="<?php echo $this->_tpl_vars['Post']['postcode']; ?>
" class="input-text <?php if ($this->_tpl_vars['Errors']['postcode']['message'] != ''): ?>error<?php endif; ?>"  validate="" />
                  
                <span class="error"><?php if ($this->_tpl_vars['Errors']['postcode']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['postcode']['message']; ?>
<?php endif; ?>  </span>
                          
        </div>       
    
        <div class="form-item input-row">
            <label for="email" class="inline">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $this->_tpl_vars['Post']['email']; ?>
" class="input-text <?php if ($this->_tpl_vars['Errors']['email']['message'] != ''): ?>error<?php endif; ?>"  validate=""  />
            
            <span class="error"><?php if ($this->_tpl_vars['Errors']['email']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['email']['message']; ?>
<?php endif; ?></span>
            
        </div>
        <div class="form-item input-row ">
            <label for="password" class="inline label">Password</label>
            <input type="password" name="password" id="password" value="<?php echo $this->_tpl_vars['Post']['password']; ?>
" class="input-text password <?php if ($this->_tpl_vars['Errors']['password']['message'] != ''): ?>error<?php endif; ?>"  validate=""  />
                  
                <span class="error"><?php if ($this->_tpl_vars['Errors']['password']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['password']['message']; ?>
<?php endif; ?> </span>
                        
        </div>
        <div class="form-item terms customCheckbox">
            <input id="check1" type="checkbox" name="tsandcs" class="checkboxLabel main_street_input <?php if ($this->_tpl_vars['Errors']['tsandcs']['message'] != ''): ?>error<?php endif; ?>" id="jgoptinyes2" value="1" <?php if ($this->_tpl_vars['Post']['tsandcs'] == 1): ?>checked="checked"<?php endif; ?> validate="required:true" />
            <label for="check1" class="label <?php if ($this->_tpl_vars['Errors']['tsandcs']['message'] != ''): ?>error<?php endif; ?>"><span class="hint" data-title=""></span><p> I&rsquo;ve read and accept the <a href="https://www.justgiving.com/info/terms-of-service">Terms &amp; Conditions</a></p></label>
                  
             <span class="error"><?php if ($this->_tpl_vars['Errors']['tsandcs']['message'] != ''): ?>Sorry! You must accept the terms and conditons. <?php endif; ?>  </span>
                
        </div>


        <input type="hidden" name="createpage" id="createpageyes" value="1" />
        <input type="hidden" name="haveaccount" id="haveaccountno" value="0" /> 
        <div class="form-item input-row centered">
            <button class="button submit site_cta" title="Complete sign up" type="submit">
                Take the challenge 
            </button>
        </div>
        <p class="form-submit">
            <input name="action" type="hidden" id="action" value="adduser" />
            <input type="hidden" name="formName" value="register" />
        </p><!-- .form-submit -->

		<?php echo $this->_tpl_vars['nonce']; ?>

       
    </form>                    </div>
                </section>

                               
            </div>