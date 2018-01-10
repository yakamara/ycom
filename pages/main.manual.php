<?php

$content = '';

$file_allgemein_txt = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_allgemein_text_md.inc'));
$file_navi_txt = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_navi_text_md.inc'));

$file_userheader_navi_txt = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_user_header_navi_text_md.inc'));
$file_userheader_navi_code = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_user_header_navi_code.inc'));

$file_form_login = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_form_login.inc'));
$file_form_register = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_form_register.inc'));
$file_form_register_proof = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_form_register_proof.inc'));
$file_form_profile = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_form_profile.inc'));
$file_form_change_password = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_form_change_password.inc'));
$file_form_reset_password = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_form_reset_password.inc'));
$file_form_reset_password_proof = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_form_reset_password_proof.inc'));

$file_emailtemplate_registration = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_emailtemplate_registration.inc'));
$file_emailtemplate_resetpassword = rex_file::get(rex_path::addon('ycom', 'pages/main.manual_emailtemplate_resetpassword.inc'));

$content .= rex_ycom::parseText($file_allgemein_txt);
$content .= '<hr/>';
$content .= rex_ycom::parseText($file_navi_txt);
$content .= '<hr/>';
$content .= rex_ycom::parseText($file_userheader_navi_txt);
$content .= rex_string::highlight($file_userheader_navi_code);
$content .= '<hr/>';
$content .= '<h3>Formulare</h3>';
$content .= '<br/>Login:<br/>';
$content .= rex_string::highlight($file_form_login);
$content .= '<br/>Registrierung:<br/>';
$content .= rex_string::highlight($file_form_register);
$content .= '<br/>Registrierungsbestätigung:<br/>';
$content .= rex_string::highlight($file_form_register_proof);
$content .= '<br/>Profil:<br/>';
$content .= rex_string::highlight($file_form_profile);
$content .= '<br/>Password ändern:<br/>';
$content .= rex_string::highlight($file_form_change_password);
$content .= '<br/>Password zurücksetzen:<br/>';
$content .= rex_string::highlight($file_form_reset_password);
$content .= '<br/>Password zurücksetzen Bestätigung:<br/>';
$content .= rex_string::highlight($file_form_reset_password_proof);
$content .= '<h3>E-Mail Templates</h3>';
$content .= '<code>access_request_de</code> für die Bestätigungsmail (HTML):<br/>';
$content .= rex_string::highlight($file_emailtemplate_registration);
$content .= '<code>resetpassword_de</code> für die Passwort zurücksetzen Mail (HTML):<br/>';
$content .= rex_string::highlight($file_emailtemplate_resetpassword);


$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('ycom_manual_title'), '');
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
