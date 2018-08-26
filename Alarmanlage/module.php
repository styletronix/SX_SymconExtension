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
			
			$ScriptID = $this->RegisterScript('onDeviceStatusChanged', 'onDeviceStatusChanged', '<? SXALERT_DeviceStatusChanged('.$this->InstanceID.', $_IPS["VARIABLE"]); ?>'); 
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
			$arrstring = $this->readpropertystring("devices");
			$arr = json_decode($arrstring);

			$scriptid = ips_getobjectidbyident("ondevicestatuschanged", $this->instanceid); 
			
			$foundids = array();

			foreach($arr as $key1) {
				$key2 = $key1("instanceid");
				
				$itemobject = ips_getobject($key2);
				$targetid = $key2;
				$targetname = ips_getname($key2);

				if ($itemobject["objecttype"] == 6){
				   $targetid = ips_getlink($key2)["targetid"];
				}


				if ($targetid > 0){
					$eventname = "targetid ".$targetid;
					$foundids[] = $eventname;

					@$eventid = ips_geteventidbyname($eventname, $scriptid);
					if ($eventid === false){
						$eventid = ips_createevent(0);
						ips_seteventtrigger($eventid, 1, $targetid);
						ips_setname($eventid, $eventname);
						ips_setparent($eventid, $scriptid);
						ips_seteventactive($eventid, true);
					}
				}
			}

			foreach(ips_getchildrenids($scriptid) as $key2) {
				$eventname = ips_getname($key2);
				if (!in_array ($eventname, $foundids)){
					ips_deleteevent($key2);
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
