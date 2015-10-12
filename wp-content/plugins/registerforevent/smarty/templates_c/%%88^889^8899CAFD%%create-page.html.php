<?php /* Smarty version 2.6.28, created on 2015-04-02 15:18:05
         compiled from create-page.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'create-page.html', 33, false),array('modifier', 'date_format', 'create-page.html', 66, false),array('modifier', 'intval', 'create-page.html', 143, false),array('function', 'html_options', 'create-page.html', 64, false),)), $this); ?>
    <section class="create_account">
        <header class="sign_up_banner">
            <div class="inner_content">
                <h2>Away we go!</h2>
                <p class="title_tag">Make waves with your JustGiving page</p>
                <p class="required_message">Nearly there. Lifejackets on. You must fill in the following:</p>
            </div>
        </header>
        <div class="inner_content">
            <!-- <p>You're logged in to JustGiving as <?php echo $this->_tpl_vars['Session']['email']; ?>
. Now create your page</p> -->
            <p class="log_out">Not <?php echo $this->_tpl_vars['Session']['email']; ?>
? Click <a href="<?php  echo get_permalink(200); ?>">here</a> to logout.</p>

            <form enctype="multipart/form-data" method="post"
                  id="createpage" class="create_page user-forms " name="createPage" action="<?php echo $this->_tpl_vars['formurl']; ?>
">

                <div class="container water-bg water-line-top createpage">
                    <div class="col-xs-12">
                        <!--div class="form-item input-row">
                            <label for="selectUni">Select Your Uni <span class="hint" data-title="This is the title that will appear at the top of your JustGiving fundraising page"></span></label>
                            <div class="selectStyle">
                                <select type="text" name="jointeam" id="team" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['jointeam']['message'] != ''): ?>error<?php endif; ?>" validate="required:true" >
                                <?php $_from = $this->_tpl_vars['teams']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
                                   <option value="<?php echo $this->_tpl_vars['k']; ?>
" <?php if ($this->_tpl_vars['Post']['jointeam'] == $this->_tpl_vars['k'] && $this->_tpl_vars['Post']['jointeam'] != ''): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['v']['label']; ?>
</option>
                                <?php endforeach; endif; unset($_from); ?>                        
                                </select>                  
                            </div>
                        </div -->
                        <div class="form-item input-row page_name">
                            <label for="pageshortname" class="jg-placeholder-title">Page short name<span class="hint" data-title="This is the address of your JustGiving fundraising page ie www.justgiving.com/johnsmith"></span>
                            <p>This is the unique web address of your Just Giving fundraising page ie www.justgiving.com/johnsbrilliantpage</p></label>
                            <!-- <label class="jg-placeholder" for="pageshortname">www.justgiving.com/</label> -->

                            <input <?php if (count($this->_tpl_vars['suggestions']) > 0 && $this->_tpl_vars['errorshortname'] != ''): ?>readonly="readonly" name="rpageshortname"<?php else: ?>name="pageshortname"<?php endif; ?> type="text"  id="pageshortname" value="<?php echo $this->_tpl_vars['Post']['pageshortname']; ?>
" class="input-text jg-placeholder-input" validate="required:true" placeholder=""/>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['pageshortname']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['pageshortname']['message']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['Errors']['shortname']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['shortname']['message']; ?>
<?php endif; ?></span>
                        </div>
                        <?php if (count($this->_tpl_vars['suggestions']) > 0 && $this->_tpl_vars['errorshortname'] != ''): ?>
                        <div class="form-item input-row url-suggest">
                            <label for="rpageshortname" class="pageSuggestionTitle">Suggested alternative names:</label> 
                            <div class="url-alternatives">          
                                <select id="rpageshortname" name="pageshortname" size="4" class="pageSuggestgion border <?php if ($this->_tpl_vars['Errors']['pageshortname']['message'] != '' || $this->_tpl_vars['Errors']['shortname']['message'] != ''): ?>error<?php endif; ?>" validate="required:true">
                                    <option class="show_mobile" value="" data-teamstory="" disabled selected>Suggested alternative names</option>
                                    <?php $_from = $this->_tpl_vars['suggestions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
                                    <option  value="<?php echo $this->_tpl_vars['v']['label']; ?>
" <?php if ($this->_tpl_vars['Post']['pageshortname'] == $this->_tpl_vars['v']['label'] && $this->_tpl_vars['Post']['pageshortname'] != ''): ?> selected="selected"<?php endif; ?> ><?php echo $this->_tpl_vars['v']['label']; ?>
</option>
                                    <?php endforeach; endif; unset($_from); ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-item input-row page_title">
                            <label for="pagetitle">Page title <span class="hint" data-title="This is the title that will appear at the top of your JustGiving fundraising page"></span>
                            <p>This is the page title that people will see when they go to your Just Giving fundraising page ie John’s Brilliant Page</p></label>
                            <input type="text" name="pagetitle" id="pagetitle" value="<?php echo $this->_tpl_vars['Post']['pagetitle']; ?>
" class="input-text <?php if ($this->_tpl_vars['Errors']['pagetitle']['message'] != ''): ?>error<?php endif; ?>" placeholder="" validate="required:true" />
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['pagetitle']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['pagetitle']['message']; ?>
<?php endif; ?></span>
                        </div>

                        <div class="form-item input-row">
                            <label for="dob">What is your year of birth? <span class="hint" data-title="What is your date of birth"></span></label>
                            <div class="selectStyle">
                            <select  name="dob" id="dob" max="<?php echo $this->_tpl_vars['maxdate']; ?>
" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['dob']['message'] != ''): ?>error<?php endif; ?>" validate="required:true" >
                                <option value="default" disabled="disabled" default selected="selected">Please select one</option>
                            
		<?php if ($this->_tpl_vars['Post']['dob'] > 0): ?>
			<?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['years'],'output' => $this->_tpl_vars['years'],'selected' => $this->_tpl_vars['Post']['dob']), $this);?>

		<?php else: ?>
			<?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['years'],'output' => $this->_tpl_vars['years'],'selected' => ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y"))), $this);?>

		<?php endif; ?>
	
                            </select>
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['dob']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['dob']['message']; ?>
<?php endif; ?></span>
                        </div>



                          <div class="form-item input-row custom-select">
                            <label for="heardAbout">How did you hear about H2Only?</label>
                            <div class="selectStyle">                                
                                <select type="text" name="heardabout" id="heardAbout" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['heardabout']['message'] != ''): ?>error<?php endif; ?>" >
                                    <option value="default" disabled="disabled" default selected="selected">Please select one</option>

                                    <option value="1" <?php if ($this->_tpl_vars['Post']['heardabout'] == 1 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>TBC 1</option>
                                    <option value="2" <?php if ($this->_tpl_vars['Post']['heardabout'] == 2 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>TBC 2</option>
                                    <option value="3" <?php if ($this->_tpl_vars['Post']['heardabout'] == 3 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>TBC 3</option>
                                    <option value="4" <?php if ($this->_tpl_vars['Post']['heardabout'] == 4 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>TBC 4</option>                              
                                </select>               
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['heardabout']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['heardabout']['message']; ?>
<?php endif; ?></span>
                        </div>  
                        <div class="form-item input-row custom-select">
                            <label for="work">Where do you work?</label>
                            <div class="">                                    
                               <input type="text" name="work" id="work" maxlength="9" class="input-text border  <?php if ($this->_tpl_vars['Errors']['work']['message'] != ''): ?>error<?php endif; ?>"   value="<?php echo $this->_tpl_vars['Post']['work']; ?>
" />
                                <aside class="optional">
                                    (Optional)
                                </aside>                  
                            </div>
                        </div>         

                        <div class="form-item input-row captain">
                            <label for="">Do you want to be an H<sub>2</sub>Only Captain?
                            <p>Lead a crew and we&rsquo;ll send you the ultimate kit to help you rally your First Mates, raise more money and have even more fun. Remember, you&rsquo;ll be stronger as a crew.</p></label>
                            <input type="radio" name="advocate" id="yes"  value="1"<?php if ($this->_tpl_vars['Post']['advocate'] == 1): ?> checked="checked"<?php endif; ?> validate="required:true"/>
                            <label for="yes" class="radio_input"><span data-title=""></span>Yes</label>

                            <input type="radio" name="advocate" id="no"  value="0"<?php if ($this->_tpl_vars['Post']['advocate'] == 0 || ! isset ( $this->_tpl_vars['Post']['advocate'] )): ?> checked="checked"<?php endif; ?>/>
                            <label for="no" class="radio_input"><span data-title=""></span>No</label>
                            
                        </div>

                       
                        <div class="form-item input-row customCheckbox">
                            <label for="">I&rsquo;d like a fundraising pack sent by post please</label>
                            <input type="radio" name="packbypost" class="checkboxLabel main_street_input" id="packbypostyes" value="1"<?php if ($this->_tpl_vars['Post']['packbypost'] == 1): ?> checked="checked"<?php endif; ?>  />
                            <label for="packbypostyes" class="radio_input"><span></span>Yes</label> <!-- 1 added at the end for test -->

                            <input type="radio" name="packbypost" class="checkboxLabel main_street_input" id="packbypostno" value="0"<?php if ($this->_tpl_vars['Post']['packbypost'] == 0 || ! isset ( $this->_tpl_vars['Post']['packbypost'] )): ?> checked="checked"<?php endif; ?>  />
                            <label for="packbypostno" class="radio_input"><span></span>No</label> <!-- 1 added at the end for test -->
                        </div>
                        <!-- 
                        <div class="form-item customCheckbox">
                            <label for="">I&rsquo;d like to be an advocate</label>
                            <input type="radio" name="advocate" class="checkboxLabel main_street_input" id="advocateyes" value="1"  <?php if ($this->_tpl_vars['Post']['advocate'] == 1): ?>checked="checked"<?php endif; ?>  />
                            <label for="advocateyes"><span></span>Yes</label>    

                            <input type="radio" name="advocate" class="checkboxLabel main_street_input" id="advocateyes" value="1"  <?php if ($this->_tpl_vars['Post']['advocate'] == 1): ?>checked="checked"<?php endif; ?>  />
                            <label for="advocateyes"><span></span>No</label> 
                        </div>      -->                           

                        
                        <div class="form-item input-row customCheckbox">
                            <label for="">Stay up to date with JustGiving's news, tips and inspiring stories.</label>
                            <input type="radio" name="jgoptin" class="checkboxLabel main_street_input" id="choptinyes" value="1"<?php if ($this->_tpl_vars['Post']['jgoptin'] == 1): ?> checked="checked"<?php endif; ?>  />
                            <label for="choptinyes" class="radio_input"><span></span>Yes</label> <!-- 1 added at the end for test -->

                            <input type="radio" name="jgoptin" class="checkboxLabel main_street_input" id="choptinno" value="0"<?php if ($this->_tpl_vars['Post']['jgoptin'] == 0 || ! isset ( $this->_tpl_vars['Post']['jgoptin'] )): ?> checked="checked"<?php endif; ?>  />
                            <label for="choptinno" class="radio_input"><span></span>No</label> <!-- 1 added at the end for test -->  
                        </div>

                        <div class="form-item input-row customCheckbox">
                            <label for="">Stay up to date with RNLI&rsquo;s news about how your support is helping.</label>
                            <input type="radio" name="charityoptin" class="checkboxLabel main_street_input" id="rnlioptinyes" value="1"<?php if ($this->_tpl_vars['Post']['charityoptin'] == 1 || ! isset ( $this->_tpl_vars['Post']['charityoptin'] )): ?> checked="checked"<?php endif; ?>  />
                            <label for="rnlioptinyes" class="radio_input"><span></span>Yes</label> <!-- <?php echo ((is_array($_tmp=$this->_tpl_vars['Post']['charityoptin'])) ? $this->_run_mod_handler('intval', true, $_tmp) : intval($_tmp)); ?>
 -->     

                            <input type="radio" name="charityoptin" class="checkboxLabel main_street_input" id="rnlioptinno" value="0"<?php if ($this->_tpl_vars['Post']['charityoptin'] == 0): ?> checked="checked"<?php endif; ?>   />
                            <label for="rnlioptinno" class="radio_input"><span></span>No</label> <!-- 1 added at the end for test -->     
                        </div> 

                                      
                        <div class="form-item input-row terms customCheckbox">                        
                            <input type="checkbox" name="tsandcs" class="checkboxLabel main_street_input <?php if ($this->_tpl_vars['Errors']['tsandcs']['message'] != ''): ?>error<?php endif; ?>" id="jgoptinyes2" value="1" <?php if ($this->_tpl_vars['Post']['tsandcs'] == 1): ?>checked="checked"<?php endif; ?> validate="required:true" />                            
                            <label for="jgoptinyes2" class="label <?php if ($this->_tpl_vars['Errors']['tsandcs']['message'] != ''): ?>error<?php endif; ?>"><span class="hint" data-title=""></span><p>I&rsquo;ve read and accept the <a href="<?php  echo get_permalink(61); ?>" target="_blank">Terms &amp; Conditions</a></p></label> 
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['tsandcs']['message'] != ''): ?>You must agree to the Terms and Conditions to proceed<?php endif; ?></span>
                        </div>      

                    </div>
                </div>

                <div class="col-xs-12 fieldset-submit water-line-bottom createpage">
        	        <div class="form-item input-row centered">
                        <button class="button submit site_cta" title="Next" type="submit">
                            Complete registration
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