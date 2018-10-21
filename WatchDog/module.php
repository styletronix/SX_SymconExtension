<?
    class WatchDog extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }
		
        public function Create() {
            parent::Create();

			// $this->RegisterVariableInteger("alarmmodus", "Status", "SX_Alarm.Modus");
			// $this->EnableAction("alarmmodus");
						
			$this->RegisterPropertyString("devices", null);
			$this->RegisterPropertyString("BatteryMonitoring", "[{\"Caption\":\"HomeMatic\",\"ModuleID\":\"{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}\",\"Ident\":\"LOWBAT\"},{\"Caption\":\"HomeMatic\",\"ModuleID\":\"{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}\",\"Ident\":\"LOW_BAT\"}]");
			$this->RegisterPropertyInteger("refreshInterval", 60);
			
			$this->RegisterVariableString("BattMon_OK", "");
			$this->RegisterVariableString("BattMon_Empty", "");
			
			$this->RegisterTimer("timer_refresh", 0, 'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "timer_refresh");');
        }
		
        public function ApplyChanges() {
            parent::ApplyChanges();

			
			$this->SetTimerInterval("timer_refresh", $this->ReadPropertyInteger("refreshInterval") * 1000);
			$this->SetStatus(102);
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
		private function CheckDevices(){
			$BattMonitorTableEmpty = "";
			$BattMonitorTableOK = "";
			
			$arr = GetDeviceParameters("BatteryMonitoring");
			if ($arr){
				foreach ($arr as $value) {
				$instances = IPS_GetInstanceListByModuleID($value["ModuleID"]);
				foreach ($instances as $instance) {
					$valID = @IPS_GetObjectIDByIdent($value["Ident"], $instance["InstanceID"]);
					if ($valID){
						$BattLevel = GetValue($valID);
						if ($BattLevel){
							$BattMonitorTableEmpty .= IPS_GetName($instance["InstanceID"]) . "<br>";
						}else{
							$BattMonitorTableOK .= IPS_GetName($instance["InstanceID"]) . "<br>";
						}						
					}
				}
			}
			}
			
			SetValueString($this->GetIDForIdent("BattMon_OK"), $BattMonitorTableOK);
			SetValueString($this->GetIDForIdent("BattMon_Empty"), $BattMonitorTableEmpty);
		}
			
		private function TimerCallback(string $TimerID){
			switch($TimerID) {
				case "timer_refresh":
					$this->CheckDevices();
					break;
					
				default:
					$this->SetTimerInterval($TimerID, 0);
					throw new Exception("Invalid TimerCallback");

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
				
			}
		}
    }
?>
