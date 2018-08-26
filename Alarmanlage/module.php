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
				IPS_SetVariableProfileAssociation("SX_Alarm.Modus", 2, "Intern Aktiviert", "Presence", 0x00FF00);
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
			$this->RegisterVariableBoolean("technik_alarm", "Technik-Alarm ausgelöst", "~Switch");
			$this->RegisterVariableBoolean("24h_alarm", "24h-Alarm (Sabotage) ausgelöst", "~Switch");
            $this->RegisterVariableBoolean("vorwarnung_aktiv", "Vorwarnung aktiv", "~Switch");
			$this->RegisterVariableBoolean("eingangszeit_aktiv", "Einganszeit aktiv", "~Switch");
			$this->RegisterVariableBoolean("ausgangszeit_aktiv", "Ausgangszeit aktiv", "~Switch");
            $this->RegisterVariableString("deviceTriggered", "Auslösender Sensor", "");
			
			$this->RegisterVariableInteger("alarmmodus", "Status", "SX_Alarm.Modus");
			$this->EnableAction("alarmmodus");
			
			$id = $this->RegisterVariableBoolean("alertactive", "Alarmmerker");
			IPS_SetHidden($id, true); 
			
			$id = $this->RegisterVariableInteger("alertcount", "Alarmzähler");
			IPS_SetHidden($id, true); 
		
			
			//Eigenschaften registrieren
			$this->RegisterPropertyString("devices", null);
			$this->RegisterPropertyString("melder", null);
			
			$this->RegisterPropertyInteger("dauer_alarmbeleuchtung", 900);
			$this->RegisterPropertyInteger("dauer_sirene", 120);
			$this->RegisterPropertyInteger("dauer_warnlicht", 900);
			$this->RegisterPropertyInteger("retrigger", 2);
			$this->RegisterPropertyInteger("verzoegerung_eingang", 30);
			$this->RegisterPropertyInteger("verzoegerung_ausgang", 120);
			
			$ScriptID = $this->RegisterScript('onDeviceStatusChanged', 'onDeviceStatusChanged', '<? SXALERT_DeviceStatusChanged('.$this->InstanceID.', $_IPS["VARIABLE"]); ?>'); 
			IPS_SetHidden($ScriptID, true); 
		
			$ScriptID = $this->RegisterTimer("ArmDelay", 0, 'SXALERT_ArmSystem('.$this->InstanceID.');');
			IPS_SetHidden($ScriptID, true); 

            if ($ApplyChanges == true){
				IPS_ApplyChanges($this->InstanceID);
			}
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

			// $this->Initialize();
			$this->SetStatus(102);
        }

		public function Initialize(){	
			$arrString = $this->ReadPropertyString("devices");
			$arr = json_decode($arrString, true);
					
			$ScriptID = IPS_GetObjectIDByIdent("onDeviceStatusChanged", $this->InstanceID); 
			
			$foundIDs = array();
			
			foreach($arr as $key1) {
				$key2 = $key1["InstanceID"];
				
				$itemObject = IPS_GetObject($key2);
				$TargetID = $key2;
				$TargetName = IPS_GetName($key2);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($key2)["TargetID"];
				}


				if ($TargetID > 0){
					$EventName = "TargetID ".$TargetID;
					$foundIDs[] = $EventName;

					@$EventID = IPS_GetEventIDByName($EventName, $ScriptID);
					if ($EventID === false){
						$EventID = IPS_CreateEvent(0);
						IPS_SetEventTrigger($EventID, 0, $TargetID);
						IPS_SetName($EventID, $EventName);
						IPS_SetParent($EventID, $ScriptID);
						IPS_SetEventActive($EventID, true);
					}
				}
			}

			foreach(IPS_GetChildrenIDs($ScriptID) as $key2) {
				$EventName = IPS_GetName($key2);
				if (!in_array ($EventName, $foundIDs)){
					IPS_DeleteEvent($key2);
				}
			}
			
		}

		public function DeviceStatusChanged(int $DeviceID){
			$alarmmodus = GetValue($this->GetIDForIdent("alarmmodus"));
						
			// Alarmanlage im Wartungsmodus. Keine Auswertung der Sensoren durchführen.
			if ($alarmmodus == 4){ return; }
			
			$DeviceParameters = $this->GetDeviceParameter($DeviceID);
			if ($DeviceParameters == null){ return; }
			
			$Status = GetValue($DeviceID);		
			// Gerät meldet false. Keine Auswertung durchführen.
			if ($Status == false){ return; }
			
			if ($DeviceParameters["24h"] == true){
				$this->TriggerDeviceAlert($DeviceParameters);
				return;
			}

			if ($DeviceParameters["verzoegerung_ausgang"] == true){
				$ausgangszeit_aktiv = GetValueBoolean($this->GetIDForIdent("ausgangszeit_aktiv"));
				// Kein Alarm bei aktiver Ausgangszeit auslösen.
				if ($ausgangszeit_aktiv == true){ return; }
			}
			
			if ($alarmmodus == 2){
				// Intern aktiviert
				if ($DeviceParameters["istInternAktiv"] == true){
					$this->TriggerDeviceAlert($DeviceParameters);
					return;
				}
			}
			
			if ($alarmmodus == 1){
				// Gesamt Aktiviert
				$this->TriggerDeviceAlert($DeviceParameters);
				return;
			}
		}
		
		public function Reset(){
			$this->SetTimerInterval ("ArmDelay", 0);
			
			SetValueString($this->GetIDForIdent("deviceTriggered"), $DeviceParameters["Bezeichnung"]);
			SetValueBoolean($this->GetIDForIdent("alarm"), false);
			SetValueBoolean($this->GetIDForIdent("technik_alarm"), false);
			SetValueBoolean($this->GetIDForIdent("24h_alarm"), false);
			SetValueBoolean($this->GetIDForIdent("vorwarnung_aktiv"), false);
			SetValueBoolean($this->GetIDForIdent("eingangszeit_aktiv"), false);
			SetValueBoolean($this->GetIDForIdent("ausgangszeit_aktiv"), false);
			SetValueBoolean($this->GetIDForIdent("alertactive"), false);
			SetValueInteger($this->GetIDForIdent("alertcount"), 0);
		}
		
		public function SetMode(int $Modus){
			switch($Modus) {
				case 0:
					// Deaktiviert
					SetValueInteger($this->GetIDForIdent("alarmmodus"), $Value);
					$this->Reset();	
					break;
	
				case 1:
					//Aktiviert
				case 2:
					//Intern Aktiviert
					SetValueInteger($this->GetIDForIdent("alarmmodus"), $Value);
					$this->ArmSystemDelayed();
					break;

				case 4:
					//Wartung
					
					$this->Reset();	
					break;
					
				default:
					throw new Exception("Ungültiger Modus");
    		}
			
			SetValueInteger($this->GetIDForIdent("alarmmodus"), $Value);
		}
		
		private function ArmSystemDelayed(){
			$ExitDelay = $this->ReadPropertyInteger("verzoegerung_ausgang");
			if ($ExitDelay > 0){
				SetValueBoolean($this->GetIDForIdent("ausgangszeit_aktiv"), true);
			}
			
			$this->SetTimerInterval ("ArmDelay", $ExitDelay * 1000);
		}
		
		public function ArmSystem(){
			SetValueBoolean($this->GetIDForIdent("ausgangszeit_aktiv"), false);
		}
		
		private function TriggerDeviceAlert($DeviceParameters){
			$triggeredDeviceID = $this->GetIDForIdent("deviceTriggered");
			$deviceTriggeredString = GetValueString($triggeredDeviceID);
			SetValueString($triggeredDeviceID, $DeviceParameters["Bezeichnung"]);
			
			if ($DeviceParameters["verzoegerung_eingang"] == true){
				$this->TriggerDelayedAlert($DeviceParameter);
			} else {
				$this->TriggerAlert($DeviceParameter);
			}
		}
		
		private function TriggerDelayedAlert($DeviceParameters){
			SetValueBoolean($this->GetIDForIdent("eingangszeit_aktiv"), true);
			
			//TODO: Alarm verzögert
			
			$this->TriggerAlert($DeviceParameters);
		}
		
		private function TriggerAlert($DeviceParameters){
			$setAlert = true;
			if ($DeviceParameters["24h"] == true){
				SetValueBoolean($this->GetIDForIdent("24h_alarm"), true);
				$setAlert = false;
			}
			if ($DeviceParameters["Technik"] == true){
				SetValueBoolean($this->GetIDForIdent("technik_alarm"), true);
				$setAlert = false;
			}
			if ($setAlert == True){
				SetValueBoolean($this->GetIDForIdent("alarm"), true);
			}
			
			//TODO: Alarm
		}
		

		private function GetDeviceParameter(int $DeviceID){
			$arrString = $this->ReadPropertyString("devices");
			$arr = json_decode($arrString, true);
			
			foreach($arr as $key1) {
				if($key1["InstanceID"] == $DeviceID){
					return $key1;
				}
			}
			
			return null;
		}
		
		public function RequestAction($Ident, $Value) {
			switch($Ident) {
				case "alarmmodus":
					$this->SetMode($Value);
					break;
	
				default:
					throw new Exception("Invalid Ident");

    		}
 		}
    }
?>
