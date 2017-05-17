<?php

rex_yform_manager_dataset::setModelClass('rex_ycom_media', rex_ycom_media::class);
rex_ycom::addTable('rex_ycom_media');

rex_extension::register( 'PACKAGES_INCLUDED', ['rex_ycom_media_handle', 'init'] );

if (!rex::isBackend())
{
    rex_extension::register( 'PACKAGES_INCLUDED', ['rex_ycom_media_request', 'init'] );
}