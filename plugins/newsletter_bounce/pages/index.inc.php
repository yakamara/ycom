<?php
if (rex_request("func", "string") == "update") {
	// update config
	$REX['ADDON']['newsletter_bounce']['mailhost'] = rex_request('mailhost', 'string');
	$REX['ADDON']['newsletter_bounce']['mailbox_username'] = rex_request('mailbox_username', 'string');
	$REX['ADDON']['newsletter_bounce']['mailbox_password'] = rex_request('mailbox_password', 'string');
	$REX['ADDON']['newsletter_bounce']['port'] = rex_request('port', 'int');
	$REX['ADDON']['newsletter_bounce']['service'] = rex_request('service', 'string');
	$REX['ADDON']['newsletter_bounce']['service_option'] = rex_request('service_option', 'string');
	$REX['ADDON']['newsletter_bounce']['boxname'] = rex_request('boxname', 'string');
	$REX['ADDON']['newsletter_bounce']['user_table'] = rex_request('user_table', 'string');
	$REX['ADDON']['newsletter_bounce']['user_table_email_field'] = rex_request('user_table_email_field', 'string');
	$REX['ADDON']['newsletter_bounce']['user_table_bounce_counter_field'] = rex_request('user_table_bounce_counter_field', 'string');
	$REX['ADDON']['newsletter_bounce']['test_mode'] = rex_request('test_mode', 'boolean');

	$configFile = $REX['INCLUDE_PATH'] . '/addons/community/plugins/newsletter_bounce/config.inc.php';

	$content = '
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'mailhost\'] = "' . $REX['ADDON']['newsletter_bounce']['mailhost'] . '";
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'mailbox_username\'] = "' . $REX['ADDON']['newsletter_bounce']['mailbox_username'] . '";
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'mailbox_password\'] = "' . $REX['ADDON']['newsletter_bounce']['mailbox_password'] . '";
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'port\'] = ' . $REX['ADDON']['newsletter_bounce']['port'] . ';
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'service\'] = "' . $REX['ADDON']['newsletter_bounce']['service'] . '";
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'service_option\'] = "' . $REX['ADDON']['newsletter_bounce']['service_option'] . '";
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'boxname\'] = "' . $REX['ADDON']['newsletter_bounce']['boxname'] . '";
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'user_table\'] = "' . $REX['ADDON']['newsletter_bounce']['user_table'] . '";
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'user_table_email_field\'] = "' . $REX['ADDON']['newsletter_bounce']['user_table_email_field'] . '";
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'user_table_bounce_counter_field\'] = "' . $REX['ADDON']['newsletter_bounce']['user_table_bounce_counter_field'] . '";
		$REX[\'ADDON\'][\'newsletter_bounce\'][\'test_mode\'] = "' . $REX['ADDON']['newsletter_bounce']['test_mode'] . '";
	';

	if (rex_replace_dynamic_contents($configFile, str_replace("\t", "", $content)) !== false) {
		echo rex_info($I18N->msg('com_newsletter_bounce_config_success'));
	} else {
		echo rex_warning($I18N->msg('com_newsletter_bounce_config_fail') . $configFile);
	}

	if (!is_writable($configFile)) {
		echo rex_warning($I18N->msg('com_newsletter_bounce_config_unwritable') . $configFile);
	}

} else if (rex_request("func", "string") == "start_bmh") {
	// create object
	$bmh = new rex_com_newsletter_bounce();
	$bmh->action_function    = 'rex_com_newsletter_bounce::callbackAction';
	//$bmh->verbose            = VERBOSE_SIMPLE; //VERBOSE_REPORT; //VERBOSE_DEBUG; //VERBOSE_QUIET; // default is VERBOSE_SIMPLE
	//$bmh->debug_body_rule    = true; // false is default, no need to specify
	//$bmh->debug_dsn_rule     = true; // false is default, no need to specify
	//$bmh->purge_unprocessed  = false; // false is default, no need to specify
	//$bmh->disable_delete     = false; // false is default, no need to specify

	if ($REX['ADDON']['newsletter_bounce']['test_mode'] == 1) {
		$bmh->testmode = true;
	} else {
		$bmh->testmode = false;
	}

	$bmh->mailhost           = $REX['ADDON']['newsletter_bounce']['mailhost']; // your mail server
	$bmh->mailbox_username   = $REX['ADDON']['newsletter_bounce']['mailbox_username']; // your mailbox username
	$bmh->mailbox_password   = $REX['ADDON']['newsletter_bounce']['mailbox_password']; // your mailbox password
	$bmh->port               = $REX['ADDON']['newsletter_bounce']['port']; // the port to access your mailbox, default is 143
	$bmh->service            = $REX['ADDON']['newsletter_bounce']['service']; // the service to use (imap or pop3), default is 'imap'
	$bmh->service_option     = $REX['ADDON']['newsletter_bounce']['service_option']; // the service options (none, tls, notls, ssl, etc.), default is 'notls'
	$bmh->boxname            = $REX['ADDON']['newsletter_bounce']['boxname']; // the mailbox to access, default is 'INBOX'
	//$bmh->moveHard           = true; // default is false
	//$bmh->hardMailbox        = 'INBOX.hardtest'; // default is 'INBOX.hard' - NOTE: must start with 'INBOX.'
	//$bmh->moveSoft           = true; // default is false
	//$bmh->softMailbox        = 'INBOX.softtest'; // default is 'INBOX.soft' - NOTE: must start with 'INBOX.'
	//$bmh->deleteMsgDate      = '2009-01-05'; // format must be as 'yyyy-mm-dd'
}

// service
$selectService = new rex_select();
$selectService->setName('service');
$selectService->setSize(1);
$selectService->addOption('IMAP', 'imap');
$selectService->addOption('POP3', 'pop3');
$selectService->setSelected($REX['ADDON']['newsletter_bounce']['service']);

// service option
$selectServiceOption = new rex_select();
$selectServiceOption->setName('service_option');
$selectServiceOption->setSize(1);
$selectServiceOption->addOption('None', 'none');
$selectServiceOption->addOption('NOTLS', 'notls');
$selectServiceOption->addOption('TLS', 'tls');
$selectServiceOption->addOption('SSL', 'ssl');
$selectServiceOption->setSelected($REX['ADDON']['newsletter_bounce']['service_option']);

// user table
$sql = rex_sql::factory();
$tables = $sql->showTables();

$selectTables = new rex_select();
$selectTables->setName('user_table');
$selectTables->setSize(1);

foreach($tables as $table) {
	$selectTables->addOption($table, $table);
}

$selectTables->setSelected($REX['ADDON']['newsletter_bounce']['user_table']);

// plugin output
echo '
	<div class="rex-form" id="rex-form-system-setup">	
			<div class="rex-area-col-2">
				<div class="rex-area-col-a">
					<h3 class="rex-hl2">' . $I18N->msg("com_newsletter_bounce_handler") . '</h3>
					<div class="rex-area-content">';
?>

<p class="rex-tx1"><?php echo $I18N->msg('com_newsletter_bounce_info_msg'); ?></p>
<p class="rex-tx1"><?php echo $I18N->msg('com_newsletter_bounce_start_msg'); ?></p>
	
<form action="index.php" method="get">
	<input type="hidden" name="page" value="community" />
	<input type="hidden" name="subpage" value="plugin.newsletter_bounce" />
	<input type="hidden" name="func" value="start_bmh" />
	<p style="text-align: center;">
		<input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N->msg('com_newsletter_bounce_start_button'); ?>" style="width: 50%;" />
  	</p>	
</form>

<?php
// process maibox and echo result
if (rex_request("func", "string") == "start_bmh") {
	echo '<br /><p><strong>Ausgabe:</strong></p>';

	$bmh->openMailbox();
	$bmh->processMailbox();
}
?>

<?php 
			echo '</div>
				</div>
			
				<div class="rex-area-col-b">
				  	<form action="index.php" method="post">
						<input type="hidden" name="page" value="community" />
						<input type="hidden" name="subpage" value="plugin.newsletter_bounce" />
						<input type="hidden" name="func" value="update" />


					<h3 class="rex-hl2">' . $I18N->msg("com_newsletter_bounce_settings") . '</h3>
					
					<div class="rex-area-content">
					
						<fieldset class="rex-form-col-1">
							<legend>' . $I18N->msg("com_newsletter_bounce_mailserver") . '</legend>
							
							<div class="rex-form-wrapper">
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-mailhost">' . $I18N->msg("com_newsletter_bounce_mailserver_host") . '</label>
										<input class="rex-form-text" type="input" id="rex-form-mailhost" name="mailhost" value="' . $REX['ADDON']['newsletter_bounce']['mailhost'] . '" />
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-mailbox_username">' . $I18N->msg("com_newsletter_bounce_mailserver_mailbox_user") . '</label>
										<input class="rex-form-text" type="input" id="rex-form-mailbox_username" name="mailbox_username" value="' . $REX['ADDON']['newsletter_bounce']['mailbox_username'] . '" />
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-mailbox_password">' . $I18N->msg("com_newsletter_bounce_mailserver_mailbox_password") . '</label>
										<input class="rex-form-text" type="input" id="rex-form-mailbox_password" name="mailbox_password" value="' . $REX['ADDON']['newsletter_bounce']['mailbox_password'] . '" />
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-port">' . $I18N->msg("com_newsletter_bounce_mailserver_port") . '</label>
										<input class="rex-form-text" type="input" id="rex-form-port" name="port" value="' . $REX['ADDON']['newsletter_bounce']['port'] . '" />
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-select">
										<label for="rex-form-service">' . $I18N->msg("com_newsletter_bounce_mailserver_service") . '</label>
											'.$selectService->get().'
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-select">
										<label for="rex-form-service_option">' . $I18N->msg("com_newsletter_bounce_mailserver_service_option") . '</label>
											'.$selectServiceOption->get().'
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-boxname">' . $I18N->msg("com_newsletter_bounce_mailserver_mailbox_name") . '</label>
										<input class="rex-form-text" type="input" id="rex-form-boxname" name="boxname" value="' . $REX['ADDON']['newsletter_bounce']['boxname'] . '" />
									</p>
								</div>

							</div>
						</fieldset>

						<fieldset class="rex-form-col-1">
							<legend>' . $I18N->msg("com_newsletter_bounce_mailserver_table") . '</legend>

							<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-select">
										<label for="rex-form-user_table">' . $I18N->msg("com_newsletter_bounce_mailserver_table_user_table") . '</label>
											'.$selectTables->get().'
									</p>
								</div>

							<div class="rex-form-row">
								<p class="rex-form-col-a rex-form-checkbox">
									<label for="rex-form-user_table_email_field">' . $I18N->msg("com_newsletter_bounce_mailserver_email_field") . '</label>
									<input class="rex-form-text" type="input" id="rex-form-user_table_email_field" name="user_table_email_field" value="' . $REX['ADDON']['newsletter_bounce']['user_table_email_field'] . '" />
								</p>
							</div>


							<div class="rex-form-row">
								<p class="rex-form-col-a rex-form-checkbox">
									<label for="rex-form-user_table_bounce_counter_field">' . $I18N->msg("com_newsletter_bounce_mailserver_bounce_counter_field") . '</label>
									<input class="rex-form-text" type="input" id="rex-form-user_table_bounce_counter_field" name="user_table_bounce_counter_field" value="' . $REX['ADDON']['newsletter_bounce']['user_table_bounce_counter_field'] . '" />
								</p>
							</div>
							
						</fieldset>	

						<fieldset class="rex-form-col-1">
							<legend>' . $I18N->msg("com_newsletter_bounce_mailserver_misc") . '</legend>

							<div class="rex-form-row">
								<p class="rex-form-col-a rex-form-checkbox">
									<label for="rex-form-test_mode">' . $I18N->msg("com_newsletter_bounce_mailserver_misc_test_mode") . '</label>
									<input class="rex-form-text" type="checkbox" id="rex-form-test_mode" name="test_mode" value="1" ';
									if ($REX['ADDON']['newsletter_bounce']['test_mode']=="1") echo 'checked="checked"';
									echo ' />
								</p>
							</div>
							
							<div class="rex-form-wrapper">
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-submit">
										<input type="submit" class="rex-form-submit" name="sendit" value="' . $I18N->msg("specials_update") . '"' . rex_accesskey($I18N->msg('specials_update'), $REX['ACKEY']['SAVE']) .' />
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


