<?php /* Smarty version 2.6.28, created on 2015-07-14 16:27:25
         compiled from create-page.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'create-page.html', 31, false),array('modifier', 'intval', 'create-page.html', 254, false),)), $this); ?>
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
" class="input-text jg-placeholder-input"  validate="required:true" placeholder=""/>
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
" class="input-text <?php if ($this->_tpl_vars['Errors']['pagetitle']['message'] != ''): ?>error<?php endif; ?>"  placeholder="" validate="required:true" />
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['pagetitle']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['pagetitle']['message']; ?>
<?php endif; ?></span>
                        </div>

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

                        <div class="form-item input-row custom-select">
                            <label for="heardAbout">Where did you hear about us?</label>
                            <div class="selectStyle">                                
                                <select type="text" name="heardabout" id="heardAbout" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['heardabout']['message'] != ''): ?>error<?php endif; ?>" >
                                    <option value="default" disabled="disabled" default selected="selected">Please select one</option>

                                    <option value="0" <?php if ($this->_tpl_vars['Post']['heardabout'] == 0 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Google</option>
                                    <option value="1" <?php if ($this->_tpl_vars['Post']['heardabout'] == 1 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>YouTube</option>
                                    <option value="2" <?php if ($this->_tpl_vars['Post']['heardabout'] == 2 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Facebook</option>
                                    <option value="3" <?php if ($this->_tpl_vars['Post']['heardabout'] == 3 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Twitter</option>
                                    <option value="4" <?php if ($this->_tpl_vars['Post']['heardabout'] == 4 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Text message</option>
                                    <option value="5" <?php if ($this->_tpl_vars['Post']['heardabout'] == 5 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Email</option>
                                    <option value="6" <?php if ($this->_tpl_vars['Post']['heardabout'] == 6 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Tube advert</option>
                                    <option value="7" <?php if ($this->_tpl_vars['Post']['heardabout'] == 7 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Weight Watchers magazine</option>
                                    <option value="8" <?php if ($this->_tpl_vars['Post']['heardabout'] == 8 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Flyer</option>
                                    <option value="9" <?php if ($this->_tpl_vars['Post']['heardabout'] == 9 && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Work colleague</option>
                                    <option value="a" <?php if ($this->_tpl_vars['Post']['heardabout'] == 'a' && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Friend / family</option>
                                    <option value="b" <?php if ($this->_tpl_vars['Post']['heardabout'] == 'b' && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Slipway Slide</option>
                                    <option value="c" <?php if ($this->_tpl_vars['Post']['heardabout'] == 'c' && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>PR event</option>
                                    <option value="d" <?php if ($this->_tpl_vars['Post']['heardabout'] == 'd' && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Newspaper article</option>
                                    <option value="e" <?php if ($this->_tpl_vars['Post']['heardabout'] == 'e' && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Celebrity ambassador</option>
                                    <option value="g" <?php if ($this->_tpl_vars['Post']['heardabout'] == 'g' && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Billboard in Cardiff</option> 
                                    <option value="f" <?php if ($this->_tpl_vars['Post']['heardabout'] == 'f' && $this->_tpl_vars['Post']['heardabout'] != ''): ?> selected="selected"<?php endif; ?>>Other</option>                               
                                </select>               
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['heardabout']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['heardabout']['message']; ?>
<?php endif; ?></span>
                        </div>  

                        <hr />
                        <div class="form-item input-row">
                            <label for="dob">What is your date of birth? <span class="hint" data-title="What is your date of birth"></span></label>
                            <div class="selectStyle">
                            <input type="date"  name="dob" id="dob" max="<?php echo $this->_tpl_vars['maxdate']; ?>
" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['dob']['message'] != ''): ?>error<?php endif; ?>" validate="required:true" value="<?php echo $this->_tpl_vars['Post']['dob']; ?>
" />
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['dob']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['dob']['message']; ?>
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

                        <div class="form-item input-row custom-select">
                            <label for="worktown">town</label>
                            <div class="">                                    
                               <input type="text" name="worktown" id="worktown" maxlength="9" class="input-text border  <?php if ($this->_tpl_vars['Errors']['worktown']['message'] != ''): ?>error<?php endif; ?>"   value="<?php echo $this->_tpl_vars['Post']['worktown']; ?>
" />
                                <aside class="optional">
                                    (Optional)
                                </aside>                  
                            </div>
                        </div>
                        
                        <div class="form-item input-row custom-select">
                            <label for="workcountry">country</label>
                            <div class="">                                    
                               <input type="text" name="workcountry" id="workcountry" maxlength="9" class="input-text border  <?php if ($this->_tpl_vars['Errors']['workcountry']['message'] != ''): ?>error<?php endif; ?>"   value="<?php echo $this->_tpl_vars['Post']['workcountry']; ?>
" />
                                <aside class="optional">
                                    (Optional)
                                </aside>                  
                            </div>
                        </div>  

                        <div class="form-item input-row custom-select">
                            <label for="workpostcode">postcode</label>
                            <div class="">                                    
                               <input type="text" name="workpostcode" id="workpostcode" maxlength="9" class="input-text border  <?php if ($this->_tpl_vars['Errors']['workpostcode']['message'] != ''): ?>error<?php endif; ?>"   value="<?php echo $this->_tpl_vars['Post']['workpostcode']; ?>
" />
                                <aside class="optional">
                                    (Optional)
                                </aside>
                            </div>
                        </div>                         
                        <hr />
                        <div class="form-item input-row custom-select">
                            <label for="workwhere">Where do you work?</label>
                            <div class="selectStyle">                                
                                <select type="text" name="workwhere" id="workwhere" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['workwhere']['message'] != ''): ?>error<?php endif; ?>" >
                                    <option value="default" disabled="disabled" default selected="selected">Please select one</option>

                                    <option value="0" <?php if ($this->_tpl_vars['Post']['workwhere'] == 0 && $this->_tpl_vars['Post']['workwhere'] != ''): ?> selected="selected"<?php endif; ?>>Google</option>
                                    <option value="1" <?php if ($this->_tpl_vars['Post']['workwhere'] == 1 && $this->_tpl_vars['Post']['workwhere'] != ''): ?> selected="selected"<?php endif; ?>>YouTube</option>
                                    <option value="2" <?php if ($this->_tpl_vars['Post']['workwhere'] == 2 && $this->_tpl_vars['Post']['workwhere'] != ''): ?> selected="selected"<?php endif; ?>>Facebook</option>
                                                                
                                </select>               
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['workwhere']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['workwhere']['message']; ?>
<?php endif; ?></span>
                        </div> 
                        <div class="form-item input-row custom-select">
                            <label for="dofereln">What is your relationship with DofE?</label>
                            <div class="selectStyle">                                
                                <select type="text" name="dofereln" id="dofereln" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['dofereln']['message'] != ''): ?>error<?php endif; ?>" >
                                    <option value="default" disabled="disabled" default selected="selected">Please select one</option>

                                    <option value="0" <?php if ($this->_tpl_vars['Post']['dofereln'] == 0 && $this->_tpl_vars['Post']['dofereln'] != ''): ?> selected="selected"<?php endif; ?>>Google</option>
                                    <option value="1" <?php if ($this->_tpl_vars['Post']['dofereln'] == 1 && $this->_tpl_vars['Post']['dofereln'] != ''): ?> selected="selected"<?php endif; ?>>YouTube</option>
                                    <option value="2" <?php if ($this->_tpl_vars['Post']['dofereln'] == 2 && $this->_tpl_vars['Post']['dofereln'] != ''): ?> selected="selected"<?php endif; ?>>Facebook</option>
                                                                
                                </select>               
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['dofereln']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['dofereln']['message']; ?>
<?php endif; ?></span>
                        </div>                         
                        <div class="form-item input-row custom-select">
                            <label for="dofegold">Are you a bronze, silver of gold medalist?</label>
                            <div class="selectStyle">                                
                                <select type="text" name="dofegold" id="dofegold" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['dofegold']['message'] != ''): ?>error<?php endif; ?>" >
                                    <option value="default" disabled="disabled" default selected="selected">Please select one</option>

                                    <option value="0" <?php if ($this->_tpl_vars['Post']['dofegold'] == 0 && $this->_tpl_vars['Post']['dofegold'] != ''): ?> selected="selected"<?php endif; ?>>Bronze</option>
                                    <option value="1" <?php if ($this->_tpl_vars['Post']['dofegold'] == 1 && $this->_tpl_vars['Post']['dofegold'] != ''): ?> selected="selected"<?php endif; ?>>Silver</option>
                                    <option value="2" <?php if ($this->_tpl_vars['Post']['dofegold'] == 2 && $this->_tpl_vars['Post']['dofegold'] != ''): ?> selected="selected"<?php endif; ?>>Gold</option>
                                                                
                                </select>               
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['dofegold']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['dofegold']['message']; ?>
<?php endif; ?></span>
                        </div> 
                        <div class="form-item input-row custom-select">
                            <label for="dofeevent">Which of the 3 event types will you be doing?</label>
                            <div class="selectStyle">                                
                                <select type="text" name="dofeevent" id="dofeevent" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['dofeevent']['message'] != ''): ?>error<?php endif; ?>" >
                                    <option value="default" disabled="disabled" default selected="selected">Please select one</option>

                                    <option value="0" <?php if ($this->_tpl_vars['Post']['dofeevent'] == 0 && $this->_tpl_vars['Post']['dofeevent'] != ''): ?> selected="selected"<?php endif; ?>>Bronze</option>
                                    <option value="1" <?php if ($this->_tpl_vars['Post']['dofeevent'] == 1 && $this->_tpl_vars['Post']['dofeevent'] != ''): ?> selected="selected"<?php endif; ?>>Silver</option>
                                    <option value="2" <?php if ($this->_tpl_vars['Post']['dofeevent'] == 2 && $this->_tpl_vars['Post']['dofeevent'] != ''): ?> selected="selected"<?php endif; ?>>Gold</option>
                                                                
                                </select>               
                            </div>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['dofeevent']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['dofeevent']['message']; ?>
<?php endif; ?></span>
                        </div>   
                        <hr />
                        <div class="form-item input-row customCheckbox">
                            <label for="">Select shirt size</label>
                            <input type="radio" name="tshirt" class="checkboxLabel main_street_input" id="tshirtyes" value="xs"<?php if ($this->_tpl_vars['Post']['tshirt'] == 'xs'): ?> checked="checked"<?php endif; ?>  />
                            <label for="tshirtyes" class="radio_input"><span></span>XS</label> <!-- 1 added at the end for test -->
                            <input type="radio" name="tshirt" class="checkboxLabel main_street_input" id="tshirts" value="s"<?php if ($this->_tpl_vars['Post']['tshirt'] == 's'): ?> checked="checked"<?php endif; ?>  />
                            <label for="tshirtyes" class="radio_input"><span></span>S</label> <!-- 1 added at the end for test -->
                            <input type="radio" name="tshirt" class="checkboxLabel main_street_input" id="tshirtm" value="m"<?php if ($this->_tpl_vars['Post']['tshirt'] == 'm'): ?> checked="checked"<?php endif; ?>  />
                            <label for="tshirtm" class="radio_input"><span></span>M</label> <!-- 1 added at the end for test -->
                            <input type="radio" name="tshirt" class="checkboxLabel main_street_input" id="tshirtl" value="l"<?php if ($this->_tpl_vars['Post']['tshirt'] == 'l'): ?> checked="checked"<?php endif; ?>  />
                            <label for="tshirtl" class="radio_input"><span></span>L</label> <!-- 1 added at the end for test -->
                            <input type="radio" name="tshirt" class="checkboxLabel main_street_input" id="tshirtxl" value="xl"<?php if ($this->_tpl_vars['Post']['tshirt'] == 'xs'): ?> checked="checked"<?php endif; ?>  />
                            <label for="tshirtxl" class="radio_input"><span></span>XL</label> <!-- 1 added at the end for test -->

                        </div>
                        <!-- 
                        <div class="form-item customCheckbox">
                            <label for="">I&rsquo;d like to be an advocate</label>
                            <input type="radio" name="advocate" class="checkboxLabel main_street_input" id="advocateyes" value="1"  <?php if ($this->_tpl_vars['Post']['advocate'] == 1): ?>checked="checked"<?php endif; ?>  />
                            <label for="advocateyes"><span></span>Yes</label>    

                            <input type="radio" name="advocate" class="checkboxLabel main_street_input" id="advocateyes" value="1"  <?php if ($this->_tpl_vars['Post']['advocate'] == 1): ?>checked="checked"<?php endif; ?>  />
                            <label for="advocateyes"><span></span>No</label> 
                        </div>      -->                           
                        <hr />
                        
                        <div class="form-item input-row custom-select">
                            <label for="discountcode">Discount code</label>
                            <div class="">                                    
                               <input type="text" name="discountcode" id="discountcode" maxlength="9" class="input-text border  <?php if ($this->_tpl_vars['Errors']['discountcode']['message'] != ''): ?>error<?php endif; ?>"   value="<?php echo $this->_tpl_vars['Post']['discountcode']; ?>
" />
                                <aside class="optional">
                                    (Optional)
                                </aside>
                            </div>
                        </div>                          
                        
                        <div class="form-item input-row customCheckbox">
                            <label for="">Would you like your funds raised to be used in your region?</label>
                            <input type="radio" name="region" class="checkboxLabel main_street_input" id="regionyes" value="1"<?php if ($this->_tpl_vars['Post']['region'] == 1): ?> checked="checked"<?php endif; ?>  />
                            <label for="regionyes" class="radio_input"><span></span>Yes</label> <!-- 1 added at the end for test -->

                            <input type="radio" name="region" class="checkboxLabel main_street_input" id="regionno" value="0"<?php if ($this->_tpl_vars['Post']['region'] == 0 || ! isset ( $this->_tpl_vars['Post']['region'] )): ?> checked="checked"<?php endif; ?>  />
                            <label for="regionno" class="radio_input"><span></span>No</label> <!-- 1 added at the end for test -->  
                        </div>
                        
                        <input type="hidden" name="jgoptin" value="0" />
                        
                        <div class="form-item input-row customCheckbox">
                            <label for="">Stay up to date with news about how your support is helping.</label>
                            <input type="radio" name="charityoptin" class="checkboxLabel main_street_input" id="rnlioptinyes" value="1"<?php if ($this->_tpl_vars['Post']['charityoptin'] == 1 || ! isset ( $this->_tpl_vars['Post']['charityoptin'] ) || $this->_tpl_vars['Post']['charityoptin'] != 0): ?> checked="checked"<?php endif; ?>  />
                            <label for="rnlioptinyes" class="radio_input"><span></span>Yes</label> <!-- <?php echo ((is_array($_tmp=$this->_tpl_vars['Post']['charityoptin'])) ? $this->_run_mod_handler('intval', true, $_tmp) : intval($_tmp)); ?>
 -->     

                            <input type="radio" name="charityoptin" class="checkboxLabel main_street_input" id="rnlioptinno" value="0"<?php if (isset ( $this->_tpl_vars['Post']['charityoptin'] ) && $this->_tpl_vars['Post']['charityoptin'] != 1): ?> checked="checked"<?php endif; ?>   />
                            <label for="rnlioptinno" class="radio_input"><span></span>No</label> <!-- 1 added at the end for test -->     
                        </div> 

                                      
                        <div class="form-item input-row terms customCheckbox">                        
                            <input type="checkbox" name="signoff" class="checkboxLabel main_street_input <?php if ($this->_tpl_vars['Errors']['signoff']['message'] != ''): ?>error<?php endif; ?>" id="jgoptinyes2" value="1" <?php if ($this->_tpl_vars['Post']['signoff'] == 1): ?>checked="checked"<?php endif; ?> validate="required:true" />                            
                            <label for="jgoptinyes2" class="label <?php if ($this->_tpl_vars['Errors']['signoff']['message'] != ''): ?>error<?php endif; ?>"><span class="hint" data-title=""></span><p>Do you have guardian sign-off (if under 18)?</p></label> 
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['signoff']['message'] != ''): ?>You must agree to the Terms and Conditions to proceed<?php endif; ?></span>
                        </div>      

                    </div>
                </div>

                <div class="col-xs-12 fieldset-submit water-line-bottom createpage">
        	        <div class="form-item input-row centered">
                        <button class="button submit site_cta" title="Next" type="submit">
                            Register
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