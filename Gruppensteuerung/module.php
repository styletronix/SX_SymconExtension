<?
    class Gruppensteuerung extends IPSModule {
        public function __construct($InstanceID) {
            // Diese Zeile nicht löschen
            parent::__construct($InstanceID);
 
            // Selbsterstellter Code
        }
		
        public function Create() {
            parent::Create();

			$this->RegisterVariableBoolean("Ergebnis_Boolean", "Ergebnis Boolean", "~Switch");
            $this->EnableAction("Ergebnis_Boolean");

            $this->RegisterVariableFloat("Ergebnis_Float", "Ergebnis Float", "~Intensity.1");
            $this->EnableAction("Ergebnis_Float");

            $this->RegisterVariableFloat("Ergebnis_Integer", "Ergebnis Integer", "~Intensity.100");
            $this->EnableAction("Ergebnis_Integer");

            $this->RegisterPropertyInteger("DeviceCategory", 0);
            
            if ($this->ReadPropertyInteger("DeviceCategory") == 0){
                @$CategoryID = IPS_GetCategoryIDByName("Devices", $this->InstanceID);
                if ($CategoryID == false){
                    $CategoryID = IPS_CreateCategory();
                    IPS_SetName($CategoryID, "Devices");
                    IPS_SetParent($CategoryID, $this->InstanceID);
                }

                IPS_SetProperty($this->InstanceID, "DeviceCategory", $CategoryID);
                IPS_ApplyChanges($this->InstanceID);
            }
            
			$this->UpdateEvents();
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->UpdateEvents();
			$this->SetStatus(102);
        }

		public function UpdateEvents(){
			$ScriptID = $this->RegisterScript("Update", "Update", "<?\n\nSXGRP_RefreshStatus(".$this->InstanceID."); \n\n?>");
			$CategoryID = $this->ReadPropertyInteger("DeviceCategory");

			$foundIDs = array();

            $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

			foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($key2)["TargetID"];
				}


				if ($TargetID > 0 and !in_array($TargetID, $ignoreIDs)){
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
			$this->RefreshStatus();
		}

        public function RefreshStatus() {
			$result = false;
            $resultFloat = 0.0;
            $resultInteger = 0;
			$CategoryID =  $this->ReadPropertyInteger("DeviceCategory");
            $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

			foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
	         $itemObject = IPS_GetObject($key2);
	         $TargetID = 0;
	         $TargetName = IPS_GetName($key2);


				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($key2)["TargetID"];
				}

			if ($TargetID > 0 and !in_array($TargetID, $ignoreIDs)){
				$var = IPS_GetVariable ($TargetID);
				$t = $var["VariableType"];
				if ($t == 0){
				   $Meldung = GetValueBoolean($TargetID);
					if ($Meldung == true){
				      $result = true;
                      $resultFloat = 1.0;
                      $resultInteger = 100;
				   }
				}
				if ($t == 1){
				   $Meldung = GetValueInteger($TargetID);
					if ($Meldung > 0){ 
                        $result = true; 
                    }
                    if ($resultFloat < $Meldung){
                        $resultFloat = $Meldung; 
                    }
                    if ($resultInteger < $Meldung){
                        $resultInteger = $Meldung;
                    }
				}
				if ($t == 2){
				   $Meldung = GetValueFloat($TargetID);
					if ($Meldung > 0){
                        $result = true;
                    }
                    if ($resultFloat < $Meldung){ 
                        $resultFloat = $Meldung;
                    }
                    if ($resultInteger < $Meldung * 100){
                        $resultInteger = $Meldung * 100;
                    }
				}
			}
	   }

	   SetValue($this->GetIDForIdent("Ergebnis_Boolean"), $result);
       SetValue($this->GetIDForIdent("Ergebnis_Float"),	$resultFloat);
       SetValue($this->GetIDForIdent("Ergebnis_Integer"), $resultInteger);

       }
	    public function SetState(boolean $Value){
            $data = $this->ReadSettings();
			$currentPreAlertState = $data["PreAlertState"];  // GetValue($this->GetIDForIdent("PreAlertState"));
			if ($currentPreAlertState !== "" and $Value == false){
				return;
			}

			$CategoryID =  $this->ReadPropertyInteger("DeviceCategory");
			$this->SetChildLinksBoolean($CategoryID, $Value);
			$this->RefreshStatus();
	}
        public function SetStateFloat(float $Value){
			$currentPreAlertState = GetValue($this->GetIDForIdent("PreAlertState"));
			if ($currentPreAlertState !== ""){
				return;
			}

			$CategoryID =  $this->ReadPropertyInteger("DeviceCategory");
			$this->SetChildLinksFloat($CategoryID, $Value);
			$this->RefreshStatus();
        }
        public function SetStateInteger(integer $Value){
			$currentPreAlertState = GetValue($this->GetIDForIdent("PreAlertState"));
			if ($currentPreAlertState !== ""){
				return;
			}

			$CategoryID =  $this->ReadPropertyInteger("DeviceCategory");
			$this->SetChildLinksInteger($CategoryID, $Value);
			$this->RefreshStatus();
        }

		public function SetAlertState(boolean $Value){
            $data = $this->ReadSettings();

			$currentPreAlertState = $data['PreAlertState'];
			$CategoryID =  $this->ReadPropertyInteger("DeviceCategory");

			if ($Value == true){
				if ($currentPreAlertState == ""){
					$result = $this->GetCurrentStateString();
                    $data["PreAlertState"] = $result;
				}

				$this->SetChildLinksBoolean($CategoryID, $Value);

			}else{
				if ($currentPreAlertState !== ""){
					$this->SetCurrentStateString($currentPreAlertState);
                    $data["PreAlertState"] = "";
				}
			}

            $this->WriteSettings($data);
			$this->RefreshStatus();
		}
		public function GetCurrentStateString(){
			$arr =array();
			$CategoryID =  $this->ReadPropertyInteger("DeviceCategory");
            $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

			foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);
			

				if ($itemObject["ObjectType"] == 6){
					$TargetID = IPS_GetLink($key2)["TargetID"];
				}

				if ($TargetID > 0 and !in_array($TargetID, $ignoreIDs)){
					$var = IPS_GetVariable ($TargetID);
					$t = $var["VariableType"];
					if ($t == 0){
						$arr[$TargetID] = GetValueBoolean($TargetID);
					}
					if ($t == 1){
						$arr[$TargetID] = GetValueInteger($TargetID);
					}
					if ($t == 2){
						$arr[$TargetID] = GetValueFloat($TargetID);
					}
				}
			}

			return json_encode($arr);
		}
		public function SetCurrentStateString(string $State){
			$arr = json_decode($State, true);
			$CategoryID =  $this->ReadPropertyInteger("DeviceCategory");
            $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

			foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
				set_time_limit(30);

				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);


				if ($itemObject["ObjectType"] == 6){
					$TargetID = IPS_GetLink($key2)["TargetID"];
				}

				if ($TargetID > 0 and !in_array($TargetID, $ignoreIDs)){
					$pID = IPS_GetParent($TargetID);
                    $VariableName = IPS_GetName($TargetID);
					$value = $arr[$TargetID];
					$var = IPS_GetVariable ($TargetID);
					$t = $var["VariableType"];

                    if (@IPS_RequestAction($pID, $VariableName, $value) == false){
                        SetValue($TargetID, $value);
                    }
				}
			}
		}

		public function RequestAction($Ident, $Value) {
    		switch($Ident) {
        	case "Ergebnis_Boolean":
                $this->SetState($Value);
				break;

            case "Ergebnis_Float":
                $this->SetStateFloat($Value);
				break;

            case "Ergebnis_Integer":
                $this->SetStateInteger($Value);
				break;

        	default:
	            throw new Exception("Invalid Ident");

    		}
 		}

        private function SetChildLinks(integer $key, boolean $value, integer $valueInteger, float $valueFloat){
            $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

            foreach(IPS_GetChildrenIDs($key) as $key2) {
				set_time_limit(30);

				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);

                // Prüfe ob Ziel ein Link ist
				if ($itemObject["ObjectType"] == 6){
					$TargetID = IPS_GetLink($key2)["TargetID"];
				}

				if ($TargetID > 0 and !in_array($TargetID, $ignoreIDs)){
					$pID = IPS_GetParent($TargetID);
                    $VariableName = IPS_GetName($TargetID);

					$var = IPS_GetVariable ($TargetID);
					$t = $var["VariableType"];
					if ($t == 0){
                        if (@IPS_RequestAction($pID, $VariableName, $value) == false){
                            SetValue($TargetID, $value);
                        }
					}
					if ($t == 1){
                        if (@IPS_RequestAction($pID, $VariableName, $valueInteger) == false){
                            SetValue($TargetID, $valueInteger);
                        }
					}
					if ($t == 2){
                        if (@IPS_RequestAction($pID, $VariableName, $valueFloat) == false){
                            SetValue($TargetID, $valueFloat);
                        }
					}
				}
			}
        }
		private function SetChildLinksBoolean(integer $key, boolean $value){
                if ($value){
                    $ValueInteger = 100;
                    $ValueFloat = 1.0;
                }else{
                    $ValueInteger = 0;
                    $ValueFloat = 0.0;
                }

                $this->SetChildLinks($key, $value, $ValueInteger, $ValueFloat);
		}
        private function SetChildLinksFloat(integer $key, float $value){
            $valbool = ($value > 0);

            $this->SetChildLinks($key, $valbool, $value * 100, $value);
		}
        private function SetChildLinksInteger(integer $key, integer $value){
            $valbool = ($value > 0);

            $this->SetChildLinks($key, $valbool, $value, $value / 100);
		}

        private function WriteSettings($data){
            $fp = fopen(IPS_GetKernelDir().$this->InstanceID.'.settings.json', 'w');
            fwrite($fp, json_encode($data));
            fclose($fp);
        }
        private function ReadSettings(){
            $filename = IPS_GetKernelDir().$this->InstanceID.'.settings.json';
            if (file_exists($filename)) {
                $contents = file_get_contents($filename);
                return json_decode($contents,true);
            }else{
                $contents = array();
                $contents["PreAlertState"] = "";
                return $contents;
            }
        }
    }
?>