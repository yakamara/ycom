generate_key|activation_key

text|email|E-Mail:|

captcha|Bitte geben Sie den entsprechenden Sicherheitscode ein. Sollten Sie den Code nicht lesen können klicken Sie bitte auf die Grafik, um einen neuen Code zu generieren.|Sie haben den Sicherheitscode falsch eingegeben.

validate|email|email|Bitte geben Sie die E-Mail ein.
validate|empty|email|Bitte geben Sie Ihre E-Mail ein.

action|copy_value|email|login
action|db_query|update rex_ycom_user set activation_key = ? where login = ?|activation_key,email
action|tpl2email|resetpassword_de|email|
