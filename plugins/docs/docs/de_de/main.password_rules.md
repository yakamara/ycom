# Passwortregeln



In der YCom sind Passwortregeln vorgesehen, welchen man an verschiedenen Stellen entsprechend hinterlegen muss, immer dann, wenn ein Passwort eingegeben werden soll. Wie z.B. beim Passwort ändern - Formular oder bei der Registrierung. Weiterhin wird im Backend selbst in der Usertabelle auch das Passwort verwendet, dort muss es auch angelegt sein (ist es per default), oder nach eigenen Wünschen abgeändert werden.

### JSON String definiert die Passwortregeln


so sieht der Standardeintrag aus.

```
{"length":{"min":6},"letter":{"min":1,"generate":10},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":1},"symbol":{"min":0}}
```

Dabei können die verschiedenen Eingabezeichen mit minimum und maximal festgelegt werden, wie auch mit generate, welche im Backend dann bei der Passworterstellung eine exakte Länge angiebt.


Beispiel:

Passwort ändern Formular:
```
ycom_auth_password|password|Ihr Passwort:*|{"length":{"min":6},"letter":{"min":1},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":1},"symbol":{"min":0}}|Das Passwort muss mindestens 6 Zeichen lang sein und mindestens eine Ziffer enthalten
```




