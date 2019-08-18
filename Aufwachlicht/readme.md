#  Aufwachlicht
---
## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)
2. [Systemanforderungen](#2-systemanforderungen)
3. [Installation](#3-installation)
4. [Änderungen](#4-änderungen)
---


## 1. Funktionsumfang
- Mit dem Befehl SXAWK_Start($id) wird ein Aufwachlicht gestartet. Ab diesem Zeitpunkt wird das Licht, welches als Ausgang angegeben wurde, in 20 Schritten innerhalb einer angegebenen Zeit von 0 auf 100% gedimmt. 
- Nach erreichen der maximalen Helligkeit wird das Licht nach einer eingestellten Dauer wieder ausgeschaltet.
---


## 2. Systemanforderungen
- IP-Symcon ab Version 5.0
---


## 3. Installation
1. Fügen Sie in der IP-Symcon Managementkonsole im Objektbaum unter "Kern Instanzen" / "Modules" die URL `https://github.com/styletronix/SX_SymconExtension`als neues Modul hinzu.
2. Fügen Sie an einer beliebigen Stelle im Objektbaum die Instanz  `Aufwachlicht` hinzu.
3. Wählen Sie bei "Ausgang" eine Variable aus, welche von 0 bis 100% gedimmt werden soll. Dabei kann jede Variable mit gültigem min/max-Wert verwendet werden, welche auch über die Web UI geschaltet werden kann.
4. Erstellen Sie ein Zyklisches Ereignis, welches zu einem gewünschten Zeitpunkt die Funktion "Start" ausführt.
5. Um das dimmen vorzeitig zu unterbrechen, weisen Sie dem Lichtschalter für das Licht die Funktion "Stop" zu. So wird das dimmen bei betätigung des regulären Lichtschalters abgebrochen.
---

## 4. Änderungen
08.06.2019
- Erstes öffentliches Release
