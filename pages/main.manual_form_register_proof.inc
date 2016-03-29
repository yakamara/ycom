/* http://redaxo//bestaetigung/?rex_ycom_activation_key=xxxx&rex_ycom_id=yyyy */

hidden|status|1
objparams|submit_btn_show|0
objparams|send|1

validate|ycom_auth_login|activation_key=rex_ycom_activation_key,id=rex_ycom_id|status=0|Zugang wurde bereits bestätigt oder ist schon fehlgeschlagen|status

action|ycom_auth_db|update
action|html|<b>Vielen Dank, Sie sind nun eingeloggt und haben Ihre E-Mail bestätigt</b>