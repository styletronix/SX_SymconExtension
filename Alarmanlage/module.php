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
			$this->RegisterVariableBoolean("alarmscharf", "Alarmanlage aktiviert", "~Switch");
			$this->RegisterVariableBoolean("alarm", "Alarm ausgelöst", "~Switch");
			$this->RegisterVariableBoolean("technik_alarm", "Technik-Alarm ausgelöst", "~Switch");
			$this->RegisterVariableBoolean("24h_alarm", "24h-Alarm (Sabotage) ausgelöst", "~Switch");
            $this->RegisterVariableBoolean("vorwarnung_aktiv", "Vorwarnung aktiv", "~Switch");
			$this->RegisterVariableBoolean("eingangszeit_aktiv", "Einganszeit aktiv", "~Switch");
			$this->RegisterVariableBoolean("ausgangszeit_aktiv", "Ausgangszeit aktiv", "~Switch");
            $this->RegisterVariableString("deviceTriggered", "Auslösender Sensor", "");
			
			$this->RegisterVariableInteger("alarmmodus", "Status", "SX_Alarm.Modus");
			$this->EnableAction("alarmmodus");
			
			$this->SetBuffer("alertcount", 0);
			$this->SetBuffer("alertactive", false);
		
			
			//Eigenschaften registrieren
			$this->RegisterPropertyString("devices", null);
			$this->RegisterPropertyString("melder", null);
			
			$this->RegisterPropertyInteger("dauer_alarmbeleuchtung", 900);
			$this->RegisterPropertyInteger("dauer_sirene", 120);
			$this->RegisterPropertyInteger("dauer_warnlicht", 900);
			$this->RegisterPropertyInteger("retrigger", 2);
			$this->RegisterPropertyInteger("verzoegerung_eingang", 30);
			$this->RegisterPropertyInteger("verzoegerung_ausgang", 120);
			
		
			$this->RegisterTimer("ArmDelay", 0, 'SXALERT_ArmSystem($_IPS["TARGET"]);');
			$this->RegisterTimer("EntryTimer", 0, 'SXALERT_onEntryTimer($_IPS["TARGET"]);');

            if ($ApplyChanges == true){
				IPS_ApplyChanges($this->InstanceID);
			}else{
				// $this->Initialize();
			}
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->Initialize();
			$this->SetStatus(102);
        }

		public function Initialize(){	
			$arrString = $this->ReadPropertyString("devices");
			$arr = json_decode($arrString, true);
					
			foreach($arr as $key1) {
				$this->RegisterMessage($key1["InstanceID"], 10603);
			}

		}

		private function DeviceStatusChanged($DeviceID){
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
			$this->SetTimerInterval ("EntryTimer", 0);
			
			$this->SetBuffer("DelayedAlertDevice", null);
			$this->SetBuffer("alertcount", 0);
			$this->SetBuffer("alertactive", false);
			
			SetValueString($this->GetIDForIdent("deviceTriggered"), "");
			SetValueBoolean($this->GetIDForIdent("alarm"), false);
			SetValueBoolean($this->GetIDForIdent("technik_alarm"), false);
			SetValueBoolean($this->GetIDForIdent("24h_alarm"), false);
			SetValueBoolean($this->GetIDForIdent("vorwarnung_aktiv"), false);
			SetValueBoolean($this->GetIDForIdent("eingangszeit_aktiv"), false);
			SetValueBoolean($this->GetIDForIdent("ausgangszeit_aktiv"), false);
			SetValueBoolean($this->GetIDForIdent("alarmscharf"), false);
		}
		
		public function SetMode(int $Modus){			
			if (GetValueInteger($this->GetIDForIdent("alarmmodus")) == $Modus){
				return;
			}
			
			$this->Reset();
			
			switch($Modus) {
				case 0:
					// Deaktiviert
					SetValueInteger($this->GetIDForIdent("alarmmodus"), $Modus);
					break;
	
				case 1:
					//Aktiviert
				case 2:
					//Intern Aktiviert
					SetValueInteger($this->GetIDForIdent("alarmmodus"), $Modus);
					$this->ArmSystemDelayed();
					break;

				case 4:
					//Wartung
					SetValueInteger($this->GetIDForIdent("alarmmodus"), $Modus);
					break;
					
				default:
					throw new Exception("Ungültiger Modus");
    		}
						
		}
		
		private function ArmSystemDelayed(){
			$ExitDelay = $this->ReadPropertyInteger("verzoegerung_ausgang");
			if ($ExitDelay > 0){
				SetValueBoolean($this->GetIDForIdent("ausgangszeit_aktiv"), true);
				$this->SetTimerInterval ("ArmDelay", $ExitDelay * 1000);
			}else{
				$this->ArmSystem();
			}
		}
		
		public function ArmSystem(){
			$this->SetTimerInterval ("ArmDelay", 0);
			SetValueBoolean($this->GetIDForIdent("ausgangszeit_aktiv"), false);
			SetValueBoolean($this->GetIDForIdent("alarmscharf"), true);			
		}
		
		private function TriggerDeviceAlert($DeviceParameters){
			$triggeredDeviceID = $this->GetIDForIdent("deviceTriggered");
			$deviceTriggeredString = GetValueString($triggeredDeviceID);
			SetValueString($triggeredDeviceID, $DeviceParameters["Bezeichnung"]);
			
			if ($DeviceParameters["verzoegerung_eingang"] == true){
				$this->TriggerDelayedAlert($DeviceParameters);
			} else {
				$this->TriggerAlert($DeviceParameters);
			}
		}
		
		private function TriggerDelayedAlert($DeviceParameters){
			if ($this->GetBuffer("alertactive") == true){ return; }
				
			$Delay = $this->ReadPropertyInteger("verzoegerung_eingang");
			if ($Delay > 0){
				if (GetValueBoolean($this->GetIDForIdent("eingangszeit_aktiv")) == false ){
					$this->SetBuffer("DelayedAlertDevice", json_encode($DeviceParameters));
					SetValueBoolean($this->GetIDForIdent("eingangszeit_aktiv"), true);
					$this->SetTimerInterval("EntryTimer", $Delay * 1000);		
				}					
			}else{
				$this->TriggerAlert($DeviceParameters);
			}
		}
		
		public function onEntryTimer(){
			$this->SetTimerInterval("EntryTimer", 0);
			$arrString = $this->GetBuffer("DelayedAlertDevice");
			$DeviceParameters = json_decode($arrString, true);
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
			if ($setAlert == true){
				SetValueBoolean($this->GetIDForIdent("alarm"), true);
			}
			

			if ($this->GetBuffer("alertactive") == false){
				$this->SetBuffer("alertactive", true);
				$this->SetBuffer("alertcount", $this->GetBuffer("alertcount") + 1);
			}
			
			//TODO:Alarm
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
		
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			IPS_LogMessage("MessageSink", "Message from SenderID ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true));
			
			if ($Message == 10603){
				$this->DeviceStatusChanged($SenderID);
			}
		}
    }
?>
