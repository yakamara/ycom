// hide group field in media pool if not needed
function change_ycom_auth_media_fields() {
	if($('#rex-metainfo-med_ycom_auth_media_users option:selected').val() != 2) {
		$('#rex-metainfo-med_ycom_auth_media_groups').closest('.rex-form-group').hide();
	}
	else {
		$('#rex-metainfo-med_ycom_auth_media_groups').closest('.rex-form-group').show();
	}
}		

$(document).on('rex:ready', function () {
	change_ycom_auth_media_fields();
	$('#rex-metainfo-med_ycom_auth_media_users').on('change', function() {
		change_ycom_auth_media_fields();
	});
});