<?php

if ($state == 'error') {
    echo rex_view::error($message);
} else {
    echo rex_view::info($message);
}
