#  Treppenhauslicht
---
## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)
2. [Systemanforderungen](#2-systemanforderungen)
3. [Installation](#3-installation)
4. [Änderungen](#4-änderungen)
---


## 1. Funktionsumfang
- Die Treppenhauslicht-Steuerung ermöglicht das einschalten eines Lichts per Taster und zeitgesteuerte ausschalten mit Vorwarn-Funktion vor dem ausschalten, welche für Mietshäuser gesetzlich vorgeschrieben ist.
---


## 2. Systemanforderungen
- IP-Symcon ab Version 5.0
---


## 3. Installation
1. Fügen Sie in der IP-Symcon Managementkonsole im Objektbaum unter "Kern Instanzen" / "Modules" die URL `https://github.com/styletronix/SX_SymconExtension`als neues Modul hinzu.
2. Fügen Sie an einer beliebigen Stelle im Objektbaum die Instanz  `Treppenhauslicht` hinzu.
3. Fügen Sie im Instanzeditor Sensoren und Melder ein.
---

## 4. Änderungen
23.09.2019
- Variablen mit benutzerdefiniertem ActionScript werden unterstützt.

31.08.2018
- Erstes öffentliches Release
