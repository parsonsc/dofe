<?php /* Smarty version 2.6.28, created on 2015-07-17 15:32:03
         compiled from stripe.html */ ?>
<?php echo '
<script type="text/javascript">
// this identifies the website in the createToken call
Stripe.setPublishableKey(\''; ?>
<?php echo $this->_tpl_vars['Settings']['stripe_pkey']; ?>
<?php echo '\');
</script> 
'; ?>

<span class="payment-errors"><?php echo $this->_tpl_vars['Errors']; ?>
</span>
<span class="payment-success"><?php echo $this->_tpl_vars['success']; ?>
</span>
<form action="" method="POST" id="payment-form">
    <div class="form-row">
        <label>Card Number</label>
        <input type="text" size="20" autocomplete="off" class="card-number" />
    </div>
    <div class="form-row">
        <label>CVC</label>
        <input type="text" size="4" autocomplete="off" class="card-cvc" />
    </div>
    <div class="form-row">
        <label>Expiration (MM/YYYY)</label>
        <input type="text" size="2" class="card-expiry-month"/>
        <span> / </span>
        <input type="text" size="4" class="card-expiry-year"/>
    </div>
    <input type="hidden" name="currency_code" value="<?php if ($this->_tpl_vars['User']['country'] == 'Ireland'): ?>EUR<?php else: ?>GBP<?php endif; ?>" />
    <input type="hidden" name="user" id="user" value="<?php echo $this->_tpl_vars['UserD']['firstname']; ?>
 <?php echo $this->_tpl_vars['UserD']['lastname']; ?>
" />
    <button type="submit" class="submit-button">Submit Payment</button>
</form>