<?php

if ($REX['REDAXO']) {
    echo 'Hier erscheint ein Filebrowser für diesen Pfad:<br /> <b>REX_VALUE[1]</b>';

} else {
    $fb = new rex_com_filebrowser("REX_VALUE[1]");
    if (rex_com_user::getMe()) {
        if (rex_com_user::getMe()->isAdmin()) {
            $fb->setAdmin();
        }
    }
    $fb->scanDir();
    echo $fb->getView();
}

?>