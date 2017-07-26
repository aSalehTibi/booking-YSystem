<?php /* Smarty version Smarty-3.1.16, created on 2017-07-24 17:36:22
         compiled from "/var/www/html/booked16/tpl/globalfooter.tpl" */ ?>
<?php /*%%SmartyHeaderCode:42999891597613f67c5161-40579195%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '042722516436ac62969dca13cc4144aa2577e281' => 
    array (
      0 => '/var/www/html/booked16/tpl/globalfooter.tpl',
      1 => 1487256677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '42999891597613f67c5161-40579195',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Version' => 0,
    'GoogleAnalyticsTrackingId' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.16',
  'unifunc' => 'content_597613f67dad66_48408801',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_597613f67dad66_48408801')) {function content_597613f67dad66_48408801($_smarty_tpl) {?>

	</div><!-- close main-->

	<footer class="footer navbar">
		&copy; 2016 <a href="http://www.twinkletoessoftware.com">Twinkle Toes Software</a> <br/><a href="http://www.bookedscheduler.com">Booked Scheduler v<?php echo $_smarty_tpl->tpl_vars['Version']->value;?>
</a>
	</footer>

	<script type="text/javascript">
		init();
		$.blockUI.defaults.css.border = 'none';
		$.blockUI.defaults.css.top = '25%';
	</script>

	<?php if (!empty($_smarty_tpl->tpl_vars['GoogleAnalyticsTrackingId']->value)) {?>
		
			<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		  
			  ga('create', '<?php echo $_smarty_tpl->tpl_vars['GoogleAnalyticsTrackingId']->value;?>
', 'auto');
			  ga('send', 'pageview');
			</script>
	<?php }?>
	</body>
</html><?php }} ?>
