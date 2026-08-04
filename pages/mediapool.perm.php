<?php

/**
 * The permission fields on the media pool form, plus saving them.
 *
 * Included from the `MEDIA_FORM_ADD` / `MEDIA_FORM_EDIT` / `MEDIA_ADDED` / `MEDIA_UPDATED`
 * listeners; returns the markup to append to the form.
 *
 * Addon::includeFile() `extract()`s its context array, so `$ep` arrives as a plain variable.
 *
 * @var Yakamara\YCom\YComAddon $this
 * @var Redaxo\Core\ExtensionPoint\ExtensionPoint $ep
 */

use Redaxo\Core\Core;
use Redaxo\Core\Database\Sql;
use Redaxo\Core\Exception\RuntimeException;
use Redaxo\Core\Form\Select\Select;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Translation\I18n;
use Redaxo\Core\View\Fragment;
use Yakamara\YCom\Group\Group;
use Yakamara\YCom\MediaAuth\MediaAuth;

$epParams = $ep->getParams();

$mediaId = null;
$activeItem = null;

switch ($ep->name) {
    case 'MEDIA_FORM_EDIT':
        $activeItem = $epParams['media'] ?? null;
        $mediaId = $epParams['id'] ?? null;
        break;

    case 'MEDIA_UPDATED':
        $mediaId = $epParams['id'] ?? null;
        break;

    case 'MEDIA_ADDED':
        // The row exists but the extension point only knows the file name.
        $sql = Sql::factory();
        $sql->setQuery('SELECT id FROM ' . Core::getTable('media') . ' WHERE filename = ?', [$epParams['filename'] ?? '']);

        if (1 !== $sql->getRows()) {
            throw new RuntimeException('Error occurred during file upload!');
        }

        $mediaId = (int) $sql->getValue('id');
        break;
}

$authDefault = Request::request('ycom_auth_type', 'string', '');
if (!array_key_exists($authDefault, MediaAuth::PERMS)) {
    $authDefault = (string) array_key_first(MediaAuth::PERMS);
}

$groupTypeDefault = Request::request('ycom_group_type', 'string', '');
if (!array_key_exists($groupTypeDefault, Group::PERMS)) {
    $groupTypeDefault = (string) array_key_first(Group::PERMS);
}

$groupsDefault = Request::request('ycom_groups', 'array', []);

// ----- save
if ('post' === Request::requestMethod() && null !== $mediaId) {
    Sql::factory()
        ->setTable(Core::getTable('media'))
        ->setWhere('id = :mediaid', ['mediaid' => $mediaId])
        ->setValue('ycom_auth_type', $authDefault)
        ->setValue('ycom_group_type', $groupTypeDefault)
        ->setValue('ycom_groups', implode(',', $groupsDefault))
        ->update();

    if (null !== $activeItem) {
        $activeItem->setValue('ycom_auth_type', $authDefault);
        $activeItem->setValue('ycom_group_type', $groupTypeDefault);
        $activeItem->setValue('ycom_groups', implode(',', $groupsDefault));
    }
}

// ----- fields
/** Renders one select as a form row. */
$field = static function (Select $select, string $label): string {
    $fragment = new Fragment();
    $fragment->setVar('elements', [['label' => '<label>' . $label . '</label>', 'field' => $select->get()]], false);

    return $fragment->parse('core/form/form.php');
};

$authTypeSelect = new Select();
$authTypeSelect->setName('ycom_auth_type');
$authTypeSelect->setSize(1);
$authTypeSelect->setAttribute('class', 'selectpicker form-control');
foreach (MediaAuth::PERMS as $key => $name) {
    $authTypeSelect->addOption(I18n::translate($name), $key);
}
$authTypeSelect->setSelected(null === $activeItem ? $authDefault : (string) $activeItem->getValue('ycom_auth_type'));

$groupTypeSelect = new Select();
$groupTypeSelect->setName('ycom_group_type');
$groupTypeSelect->setSize(1);
$groupTypeSelect->setAttribute('class', 'selectpicker form-control');
foreach (Group::PERMS as $key => $name) {
    $groupTypeSelect->addOption(I18n::translate($name), $key);
}
$groupTypeSelect->setSelected(null === $activeItem ? $groupTypeDefault : (string) $activeItem->getValue('ycom_group_type'));

$groupsSelect = new Select();
$groupsSelect->setName('ycom_groups[]');
$groupsSelect->setSize(5);
$groupsSelect->setMultiple();
$groupsSelect->setAttribute('class', 'selectpicker form-control');
foreach (Group::getGroups() as $groupId => $groupName) {
    $groupsSelect->addOption($groupName, $groupId);
}
if (null !== $activeItem && '' !== (string) $activeItem->getValue('ycom_groups')) {
    $groupsSelect->setSelected(explode(',', (string) $activeItem->getValue('ycom_groups')));
} elseif (0 < count($groupsDefault)) {
    $groupsSelect->setSelected($groupsDefault);
}

return $ep->subject
    . $field($authTypeSelect, I18n::msg('ycom_auth_perm'))
    . $field($groupTypeSelect, I18n::msg('ycom_group_type'))
    . $field($groupsSelect, I18n::msg('ycom_groups'));
