<?
    class Treppenhauslicht extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }
		
        public function Create() {
            parent::Create();

			// $this->RegisterVariableInteger("alarmmodus", "Status", "SX_Alarm.Modus");
			// $this->EnableAction("alarmmodus");
						
			//Eigenschaften registrieren
			$this->RegisterPropertyString("triggers", null);
			$this->RegisterPropertyString("devices", null);
			
			$this->SetBuffer("IsOn", "false");
			
			$this->RegisterPropertyInteger("on_time", 240);
			$this->RegisterPropertyInteger("off_warning_time", 30);
			
			$this->RegisterTimer("warning_timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "warning_timer");');
			$this->RegisterTimer("on_timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "on_timer");');
			$this->RegisterTimer("blink_timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "blink_timer");');
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
			$this->SetAllDeviceStatus(true);				
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
			$this->SetAllDeviceStatus(false);
		}
		
		private function setDeviceStatus(int $TargetID, bool $Value){
			if (!IPS_VariableExists($TargetID)){ return; }
			$actionValue = $Value;
			
			$pID = IPS_GetParent($TargetID);
            $obj = IPS_GetObject($TargetID);
			$VariableName = $obj["ObjectIdent"];
					

			if (@IPS_RequestAction($pID, $VariableName, $Value) == false){
				SetValue($TargetID, $Value);
			}
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

    }
?>
