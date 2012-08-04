<?php

$info = '';
$warning = '';
$auth_active_checked = '';

/*
 * Update config.inc.php
 */
if(rex_request("func","string")=="update")
{
  ## get request parameters
  if(rex_request("auth_active","boolean"))
    $REX['ADDON']['community']['plugin_auth_media']['auth_active'] = 1;
  else
    $REX['ADDON']['community']['plugin_auth_media']['auth_active'] = 0;
    
  $REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext'] = rex_request("unsecure_fileext","string");
  $REX['ADDON']['community']['plugin_auth_media']['error_article_id'] = rex_request("error_article_id","int");
  
  ## build new config content
  $content = '
$REX[\'ADDON\'][\'community\'][\'plugin_auth_media\'][\'auth_active\'] = '.$REX['ADDON']['community']['plugin_auth_media']['auth_active'].';
$REX[\'ADDON\'][\'community\'][\'plugin_auth_media\'][\'unsecure_fileext\'] = "'.$REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext'].'";
$REX[\'ADDON\'][\'community\'][\'plugin_auth_media\'][\'error_article_id\'] = '.$REX['ADDON']['community']['plugin_auth_media']['error_article_id'].';
';

  ## update files
  if(rex_replace_dynamic_contents($REX['INCLUDE_PATH'].'/addons/community/plugins/auth_media/config.inc.php', $content) !== false)
    if(rex_com_auth_media::createHtaccess())
      echo rex_info($I18N->msg('com_auth_media_settings_update'));
    else
      echo rex_warning($I18N->msg('com_auth_media_htaccess_failupdate'));      
  else
    echo rex_warning($I18N->msg('com_auth_media_settings_failupdate'));
}

/*
 * Formular output
 */
if($REX['ADDON']['community']['plugin_auth_media']['auth_active'])
  $auth_active_checked = 'checked="checked"';



echo '
	<div class="rex-form" id="rex-form-system-setup">
  	<form action="index.php" method="post">
    	<input type="hidden" name="page" value="community" />
    	<input type="hidden" name="subpage" value="plugin.auth_media" />
    	<input type="hidden" name="func" value="update" />
		
			<div class="rex-area-col-2">
				<div class="rex-area-col-a">
	
					<h3 class="rex-hl2">'.$I18N->msg("description").'</h3>
	
					<div class="rex-area-content">
            <p class="rex-tx1">'.$I18N->msg("com_auth_media_settings_description").'</p>
            <h3 class="rex-hl3">'.$I18N->msg("com_auth_media_settings_unsecure_fileext").'</h3>
            <p class="rex-tx1">'.$I18N->msg("com_auth_media_help_unsecure_fileext").'</p>
					</div>
				</div>
			
				<div class="rex-area-col-b">
					
					<h3 class="rex-hl2">'.$I18N->msg("com_auth_media_settings").'</h3>
					
					<div class="rex-area-content">
					
						<fieldset class="rex-form-col-1">
							<legend>'.$I18N->msg("com_auth_media_settings_config").'</legend>
							
							<div class="rex-form-wrapper">
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-auth_active">'.$I18N->msg("com_auth_media_settings_auth_active").'</label>
										<input class="rex-form-checkbox" type="checkbox" id="rex-form-auth_active" name="auth_active" value="true" '.$auth_active_checked.' />
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-unsecure_fileext">'.$I18N->msg("com_auth_media_settings_unsecure_fileext").'</label>
										<input class="rex-form-text" type="input" id="rex-form-unsecure_fileext" name="unsecure_fileext" value="'.$REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext'].'" />
									</p>
								</div>
      
                <div class="rex-form-row">
									<p class="rex-form-col-a rex-form-widget">
										<label for="rex-form-error_article_id">'.$I18N->msg("com_auth_media_settings_error_article_id").'</label>
										'. rex_var_link::_getLinkButton('error_article_id', 1, stripslashes($REX['ADDON']['community']['plugin_auth_media']['error_article_id'])) .'
									</p>
								</div>


							</div>
							<div class="rex-form-wrapper">
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-submit">
										<input type="submit" class="rex-form-submit" name="sendit" value="'.$I18N->msg("specials_update").'"'. rex_accesskey($I18N->msg('specials_update'), $REX['ACKEY']['SAVE']) .' />
									</p>
								</div>
							</div>
						</fieldset>
					</div> <!-- Ende rex-area-content //-->					
				</div> <!-- Ende rex-area-col-b //-->
			</div> <!-- Ende rex-area-col-2 //-->
			
		</form>
	</div>
  ';
