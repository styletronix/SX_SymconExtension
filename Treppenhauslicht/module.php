<?php
    class Treppenhauslicht extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }
		
        public function Create() {
            parent::Create();
						
			$this->RegisterPropertyString("triggers", null);
			$this->RegisterPropertyString("devices", null);
			
			$this->SetBuffer("IsOn", "false");
			
			$this->RegisterPropertyInteger("on_time", 240);
			$this->RegisterPropertyInteger("off_warning_time", 30);
			$this->RegisterPropertyBoolean("showStatus", false);
			
			$this->RegisterTimer("warning_timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "warning_timer");');
			$this->RegisterTimer("on_timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "on_timer");');
			$this->RegisterTimer("blink_timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "blink_timer");');
			
			$this->RegisterVariableString("statusString", "Status");
        }
		
        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->Initialize();
			$this->SetStatus(102);
        }

		private function Initialize(){
			$arr = $this->GetDeviceParameters("triggers");	
			if ($arr){
				foreach($arr as $key1) {
					$this->RegisterMessage($key1["InstanceID"], 10603);
				}
			}	
		}
		
		public function Trigger(){
			$onTime = $this->ReadPropertyInteger("on_time");
			
			$this->SetTimerInterval("on_timer", $onTime * 1000);
			$this->SetTimerInterval("blink_timer", 0);
			$this->SetTimerInterval("warning_timer", 0);
			
			$this->SetBuffer("IsOn", "true");
			$this->SetValue("statusString", "Zeit läuft...");
			$this->SetAllDeviceStatus(true);				
		}
		
		public function Off_without_warning(){
			$this->SetTimerInterval("on_timer", 0);
			$this->SetTimerInterval("blink_timer", 0);
			$this->SetTimerInterval("warning_timer", 0);
			
			$this->SetBuffer("IsOn", "false");
			$this->SetValue("statusString", "Reset");
			$this->SetAllDeviceStatus(false);				
		}
		
		public function Off_with_warning(){
			$this->SetTimerInterval("on_timer", 0);
			$this->SetTimerInterval("blink_timer", 0);
			$this->SetTimerInterval("warning_timer", 0);
			
			$this->TimerCallback("on_timer");
		}
		
		private function SetAllDeviceStatus(bool $Value){
			$arr = $this->GetDeviceParameters("devices");	
			if ($arr){
				foreach($arr as $key1) {
					$this->setDeviceStatus($key1["InstanceID"], $Value);
				}
			}
		}
		
		private function DeviceStatusChanged(int $DeviceID){
			$device = $this->GetDeviceParameter("triggers", $DeviceID);
			if($device){
				if($device["triggerOnEachUpdate"]){
					$this->Trigger();
				}else{
					$val = GetValue($DeviceID);
					if ($val){
						$this->Trigger();
					}
				}
			}			
		}

		private function GetDeviceParameters(string $Liste){
			$arrString = $this->ReadPropertyString($Liste);
			if ($arrString){
				$arr = json_decode($arrString, true);
												
				return $arr;
			}	
			return null;
		}
		private function GetDeviceParameter(string $Liste, int $DeviceID){			
			$arr = $this->GetDeviceParameters($Liste);
			if ($arr){
				foreach($arr as $key1) {
					if($key1["InstanceID"] == $DeviceID){
						return $key1;
					}
				}
			}			
						
			return null;
		}
		private function Blink(){		
			$this->SetAllDeviceStatus(false);
			IPS_Sleep(1000);
			
			if ($this->GetBuffer("IsOn") == "true"){
				$this->SetAllDeviceStatus(true);
				$this->SetTimerInterval("blink_timer", 8 * 1000);
			}	
		}
		
		private function Off(){
			$this->SetBuffer("IsOn", "false");
			$this->SetValue("statusString", "Aus");
			$this->SetAllDeviceStatus(false);
		}
		
		private function TimerCallback(string $TimerID){
			$this->SetTimerInterval($TimerID, 0);
			
			switch($TimerID) {
				case "warning_timer":
					$this->Off();
					break;
					
				case "on_timer":
					$offWarningTime = $this->ReadPropertyInteger("off_warning_time");
					if ($offWarningTime > 0){
						$this->SetTimerInterval("warning_timer", $offWarningTime * 1000);
						$this->SetValue("statusString", "Ausschaltwarnung");
						$this->Blink();
					}else{
						$this->Off();
					}
					
					break;
					
				case "blink_timer":					
					$this->Blink();
					break;
					
				default:
					throw new Exception("Invalid Ident");

    		}
		}
		
		public function RequestAction($Ident, $Value) {
			switch($Ident) {
			case "TimerCallback":
					$this->TimerCallback($Value);
					break;
				
				default:
					throw new Exception("Invalid Ident");

    		}
 		}
		
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			if ($Message == 10603){
				$this->DeviceStatusChanged($SenderID);
			}
		}
			
		private function setDeviceStatus(int $outputID, bool $Value){
			// Vorlage aus Symcon Misc
			
            $object = IPS_GetObject($outputID);
            $variable = IPS_GetVariable($outputID);
            $actionID = $this->GetProfileAction($variable);

            $profileName = $this->GetProfileName($variable);

            if($profileName != "") {
                if ($Value) {
                    $actionValue = IPS_GetVariableProfile($profileName)['MaxValue'];
                } else {
                    $actionValue = 0;
                }
                if($variable['VariableType'] == 0) {
                    $actionValue = ($actionValue > 0);
                }
            } else {
                $actionValue = $Value;
            }

            if(IPS_InstanceExists($actionID)){
                IPS_RequestAction($actionID, $object['ObjectIdent'], $actionValue);
            } else if(IPS_ScriptExists($actionID)) {
                echo IPS_RunScriptWaitEx($actionID, Array("VARIABLE" => $outputID, "VALUE" => $actionValue));
            } else {
				SetValue($outputID, $actionValue);
			}
        }

        private function GetProfileName($variable){
            if($variable['VariableCustomProfile'] != ""){
                return $variable['VariableCustomProfile'];
            } else {
                return $variable['VariableProfile'];
            }
        }

        private function GetProfileAction($variable){
            if($variable['VariableCustomAction'] > 0){
                return $variable['VariableCustomAction'];
            } else {
                return $variable['VariableAction'];
            }
        }
		

    }
?>
