/**
 * Fills a ycom password field with a password that satisfies the configured policy.
 *
 * The button carries the policy as JSON in `data-rules` and the target field's name in
 * `data-field`; both are written by the `ycom_password` fragment.
 *
 * REDAXO 5 shipped this as an inline script that extended String.prototype and depended on
 * jQuery and the backend's `rex:ready` event, which meant it never ran in the frontend.
 */
(function () {
    'use strict';

    var SETS = {
        uppercase: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        lowercase: 'abcdefghijklmnopqrstuvwxyz',
        digit: '0123456789',
        symbol: '!@#$%^&*()_+{}:"<>?|[];\',./`~'
    };

    SETS.letter = SETS.uppercase + SETS.lowercase;

    /** A cryptographically random integer in [0, max). */
    function randomInt(max) {
        if (max <= 0) {
            return 0;
        }

        var buffer = new Uint32Array(1);
        var limit = Math.floor(0xffffffff / max) * max;

        do {
            window.crypto.getRandomValues(buffer);
        } while (buffer[0] >= limit);

        return buffer[0] % max;
    }

    function pick(chars, count) {
        var out = '';

        for (var i = 0; i < count; i++) {
            out += chars.charAt(randomInt(chars.length));
        }

        return out;
    }

    /** How many characters a rule asks for; `generate` overrides min/max. */
    function amount(rule) {
        if (!rule || typeof rule !== 'object') {
            return 0;
        }

        if (typeof rule.generate !== 'undefined') {
            return parseInt(rule.generate, 10) || 0;
        }

        var min = typeof rule.min === 'undefined' ? 1 : parseInt(rule.min, 10);
        var max = typeof rule.max === 'undefined' ? min : parseInt(rule.max, 10);

        if (max < min) {
            max = min;
        }

        return min + randomInt(max - min + 1);
    }

    function shuffle(value) {
        var chars = value.split('');

        for (var i = chars.length - 1; i > 0; i--) {
            var j = randomInt(i + 1);
            var tmp = chars[i];
            chars[i] = chars[j];
            chars[j] = tmp;
        }

        return chars.join('');
    }

    function generate(rules) {
        var password = '';
        var pool = '';

        // The character-class rules first — they are the ones that must be satisfied.
        ['uppercase', 'lowercase', 'letter', 'digit', 'symbol'].forEach(function (name) {
            if (!rules[name]) {
                return;
            }

            var chars = SETS[name];
            pool += chars;
            password += pick(chars, amount(rules[name]));
        });

        if (pool === '') {
            pool = SETS.letter + SETS.digit + SETS.symbol;
        }

        // Then pad to the required length.
        var minLength = rules.length && typeof rules.length.min !== 'undefined'
            ? parseInt(rules.length.min, 10)
            : 0;

        if (password.length < minLength) {
            password += pick(pool, minLength - password.length);
        }

        return shuffle(password);
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('.rex-ycom-password-refresh-button');

        if (!button) {
            return;
        }

        event.preventDefault();

        var rules;
        try {
            rules = JSON.parse(button.getAttribute('data-rules')) || {};
        } catch (e) {
            rules = {};
        }

        var field = document.getElementsByName(button.getAttribute('data-field')).item(0);

        if (!field) {
            return;
        }

        field.value = generate(rules);
        // Show the generated password — it is useless if the user cannot read it once.
        field.type = 'text';
        field.dispatchEvent(new Event('input', { bubbles: true }));
    });
})();
