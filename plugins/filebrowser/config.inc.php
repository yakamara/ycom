<?php

/**
 * Plugin filebrowser
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

/*
TODOS:
    - Add/Edit/Delete von Ordnern
*/

$mypage = 'filebrowser';
$REX['ADDON']['version'][$mypage] = '1.0';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus, Gregor Harlan';
$REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/redaxo/';

if (isset($I18N) && is_object($I18N)) {
    $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/community/plugins/filebrowser/lang');
}

include $REX['INCLUDE_PATH'] . '/addons/community/plugins/filebrowser/classes/class.rex_com_filebrowser.inc.php';

if ($REX['REDAXO'] && !$REX['SETUP']) {
    if ($REX['USER']) {
        $REX['ADDON']['community']['SUBPAGES'][] = array('plugin.filebrowser' , $I18N->msg('com_filebrowser'));
    }
}

if (rex_request('rex_com_filebrowser_path', 'string') != '' && rex_request('rex_img_type', 'string') != '' && rex_request('rex_img_file', 'string') != '') {

    function rex_com_filebrowser_im($params)
    {

        $login_status = rex_com_auth::login();
        if ($login_status != 1) { // Logged in
            return $params['subject'];
        }

        $image_path = rex_request('rex_com_filebrowser_path', 'string');
        $image_file = rex_request('rex_img_file', 'string');

        $fb = new rex_com_filebrowser();
        $image_path = $fb->setCurrentPath($image_path);
        $image_file = $fb->setCurrentFile($image_file);

        // TODO: check if file is in realm

        $params['subject']['imagepath'] = $fb->getFullPath() . '/' . $image_file;
        $params['subject']['rex_img_file'] = $image_file;

        return $params['subject'];

    }

    rex_register_extension('IMAGE_MANAGER_INIT', 'rex_com_filebrowser_im', array());
}
