#  Alarmanlage
---
## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)
2. [Systemanforderungen](#2-systemanforderungen)
3. [Installation](#3-installation)


## 1. Funktionsumfang
Die Alarmanlage wurde einer klassischen Alarmanlage nachempfunden und bietet unter anderem folgende Möglichkeiten.
- Alarmzonen unterteilt in 24h-Alarm (Rauchmelder / Technik / Sabotage), Technik-Alarm und Einbruch-Alarm.
- Eingangs- und Ausgangsverzögerung je nach ausgelöstem Sensor.
- Vorwarnung bei der Eingangs- und Ausgangsverzögerung.
- Ansteuerung von Sirenen, Warnlichtern und Alarmbeleuchtung mit unterschiedlicher Dauer bis zur automatischen Deaktivierung.
- Alarm-Sperre bei zu häufiger auslösung bis zum Reset.
- Betriebsmodus: Deaktiviert, Aktiviert, Intern Aktiviert und Wartung.
- Statustext zur Nutzung in Text-To-Speech systemen oder Anzeige auf einem Display.


## 2. Systemanforderungen
- IP-Symcon ab Version 5.0

## 3. Installation
1. Fügen Sie in der IP-Symcon Managementkonsole im Objektbaum unter "Kern Instanzen" / "Modules" die URL `https://github.com/styletronix/SX_SymconExtension`als neues Modul hinzu.
2. Fügen Sie an einer beliebigen Stelle im Objektbaum die Instanz  `Alarmanlage` hinzu.
3. Fügen Sie im Instanzeditor Sensoren und Melder ein.

## 4. Sensoren
Sensoren dienen der Überwachung der Umgebung. Dies können z.b. Bewegungsmelder oder Tür- und Fensterkontakte sein.
Bei jedem Sensor können bestimmte Einstellungen vorgenommen werden. Es sind allerdings nicht alle Kombinationen von Einstellungen möglich.

#### Sensor
Fügen Sie einen neuen Sensor durch klick auf "Hinzufügen" ein. Wählen Sie eine Variable eines Sensors aus, der sich bei Alarm ändert.

#### Bezeichnung
Die Bezeichnung wird für das Feld "Auslösender Sensor" und für den "TTS Ausgabetext" verwendet.

#### 24h-Alarm (Sabotage / Technik)
Wenn diese Option aktiviert ist, wird der Sensor dauerhaft überwacht. Er löst auch bei deaktivierter Alarmanlage einen Alarm aus. Die Überwachung dieses Sensors löst nur dann keinen Alarm aus, wenn sich die Alarmanlage im Status "WARTUNG" befindet.
Dies ist unter anderem für Sabotage-Kontakte, Wassermelder, Brandmelder usw. gedacht.

#### Technik-Alarm
Bei aktiver Option ist der Sensor als Technik-Alarm deklariert. Die Alarmauslösung erfolgt wie bei den anderen Sensoren, allerdings kann für einen Technik-Alarm eine andere Signalisierungsart ausgewählt werden. z.B. kann bei Technik-Alarm die Aussensirene deaktiviert bleiben.
###### Hinweis:
Ist sowol 24h-Alarm als auch Technik-Alarm für einen Sensor aktiv, wird beim auslösen ein Technik-Alarm und kein 24h-Alarm ausgelöst.

#### Bei jeder Aktualisierung auslösen
Normalerweise löst die Alarmanlage nur dann aus, wenn der Sensor vom Wert "false" auf den Wert "true" wechselt oder bei einer aktualisierung der variable immer noch auf "true" steht. Ein wechsel von "true" auf "false" oder aktualisieren der Variable auf "false" führt zu keinem Alarm.
Bei aktiver Option führt jede aktualisierung der Variable, gleichgültig ob sich deren Status geändert hat, zu einem Alarm.

### Ausgangsverzögerung
Die Ausgangsverzögerung ist wirksam, nachdem die Alarmanlage aktiviert wurde. Es läuft eine voreinstellbare Zeit ab, bevor der Sensor scharf geschaltet wird.
Sensoren mit deaktivierter Ausgangsverzögerung können direkt nach dem aktivieren der Alarmanlage einen Alarm auslösen, während Sensoren mit aktivierter Ausgangsverzögerung erst nach dem Ablauf der eingestellten Ausgangsverzögerung einen Alarm melden können.

### Eingangsverzögerung
Ein Sensor mit deaktivierter Eingangsverzögerung löst bei aktivierung sofort einen Alarm aus, während ein Sensor mit aktivierter Eingagnsverzögerung zuerst eine Vorwarnung mit einstellbarer Zeit auslöst, bevor der eigentliche Alarm gestartet wird.

### Intern aktiv
Die Alarmanlage kann "aktiviert" und "intern aktiviert" werden. Im Zustand "aktiviert" werden alle Sensoren überwacht, während im Zustand "intern aktiviert" nur Sensoren überwacht werden, bei denen die Option "Intern aktiv" aktiviert wurde.

Bei Sensoren welche im Wohnbereich angebracht sind sollte die Option "intern aktiv" deaktiviert und bei Sensoren, welche den aussenbereich überwachen aktiviert sein.




## Änderungen
31.08.2018
- Erstes öffentliches Release

28.08.2018
- Erste Beta-Version




## Betriebsmodus
TODO

## Sensoren
TODO

## Aktoren
TODO

## PHP-Funktionen
TODO
