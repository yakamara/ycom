<?php

/**
 * The `ycom_auth_password` field.
 *
 * Like yform's own text field, plus an optional button that generates a password matching the
 * configured policy. The generator itself lives in ycom's `password-generator.js`.
 *
 * @var \Yakamara\YForm\View\Fragment $this
 * @var \Yakamara\YForm\Value\AbstractValue $yfield
 */

use Redaxo\Core\Addon\Addon;
use Redaxo\Core\Core;
use Redaxo\Core\Http\Response;
use Redaxo\Core\Translation\I18n;

use function Redaxo\Core\View\escape;

extract($this->getVariables());

$type ??= 'password';
$script ??= false;
$rules ??= [];

if (!isset($value)) {
    $value = $yfield->getValue();
}

$notice = [];
if ('' != $yfield->getElement('notice')) {
    $notice[] = I18n::translate($yfield->getElement('notice'), false);
}
if (isset($yfield->params['warning_messages'][$yfield->getId()]) && !$yfield->params['hide_field_warning_messages']) {
    $notice[] = '<span class="text-warning">' . I18n::translate($yfield->params['warning_messages'][$yfield->getId()]) . '</span>';
}
$notice = count($notice) > 0 ? '<p class="help-block small">' . implode('<br />', $notice) . '</p>' : '';

$classGroup = trim('form-group yform-element ' . $yfield->getWarningClass());

$attributes = [
    'class' => 'form-control',
    'name' => $yfield->getFieldName(),
    'type' => $type,
    'id' => $yfield->getFieldId(),
    'value' => $value,
];

if (Core::isFrontend()) {
    // Keep password managers from offering the *current* password for a new one.
    $attributes['autocomplete'] = 'new-password';
}

$attributes = $yfield->getAttributeElements($attributes, ['autocomplete', 'pattern', 'required', 'disabled', 'readonly']);

$span = '';
$inputGroupStart = '';
$inputGroupEnd = '';

if ($script) {
    $span = '<span class="input-group-btn">'
        . '<button type="button" class="btn btn-default rex-ycom-password-refresh-button"'
        . ' data-rules="' . escape((string) json_encode($rules)) . '"'
        . ' data-field="' . escape($yfield->getFieldName()) . '"'
        . ' title="' . I18n::msg('ycom_password_generate') . '">'
        . '<span class="fa fa-refresh"></span></button>'
        . '</span>';

    $inputGroupStart = '<div class="input-group">';
    $inputGroupEnd = '</div>';

    echo '<script src="' . Addon::require('ycom')->getAssetsUrl('password-generator.js')
        . '" nonce="' . Response::getNonce() . '" defer></script>';
}

echo '
<div class="' . $classGroup . '" id="' . $yfield->getHTMLId() . '">
    <label class="control-label" for="' . $yfield->getFieldId() . '">' . $yfield->getLabel() . '</label>
    ' . $inputGroupStart . '
        <input ' . implode(' ', $attributes) . ' />' . $span . '
    ' . $inputGroupEnd . $notice . '
</div>';
