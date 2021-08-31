<?php

if ('ycom/docs' != rex_be_controller::getCurrentPage()) {
    echo rex_view::title($this->i18n('ycom_title'));
}

rex_be_controller::includeCurrentPageSubPath();

