$(document).on('rex:ready', function () {

    // YCom-Artikel Rechte
    $('#yform-ycom_auth-perm-ycom_auth_type select,#yform-ycom_auth-perm-ycom_group_type select').on('change', function() {
        if($('#yform-ycom_auth-perm-ycom_auth_type select option:selected').val() !== "1") {
            $('#yform-ycom_auth-perm-ycom_group_type').hide();
            $('#yform-ycom_auth-perm-ycom_groups').hide();
        } else {
            $('#yform-ycom_auth-perm-ycom_group_type').show();
            let group_type = $('#yform-ycom_auth-perm-ycom_group_type option:selected').val();
            if(group_type === "1" || group_type === "2") {
                $('#yform-ycom_auth-perm-ycom_groups').show();
            } else {
                $('#yform-ycom_auth-perm-ycom_groups').hide();
            }
        }
    });
    $('#yform-ycom_auth-perm-ycom_auth_type select').trigger('change');

    // Medienpool Rechte
    $('select[name="ycom_auth_type"],select[name="ycom_group_type"]').on('change', function() {

        if($('select[name="ycom_auth_type"] option:selected').val() !== "1") {
            $('select[name="ycom_group_type"]').closest('.form-group').hide();
            $('select[name="ycom_groups[]"]').closest('.form-group').hide();
        } else {
            $('select[name="ycom_group_type"]').closest('.form-group').show();
            let group_type_selected = $('select[name="ycom_group_type"] option:selected').val();
            if(group_type_selected === "1" || group_type_selected === "2") {
                $('select[name="ycom_groups[]"]').closest('.form-group').show();
            } else {
                $('select[name="ycom_groups[]"]').closest('.form-group').hide();
            }
        }
    });
    $('select[name="ycom_auth_type"]').trigger('change');
});
