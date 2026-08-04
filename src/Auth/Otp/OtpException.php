<?php

namespace Yakamara\YCom\Auth\Otp;

use Redaxo\Core\Exception\RuntimeException;

/**
 * Thrown when an OTP challenge cannot be issued or verified.
 *
 * REDAXO 6's `Exception\Exception` is only a marker interface, so this extends the concrete
 * `RuntimeException` — which implements that interface — instead of `rex_exception`.
 */
class OtpException extends RuntimeException {}
