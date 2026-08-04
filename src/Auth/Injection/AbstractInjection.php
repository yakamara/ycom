<?php

namespace Yakamara\YCom\Auth\Injection;

/**
 * A step the auth flow can force a logged-in user through before letting them go anywhere else —
 * accepting new terms of use, changing an expired password, passing an OTP challenge.
 *
 * Injections are registered in {@see \Yakamara\YCom\YComAddon::boot()} with a level that determines
 * the order they are asked in; {@see \Yakamara\YCom\Auth\Auth::init()} walks them on every frontend
 * request.
 */
abstract class AbstractInjection
{
    /**
     * Where this request has to go instead.
     *
     * - a URL: redirect there
     * - `true`: this request already is the injection's own page — stop asking further injections
     * - `false`: nothing to do
     */
    abstract public function getRewrite(): bool|string;

    /** The fieldset this injection contributes to the backend settings page. */
    abstract public function getSettingsContent(): string;

    /** Persists this injection's own settings from the submitted settings form. */
    abstract public function triggerSaveSettings(): void;
}
