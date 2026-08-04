<?php

/**
 * Frontend variant of the `ycom_auth_password` field template.
 *
 * Hands over to the backend markup; replace the delegate call with your own markup to give the
 * frontend a different look.
 *
 * @var \Yakamara\YForm\View\Fragment $this
 */
echo $this->delegate('yform/value/backend/ycom_password.php');
