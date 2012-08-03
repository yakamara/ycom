<?php

$info = '';
$warning = '';
$xsendfile_checked = '';

/*
 * Update config.inc.php
 */
if(rex_request("func","string")=="update")
{
  ## get request parameters
  if(rex_request("xsendfile","boolean"))
    $REX['ADDON']['community']['plugin_auth_media']['xsendfile'] = 1;
  else
    $REX['ADDON']['community']['plugin_auth_media']['xsendfile'] = 0;
    
  $REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext'] = rex_request("unsecure_fileext","string");

  ## build new config content
  $content = '
$REX[\'ADDON\'][\'community\'][\'plugin_auth_media\'][\'xsendfile\'] = '.$REX['ADDON']['community']['plugin_auth_media']['xsendfile'].';
$REX[\'ADDON\'][\'community\'][\'plugin_auth_media\'][\'unsecure_fileext\'] = "'.$REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext'].'";
';

  ## update files
  if(rex_replace_dynamic_contents($REX['INCLUDE_PATH'].'/addons/community/plugins/auth_media/config.inc.php', $content) !== false)
    if(rex_com_auth_media::updateHtaccess())
      echo rex_info($I18N->msg('com_auth_media_settings_update'));
    else
      echo rex_warning($I18N->msg('com_auth_media_htaccess_failupdate'));      
  else
    echo rex_warning($I18N->msg('com_auth_media_settings_failupdate'));
}

/*
 * Formular output
 */
if($REX['ADDON']['community']['plugin_auth_media']['xsendfile'])
  $xsendfile_checked = 'checked="checked"';

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
            <h3 class="rex-hl3">'.$I18N->msg("com_auth_media_settings_xsendfile").'</h3>
            <p class="rex-tx1">'.$I18N->msg("com_auth_media_help_xsendfile").'</p>
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
										<label for="rex-form-appId">'.$I18N->msg("com_auth_media_settings_unsecure_fileext").'</label>
										<input class="rex-form-text" type="input" id="rex-form-unsecure_fileext" name="unsecure_fileext" value="'.$REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext'].'" />
									</p>
								</div>
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-appSecret">'.$I18N->msg("com_auth_media_settings_xsendfile").'</label>
										<input class="rex-form-checkbox" type="checkbox" id="rex-form-xsendfile" name="xsendfile" value="true" '.$xsendfile_checked.' />
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
