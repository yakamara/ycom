/**
 * Shows the group fields only where they apply: group permissions narrow the "logged-in users only"
 * case, and the group list itself only matters for the "in every / in one group" types.
 *
 * Used by the article permission panel in the structure sidebar and by the media pool form.
 */
$(document).on('rex:ready', function () {
    // ----- article permissions (structure sidebar)
    // The prefix follows the form name set in pages/content.perm.php.
    var articlePrefix = '#yform-ycom-auth-perm-';

    function syncArticleFields() {
        var authType = $(articlePrefix + 'ycom_auth_type select option:selected').val();
        var groupTypeGroup = $(articlePrefix + 'ycom_group_type');
        var groupsGroup = $(articlePrefix + 'ycom_groups');

        if (authType !== '1') {
            groupTypeGroup.hide();
            groupsGroup.hide();
            return;
        }

        groupTypeGroup.show();

        var groupType = $(articlePrefix + 'ycom_group_type option:selected').val();
        groupsGroup.toggle(groupType === '1' || groupType === '2');
    }

    $(articlePrefix + 'ycom_auth_type select, ' + articlePrefix + 'ycom_group_type select').on('change', syncArticleFields);
    syncArticleFields();

    // ----- media pool permissions
    function syncMediaFields() {
        var authType = $('select[name="ycom_auth_type"] option:selected').val();
        var groupTypeGroup = $('select[name="ycom_group_type"]').closest('.form-group');
        var groupsGroup = $('select[name="ycom_groups[]"]').closest('.form-group');

        if (authType !== '1') {
            groupTypeGroup.hide();
            groupsGroup.hide();
            return;
        }

        groupTypeGroup.show();

        var groupType = $('select[name="ycom_group_type"] option:selected').val();
        groupsGroup.toggle(groupType === '1' || groupType === '2');
    }

    $('select[name="ycom_auth_type"], select[name="ycom_group_type"]').on('change', syncMediaFields);
    syncMediaFields();
});
