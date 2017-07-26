<?php /* Smarty version Smarty-3.1.16, created on 2017-07-24 21:04:03
         compiled from "/var/www/html/booked16/tpl/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1877024009597644a33ee902-19710545%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '78f6dbf5d2ab3d8a2a974fe655fead3eeeebf232' => 
    array (
      0 => '/var/www/html/booked16/tpl/login.tpl',
      1 => 1487256677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1877024009597644a33ee902-19710545',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ShowLoginError' => 0,
    'ShowUsernamePrompt' => 0,
    'ShowPasswordPrompt' => 0,
    'ResumeUrl' => 0,
    'ShowRegisterLink' => 0,
    'RegisterUrl' => 0,
    'RegisterUrlNew' => 0,
    'AllowSocialLogin' => 0,
    'GoogleState' => 0,
    'Protocol' => 0,
    'ScriptUrlNoProtocol' => 0,
    'ShowForgotPasswordPrompt' => 0,
    'ForgotPasswordUrl' => 0,
    'ForgotPasswordUrlNew' => 0,
    'Languages' => 0,
    'SelectedLanguage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.16',
  'unifunc' => 'content_597644a3591463_37124079',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_597644a3591463_37124079')) {function content_597644a3591463_37124079($_smarty_tpl) {?>
<?php echo $_smarty_tpl->getSubTemplate ('globalheader.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>


<div id="page-login">
	<?php if ($_smarty_tpl->tpl_vars['ShowLoginError']->value) {?>
		<div id="loginError" class="alert alert-danger">
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'LoginError'),$_smarty_tpl);?>

		</div>
	<?php }?>

	<div class="col-md-offset-3 col-md-6 col-xs-12 ">
		<div id="login-header" class="default-box-header">
			<span class="sign-in"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'SignIn'),$_smarty_tpl);?>
</span>

		</div>
		<form role="form" name="login" id="login" class="form-horizontal" method="post"
			  action="<?php echo $_SERVER['SCRIPT_NAME'];?>
">
			<div id="login-box" class="col-xs-12 default-box straight-top">
				<?php if ($_smarty_tpl->tpl_vars['ShowUsernamePrompt']->value) {?>
					<div class="col-xs-12">
						<div class="input-group margin-bottom-25">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
							<input type="text" required="" class="form-control"
								   id="email" <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['formname'][0][0]->GetFormName(array('key'=>'EMAIL'),$_smarty_tpl);?>

								   placeholder="Uni-Kennung\ Uni-Account"/>
						</div>
					</div>
				<?php }?>

				<?php if ($_smarty_tpl->tpl_vars['ShowPasswordPrompt']->value) {?>
					<div class="col-xs-12">
						<div class="input-group margin-bottom-25">
							<span class="input-group-addon">
							<i class="glyphicon glyphicon-lock"></i>
							</span>
							<input type="password" required="" id="password" <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['formname'][0][0]->GetFormName(array('key'=>'PASSWORD'),$_smarty_tpl);?>

								   class="form-control"
								   value="" placeholder="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'Password'),$_smarty_tpl);?>
"/>
						</div>
					</div>
				<?php }?>

				<div class="col-xs-12">
					<button type="submit" class="btn btn-large btn-primary  btn-block" name="<?php echo Actions::LOGIN;?>
"
							value="submit"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'LogIn'),$_smarty_tpl);?>
</button>
					<input type="hidden" <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['formname'][0][0]->GetFormName(array('key'=>'RESUME'),$_smarty_tpl);?>
 value="<?php echo $_smarty_tpl->tpl_vars['ResumeUrl']->value;?>
"/>
				</div>

				<div class="col-xs-12 <?php if ($_smarty_tpl->tpl_vars['ShowRegisterLink']->value) {?>col-sm-6<?php }?>">
					<div class="checkbox">
						<input id="rememberMe" type="checkbox" <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['formname'][0][0]->GetFormName(array('key'=>'PERSIST_LOGIN'),$_smarty_tpl);?>
>
						<label for="rememberMe"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'RememberMe'),$_smarty_tpl);?>
</label>
					</div>
				</div>

                <?php if ($_smarty_tpl->tpl_vars['ShowRegisterLink']->value) {?>
                    <div class="col-xs-12 col-sm-6 register">
                    <span class="bold"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>"FirstTimeUser?"),$_smarty_tpl);?>

                    <a href="<?php echo $_smarty_tpl->tpl_vars['RegisterUrl']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['RegisterUrlNew']->value;?>

                       title="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'Register'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'Register'),$_smarty_tpl);?>
</a>
                    </span>
                    </div>
                <?php }?>

				<?php if ($_smarty_tpl->tpl_vars['AllowSocialLogin']->value) {?>
					<div class="col-lg-6 col-md-12">
						<a href="https://accounts.google.com/o/oauth2/v2/auth?scope=email%20profile&state=<?php echo $_smarty_tpl->tpl_vars['GoogleState']->value;?>
&redirect_uri=http://www.social.twinkletoessoftware.com/googleresume.php&response_type=code&client_id=531675809673-3sfvrchh6svd9bfl7m55dao8n4s6cqpc.apps.googleusercontent.com"
						   class="pull-left-lg">
							<img src="img/external/btn_google_signin_dark_normal_web.png" alt="Sign in with Google"/>
						</a>
					</div>
					<div class="col-lg-6 col-md-12">
						<a href="http://www.social.twinkletoessoftware.com/fblogin.php?protocol=<?php echo $_smarty_tpl->tpl_vars['Protocol']->value;?>
&resume=<?php echo $_smarty_tpl->tpl_vars['ScriptUrlNoProtocol']->value;?>
/external-auth.php%3Ftype%3Dfb" class="pull-right-lg">
							<img style="max-height:42px" src="img/external/btn_facebook_login.png" alt="Sign in with Facebook"/>
						</a>
					</div>
				<?php }?>
			</div>
			<div id="login-footer" class="col-xs-12">
				<?php if ($_smarty_tpl->tpl_vars['ShowForgotPasswordPrompt']->value) {?>
					<div id="forgot-password" class="col-xs-12 col-sm-6">
						<a href="<?php echo $_smarty_tpl->tpl_vars['ForgotPasswordUrl']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['ForgotPasswordUrlNew']->value;?>
 class="btn btn-link pull-left-sm"><span><i
										class="glyphicon glyphicon-question-sign"></i></span> <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'ForgotMyPassword'),$_smarty_tpl);?>
</a>
					</div>
				<?php }?>
				<div id="change-language" class="col-xs-12 col-sm-6">
					<button type="button" class="btn btn-link pull-right-sm" data-toggle="collapse"
							data-target="#change-language-options"><span><i class="glyphicon glyphicon-globe"></i></span>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['translate'][0][0]->SmartyTranslate(array('key'=>'ChangeLanguage'),$_smarty_tpl);?>

					</button>
					<div id="change-language-options" class="collapse">
						<select <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['formname'][0][0]->GetFormName(array('key'=>'LANGUAGE'),$_smarty_tpl);?>
 class="form-control input-sm" id="languageDropDown">
							<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['object_html_options'][0][0]->ObjectHtmlOptions(array('options'=>$_smarty_tpl->tpl_vars['Languages']->value,'key'=>'GetLanguageCode','label'=>'GetDisplayName','selected'=>$_smarty_tpl->tpl_vars['SelectedLanguage']->value),$_smarty_tpl);?>

						</select>
					</div>
				</div>
			</div>


		</form>
	</div>
</div>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['setfocus'][0][0]->SetFocus(array('key'=>'EMAIL'),$_smarty_tpl);?>


<script type="text/javascript">
	var url = 'index.php?<?php echo QueryStringKeys::LANGUAGE;?>
=';
	$(document).ready(function () {
		$('#languageDropDown').change(function () {
			window.location.href = url + $(this).val();
		});

		var langCode = readCookie('<?php echo CookieKeys::LANGUAGE;?>
');

		if (!langCode)
		{
		}
	});
</script>
<?php echo $_smarty_tpl->getSubTemplate ('globalfooter.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php }} ?>
