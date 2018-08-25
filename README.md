# SX_SymconExtension

Dies ist ein Modul für IP-Symcon, um dessen funktionalität zu erweitern. Derzeit wird in IP-Symcon ab Version 5.0 nur die Gruppensteuerung unterstützt. Die anderen Module werden derzeit überarbeitet.

# Gruppensteuerung
Die Gruppensteuerung ermöglicht das gleichzeitige Schalten mehrerer Endgeräte. Der primäre Zweck ist die Steuerung von Beleuchtung oder Rollos. Allerdings kann die Gruppensteuerung auch universell für andere Zwecke eingesetzt werden. z.B. überall dort, wo der Maximalwert mehrerer Geräte benötigt wird.

Die Gruppensteuerung kann für komplexe Aufgaben kaskadiert werden. Das bedeutet dass eine Gruppe aus mehreren Untergruppen bestehen kann.

### Installation
1.  Fügen Sie SX_SymconExtension zu den Modulen von IP-Symcon hinzu.
2.  Erstellen Sie eine neue Instanz der Gruppensteuerung und speichern Sie die Instanz erst mal ohne weiter Änderung an dieser Stelle
3.  Fügen Sie Links zu schaltbaren variablen in der Kategorie "Geräte" hinzu, welche per Gruppe gesteuert werden sollen.
4.  Öffnen Sie den Instanzeditor und klicken Sie auf "Geräte aktualisieren" damit die Gruppensteuerung die entsprechenden Skripts für die Geräte generiert. Führen Sie diesen Schritt jedes mal aus, wenn Sie Geräte hinzufügen oder entfernen.
5.  Prüfen Sie die Funktion durch verwenden der Taste "An" und "Aus" im Instanzeditor.
6. Fügen Sie gegebenenfalls Bewegungsmelder und Helligkeitssensoren in der entsprechenden Kategorie hinzu und aktualisieren Sie auch dann die Geräte über den Instanzeditor.


### Profile
Der Status der Geräte kann in Profilen gespeichert werden. Wobei für jedes Profil der einzelstatus der Geräte gespeichert wird. Wird z.b. in einem Profil die Lampe1 auf 50% und die Lampe2 auf 100% gesetzt, wird genau dieser Zustand wiederhergestellt, sobald das Profil wieder aufgerufen wird.

Profile können per Event ausgewählt werden. z.B. ist es möglich für Tags und für Nachts getrennte Profile zu speichern und diese nach Zeit oder einen externen Helligkeitssensor umzuschalten. So geht im Wohnzimmer Tags das Licht mit voller Helligkeit an. Und abends z.b. nur die Stehlampe und das Hauptlicht auf 10% gedimmt.
Ist das Licht bereits durch Bewegungsmelder aktiviert worden, führt ein Wechsel des Profils per Event auch zum sofortigen Umschalten der aktuellen Beleuchtung. Ist das Licht ausgeschaltet führt ein Profilwechsel per Event zu keiner Änderung, bis Bewegung im Raum erkannt wurde. Als Ziel für das Event sollte die Funktion SXGRP_UseProfileIDAsPresenceStateTeplateAndApplyToCurrentStateIfPresent verwendet werden.

### Bewegungsmelder
Es werden Bewegungsmelder unterstützt. Sobald Bewegung erkannt wird kann die Gruppe entweder ein-, ausgeschaltet, auf ein bestimmtes Profil gesetzt werden oder den Zustand annehmen in dem sich Geräte zuletzt befanden, als die Bewegung erkannt wurde. Als Bewegungsmelder kann nahezu jedes Gerät verwendet werden, das entweder Abwesend / Anwesend meldet oder nur den Status aktualisiert. So können auch Taster verwendet werden um die Gruppe für eine voreingestellte Zeit einzuschalten. Zusätzlich überwacht die Gruppensteuerung, ob ein Bewegungsmelder auf grund von Störung nicht mehr von Anwesend auf Abwesend meldet. Verbleibt ein Bewegungsmelder also ohne erneute Aktualisierung der Variable auf "Anwesend" wird die Gruppe nach einer einstellbaren Zeit auf "Abwesend" gesetzt.

Vor der Abschaltung der Gruppe kann eine Zeitspanne und ein Level angegeben werden auf das vor der Abschaltung gedimmt werden soll. Wird keine Bewegung mehr erkannt, kann die Gruppe z.b. 20 Sekunden warten, dann die Helligkeit auf 10% stellen, dort nochmals 30 Sekunden warten und dann das Licht ausschalten. So vermeidet man es unerwartet im dunkeln zu stehen.

Bewegungsmelder können über die GUI aktiviert und deaktiviert werden. 

Beispiel:
Ist das Profil für Anwesend "Automatik" und für Abwesend auf "Aus" gestellt, verhält sich die Steuerung wie folgt: Wenn Bewegung erkannt wird (z.B. durch betreten eines Raumes), geschieht erst ein mal nichts. Schaltet man nun das Licht manuell ein, wird dieser Zustand gespeichert. Meldet der Bewegungsmelder nun "Abwesend", wird das Licht im Raum ausgeschaltet. Beim nächsten betreten des Raumes wird das Licht nun in den Zustand gesetzt, wie er zuletzt vor verlassen des Raumes war. Das Licht wird wieder eingeschaltet. Der Status wird dabei für jedes Gerät, welcher der Gruppe zugeordnet ist einzeln gespeichert. Jedes Licht in der Gruppe kann also einen anderen Zustand haben. Dies ist z.b. für Schlafzimmer interessant wo man nachts bei Bewegung nicht unbedingt licht möchte aber dennoch sichergehen will, dass niemand vergisst das licht auszuschalten wenn niemand mehr im Raum ist.

### Steuerung nach Helligkeit
Bewegungsmelder können mit einem Helligkeitssensor kombiniert werden, damit das Licht nur aktiviert wird, wenn dies erforderlich ist. Als Helligkeitssensor kann entweder ein eigener Sensor verwendet werden, oder einen im Bewegungsmelder integrierte Sensor. Wenn der Sensor integriert ist und man für einen Raum (z.b. Treppenhaus) mehrere Bewegungsmelder für eine Gruppe verwendet, kann die Gruppensteuerung so eingestellt werden, dass die Beleuchtung nur aktiviert wird, wenn an dem Bewegungsmelder, welcher die Bewegung erkannt hat, auch die Helligkeit zum einschalten unterschritten wurde. Eine Bewegung im Obergeschoss führt so Tagsüber nicht zu einem einschalten der Gruppe, im dunkeln Keller wird eine Bewegung aber dennoch das Treppenhauslicht aktivieren.

### Alarmbeleuchtung
Die Gruppen verfügen über eine "Alarmbeleuchtung". Wird diese Funktion aktiviert, werden alle Geräte eingeschaltet und können über die Gruppenfunktion nicht mehr abgeschaltet werden. Nach deaktivierung der alarmbeleuchtung kehren alle Geräte in den Zustand vor der aktivierung der Alarmfunktion zurück.

### Manuelle Steuerung
Zur manuellen Steuerung gibt es sowohl einen Schieberegler für Dimmbare Geräte, als auch einen Ein/Aus Schalter für nicht dimmbare Geräte in der Weboberfläche. Werden mehrere Gerätearten in einer Gruppe kombiniert, wird ein dimmen >= 1% automatisch alle nicht dimmbaren Geräte einschalten.

Werden einzelne Geräte nicht über die Gruppe gesteuert, so zeigt die Gruppensteuerung als Status den höchsten Dimm-Wert der Geräte an.

### Liste der unterstützten Befehle
#### SXGRP_StoreProfile: 
Speichert den aktuellen Zustand der Geräte in dem angegebenen Profil (1 - 10)

#### SXGRP_CallProfile:  
Setzt die Beleuchtung auf ein bestimmtes Profil.
Entspricht der Auswahl eines Profils in der Variable "Profil" im WebFront.

#### SXGRP_StoreCurrentProfile: 
Speichert den aktuellen Zustand in dem zuletzt gewählten (dem aktuellen) Profil.
Entspricht der Auswahl "Speichern" der Variable "Profil" im WebFront.

#### SXGRP_UseProfileIDAsPresenceStateTeplateAndApplyToCurrentStateIfPresent: 
Aktiviert Profil X wenn Bewegung erkannt wurde und aktiviert dieses sofort, falls derzeit Bewegung vorhanden ist.

#### SXGRP_UseProfileIDAsPresenceStateTeplate: 
Aktiviert das Profil X wenn Bewegung erkannt wurde. Wenn der Bewegungsstatus bereits "Anwesend" ist, wird der aktuelle Zustand beibehalten und das neue profil erst bei erneiter Anwesenheit aktiviert.

#### SXGRP_SetState: 
Setzt alle Geräte auf einen bestimmten Status (Bool)
Entspricht der Verwendung des Ein / Aus -Schalters "Gesamt" im WebFront

#### SXGRP_SetStateFloat: 
Setzt alle Geräte auf einen bestimmten Status (Float)

#### SXGRP_SetStateInteger: 
Setzt alle Geräte auf einen bestimmten Status (Integer)
Entspricht der Benutzung des Schiebereglers "Gesamt" im WebFront

#### SXGRP:SetProfileAbsent: 
Setzt Profil x für "Abwesenheit" ohne den aktuellen Zustand zu ändern. -3= Ein, -2 = Aus, -1 = Automatik
Entspricht der Variable "Profil Abwesend" im WebFront

#### SXGRP:SetProfilePresent: 
Setzt Profil x für "Anwesenheit" ohne den aktuellen Zustand zu ändern. -3= Ein, -2 = Aus, -1 = Automatik
Entspricht der Variable "Profil Anwesend" im WebFront

#### SXGRP_DisablePresenceDetection:
Deaktiviert die Bewegungsmelder.
Entspricht der Variable "Bewegungsmelder aktiviert" im WebFront.

#### SXGRP_EnablePresenceDetection:
Aktiviert die Bewegungsmelder.
Entspricht der Variable "Bewegungsmelder aktiviert" im WebFront.
Achtung: Leider gibt es in den Befehlen einen Schreibfehler. Dieser muss dummerweise aus kompatibilitätsgründen so übernommen werden.

#### SXGRP_GetCurrentStateString:
Liefert einen String mit dem Wert aller Geräte.

#### SXGRP_SetCurrentStateString:
Setzt alle Geräte auf den Zustand, welcher mit SXGRP_GetCurrentStateString ausgelesen wurde.

#### SXGRP_SetAlertState:
Aktiviert oder deaktiviert die Alarmbeleuchtung.
Entspricht der Variable "Alarmbeleuchtung aktiviert" im WebFront

#### SXGRP_UpdateEvents:
Entspricht der Option "Geräte aktualisieren" im Instanzeditor.


 Alle anderen Funktionen sind für Interne Verwendung bestimmt.
