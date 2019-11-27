<?php

if (rex::isBackend() && rex::getUser()) {
    if ('ycom/docs' == rex_be_controller::getCurrentPage()) {
        rex_view::addCssFile($this->getAssetsUrl('docs.css'));
    }
}
