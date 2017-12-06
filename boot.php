<?php

if (rex::isBackend()) {
    rex_extension::register('PACKAGES_INCLUDED', function ($params) {
        $plugin = rex_plugin::get('yform', 'manager');

        if ($plugin) {
            $pages = $plugin->getProperty('pages');
            $ycom_tables = rex_ycom::getTables();

            if (isset($pages) && is_array($pages)) {
                foreach ($pages as $page) {
                    if (in_array($page->getKey(), $ycom_tables)) {
                        $page->setBlock('ycom');
                    }
                }
            }
        }
    });
}

rex_ycom::addTable('rex_ycom_user');
rex_yform_manager_dataset::setModelClass('rex_ycom_user', rex_ycom_user::class);

if(rex::isBackend() && rex_url::currentBackendPage() == "index.php?page=content/edit") {
	rex_extension::register('OUTPUT_FILTER', 'append_ycom_script');
}

/**
 * Adds JS to media pool, showing goup field only if needed
 * @param rex_extension_point $ep Redaxo extension point
 */
function append_ycom_script(rex_extension_point $ep) {
	// Insert before </body> element
	$insert_body = "<script type='text/javascript'>". PHP_EOL
		."function change_ycom_fields() {". PHP_EOL
		."	if($('#yform-ycom_auth-perm-field-0 option:selected').val() != 1) {". PHP_EOL
		."		$('#yform-ycom_auth-perm-field-1').closest('.form-group').hide();". PHP_EOL
		."		$('#yform-ycom_auth-perm-field-2').closest('.form-group').hide();". PHP_EOL
		."	}". PHP_EOL
		."	else {". PHP_EOL
		."		$('#yform-ycom_auth-perm-field-1').closest('.form-group').show();". PHP_EOL
		."		if($('#yform-ycom_auth-perm-field-1 option:selected').val() == 1 || $('#yform-ycom_auth-perm-field-1 option:selected').val() == 2) {". PHP_EOL
		."			$('#yform-ycom_auth-perm-field-2').closest('.form-group').show();". PHP_EOL
		."		}". PHP_EOL
		."		else {". PHP_EOL
		."			$('#yform-ycom_auth-perm-field-2').closest('.form-group').hide();". PHP_EOL
		."		}". PHP_EOL
		."	}". PHP_EOL
		."}". PHP_EOL

		."$(document).on('rex:ready', function () {". PHP_EOL
		."	change_ycom_fields();". PHP_EOL	
		."	$('#yform-ycom_auth-perm-field-0').on('change', function() {". PHP_EOL
		."		change_ycom_fields();". PHP_EOL
		."	});". PHP_EOL
		."	$('#yform-ycom_auth-perm-field-1').on('change', function() {". PHP_EOL
		."		change_ycom_fields();". PHP_EOL
		."	});". PHP_EOL
		."});". PHP_EOL
		."</script>";
	$ep->setSubject(str_replace('</body>', $insert_body .'</body>', $ep->getSubject()));
}
