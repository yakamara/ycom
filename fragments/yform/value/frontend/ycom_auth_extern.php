<?php

/**
 * Frontend variant of the external-auth login link.
 *
 * Hands over to the backend markup; replace the delegate call with your own markup to give the
 * frontend a different look.
 *
 * @var \Yakamara\YForm\View\Fragment $this
 */
echo $this->delegate('yform/value/backend/ycom_auth_extern.php');
