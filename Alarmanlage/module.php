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
				IPS_SetVariableProfileAssociation("SX_Alarm.Modus", 2, "Intern Aktiviert", "LockClosed", 0x00FF00);
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
			$this->RegisterPropertyString("devices", null);
			$this->RegisterPropertyString("melder", null);
			
			$this->RegisterPropertyInteger("dauer_alarmbeleuchtung", 900);
			$this->RegisterPropertyInteger("dauer_sirene", 120);
			$this->RegisterPropertyInteger("dauer_warnlicht", 900);
			$this->RegisterPropertyInteger("retrigger", 2);
			$this->RegisterPropertyInteger("verzoegerung_eingang", 30);
			$this->RegisterPropertyInteger("verzoegerung_ausgang", 120);
			
			$ScriptID = $this->RegisterScript('onDeviceStatusChanged', 'onDeviceStatusChanged', '<? SXALERT_DeviceStatusChanged('.$this->InstanceID.', $_IPS[\"VARIABLE\"]); ?>'); 
			IPS_SetHidden($ScriptID, true); 
		

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

			$ScriptID = IPS_GetObjectIDByIdent("onDeviceStatusChanged", $this->InstanceID); 
			
			$foundIDs = array();

			foreach($arr as $key1) {
				$key2 = $key1("InstanceID");
				
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
						IPS_SetEventTrigger($EventID, 1, $TargetID);
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
