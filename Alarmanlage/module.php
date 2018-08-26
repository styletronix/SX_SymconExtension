<?
    class Alarmanlage extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }
		
        public function Create() {
            parent::Create();

			$ApplyChanges = false;
			
			//Variablenprofile erstellen
			if (IPS_VariableProfileExists("SX_Alarm.Modus") == false){
				IPS_CreateVariableProfile("SX_Alarm.Modus", 1);
				IPS_SetVariableProfileValues("Alarm.Modus", 0, 4, 0);
				IPS_SetVariableProfileAssociation("SX_Alarm.Modus", 0, "Deaktiviert", "LockOpen", 0xFFFF00);
				IPS_SetVariableProfileAssociation("SX_Alarm.Modus", 1, "Aktiviert", "LockClosed", 0x00FF00);
				IPS_SetVariableProfileAssociation("SX_Alarm.Modus", 2, "Nur extern aktiviert", "LockClosed", 0x00FF00);
				IPS_SetVariableProfileAssociation("SX_Alarm.Modus", 3, "! ALARM !", "Alert", 0xFF0000);
				IPS_SetVariableProfileAssociation("SX_Alarm.Modus", 4, "WARTUNG", "CloseAll", 0x0000FF);
				IPS_SetVariableProfileIcon("SX_Alarm.Modus",  "Alert");
			}

			if (IPS_VariableProfileExists("SX_Alarm.Sekunden") == false){
				IPS_CreateVariableProfile("SX_Alarm.Sekunden", 1);
				IPS_SetVariableProfileValues("SX_Alarm.Sekunden", 0, 900, 5);
				IPS_SetVariableProfileIcon("SX_Alarm.Sekunden",  "Clock");
				IPS_SetVariableProfileText("SX_Alarm.Sekunden", "", " Sekunden");
			}

			if (IPS_VariableProfileExists("SX_Alarm.Anzahl") == false){
				IPS_CreateVariableProfile("SX_Alarm.Anzahl", 1);
				IPS_SetVariableProfileValues("SX_Alarm.Anzahl", 0, 20, 1);
			}

			
			//Variablen registrieren
			$this->RegisterVariableBoolean("alarm", "Alarm ausgelöst", "~Switch");
            $this->RegisterVariableBoolean("vorwarnung_aktiv", "Vorwarnung aktiv", "~Switch");
			$this->RegisterVariableBoolean("ausgangszeit_aktiv", "Ausgangszeit aktiv", "~Switch");
            
			$this->RegisterVariableInteger("alarmmodus", "Status", "SX_Alarm.Modus");
			$this->EnableAction("alarmmodus");
			

			//Eigenschaften registrieren
			$this->RegisterPropertyString("devices", "");
			$this->RegisterPropertyInteger("dauer_alarmbeleuchtung", 900);
			$this->RegisterPropertyInteger("dauer_sirene", 120);
			$this->RegisterPropertyInteger("dauer_warnlicht", 900);
			$this->RegisterPropertyInteger("retrigger", 2);
			$this->RegisterPropertyInteger("verzoegerung_eingang", 30);
			$this->RegisterPropertyInteger("verzoegerung_ausgang", 120);
			
		

            if ($ApplyChanges == true){
				IPS_ApplyChanges($this->InstanceID);
			}
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->Initialize();
			$this->SetStatus(102);
        }

		public function Initialize(){	
			$arrString = $this->ReadPropertyString("devices");
			$arr = json_decode($arrString);


			
		}


		public function RequestAction($Ident, $Value) {
			switch($Ident) {
				case "alarmmodus":
					SetValueInteger($this->GetIDForIdent($Ident), $Value);
					break;
	
				default:
					throw new Exception("Invalid Ident");

    		}
 		}
    }
?>
