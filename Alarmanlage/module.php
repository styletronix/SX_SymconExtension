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


			// $ScriptID = IPS_GetObjectIDByIdent("Update", $this->InstanceID); 
			
			// $CategoryID = $this->ReadPropertyInteger("DeviceCategory");
			// if ($CategoryID > 0){
			
			// $foundIDs = array();

            // $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

			// foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
				// $itemObject = IPS_GetObject($key2);
				// $TargetID = $key2;
				// $TargetName = IPS_GetName($key2);

				// if ($itemObject["ObjectType"] == 6){
				   // $TargetID = IPS_GetLink($key2)["TargetID"];
				// }


				// if ($TargetID > 0 and !in_array($TargetID, $ignoreIDs)){
					// $EventName = "TargetID ".$TargetID;
					// $foundIDs[] = $EventName;

					// @$EventID = IPS_GetEventIDByName($EventName, $ScriptID);
					// if ($EventID === false){
						// $EventID = IPS_CreateEvent(0);
						// IPS_SetEventTrigger($EventID, 1, $TargetID);
						// IPS_SetName($EventID, $EventName);
						// IPS_SetParent($EventID, $ScriptID);
						// IPS_SetEventActive($EventID, true);
					// }
				// }
			// }

			// foreach(IPS_GetChildrenIDs($ScriptID) as $key2) {
				// $EventName = IPS_GetName($key2);
				// if (!in_array ($EventName, $foundIDs)){
					// IPS_DeleteEvent($key2);
				// }
			// }
			
			// }
			
			// $PresenceScriptID = IPS_GetObjectIDByIdent("UpdateAnwesenheit", $this->InstanceID);
			// IPS_SetHidden($PresenceScriptID, true); 
			// $PresenceCategoryID = $this->ReadPropertyInteger("PresenceCategory");
			// if ($PresenceCategoryID > 0){
				// $foundIDs = array();
				
			// foreach(IPS_GetChildrenIDs($PresenceCategoryID) as $key2) {
				// $itemObject = IPS_GetObject($key2);
				// $TargetID = $key2;
				// $TargetName = IPS_GetName($key2);

				// if ($itemObject["ObjectType"] == 6){
				   // $TargetID = IPS_GetLink($key2)["TargetID"];
				// }

				// if ($TargetID > 0 and !in_array($TargetID, $ignoreIDs)){
					// $EventName = "TargetID ".$TargetID;
					// $foundIDs[] = $EventName;

					// @$EventID = IPS_GetEventIDByName($EventName, $PresenceScriptID);
					// if ($EventID === false){
						// $EventID = IPS_CreateEvent(0);
						// IPS_SetEventTrigger($EventID, 0, $TargetID);
						// IPS_SetName($EventID, $EventName);
						// IPS_SetParent($EventID, $PresenceScriptID);
						// IPS_SetEventActive($EventID, true);
					// }
				// }
			// }
			
			// foreach(IPS_GetChildrenIDs($PresenceScriptID) as $key2) {
				// $EventName = IPS_GetName($key2);
				// if (!in_array ($EventName, $foundIDs)){
					// IPS_DeleteEvent($key2);
				// }
			// }
			
			// }
			
			// $RefreshIlluminationLevelScriptID = IPS_GetObjectIDByIdent("RefreshIlluminationLevel", $this->InstanceID);
			// $IlluminationCategoryID = $this->ReadPropertyInteger("IlluminationCategory");
			// if ($IlluminationCategoryID > 0){
				// $foundIDs = array();
				
				// foreach(IPS_GetChildrenIDs($IlluminationCategoryID) as $key2) {
				// $itemObject = IPS_GetObject($key2);
				// $TargetID = $key2;
				// $TargetName = IPS_GetName($key2);

				// if ($itemObject["ObjectType"] == 6){
				   // $TargetID = IPS_GetLink($key2)["TargetID"];
				// }

				// if ($TargetID > 0 and !in_array($TargetID, $ignoreIDs)){
					// $EventName = "TargetID ".$TargetID;
					// $foundIDs[] = $EventName;

					// @$EventID = IPS_GetEventIDByName($EventName, $RefreshIlluminationLevelScriptID);
					// if ($EventID === false){
						// $EventID = IPS_CreateEvent(0);
						// IPS_SetEventTrigger($EventID, 0, $TargetID);
						// IPS_SetName($EventID, $EventName);
						// IPS_SetParent($EventID, $RefreshIlluminationLevelScriptID);
						// IPS_SetEventActive($EventID, true);
						
					// }
				// }
			// }
				
				// foreach(IPS_GetChildrenIDs($RefreshIlluminationLevelScriptID) as $key2) {
					// $EventName = IPS_GetName($key2);
					// if (!in_array ($EventName, $foundIDs)){
						// IPS_DeleteEvent($key2);
					// }
				// }	
			// }
			

			// $this->RefreshStatus();
			// $this->RefreshIlluminationLevel();
			// $this->RefreshPresence();
		}


		public function RequestAction($Ident, $Value) {
    	switch($Ident) {
        	case "alarmmodus":
                SetValue($Ident, $Value);
				break;
	
        	default:
	            throw new Exception("Invalid Ident");

    		}
 		}
		private function SetChildLinks(int $key, bool $value, int $valueInteger, float $valueFloat){
			$this->SetChildLinks3($key, $value, $valueInteger, $valueFloat, false, false);
		}
		private function SetChildLinks2(int $key, bool $value, int $valueInteger, float $valueFloat, bool $lowerOnly){
			$this->SetChildLinks3($key, $value, $valueInteger, $valueFloat, $lowerOnly, false);
		}
        private function SetChildLinks3(int $key, bool $value, int $valueInteger, float $valueFloat, bool $lowerOnly, bool $higherOnly){
            $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

            foreach(IPS_GetChildrenIDs($key) as $key2) {
				set_time_limit(30);

				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;

                // Prüfe ob Ziel ein Link ist
				if ($itemObject["ObjectType"] == 6){
					$TargetID = IPS_GetLink($key2)["TargetID"];
				}elseif($itemObject["ObjectType"] == 2){
					$TargetID = $key2;
				}

				if ($TargetID > 0 and !in_array($TargetID, $ignoreIDs)){
					$pID = IPS_GetParent($TargetID);
                    //$VariableName = IPS_GetName($TargetID);
					
					$obj = IPS_GetObject($TargetID);
					$VariableName = $obj["ObjectIdent"];
					
					$var = IPS_GetVariable ($TargetID);
					$t = $var["VariableType"];
					$currentVal = GetValue($TargetID);
					
					if ($t == 0){
						if ($currentVal != $value){
						  if (($lowerOnly == true and $value < $currentVal) or ($higherOnly == true and $value > $currentVal) or ($higherOnly == false and $lowerOnly == false)){
							if (@IPS_RequestAction($pID, $VariableName, $value) == false){
								SetValue($TargetID, $value);
							}
			
						  }
						}
					}
					if ($t == 1){
						if ($currentVal != $valueInteger){
						if (($lowerOnly == true and $valueInteger < $currentVal) or ($higherOnly == true and $valueInteger > $currentVal) or ($higherOnly == false and $lowerOnly == false)){
							if (@IPS_RequestAction($pID, $VariableName, $valueInteger) == false){
								SetValue($TargetID, $valueInteger);
							}
						}
						}
					}
					if ($t == 2){
						if ($currentVal != $valueFloat){
						if (($lowerOnly == true and $valueFloat < $currentVal ) or ($higherOnly == true and $valueFloat > $currentVal ) or ($higherOnly == false and $lowerOnly == false)){
							if (@IPS_RequestAction($pID, $VariableName, $valueFloat) == false){
								SetValue($TargetID, $valueFloat);
							}
						}
						}
					}
				}
			}
        }
		private function SetChildLinksBoolean(int $key, bool $value){
                if ($value){
                    $ValueInteger = 100;
                    $ValueFloat = 1.0;
                }else{
                    $ValueInteger = 0;
                    $ValueFloat = 0.0;
                }

                $this->SetChildLinks($key, $value, $ValueInteger, $ValueFloat);
		}
        private function SetChildLinksFloat(int $key, float $value){
            $valbool = ($value > 0);

            $this->SetChildLinks($key, $valbool, $value * 100, $value);
		}
        private function SetChildLinksInteger(int $key, int $value){
            $valbool = ($value > 0);

            $this->SetChildLinks($key, $valbool, $value, $value / 100);
		}
			IPS_SemaphoreEnter("SXGRP_SettingAccess".$this->InstanceID,  2000);
			
            $filename = IPS_GetKernelDir().$this->InstanceID.'.settings.json';
            if (file_exists($filename)) {
				try {
					$contents = file_get_contents($filename);
					$data = json_decode($contents,true);
				
					if (array_key_exists('PrePresenceState', $data) == false) {
						$data['PrePresenceState'] = "";
					}
					if (array_key_exists('PresenceStateTemplate', $data) == false) {
						$data['PresenceStateTemplate'] = "";
					}
					IPS_SemaphoreLeave("SXGRP_SettingAccess".$this->InstanceID);
					return $data;
					
				} catch (Exception $e) {
					$contents = array();
					$contents["PreAlertState"] = "";
					$contents["PrePresenceState"] = "";
					$contents["PresenceStateTemplate"] = "";
					IPS_SemaphoreLeave("SXGRP_SettingAccess".$this->InstanceID);
					return $contents;
					
				}
   
            }else{
                $contents = array();
                $contents["PreAlertState"] = "";
				$contents["PrePresenceState"] = "";
				$contents["PresenceStateTemplate"] = "";
				IPS_SemaphoreLeave("SXGRP_SettingAccess".$this->InstanceID);
                return $contents;
				
            }
			
			IPS_SemaphoreLeave("SXGRP_SettingAccess".$this->InstanceID);
        }
    }
?>
