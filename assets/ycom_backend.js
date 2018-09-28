// hide group fields in article edit page if not needed
function change_ycom_article_fields() {
    if($('#yform-ycom_auth-perm-field-0 option:selected').val() != 1) {
        $('#yform-ycom_auth-perm-field-1').closest('.form-group').hide();
        $('#yform-ycom_auth-perm-field-2').closest('.form-group').hide();
    }
    else {
        $('#yform-ycom_auth-perm-field-1').closest('.form-group').show();
        if($('#yform-ycom_auth-perm-field-1 option:selected').val() == 1 || $('#yform-ycom_auth-perm-field-1 option:selected').val() == 2) {
            $('#yform-ycom_auth-perm-field-2').closest('.form-group').show();
        }
        else {
            $('#yform-ycom_auth-perm-field-2').closest('.form-group').hide();
        }
    }
}

function change_ycom_media_fields() {

    if($('select[name="ycom_auth_type"] option:selected').val() != 1) {
        $('select[name="ycom_group_type"]').closest('.form-group').hide();
        $('select[name="ycom_groups[]"]').closest('.form-group').hide();
    } else {
        $('select[name="ycom_group_type"]').closest('.form-group').show();
        if($('select[name="ycom_group_type"] option:selected').val() == 1 || $('select[name="ycom_group_type"] option:selected').val() == 2) {
            $('select[name="ycom_groups[]"]').closest('.form-group').show();
        } else {
            $('select[name="ycom_groups[]"]').closest('.form-group').hide();
        }
    }
}

$(document).on('rex:ready', function () {

    change_ycom_article_fields();
    $('#yform-ycom_auth-perm-field-0').on('change', function() {
        change_ycom_article_fields();
    });
    $('#yform-ycom_auth-perm-field-1').on('change', function() {
        change_ycom_article_fields();
    });

    change_ycom_media_fields();
    $('select[name="ycom_auth_type"]').on('change', function() {
        change_ycom_media_fields();
    });
    $('select[name="ycom_group_type"]').on('change', function() {
        change_ycom_media_fields();
    });

});
