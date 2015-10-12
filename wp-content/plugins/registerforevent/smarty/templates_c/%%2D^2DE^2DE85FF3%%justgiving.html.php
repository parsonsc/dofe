<?php /* Smarty version 2.6.28, created on 2014-08-18 10:36:59
         compiled from justgiving.html */ ?>
    <form action="" id="justgiving" method="post">
        <input type="hidden" name="action" value="process" />
        <input type="hidden" name="currency_code" value="GBP" />
        <input type="hidden" name="invoice" value="<?php echo $this->_tpl_vars['Invoice']['invoiceid']; ?>
" />
        <div class="form-item">
            <label>Amount<span class="error"><?php echo $this->_tpl_vars['errorMark']; ?>
</span></label>
            <input type="number" step="any" name="product_amount" value="<?php echo $this->_tpl_vars['Settings']['payamount']; ?>
" class="input-text" validate="required:true" />
        </div>
        <div class="form-item">
            <button class="button" title="Give to the cause" type="submit" name="give_justgiving" id="give_justgiving">
                Give
            </button>
        </div>
    </form>