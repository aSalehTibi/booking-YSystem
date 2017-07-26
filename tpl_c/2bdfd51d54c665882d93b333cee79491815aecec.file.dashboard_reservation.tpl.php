<?php /* Smarty version Smarty-3.1.16, created on 2017-07-24 21:04:06
         compiled from "/var/www/html/booked16/tpl/Dashboard/dashboard_reservation.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2146412352597644a68ff9c9-20606583%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2bdfd51d54c665882d93b333cee79491815aecec' => 
    array (
      0 => '/var/www/html/booked16/tpl/Dashboard/dashboard_reservation.tpl',
      1 => 1487256677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2146412352597644a68ff9c9-20606583',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'reservation' => 0,
    'DefaultTitle' => 0,
    'UserId' => 0,
    'Timezone' => 0,
    'checkin' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.16',
  'unifunc' => 'content_597644a69f6767_37130548',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_597644a69f6767_37130548')) {function content_597644a69f6767_37130548($_smarty_tpl) {?><?php $_smarty_tpl->tpl_vars['checkin'] = new Smarty_variable($_smarty_tpl->tpl_vars['reservation']->value->IsCheckinEnabled()&&$_smarty_tpl->tpl_vars['reservation']->value->RequiresCheckin(), null, 0);?>
<div class="reservation row" id="<?php echo $_smarty_tpl->tpl_vars['reservation']->value->ReferenceNumber;?>
">
    <div class="col-sm-3 col-xs-12"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['reservation']->value->Title)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['DefaultTitle']->value : $tmp);?>
</div>
    <div class="col-sm-2 col-xs-12"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['fullname'][0][0]->DisplayFullName(array('first'=>$_smarty_tpl->tpl_vars['reservation']->value->FirstName,'last'=>$_smarty_tpl->tpl_vars['reservation']->value->LastName,'ignorePrivacy'=>$_smarty_tpl->tpl_vars['reservation']->value->IsUserOwner($_smarty_tpl->tpl_vars['UserId']->value)),$_smarty_tpl);?>
 <?php if (!$_smarty_tpl->tpl_vars['reservation']->value->IsUserOwner($_smarty_tpl->tpl_vars['UserId']->value)) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['html_image'][0][0]->PrintImage(array('src'=>"users.png",'altKey'=>'Participant'),$_smarty_tpl);?>
<?php }?></div>
    <div class="col-sm-2 col-xs-6"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['formatdate'][0][0]->FormatDate(array('date'=>$_smarty_tpl->tpl_vars['reservation']->value->StartDate->ToTimezone($_smarty_tpl->tpl_vars['Timezone']->value),'key'=>'dashboard'),$_smarty_tpl);?>
</div>
    <div class="col-sm-2 col-xs-6"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['formatdate'][0][0]->FormatDate(array('date'=>$_smarty_tpl->tpl_vars['reservation']->value->EndDate->ToTimezone($_smarty_tpl->tpl_vars['Timezone']->value),'key'=>'dashboard'),$_smarty_tpl);?>
</div>
    <div class="col-sm-<?php if ($_smarty_tpl->tpl_vars['checkin']->value) {?>2<?php } else { ?>3<?php }?> col-xs-12"><?php echo $_smarty_tpl->tpl_vars['reservation']->value->ResourceName;?>
</div>
    <?php if ($_smarty_tpl->tpl_vars['checkin']->value) {?>
        <div class="col-sm-1 col-xs-12">
            <button type="button" class="btn btn-xs col-xs-12 btn-success btnCheckin" data-referencenumber="<?php echo $_smarty_tpl->tpl_vars['reservation']->value->ReferenceNumber;?>
">
                <i class="fa fa-sign-in"></i> <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'CheckIn'),$_smarty_tpl);?>

            </button>
        </div>
    <?php }?>
</div>
<div class="clearfix"></div>
<?php }} ?>
