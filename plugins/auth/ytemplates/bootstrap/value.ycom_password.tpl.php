<?php

/**
 * @var rex_yform_value_abstract $this
 * @psalm-scope-this rex_yform_value_abstract
 */

$type ??= 'text';
$class = 'text' == $type ? '' : 'form-' . $type . ' ';
$script ??= false;
$rules ??= [];

if (!isset($value)) {
    $value = $this->getValue();
}

$notice = [];
if ('' != $this->getElement('notice')) {
    $notice[] = rex_i18n::translate($this->getElement('notice'), false);
}
if (isset($this->params['warning_messages'][$this->getId()]) && !$this->params['hide_field_warning_messages']) {
    $notice[] = '<span class="text-warning">' . rex_i18n::translate($this->params['warning_messages'][$this->getId()]) . '</span>';
}
if (count($notice) > 0) {
    $notice = '<p class="help-block">' . implode('<br />', $notice) . '</p>';
} else {
    $notice = '';
}

$class_group = trim('form-group yform-element ' . $this->getWarningClass());

$class_label = [];
$class_label[] = 'control-label';

$attributes = [
    'class' => 'form-control',
    'name' => $this->getFieldName(),
    'type' => $type,
    'id' => $this->getFieldId(),
    'value' => $value,
];

if (rex::isFrontend()) {
    $attributes['autocomplete'] = 'new-password';
}

$attributes = $this->getAttributeElements($attributes, ['autocomplete', 'pattern', 'required', 'disabled', 'readonly']);

$span = '';
$input_group_start = '';
$input_group_end = '';

if ($script) {
    $funcName = uniqid('rex_ycom_password_create' . $this->getId());
    $span = '<span class="input-group-btn">
    <button type="button" class="btn btn-default getNewPass rex-ycom-password-refresh-button" data-myRules=\'' . json_encode($rules) . '\' data-myField="' . rex_escape($this->getFieldName()) . '"><span class="fa fa-refresh"></span></button>
    </span>';

    $nonce = '';
    $nonce = ' nonce="' . rex_response::getNonce() . '"';

    ?><script type="text/javascript"<?= $nonce ?>>

        $(document).on('rex:ready', function () {
            $(".rex-ycom-password-refresh-button").each(function() {
                $(this).off("click");
                $(this).on("click", function() {
                    rex_ycom_password_refresh(this);
                });
            });
        });

        // Credit to @Blender https://stackoverflow.com/users/464744/blender
        String.prototype.pick = function(min, max) {
            var n, chars = '';

            if (typeof max === 'undefined') {
                n = min;
            } else {
                n = min + Math.floor(Math.random() * (max - min + 1));
            }

            for (var i = 0; i < n; i++) {
                chars += this.charAt(Math.floor(Math.random() * this.length));
            }

            console.log(min+" "+chars);

            return chars;
        };

        // Credit to @Christoph: http://stackoverflow.com/a/962890/464744
        String.prototype.shuffle = function() {
            var array = this.split('');
            var tmp, current, top = array.length;

            if (top) while (--top) {
                current = Math.floor(Math.random() * (top + 1));
                tmp = array[current];
                array[current] = array[top];
                array[top] = tmp;
            }

            return array.join('');
        };

        if (typeof rex_ycom_password_refresh !== 'function') {
            function rex_ycom_password_refresh(button) {

                var rules = {
                    letter: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz",
                    uppercase: "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
                    lowercase: "abcdefghijklmnopqrstuvwxyz",
                    digit: "0123456789",
                    symbol: "!@#$%^&*()_+{}:\"<>?\|[];',./`~",
                    all: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+{}:\"<>?|[];',./`~",
                };
                rules.letter = rules.uppercase + rules.lowercase;
                rules.all = rules.uppercase + rules.lowercase + rules.digit + rules.symbol;

                var ruleset = '';
                var myRules = JSON.parse(decodeURIComponent(button.getAttribute('data-myRules')));
                var myPassword = '';

                if (typeof myRules.uppercase === "object") {
                    min = myRules.uppercase.min;
                    if (typeof myRules.uppercase.min === "undefined") {
                        min = 1;
                    }
                    max = myRules.uppercase.max;
                    if (typeof myRules.uppercase.max === "undefined") {
                        max = min;
                    }
                    generate = myRules.uppercase.generate;
                    if (typeof myRules.uppercase.generate !== "undefined") {
                        min = generate;
                        max = generate;
                    }
                    ruleset += rules.uppercase;
                    myPassword += rules.uppercase.pick(min, max);
                }
                if (typeof myRules.lowercase === "object") {
                    min = myRules.lowercase.min;
                    if (typeof myRules.lowercase.min === "undefined") {
                        min = 1;
                    }
                    max = myRules.lowercase.max;
                    if (typeof myRules.lowercase.max === "undefined") {
                        max = min;
                    }
                    generate = myRules.lowercase.generate;
                    if (typeof myRules.lowercase.generate !== "undefined") {
                        min = generate;
                        max = generate;
                    }
                    ruleset += rules.lowercase;
                    myPassword += rules.lowercase.pick(min, max);
                }
                if (typeof myRules.letter === "object") {
                    min = myRules.letter.min;
                    if (typeof myRules.letter.min === "undefined") {
                        min = 1;
                    }
                    max = myRules.letter.max;
                    if (typeof myRules.letter.max === "undefined") {
                        max = min;
                    }
                    if (min > myPassword.length) {
                        min = min - myPassword.length;
                    } else {
                        min = 0;
                    }
                    if (max > myPassword.length) {
                        max = max - myPassword.length;
                    } else {
                        min = 0;
                    }
                    generate = myRules.letter.generate;
                    if (typeof myRules.letter.max !== "undefined") {
                        min = generate;
                        max = generate;
                    }
                    myPassword += ruleset.pick(min, max);

                }
                if (typeof myRules.digit === "object") {
                    min = myRules.digit.min;
                    if (typeof myRules.digit.min === "undefined") {
                        min = 1;
                    }
                    max = myRules.digit.max;
                    if (typeof myRules.digit.max === "undefined") {
                        max = min;
                    }
                    generate = myRules.digit.generate;
                    if (typeof myRules.digit.max !== "undefined") {
                        min = generate;
                        max = generate;
                    }
                    ruleset += rules.digit;
                    myPassword += rules.digit.pick(min, max);
                }
                if (typeof myRules.symbol === "object") {
                    min = myRules.symbol.min;
                    if (typeof myRules.symbol.min === "undefined") {
                        min = 1;
                    }
                    max = myRules.symbol.max;
                    if (typeof myRules.symbol.max === "undefined") {
                        max = min;
                    }
                    generate = myRules.symbol.generate;
                    if (typeof myRules.symbol.max !== "undefined") {
                        min = generate;
                        max = generate;
                    }
                    ruleset += rules.symbol;
                    myPassword += rules.symbol.pick(min, max);
                }

                if (typeof myRules.length === "object") {
                    min = myRules.length.min;
                    if (typeof myRules.length.min === "undefined") {
                        min = 1;
                    }
                    max = myRules.length.max;
                    if (typeof myRules.length.max === "undefined") {
                        max = min;
                    }
                    if (min > myPassword.length) {
                        min = min - myPassword.length;
                    } else {
                        min = 0;
                    }
                    if (max > myPassword.length) {
                        max = max - myPassword.length;
                    } else {
                        min = 0;
                    }
                    generate = myRules.length.generate;
                    if (typeof myRules.length.max !== "undefined") {
                        min = generate;
                        max = generate;
                    }
                    myPassword += ruleset.pick(min, max);

                }

                var item = document.getElementsByName(button.getAttribute('data-myField')).item(0);
                var name = item.getAttribute('name');
                var type = item.getAttribute('value');

                item.value = myPassword;

            }
        }
    </script><?php

    $input_group_start = '<div class="input-group mif">';
    $input_group_end = '</div>';
}

echo '
<div class="' . $class_group . '" id="' . $this->getHTMLId() . '">
<label class="' . implode(' ', $class_label) . '" for="' . $this->getFieldId() . '">' . $this->getLabel() . '</label>
' . $input_group_start . '
    <input ' . implode(' ', $attributes) . ' />' .
    $notice .
    $span . '
' . $input_group_end . '
</div>';

?>
