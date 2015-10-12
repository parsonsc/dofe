<?php /* Smarty version 2.6.28, created on 2015-07-20 15:18:06
         compiled from event-date.html */ ?>
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

            <form enctype="multipart/form-data" method="post"
                  id="createpage" class="create_page user-forms " name="createPage" action="<?php echo $this->_tpl_vars['formurl']; ?>
">

                <div class="container water-bg water-line-top createpage">
                    <div class="col-xs-12">
  

                        <h4>Event dates</h4>
                        <div class="form-item input-row">
                            <label for="doe">Start date <span class="hint" data-title="What is the date of your event"></span></label>
                            <div class="selectStyle">
                            <input type="date"  name="eventstart" id="doe" min="<?php echo $this->_tpl_vars['maxdate']; ?>
" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['eventstart']['message'] != ''): ?>error<?php endif; ?>" validate="required:true" value="<?php echo $this->_tpl_vars['Post']['eventstart']; ?>
" />
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['eventstart']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventstart']['message']; ?>
<?php endif; ?></span>
                        </div>      
                        <div class="form-item input-row">
                            <label for="eoe">End date <span class="hint" data-title="What is the date of your event"></span></label>
                            <div class="selectStyle">
                            <input type="date"  name="eventend" id="eoe" min="<?php echo $this->_tpl_vars['maxdate']; ?>
" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['eventend']['message'] != ''): ?>error<?php endif; ?>" validate="required:true" value="<?php echo $this->_tpl_vars['Post']['eventend']; ?>
" />
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['eventend']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventend']['message']; ?>
<?php endif; ?></span>
                        </div>                          

                         

                    </div>
                </div>

                <div class="col-xs-12 fieldset-submit water-line-bottom createpage">
        	        <div class="form-item input-row centered">
                        <button class="button submit site_cta" title="Next" type="submit">
                            Complete
                        </button>          
                        <p class="form-submit">
                            <input name="action" type="hidden" id="action" value="createpage" />
                            <input type="hidden" name="formName" value="createpage" />
                        </p>
                        <?php echo $this->_tpl_vars['nonce']; ?>
               
        	        </div>
                </div>
            </form>
        </div>
    </section>