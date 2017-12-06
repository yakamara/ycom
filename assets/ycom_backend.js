// hide group fields in article edit page if not needed
function change_ycom_fields() {
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

$(document).on('rex:ready', function () {
	change_ycom_fields();	
	$('#yform-ycom_auth-perm-field-0').on('change', function() {
		change_ycom_fields();
	});
	$('#yform-ycom_auth-perm-field-1').on('change', function() {
		change_ycom_fields();
	});
});
