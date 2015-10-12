<?php /* Smarty version 2.6.28, created on 2015-05-14 09:26:25
         compiled from create-event.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'create-event.html', 74, false),)), $this); ?>
    <section class="create_event">
        <header class="sign_up_banner">
        </header>
        <div class="inner_content">
            <p class="log_out">Not <?php echo $this->_tpl_vars['Session']['email']; ?>
? Click <a href="<?php  echo get_permalink(24); ?>">here</a> to logout.</p>

            <form enctype="multipart/form-data" method="post"
                  id="eventadd" class="create_page user-forms " name="eventadd" action="<?php echo $this->_tpl_vars['formurl']; ?>
">

                <div class="container water-bg water-line-top eventadd">
                    <div class="col-xs-12">

                        <div class="form-item input-row eventname">
                            <label for="eventname" class="jg-placeholder-title">Event name</label>
                           
                            <input name="eventname" type="text"  id="eventname" value="<?php echo $this->_tpl_vars['Post']['eventname']; ?>
" class="input-text jg-placeholder-input"  validate="required:true" placeholder=""/>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['eventname']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventname']['message']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['Errors']['eventname']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventname']['message']; ?>
<?php endif; ?></span>
                        </div>
                        <div class="form-item input-row eventdescr">
                            <label for="eventdescr" class="jg-placeholder-title">Event description</label>
                           
                            <input name="eventdescr" type="text"  id="eventdescr" value="<?php echo $this->_tpl_vars['Post']['eventdescr']; ?>
" class="input-text jg-placeholder-input"  validate="required:true" placeholder=""/>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['eventdescr']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventdescr']['message']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['Errors']['eventdescr']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventdescr']['message']; ?>
<?php endif; ?></span>
                        </div>
                        <div class="form-item input-row eventstart">
                            <label for="eventstart" class="jg-placeholder-title">Event start date</label>
                           
                            <input name="eventstart-date" type="date"  id="eventstart" <?php if ($this->_tpl_vars['Post']['eventstart']-$this->_tpl_vars['ate'] != 0): ?>value="<?php echo $this->_tpl_vars['Post']['eventstart']-$this->_tpl_vars['ate']; ?>
"<?php endif; ?> class="input-text jg-placeholder-input"  validate="required:true" placeholder=""/>
                            <input name="eventstart-time" type="time"  id="eventstart"  <?php if ($this->_tpl_vars['Post']['eventstart']-$this->_tpl_vars['ime'] != 0): ?>value="<?php echo $this->_tpl_vars['Post']['eventstart']-$this->_tpl_vars['ime']; ?>
"<?php endif; ?> class="input-text jg-placeholder-input" placeholder=""/>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['eventstart']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventstart']['message']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['Errors']['eventstart']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventstart']['message']; ?>
<?php endif; ?></span>
                        </div>
                        <div class="form-item input-row eventend">
                            <label for="eventend" class="jg-placeholder-title">Event end date</label>
                           
                            <input name="eventend-date" type="date"  id="eventend" <?php if ($this->_tpl_vars['Post']['eventend']-$this->_tpl_vars['ate'] != 0): ?>value="<?php echo $this->_tpl_vars['Post']['eventend']-$this->_tpl_vars['ate']; ?>
"<?php endif; ?> class="input-text jg-placeholder-input"  validate="required:true" placeholder=""/>
                            <input name="eventend-time" type="time"  id="eventend" <?php if ($this->_tpl_vars['Post']['eventend']-$this->_tpl_vars['ime'] != 0): ?>value="<?php echo $this->_tpl_vars['Post']['eventend']-$this->_tpl_vars['ime']; ?>
"<?php endif; ?> class="input-text jg-placeholder-input" placeholder=""/>
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['eventend']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventend']['message']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['Errors']['eventend']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventend']['message']; ?>
<?php endif; ?></span>
                        </div>
                     
                        
                        <div class="form-item input-row eventlocn">
                            <label for="eventlocn" class="jg-placeholder-title">Event location</label>
                            <input id="geocomplete" placeholder="Enter your address" type="text" name="eventlocn" value="<?php echo $this->_tpl_vars['Post']['eventlocn']; ?>
" class="input-text jg-placeholder-input"  validate="required:true" />
                            <span class="error"><?php if ($this->_tpl_vars['Errors']['eventlocn']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventlocn']['message']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['Errors']['eventlocn']['message'] != ''): ?><?php echo $this->_tpl_vars['Errors']['eventlocn']['message']; ?>
<?php endif; ?></span>
                            <fieldset class="location">
                                <div class="label">
                                    <p>Street address</p>
                                    <input class="field" id="street_number" data-geo="street_number" name="street_number" disabled="true" />
                                    <input class="field" id="route" data-geo="route" name="street_name" disabled="true" />
                                </div>
                                <div class="label">
                                    <p>City</p>
                                    <input class="field" id="locality" name="city" data-geo="locality" disabled="true" />
                                </div>                                
                                <div class="label">
                                    <label>State</label>
                                    <input class="field" id="administrative_area_level_1" data-geo="administrative_area_level_1" name="state" disabled="true" />
                                    <label>Postcode</label>
                                    <input class="field" id="postal_code" data-geo="postal_code" name="postcode" disabled="true" />
                                </div>
                                <div class="label">
                                    <label>Country</label>
                                    <input class="field" id="country" data-geo="country" name="country" disabled="true" />
                                    <input type="hidden" class="field" id="lat" data-geo="lat" name="lat" disabled="true" />
                                    <input type="hidden" class="field" id="lng" data-geo="lng" name="lng" disabled="true" />
                                </div>                                 
                            </fieldset>
                        </div>                        
                        
                        <div class="form-item input-row">
                            <label for="eventtype">What sort of event are you doing?</label>
                            <div class="selectStyle">
                                <select type="text" name="eventtype" id="eventtype" class="input-text border styled2 <?php if ($this->_tpl_vars['Errors']['eventtype']['message'] != ''): ?>error<?php endif; ?>" validate="required:true" >
                                <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['eventtypes'],'output' => $this->_tpl_vars['eventtypes'],'selected' => $this->_tpl_vars['Post']['eventtype']), $this);?>

                                <?php $_from = $this->_tpl_vars['eventtypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
                                   <option value="<?php echo $this->_tpl_vars['k']; ?>
" <?php if ($this->_tpl_vars['Post']['eventtype'] == $this->_tpl_vars['k'] && $this->_tpl_vars['Post']['eventtype'] != ''): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['v']; ?>
</option>
                                <?php endforeach; endif; unset($_from); ?>                        
                                </select>                  
                            </div>
                        </div>                        

                    </div>
                </div>

                <div class="col-xs-12 fieldset-submit water-line-bottom createpage">
        	        <div class="form-item input-row centered">
                        <button class="button submit site_cta" title="Next" type="submit">
                            Create event
                        </button>          
                        <p class="form-submit">
                            <input name="action" type="hidden" id="action" value="eventadd" />
                            <input type="hidden" name="formName" value="eventadd" />
                        </p>
                        <?php echo $this->_tpl_vars['nonce']; ?>
               
        	        </div>
                </div>
            </form>
        </div>
    </section>