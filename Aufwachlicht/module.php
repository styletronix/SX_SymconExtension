<?php
    class Aufwachlicht extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }
		
        public function Create() {
            parent::Create();
						
			//Eigenschaften registrieren
			$this->RegisterPropertyInteger("device", null);
			
			$this->SetBuffer("CurrentLevel", 0);
			
			$this->RegisterPropertyInteger("on_time", 30);
			$this->RegisterPropertyInteger("off_time", 10);
			$this->RegisterPropertyInteger("dimm_steps", 20);
			
			$this->RegisterTimer("off_timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "off_timer");');
			$this->RegisterTimer("on_timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "on_timer");');
        }
		
        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->Initialize();
			$this->SetStatus(102);
        }

		private function Initialize(){
			// $arr = $this->GetDeviceParameters("triggers");	
			// if ($arr){
				// foreach($arr as $key1) {
					// $this->RegisterMessage($key1["InstanceID"], 10603);
				// }
			// }	
		}
		

		private function DeviceStatusChanged(int $DeviceID){
			// $device = $this->GetDeviceParameter("triggers", $DeviceID);
			// if($device){
				// if($device["triggerOnEachUpdate"]){
					// $this->Trigger();
				// }else{
					// $val = GetValue($DeviceID);
					// if ($val){
						// $this->Trigger();
					// }
				// }
			// }			
		}

					
		private function TimerCallback(string $TimerID){		
			switch($TimerID) {
				case "off_timer":
					$this->SetTimerInterval($TimerID, 0);
					$this->SetBuffer("CurrentLevel", 0);
					$this->SetObjectValuePercent($deviceID, 0, false, false);
					
					break;
					
				case "on_timer":
					$current = $this->GetBuffer("CurrentLevel");	
					$deviceID = $this->ReadPropertyInteger("device");
					
					if ($current < 100){
						$dimm_steps = $this->ReadPropertyInteger("dimm_steps");
						$current = $current + (100 / $dimm_steps);
						$this->SetBuffer("CurrentLevel", $current);
						$this->SetObjectValuePercent($deviceID, $current / 100.0, false, true);
					}else{
						$this->SetTimerInterval($TimerID, 0);
						$this->SetTimerInterval("off_timer", $this->ReadPropertyInteger("off_time") * 60 * 1000);
					};
					
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
		
		public function Start() {
			$on_time = $this->ReadPropertyInteger("on_time");
			$dimm_steps = $this->ReadPropertyInteger("dimm_steps");
			$on_steps = $on_time * 60 / $dimm_steps;
				
			$this->SetTimerInterval("on_timer", $on_steps * 1000);	
		}

		public function Stop() {
			$this->SetTimerInterval("on_timer", 0);
			$this->SetTimerInterval("off_timer", 0);
			$this->SetBuffer("CurrentLevel", 0);
		}
		
		private function SetObjectValuePercent(int $TargetID, float $value, bool $lowerOnly, bool $higherOnly){
			set_time_limit(30);
			
			if (IPS_VariableExists($TargetID)){
				$object = IPS_GetObject($TargetID);
				$variable = IPS_GetVariable($TargetID);
				$actionID = $this->GetProfileAction($variable);
										
				$t = $variable["VariableType"];
				$currentVal = GetValue($TargetID);				
				$profileName = $this->GetProfileName($variable);
				
				if ($t == 0){
					$MaxValue = 1;
					$MinValue = 0;
				}else if ($t == 1){
					$MaxValue = 100;
					$MinValue = 0;
				}else if ($t == 2){
					$MaxValue = 1.0;
					$MinValue = 0.0;
				}else{
					$MaxValue = 100;
					$MinValue = 0;
				}
				
				if ($profileName != "" and $t != 0) {
					$MaxValue = IPS_GetVariableProfile($profileName)['MaxValue'];
					$MinValue = IPS_GetVariableProfile($profileName)['MinValue'];
				}					
				
				$TargetValue = (($MaxValue - $MinValue) * $value) + $MinValue;
				
				if ($t == 0){
					if ($value > 0.0){
						$TargetValue = true;
					}else{
						$TargetValue = false;
					}
				}else if ($t == 1){
					$currentVal = round($currentVal, 0);
					$TargetValue = round($TargetValue, 0);
				}else {
					$currentVal = round($currentVal, 2);
					$TargetValue = round($TargetValue, 2);
				}

				if ($currentVal != $TargetValue){	
					if (($lowerOnly == true and $TargetValue < $currentVal) or ($higherOnly == true and $TargetValue > $currentVal) or ($higherOnly == false and $lowerOnly == false)){
							if(IPS_InstanceExists($actionID)){
								IPS_RequestAction($actionID, $object['ObjectIdent'], $TargetValue);
							} else if(IPS_ScriptExists($actionID)) {
								echo IPS_RunScriptWaitEx($actionID, Array("VARIABLE" => $TargetID, "VALUE" => $TargetValue));
							} else {
								SetValue($TargetID, $TargetValue);
							}		
					}
				}
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
