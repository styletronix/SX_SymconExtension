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
