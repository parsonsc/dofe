<?php
if (!defined('JUSTGIVING_VERSION'))
	exit('No direct script access allowed');
?>
<div id="framework_wrap" class="wrap">
    <div id="header">
        <h1>JustGiving</h1>
        <span class="icon">&nbsp;</span>
        <div class="version">
          <?php echo 'Version ' . JUSTGIVING_VERSION; ?>
        </div>
    </div>
    <div id="content_wrap">
        <div id="general-settings" class="block">
            <form method="post" action="options.php#general-settings">
            <?php $wppb_generalSettings = get_option('jg_general_settings'); ?>
            <?php settings_fields('jg_general_settings'); ?>
            <h2><?php _e('General Settings', 'justgiving');?></h2>
            <div class="form-item">
            <label for="ApiLocation"><?php _e('API Location:', 'justgiving');?></label>
            <select name="jg_general_settings[ApiLocation]" id="ApiLocation" class="jg_general_settings">
                <option value="" <?php if ($wppb_generalSettings['ApiLocation'] == '') echo 'selected="selected"';?>></option>
                <option value="https://api-sandbox.justgiving.com/" <?php if ($wppb_generalSettings['ApiLocation'] == 'https://api-sandbox.justgiving.com/') echo 'selected="selected"';?>><?php _e('Sandbox', 'justgiving');?></option>
                <option value="https://api.justgiving.com/" <?php if ($wppb_generalSettings['ApiLocation'] == 'https://api.justgiving.com/') echo 'selected="selected"';?>><?php _e('Live', 'justgiving');?></option>
            </select>
            </div>
            <div class="form-item">
            <label for="ApiKey"><?php _e('API Key:', 'justgiving');?></label>
            <input type="text" id="ApiKey" name="jg_general_settings[ApiKey]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['ApiKey']; ?>" />
            </div>
            <div class="form-item">

            <label for="TestUsername"><?php _e('Test Username:', 'justgiving');?></label>
            <input type="text" id="TestUsername" name="jg_general_settings[TestUsername]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['TestUsername']; ?>" />
            </div>
            <div class="form-item">

            <label for="TestValidPassword"><?php _e('Test ValidPassword:', 'justgiving');?></label>
            <input type="text" id="TestValidPassword" name="jg_general_settings[TestValidPassword]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['TestValidPassword']; ?>" />
            </div>
            <div class="form-item">
            <label for="TestInvalidPassword"><?php _e('Test InvalidPassword:', 'justgiving');?></label>
            <input type="text" id="TestInvalidPassword" name="jg_general_settings[TestInvalidPassword]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['TestInvalidPassword']; ?>" />
            </div>
            <div class="form-item">
            <label for="ApiVersion"><?php _e('API Version:', 'justgiving');?></label>
            <input type="text" id="ApiVersion" name="jg_general_settings[ApiVersion]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['ApiVersion']; ?>" />
            </div>
            <div class="form-item">
            <label for="Charity"><?php _e('JG Charity ID:', 'justgiving');?></label>
            <input type="text" id="Charity" name="jg_general_settings[Charity]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['Charity']; ?>" />
            </div>               
            <div class="form-item">
            <label for="Event"><?php _e('JG Event ID:', 'justgiving');?></label>
            <input type="text" id="Event" name="jg_general_settings[Event]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['Event']; ?>" />
            </div>   

            <div class="form-item">
            <label for="pagestory"><?php _e('Page Story:', 'justgiving');?></label>
            <textarea id="pagestory" name="jg_general_settings[pageStory]" class="jg_general_settings" ><?php echo $wppb_generalSettings['pageStory']; ?></textarea>
            </div>  
            <div class="form-item">
            <label for="pageSummaryWhat"><?php _e('page Summary What:', 'justgiving');?></label>
            <input type="text" id="pageSummaryWhat" name="jg_general_settings[pageSummaryWhat]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['pageSummaryWhat']; ?>" />
            </div>
            <div class="form-item">
            <label for="pageSummaryWhy"><?php _e('page Summary Why:', 'justgiving');?></label>
            <input type="text" id="pageSummaryWhy" name="jg_general_settings[pageSummaryWhy]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['pageSummaryWhy']; ?>" />
            </div>
            <div class="form-item">
            <label for="imageurl"><?php _e('page image url:', 'justgiving');?></label>
            <input type="text" id="imageurl" name="jg_general_settings[imageurl]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['imageurl']; ?>" />
            </div> 
            <div class="form-item">
            <label for="targetAmount"><?php _e('page default target:', 'justgiving');?></label>
            <input type="number" step="any" id="targetAmount" name="jg_general_settings[targetAmount]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['targetAmount']; ?>" />
            </div> 
            <div class="form-item">
            <label for="cc1"><?php _e('custom code 1:', 'justgiving');?></label>
            <input type="text" id="cc1" name="jg_general_settings[cc1]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['cc1']; ?>" />
            </div>   
            <div class="form-item">
            <label for="cc2"><?php _e('custom code 2:', 'justgiving');?></label>
            <input type="text" id="cc1" name="jg_general_settings[cc2]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['cc2']; ?>" />
            </div>
            <div class="form-item">
            <label for="cc3"><?php _e('custom code 3:', 'justgiving');?></label>
            <input type="text" id="cc1" name="jg_general_settings[cc3]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['cc3']; ?>" />
            </div>
            <div class="form-item">
            <label for="cc4"><?php _e('custom code 4:', 'justgiving');?></label>
            <input type="text" id="cc1" name="jg_general_settings[cc4]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['cc4']; ?>" />
            </div>
            <div class="form-item">
            <label for="cc5"><?php _e('custom code 5:', 'justgiving');?></label>
            <input type="text" id="cc1" name="jg_general_settings[cc5]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['cc5']; ?>" />
            </div>
            <div class="form-item">
            <label for="cc6"><?php _e('custom code 6:', 'justgiving');?></label>
            <input type="text" id="cc1" name="jg_general_settings[cc6]" class="jg_general_settings" value="<?php echo $wppb_generalSettings['cc6']; ?>" />
            </div>
            <div class="form-item">
            <label for="paid"><?php _e('Paid access?:', 'justgiving');?></label>
            <input type="checkbox" value="1" id="paid" name="jg_general_settings[paidaccess]" class="jg_general_settings" <?php echo (isset($wppb_generalSettings['paidaccess']) && $wppb_generalSettings['paidaccess'] == 1)? ' checked="checked"':''; ?>  />
            </div>
            <div class="form-item">
            <label for="payup"><?php _e('Pay amount?:', 'justgiving');?></label>
            <input type="number" step="any" min="0" value="<?php echo $wppb_generalSettings['payamount']; ?>" id="payup" name="jg_general_settings[payamount]" class="jg_general_settings"   />
            </div>   
            <div class="form-item">
            <label for="paypale"><?php _e('Paypal account? - if using paypal:', 'justgiving');?></label>
            <input type="email" value="<?php echo $wppb_generalSettings['paypal_email']; ?>" id="paypale" name="jg_general_settings[paypal_email]" class="jg_general_settings"   />
            </div>   
            <div class="form-item">
            <label for="paypalf"><?php _e('Paypal facilitator account? - if using paypal:', 'justgiving');?></label>
            <input type="email" value="<?php echo $wppb_generalSettings['paypal_femail']; ?>" id="paypalf" name="jg_general_settings[paypal_femail]" class="jg_general_settings"   />
            </div> 
            <div class="form-item">
            <label for="fbappid"><?php _e('FB App ID:', 'justgiving');?></label>
            <input type="number" value="<?php echo $wppb_generalSettings['fbappid']; ?>" id="fbappid" name="jg_general_settings[fbappid]" class="jg_general_settings"   />
            </div>
            <div class="form-item">
                <label for="ApiLocation"><?php _e('Use SMTP:', 'justgiving');?></label>
                <select name="jg_general_settings[useSMTP]" id="useSMTP" class="jg_general_settings">
                    <option value="" <?php if ($wppb_generalSettings['useSMTP'] == '') echo 'selected="selected"';?>></option>
                    <option value="1" <?php if ($wppb_generalSettings['useSMTP'] == 1) echo 'selected="selected"';?>><?php _e('Yes', 'justgiving');?></option>
                    <option value="-1" <?php if ($wppb_generalSettings['useSMTP'] != 1) echo 'selected="selected"';?>><?php _e('NO', 'justgiving');?></option>
                </select>
            </div>
            <div class="form-item">
            <label for="smtp_uname"><?php _e('SMTP User ID:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['smtp_uname']; ?>" id="smtp_uname" name="jg_general_settings[smtp_uname]" class="jg_general_settings"   />
            </div> 
            <div class="form-item">
            <label for="smtp_pword"><?php _e('SMTP password:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['smtp_pword']; ?>" id="smtp_pword" name="jg_general_settings[smtp_pword]" class="jg_general_settings"   />
            </div>
            <div class="form-item">
            <label for="smtp_server"><?php _e('SMTP server:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['smtp_server']; ?>" id="smtp_server" name="jg_general_settings[smtp_server]" class="jg_general_settings"   />
            </div> 
            <div class="form-item">
            <label for="smtp_port"><?php _e('SMTP port:', 'justgiving');?></label>
            <input type="number" value="<?php echo $wppb_generalSettings['smtp_port']; ?>" id="smtp_port" name="jg_general_settings[smtp_port]" class="jg_general_settings"   />
            </div>             
            <div class="form-item">
            <label for="smtp_helo"><?php _e('SMTP helo:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['smtp_helo']; ?>" id="smtp_helo" name="jg_general_settings[smtp_helo]" class="jg_general_settings"   />
            </div>
            <div class="form-item">
            <label for="mailer_id"><?php _e('SMTP mailer_id:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['mailer_id']; ?>" id="mailer_id" name="jg_general_settings[mailer_id]" class="jg_general_settings"   />
            </div>   
            <div class="form-item">
            <label for="email_from"><?php _e('SMTP email_from:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['email_from']; ?>" id="email_from" name="jg_general_settings[email_from]" class="jg_general_settings"   />
            </div>               
            <div class="form-item">
            <label for="reply_to"><?php _e('SMTP reply_to:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['reply_to']; ?>" id="reply_to" name="jg_general_settings[reply_to]" class="jg_general_settings"   />
            </div>   
            <div class="form-item">
            <label for="friendly_name"><?php _e('SMTP friendly_name:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['friendly_name']; ?>" id="friendly_name" name="jg_general_settings[friendly_name]" class="jg_general_settings"   />
            </div>
            <div class="form-item">
            <label for="lolagrove"><?php _e('Lolagrove?:', 'justgiving');?></label>
            <input type="checkbox" value="1" id="lolagrove" name="jg_general_settings[lolagrove]" class="jg_general_settings" <?php echo (isset($wppb_generalSettings['lolagrove']) && $wppb_generalSettings['lolagrove'] == 1)? ' checked="checked"':''; ?>  />
            </div>   
            <div class="form-item">
            <label for="stripe_key"><?php _e('Stripe key:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['stripe_key']; ?>" id="stripe_key" name="jg_general_settings[stripe_key]" class="jg_general_settings"   />
            </div>
            <div class="form-item">
            <label for="stripe_pkey"><?php _e('Stripe public key:', 'justgiving');?></label>
            <input type="text" value="<?php echo $wppb_generalSettings['stripe_pkey']; ?>" id="stripe_pkey" name="jg_general_settings[stripe_pkey]" class="jg_general_settings"   />
            </div>             
            <div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
            </div>
            </form>
    	</div>
        <div class="info bottom"></div> 
    </div>
</div>    