<?php

$content = rex_file::get(rex_path::addon('ycom','install/tablesets/yform_user.json'));
rex_yform_manager_table_api::importTablesets($content);



