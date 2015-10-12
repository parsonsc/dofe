<?php /* Smarty version 2.6.28, created on 2014-08-20 15:24:32
         compiled from leaderboard.html */ ?>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['pluginurl']; ?>
/js/jquery.tablesorter.js"></script> 
<?php echo '
<script type="text/javascript">
jQuery(document).ready(function($)
    {
        $("#myTable").tablesorter({widthFixed: true, widgets: [\'zebra\']}).tablesorterPager({container: $("#pager")}); 
    } 
); 
</script>
<!--
#####################
### foreach teams ###
#####################

    [dateCreated] => {sjondate}
    [id] => {int}
    [name] => {string}
    [raisedSoFar] => {decimal}
    [story] => {string}
    [targetType] => {int}
    [teamMembers] => Array
        (
            [1-n] => Class
                (
                    [id] => {int}
                    [numberOfDonations] => {int}
                    [pageShortName] => {string}
                    [pageTitle] =>{string}
                    [ref] => {string}
                    [totalAmountRaised] => {decimal}
                )
        )

    [teamShortName] => {string}
    [teamTarget] => {int}
    [teamType] => {int}
    [owner] => {int}
    [teamname] => {string}
    [teamshortname] => {string}
    [teamstory] => {string}
    [teamtargettype] => {string}
    [teamtarget] => {decimal}
    [teamfbpage] => {url}
    [teamtwpage] => {url}
    [teamtype] => {enum}
    [submittedtime] => {datetime Y-m-d H:i:s}
    [lastmodified] => {datetime Y-m-d H:i:s}
    [numMembers] => {int}
-->
<style type="text/css">
#myTable { margin: 0 auto; font-size: 1.2em; margin-bottom: 15px;}
#myTable thead { cursor: pointer; background: #c9dff0;}
#myTable thead tr th { font-weight: bold; padding: 12px 30px; padding-left: 42px;}
#myTable thead tr th span { padding-right: 20px; background-repeat: no-repeat; background-position: 100% 100%;}
#myTable thead tr th.headerSortUp, #myTable thead tr th.headerSortDown { background: #acc8dd;}
#myTable thead tr th.headerSortUp span { background-image: url(\''; ?>
<?php echo $this->_tpl_vars['pluginurl']; ?>
<?php echo '/img/up-arrow.png\'); }
#myTable thead tr th.headerSortDown span { background-image: url(\''; ?>
<?php echo $this->_tpl_vars['pluginurl']; ?>
<?php echo '/img/down-arrow.png\');}
#myTable tbody tr { color: #555; }
#myTable tbody tr td { text-align: center; padding: 15px 10px; }
#myTable tbody tr td.lalign { text-align: left; }
</style>
'; ?>

<table id="myTable" class="tablesorter <?php echo '{sortlist: [[2,1]]}'; ?>
"> 
    <thead> 
        <tr> 
            <th><span>Name</span></th>  
            <th><span>Members</span></th>  
            <th><span>Raised so far</span></th> 
            <th><span>Target</span></th> 
            <th><span>Facebook</span></th> 
            <th><span>Twitter</span></th> 
        </tr> 
    </thead> 
    <tbody>
<?php $_from = $this->_tpl_vars['teams']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
        <tr>
            <td><?php echo $this->_tpl_vars['v']['name']; ?>
</td>
            <td><?php echo $this->_tpl_vars['v']['numMembers']; ?>
</td>
            <td><?php echo $this->_tpl_vars['v']['raisedSoFar']; ?>
</td>
            <td><?php echo $this->_tpl_vars['v']['teamTarget']; ?>
</td>
            <td><?php if ($this->_tpl_vars['v']['teamfbpage'] != ''): ?><a href="<?php echo $this->_tpl_vars['v']['teamfbpage']; ?>
">Facebook</a><?php endif; ?></td>
            <td><?php if ($this->_tpl_vars['v']['teamtwpage'] != ''): ?><a href="<?php echo $this->_tpl_vars['v']['teamtwpage']; ?>
">Twitter</a><?php endif; ?></td>
        </tr>
<?php endforeach; endif; unset($_from); ?> 
    </tbody> 
</table>                        