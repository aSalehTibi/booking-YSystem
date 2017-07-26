<?php /* Smarty version Smarty-3.1.16, created on 2017-07-24 17:36:22
         compiled from "/var/www/html/booked16/tpl/Test-purpose.tpl" */ ?>
<?php /*%%SmartyHeaderCode:222067078597613f62b0876-20722143%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e9efc84e08a62156fbefc383ded82555429dade0' => 
    array (
      0 => '/var/www/html/booked16/tpl/Test-purpose.tpl',
      1 => 1500910574,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '222067078597613f62b0876-20722143',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Path' => 0,
    'ScriptUrl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.16',
  'unifunc' => 'content_597613f6318b76_92293654',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_597613f6318b76_92293654')) {function content_597613f6318b76_92293654($_smarty_tpl) {?>
<?php echo $_smarty_tpl->getSubTemplate ('globalheader.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('cssFiles'=>"css/reports.css,scripts/js/jqplot/jquery.jqplot.min.css"), 0);?>


<h1> Automatic report for all Groups </h1>
<fieldset id="customReportInput-container">
    <form id="customReportInput" action="../Test-purpose3.php" method="post">
       
        <div id="custom-report-input">
            
            <div class="input-set">
                <label for="range_within" style="width:auto;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'Between'),$_smarty_tpl);?>
</label>
                <input type="input" class="textbox dateinput" name="startDate" id="startDate"/> -
                <input type="hidden" id="formattedBeginDate"/>
                <input type="input" class="textbox dateinput" name="endDate" id="endDate"/>
                <input type="hidden" id="formattedEndDate"/>
            </div>
           
            <div class="input-set">
                <label for="range_within" style="width:auto;">Format</label><br/>
                <input type="radio" name="format" value="3" checked="checked"/> Summiert nach Ressource <br/>
                <input type="radio" name="format" value="4" /> Details (für Profs.)<br/>
                <input type="radio" name="format" value="5" /> users sind nicht Mitglieder in eine Gruppe<br/>
                
            </div>
             
        <input type="submit" class="button" value="generate" />
         <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['csrf_token'][0][0]->CSRFToken(array(),$_smarty_tpl);?>
 
    </form>
</fieldset>


<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['jsfile'][0][0]->IncludeJavascriptFile(array('src'=>"autocomplete.js"),$_smarty_tpl);?>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['jsfile'][0][0]->IncludeJavascriptFile(array('src'=>"ajax-helpers.js"),$_smarty_tpl);?>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['jsfile'][0][0]->IncludeJavascriptFile(array('src'=>"reports/generate-reports.js"),$_smarty_tpl);?>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['jsfile'][0][0]->IncludeJavascriptFile(array('src'=>"reports/common.js"),$_smarty_tpl);?>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['jsfile'][0][0]->IncludeJavascriptFile(array('src'=>"reports/chart.js"),$_smarty_tpl);?>


<script type="text/javascript">
    $(document).ready(function () {
        var reportOptions = {
            userAutocompleteUrl: "<?php echo $_smarty_tpl->tpl_vars['Path']->value;?>
ajax/autocomplete.php?type=<?php echo AutoCompleteType::User;?>
",
            groupAutocompleteUrl: "<?php echo $_smarty_tpl->tpl_vars['Path']->value;?>
ajax/autocomplete.php?type=<?php echo AutoCompleteType::Group;?>
",
        };

        var reports = new GenerateReports(reportOptions);
        reports.init();

        var common = new ReportsCommon(
                {
                    scriptUrl: '<?php echo $_smarty_tpl->tpl_vars['ScriptUrl']->value;?>
'
                }
        );
        common.init();
    });
</script>


<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['control'][0][0]->DisplayControl(array('type'=>"DatePickerSetupControl",'ControlId'=>"startDate",'AltId'=>"formattedBeginDate"),$_smarty_tpl);?>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['control'][0][0]->DisplayControl(array('type'=>"DatePickerSetupControl",'ControlId'=>"endDate",'AltId'=>"formattedEndDate"),$_smarty_tpl);?>


<?php echo $_smarty_tpl->getSubTemplate ('globalfooter.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php }} ?>
