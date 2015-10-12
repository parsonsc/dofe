<?php /* Smarty version 2.6.28, created on 2014-09-22 08:42:08
         compiled from leaderboard-dropdown.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'ucfirst', 'leaderboard-dropdown.html', 43, false),array('modifier', 'number_format', 'leaderboard-dropdown.html', 52, false),)), $this); ?>
<?php echo '
<style type="text/css">
.js-active .teams-dropdown button { display: none}
</style>
<script type="text/javascript">
jQuery(document).ready(function($)
{
    $("body").addClass(\'js-active\');
    $("#teams").change(function() {
        //$("#intro").html($("#teams option:selected").data(\'teamstory\'));
        if (parseInt($("#teams option:selected").data(\'members\')) > 0){
            if (parseInt($("#teams option:selected").data(\'members\')) == 1)
                 $("#mems").html($("#teams option:selected").data(\'members\') +\' Undie Runner registered\');
            else $("#mems").html($("#teams option:selected").data(\'members\') +\' Undie Runners registered\');
            $("#mems").show();
        }
        else{
            $("#mems").hide();        
        }
        if ($("#teams option:selected").data(\'fbpage\').length > 0){
            var link = $("#teams option:selected").data(\'fbpage\');
            $("#joinevent").empty()
            $(\'<a >\',{
                text: \'Show Me The Event Page\',
                title: \'Join this event\',
                href: link,
                target: \'_blank\'
            }).addClass(\'uni_register\').appendTo(\'#joinevent\');        
            $("#joinevent").show()
        }else{
            $("#joinevent").hide();        
        }
    });
});    
</script>
'; ?>

    <div class="teams-dropdown">
        <form action="" method="get">
            <div class="selectStyle">
                <select id="teams" name="selteam" class="styled border" placeholder="Select A Uni" value="Select A Uni">
                <option class"selectUniHead" value=""  data-teamstory="" disabled selected> <strong>Select A Uni</strong></option>
        <?php $_from = $this->_tpl_vars['teams']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
                <option value="<?php echo $this->_tpl_vars['v']['teamshortname']; ?>
" data-teamstory="" data-members="<?php echo $this->_tpl_vars['v']['numMembers']; ?>
" data-fbpage="<?php echo $this->_tpl_vars['v']['teamfbpage']; ?>
"><?php echo ucfirst($this->_tpl_vars['v']['teamname']); ?>
</option>
        <?php endforeach; endif; unset($_from); ?> 
                </select> 
            </div>
            <button type="submit"> Go </button>
        </form>
        <h3 id="mems">
            <?php if ($this->_tpl_vars['sel']['numMembers'] > 0): ?>
                <?php if ($this->_tpl_vars['sel']['numMembers'] == 1): ?>
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['sel']['numMembers'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 Undie Runner
                <?php else: ?> 
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['sel']['numMembers'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 Undie Runners
                <?php endif; ?>
            <?php else: ?>
                <?php echo ((is_array($_tmp=$this->_tpl_vars['players'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 Undie Runner<?php if ($this->_tpl_vars['players'] != 1): ?>s<?php endif; ?> 
            <?php endif; ?> registered       
        </h3> 

        <div id="intro">
            <?php if ($this->_tpl_vars['sel']['teamstory'] == ''): ?>
                
            <?php else: ?>
                <?php echo $this->_tpl_vars['sel']['teamstory']; ?>

            <?php endif; ?>
        </div>
        <div id="joinevent" class="joinEvent">
            <?php if ($this->_tpl_vars['sel']['teamfbpage'] != ''): ?>
            <a href="<?php echo $this->_tpl_vars['v']['teamfbpage']; ?>
" class="uni_register">
                Show Me The Event Page <span class="redArrow"></span>
            </a>        
            <?php endif; ?>
        </div>    
         <div class="teamInfo">
            <p>Prove your Uni&rsquo;s the best by gathering the biggest team together. Then suss out the competition on our leader board, and prepare to share. Tweet about it. Facebook it. Instagram selfies in your smalls. Show the world how you&rsquo;re helping to beat cancer sooner.</p>
        </div>    
        
    </div>
    