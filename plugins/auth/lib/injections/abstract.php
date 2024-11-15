<?php

abstract class rex_ycom_injection_abtract
{
    abstract public function getRewrite(): bool|string;

    abstract public function getSettingsContent(): string;

    abstract public function triggerSaveSettings(): void;
}
