<?php

/**
 * @var Yakamara\YCom\YComAddon $this
 */

use Redaxo\Core\Backend\Controller;
use Redaxo\Core\View\View;

echo View::title($this->i18n('ycom_title'));

Controller::includeCurrentPageSubPath();
