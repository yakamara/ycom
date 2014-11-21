<?php

$dir = rex_path::pluginData('community', 'filebrowser')

?>
<table class="rex-table">
  <tr>
    <th>Pfad: </th>
    <td><input type="text" size="100" name="VALUE[1]" value="<?php echo "REX_VALUE[1]" ?: $dir ?>" /></td>
  <tr>
  <tr>
    <th>Default:</th>
    <td style="color: #666"><?php echo $dir ?></td>
  </tr>
</table>