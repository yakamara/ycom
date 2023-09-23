<?php

/**
 * @var rex_yform_value_abstract $this
 * @psalm-scope-this rex_yform_value_abstract
 */

$url ??= '';
$name ??= '';

echo '<a class="' . $this->name . '" href="' . $url . '">' . $this->name . '</a>';
