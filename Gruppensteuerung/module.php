<?
    class Gruppensteuerung extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }
		
        public function Create() {
            parent::Create();

			$ApplyChanges = false;
			
			$this->RegisterVariableBoolean("Ergebnis_Boolean", "Gesamt", "~Switch");
            $this->EnableAction("Ergebnis_Boolean");
			IPS_SetHidden($this->GetIDForIdent("Ergebnis_Boolean"), true); 
			
            $this->RegisterVariableFloat("Ergebnis_Float", "Gesamt", "~Intensity.1");
            $this->EnableAction("Ergebnis_Float");
			IPS_SetHidden($this->GetIDForIdent("Ergebnis_Float"), true); 

            $this->RegisterVariableInteger("Ergebnis_Integer", "Gesamt", "~Intensity.100");
            $this->EnableAction("Ergebnis_Integer");
			
			$this->RegisterVariableBoolean("EnablePresenceDetection", "Bewegungsmelder aktiviert", "~Switch");
            $this->EnableAction("EnablePresenceDetection");
			
			$this->RegisterVariableBoolean("AlertModeAktive", "Alarmbeleuchtung aktiviert", "~Switch");
            $this->EnableAction("AlertModeAktive");
			
			$this->RegisterVariableBoolean("PresenceDetected", "Anwesenheit", "~Presence");
			
			$this->RegisterVariableBoolean("ManualPresence", "manuelle Anwesenheit", "~Switch");
            $this->EnableAction("ManualPresence");
			
			$this->RegisterVariableFloat("CurrentMinBrightness", "Aktuelle Helligkeit", "");
			
			if (IPS_VariableProfileExists ( "SXGRP.Profiles" ) == false){
				IPS_CreateVariableProfile("SXGRP.Profiles", 1);
				IPS_SetVariableProfileValues("SXGRP.Profiles", 0, 10, 0);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 0, "Speichern", "Ok", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 1, "Profil 1", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 2, "Profil 2", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 3, "Profil 3", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 4, "Profil 4", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 5, "Profil 5", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 6, "Profil 6", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 7, "Profil 7", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 8, "Profil 8", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 9, "Profil 9", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles", 10, "Profil 10", "Light", null);
			}
			if (IPS_VariableProfileExists ( "SXGRP.Profiles2" ) == false){
				IPS_CreateVariableProfile("SXGRP.Profiles2", 1);
				IPS_SetVariableProfileValues("SXGRP.Profiles2", -3, 10, 0);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", -3, "Ein", "Power", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", -2, "Aus", "Power", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", -1, "Automatik", "Climate", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 1, "Profil 1", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 2, "Profil 2", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 3, "Profil 3", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 4, "Profil 4", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 5, "Profil 5", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 6, "Profil 6", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 7, "Profil 7", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 8, "Profil 8", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 9, "Profil 9", "Light", null);
				IPS_SetVariableProfileAssociation("SXGRP.Profiles2", 10, "Profil 10", "Light", null);
			}
		
			if (IPS_VariableProfileExists ( "SXGRP.Brightness" ) == false){
				IPS_CreateVariableProfile("SXGRP.Brightness", 2);
				IPS_SetVariableProfileValues("SXGRP.Brightness", 0, 2000, 5);
			}
				
			$this->RegisterVariableInteger("ProfileID", "Profil", "SXGRP.Profiles");
            $this->EnableAction("ProfileID");
			
			$this->RegisterVariableInteger("ProfileID2", "Profil Anwesend", "SXGRP.Profiles2");
            $this->EnableAction("ProfileID2");
			
			$this->RegisterVariableInteger("ProfileID3", "Profil Abwesend", "SXGRP.Profiles2");
            $this->EnableAction("ProfileID3");

			$this->RegisterVariableFloat("IlluminationLevelMotion", "Helligkeitsgrenze für Bewegungsmelder", "SXGRP.Brightness");
            $this->EnableAction("IlluminationLevelMotion");
			
			$this->RegisterPropertyInteger("PresenceTimeout", 10);
			$this->RegisterPropertyInteger("PresenceOffDelay", 0);
			$this->RegisterPropertyInteger("PresenceDimmerOffPercent", 10);
			$this->RegisterPropertyInteger("PresenceRefreshTimeout", 300);
			$this->RegisterPropertyInteger("PresenceResetToTemplateTimeout", 0);
			$this->RegisterPropertyInteger("BrightnessSegmentationLevel", 0);
			
			$this->RegisterPropertyInteger("IsVersion", 0);
			

            $this->RegisterPropertyInteger("DeviceCategory", 0); // Veraltet
			$this->RegisterPropertyString("actors", "");
			
			$this->RegisterPropertyInteger("PresenceCategory", 0); // Veraltet
			$this->RegisterPropertyString("sensors", "");
			
			$this->RegisterPropertyInteger("IlluminationCategory", 0); // Veraltet
			$this->RegisterPropertyString("brightness", "");			
			
			$ScriptID = $this->RegisterScript("StoreCurrentAsPresenceStateTemplate", "Als Vorlage für Anwesenheit speichern", "<?\n\nSXGRP_StoreCurrentAsPresenceStateTemplate(".$this->InstanceID."); \n\n?>");			
					
			//$this->RegisterTimer("UpdatePresence_Timer",0,'SXGRP_TimerCallback($_IPS["TARGET"], "UpdatePresence_Timer");');
			$this->RegisterTimer("UpdatePresence_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "UpdatePresence_Timer");');
			//$this->RegisterTimer("PresenceTimeoutOff_Timer",0,'SXGRP_TimerCallback($_IPS["TARGET"], "PresenceTimeoutOff_Timer");');
			$this->RegisterTimer("PresenceTimeoutOff_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "PresenceTimeoutOff_Timer");');
			// $this->RegisterTimer("PresenceOffDelayScript_Timer",0,'SXGRP_TimerCallback($_IPS["TARGET"], "PresenceOffDelayScript_Timer");');
			$this->RegisterTimer("PresenceOffDelayScript_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "PresenceOffDelayScript_Timer");');
			$this->RegisterTimer("ResetPresenceStateToTemplate_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "ResetPresenceStateToTemplate_Timer");');
			//$this->RegisterTimer("ResetPresenceStateToTemplate_Timer",0,'SXGRP_TimerCallback($_IPS["TARGET"], "ResetPresenceStateToTemplate_Timer");');
			
			
            if ($ApplyChanges == true){
				IPS_ApplyChanges($this->InstanceID);
			}
			
			//$this->UpdateEvents();
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->UpgradeToNewVersion();
			$this->UpdateEvents();
			$this->SetStatus(102);
        }

		public function UpgradeToNewVersion(){
			$vers = $this->ReadPropertyInteger("IsVersion");
			if ($vers >= 5){ 
				return; 
			}
			
			$actorsChanged = false;
			$CategoryID = $this->ReadPropertyInteger("DeviceCategory");
			if ($CategoryID > 0 and IPS_CategoryExists($CategoryID)){
				$arr = $this->GetListItems("actors");
				
				foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
					$itemObject = IPS_GetObject($key2);
					$TargetID = $key2;
					
					if ($itemObject["ObjectType"] == 6){
						$TargetID = IPS_GetLink($key2)["TargetID"];
					}

					if ($TargetID > 0){
						$actorExists = false;
						
						if ($arr){
							foreach($arr as $actor) {
								if($actor["InstanceID"] == $TargetID){
									$actorExists = true;
								}
							}
						}
						
						if ($actorExists == false){
							$arr[] = array("InstanceID" => $TargetID);
							$actorsChanged = true;
						}
					}
				}	
				
				if ($actorsChanged == true){
					$jsonString = json_encode($arr);
					IPS_SetProperty($this->InstanceID, "actors", $jsonString);
				}
			}
			
			
			$sensorsChanged = false;
			$CategoryID = $this->ReadPropertyInteger("PresenceCategory");
			if ($CategoryID > 0 and IPS_CategoryExists($CategoryID)){
				$arr = $this->GetListItems("sensors");
				
				foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
					$itemObject = IPS_GetObject($key2);
					$TargetID = $key2;
					
					if ($itemObject["ObjectType"] == 6){
						$TargetID = IPS_GetLink($key2)["TargetID"];
					}

					if ($TargetID > 0){
						$actorExists = false;
						
						if ($arr){
							foreach($arr as $actor) {
								if($actor["InstanceID"] == $TargetID){
									$actorExists = true;
								}
							}
						}
						
						if ($actorExists == false){
							$arr[] = array("InstanceID" => $TargetID, "typ" => 0);
							$sensorsChanged = true;
						}
					}
				}	
				
				if ($sensorsChanged == true){
					$jsonString = json_encode($arr);
					IPS_SetProperty($this->InstanceID, "sensors", $jsonString);
				}
			}
			
			
			$sensorsChanged = false;
			$CategoryID = $this->ReadPropertyInteger("IlluminationCategory");
			if ($CategoryID > 0 and IPS_CategoryExists($CategoryID)){
				$arr = $this->GetListItems("brightness");
				
				foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
					$itemObject = IPS_GetObject($key2);
					$TargetID = $key2;
					
					if ($itemObject["ObjectType"] == 6){
						$TargetID = IPS_GetLink($key2)["TargetID"];
					}

					if ($TargetID > 0){
						$actorExists = false;
						
						if ($arr){
							foreach($arr as $actor) {
								if($actor["InstanceID"] == $TargetID){
									$actorExists = true;
								}
							}
						}
						
						if ($actorExists == false){
							$arr[] = array("InstanceID" => $TargetID, "typ" => 0);
							$sensorsChanged = true;
						}
					}
				}	
				
				if ($sensorsChanged == true){
					$jsonString = json_encode($arr);
					IPS_SetProperty($this->InstanceID, "brightness", $jsonString);
				}
			}

			
			//Delete old scripts
			$oldScript = false;
			$oldScript = @IPS_GetObjectIDByIdent("Update", $this->InstanceID);
			if ($oldScript){ 
				foreach(IPS_GetChildrenIDs($oldScript) as $key) {
					if(IPS_EventExists($key)){ IPS_DeleteEvent($key); }
				}
				IPS_DeleteScript($oldScript, true); 
			}			
			
			$oldScript = false;
			$oldScript = @IPS_GetObjectIDByIdent("RefreshIlluminationLevel", $this->InstanceID);
			if ($oldScript){ 
				foreach(IPS_GetChildrenIDs($oldScript) as $key) {
					if(IPS_EventExists($key)){ IPS_DeleteEvent($key); }
				}
				IPS_DeleteScript($oldScript, true); 
			}		
			
			$oldScript = false;
			$oldScript = @IPS_GetObjectIDByIdent("UpdateAnwesenheit", $this->InstanceID);
			if ($oldScript) {
				foreach(IPS_GetChildrenIDs($oldScript) as $key) {
					if(IPS_EventExists($key)){ IPS_DeleteEvent($key); }
				}
				IPS_DeleteScript($oldScript, true); 
			}
			
			$oldScript = false;
			$oldScript = @IPS_GetObjectIDByIdent("PresenceTimeoutOff", $this->InstanceID);
			if ($oldScript) {
				foreach(IPS_GetChildrenIDs($oldScript) as $key) {
					if(IPS_EventExists($key)){ IPS_DeleteEvent($key); }
				}
				IPS_DeleteScript($oldScript, true); 
			}
			
			$oldScript = false;
			$oldScript = @IPS_GetObjectIDByIdent("PresenceOffDelayScript", $this->InstanceID);
			if ($oldScript) {
				foreach(IPS_GetChildrenIDs($oldScript) as $key) {
					if(IPS_EventExists($key)){ IPS_DeleteEvent($key); }
				}
				IPS_DeleteScript($oldScript, true); 
			}
			
			$oldScript = false;
			$oldScript = @IPS_GetObjectIDByIdent("ResetPresenceStateToTemplate", $this->InstanceID);
			if ($oldScript) {
				foreach(IPS_GetChildrenIDs($oldScript) as $key) {
					if(IPS_EventExists($key)){ IPS_DeleteEvent($key); }
				}
				IPS_DeleteScript($oldScript, true); 
			}
			
			IPS_SetProperty($this->InstanceID, "IsVersion", 5);
			
			if (IPS_HasChanges($this->InstanceID)){		
				IPS_ApplyChanges($this->InstanceID);
			}			
		}
		
		private function GetListItems($List){
			$arrString = $this->ReadPropertyString($List);
			if ($arrString){
				$arr = json_decode($arrString, true);
				
				return $arr;
			}	
			return null;
		}
		
		private function DeviceStatusChanged($DeviceID){
			$arr = $this->GetListItems("actors");
			if ($arr){
				foreach($arr as $key1) {
					if($key1["InstanceID"] == $DeviceID){
						$this->RefreshStatus();
						break;
					}
				}
			}	
			
			$arr = $this->GetListItems("sensors");
			if ($arr){
				foreach($arr as $key1) {
					if($key1["InstanceID"] == $DeviceID){
						$this->RefreshPresence();
						break;
					}
				}
			}	
			
			$arr = $this->GetListItems("brightness");
			if ($arr){
				foreach($arr as $key1) {
					if($key1["InstanceID"] == $DeviceID){
						$this->RefreshIlluminationLevel();
						break;
					}
				}
			}
		}
		
		
		public function UpdateEvents(){	
			$arr = $this->GetListItems("actors");
			if ($arr){
				foreach($arr as $key1) {
					$this->RegisterMessage($key1["InstanceID"], 10603);
				}
			}	
			
			$arr = $this->GetListItems("sensors");
			if ($arr){
				foreach($arr as $key1) {
					$this->RegisterMessage($key1["InstanceID"], 10603);
				}
			}	
			
			$arr = $this->GetListItems("brightness");
			if ($arr){
				foreach($arr as $key1) {
					$this->RegisterMessage($key1["InstanceID"], 10603);
				}
			}

			
		
			
			

			$this->RefreshStatus();
			$this->RefreshIlluminationLevel();
			$this->RefreshPresence();
		}

		private function RefreshPresence() {
			$enabled = GetValueBoolean(IPS_GetObjectIDByIdent("EnablePresenceDetection", $this->InstanceID));
			
			// Bricht ausführung ab wenn Bewegungsmelder deaktiviert sind. 
			// Wird diese Option verwendet, so wird das Profil beim deaktivieren der Bewegungsmelder nicht auf "Abwesend" gesetzt sondern verbleibt im aktuellen zustand.
			// if ($enabled == false){return;}	
			
			
			// $SkriptID = IPS_GetObjectIDByIdent("UpdateAnwesenheit", $this->InstanceID);
			//$PresenceOffDelayScriptID = IPS_GetObjectIDByIdent("PresenceOffDelayScript", $this->InstanceID);
					
			
			// Manuelle Anwesenheit überschreibt Bewegungsmelder
			$ManualPresence = GetValueBoolean($this->GetIDForIdent("ManualPresence"));
			if ($ManualPresence == true){
				$this->SetTimerInterval("PresenceOffDelayScript_Timer", 0);
				//IPS_SetScriptTimer($PresenceOffDelayScriptID, 0);
				$this->SetPresenceState(true);
				return;
			}

					
			$result = false;
			$PresenceDetectorsExist = false;
			$PresenceDeviceList = $this->GetListItems("sensors");
			$PresenceRefreshTimeout = $this->ReadPropertyInteger("PresenceRefreshTimeout");
			$PresenceOffDelay = $this->ReadPropertyInteger("PresenceOffDelay");
			$BrightnessSegmentationLevel = $this->ReadPropertyInteger("BrightnessSegmentationLevel");
			$BrightnessDeviceList = $this->GetListItems("brightness");
			$IlluminationLevelMotion = GetValueFloat(IPS_GetObjectIDByIdent("IlluminationLevelMotion", $this->InstanceID));  
			
			if (!$PresenceDeviceList){return; }
			
			foreach($PresenceDeviceList as $Device) {
				$key2 = $Device["InstanceID"];
				if (!IPS_ObjectExists($key2)){ continue; }
				
				$itemObject = IPS_GetObject($key2);
				$TargetID = $key2;
				$TargetName = IPS_GetName($key2);

			if ($itemObject["ObjectType"] == 2){
				$PresenceDetectorsExist = true;
				
				$var = IPS_GetVariable ($TargetID);
				// Prüfe ob Bewegungsmelder innerhalb des angegebenen sendeabstandes eine neue Bewegung gemeldet hat.
				if ($PresenceRefreshTimeout > 0){
					$timediff = time() - $var['VariableUpdated'];
					if ($timediff >= $PresenceRefreshTimeout){
						goto skipElement;
					}
				}
				
				// Prüfe ob Helligkeit am Bewegungsmelder dem Mindestlevel entspricht
				if ($BrightnessSegmentationLevel == 1 and $BrightnessDeviceList){
					$PresenceParent = IPS_GetParent($TargetID);
					
					foreach($BrightnessDeviceList as $BrightnessDevice) {
						$key3 = $BrightnessDevice["InstanceID"];
						if (!IPS_ObjectExists($key3)){ continue; }
						
						$itemObject = IPS_GetObject($key3);
						$TargetID3 = $key3;

						if ($PresenceParent == IPS_GetParent($TargetID3)){
							if (GetValue($TargetID3) > $IlluminationLevelMotion){
								goto skipElement;
							}
						}
					}
				}
				
				$t = $var["VariableType"];
				if ($t == 0){
				   $Meldung = GetValueBoolean($TargetID);
					if ($Meldung == true){
				      $result = true;
				   }
				}
				if ($t == 1){
				   $Meldung = GetValueInteger($TargetID);
					if ($Meldung > 0){ 
                        $result = true; 
                    }
				}
				if ($t == 2){
				   $Meldung = GetValueFloat($TargetID);
					if ($Meldung > 0){
                        $result = true;
                    }
				}
			}
			skipElement:
			
			}
			$this->SetTimerInterval("PresenceOffDelayScript_Timer", 0);
			//IPS_SetScriptTimer($PresenceOffDelayScriptID, 0);
			
						
			if ($PresenceDetectorsExist == true){
				if ($enabled == false){$result = false;}	//Setze Anwesenheit auf FALSCH wenn Bewegungsmelder deaktiviert wurden.
				
				if ($result == true){
				   	$this->SetTimerInterval("UpdatePresence_Timer", $PresenceRefreshTimeout * 1000);
					//IPS_SetScriptTimer($SkriptID, $PresenceRefreshTimeout);
					$this->SetPresenceState($result);
				}else{
					$this->SetTimerInterval("UpdatePresence_Timer", 0);
					// IPS_SetScriptTimer($SkriptID, 0);
					
					if ($PresenceOffDelay <= 0){
						$this->SetPresenceState($result);
					}else{
						$this->SetTimerInterval("PresenceOffDelayScript_Timer", $PresenceOffDelay * 1000);
						//IPS_SetScriptTimer($PresenceOffDelayScriptID, $PresenceOffDelay);
					}
				}
			}
		}
		
        private function RefreshStatus() {
			$result = false;
            $resultFloat = 0.0;
            $resultInteger = 0;
			$ActorDeviceList = $this->GetListItems("actors");
			
			if (!$ActorDeviceList){ return; }
			

			foreach($ActorDeviceList as $device) {
				$key2 = $device["InstanceID"];
				
	         $itemObject = IPS_GetObject($key2);
	         $TargetID = $key2;
	         $TargetName = IPS_GetName($key2);
		
		
			if ($itemObject["ObjectType"] == 2){
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
                    if ($resultFloat < $Meldung / 100){
                        $resultFloat = $Meldung / 100; 
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
		private function RefreshIlluminationLevel(){
			$IlluminationLevelMotion = GetValueFloat(IPS_GetObjectIDByIdent("IlluminationLevelMotion", $this->InstanceID));  
			$illumination = $this->GetIlluminationLevelMin();
			
			SetValue($this->GetIDForIdent("CurrentMinBrightness"), $illumination);
						
			if ($IlluminationLevelMotion > -1){
				if ($illumination <= $IlluminationLevelMotion){
					$this->RefreshPresence(); // Bei unterschreiten der Helligkeitsgrenze die Bewegungsmelder neu auswerten.
				}
			}			
		}
		public function GetIlluminationLevelMin(){
			$IlluminationDetectorsExist = false;
			$BrightnessDeviceList = $this->GetListItems("brightness");
			
			$result = 9999.9;
			
			if (!$BrightnessDeviceList){ return; }
			
			foreach($BrightnessDeviceList as $device) {
				$key2 = $device["InstanceID"];
				if (!IPS_ObjectExists($key2)){ continue; }
				
				$itemObject = IPS_GetObject($key2);
				$TargetID = $key2;
				$TargetName = IPS_GetName($key2);

				if ($itemObject["ObjectType"] == 2){
					$IlluminationDetectorsExist = true;
				
					$var = IPS_GetVariable ($TargetID);
								
					$t = $var["VariableType"];
					if ($t == 0){
						$Meldung = GetValueBoolean($TargetID);
						if ($Meldung == true){
							$Meldung1 = 1;
						}else{
							$Meldung1 = 0;
						}
						if ($Meldung1 < $result){ 
							$result = $Meldung; 
						}
					}
					if ($t == 1){
						$Meldung = GetValueInteger($TargetID);
						if ($Meldung < $result){ 
							$result = $Meldung; 
						}
					}
					if ($t == 2){
						$Meldung = GetValueFloat($TargetID);
						if ($Meldung < $result){ 
							$result = $Meldung; 
						}
					}
				}
			}

			if ($IlluminationDetectorsExist == false){
				$result = -1;
			}
			
			return $result;
	   }
	    public function SetState(bool $Value){
			if ($Value){
                    $ValueInteger = 100;
                    $ValueFloat = 1.0;
                }else{
                    $ValueInteger = 0;
                    $ValueFloat = 0.0;
            }
								
            $data = $this->ReadSettings();
			$currentPreAlertState = $data["PreAlertState"];
			if ($currentPreAlertState !== "" and $Value == false){
				return;
			}

			$arr = $this->GetListItems("actors");
			if ($arr){
				foreach($arr as $device){
					$this->SetObjectValue($device["InstanceID"], $Value, $ValueInteger, $ValueFloat, false, false);
				}
			}
		}
	
        public function SetStateFloat(float $Value){
			$ValueInteger = $Value * 100;
			
			if ($Value > 0.0){           
                    $ValueBool = true;
                }else{
                    $ValueBool = false;
            }
								
            $data = $this->ReadSettings();
			$currentPreAlertState = $data["PreAlertState"];
			if ($currentPreAlertState !== "" and $Value == false){
				return;
			}

			$arr = $this->GetListItems("actors");
			if ($arr){
				foreach($arr as $device){
					$this->SetObjectValue($device["InstanceID"], $ValueBool, $ValueInteger, $Value, false, false);
				}
			}
        }
        public function SetStateInteger(int $Value){
			$ValueFloat = $Value / 100;
			
			if ($Value > 0){           
                    $ValueBool = true;
                }else{
                    $ValueBool = false;
            }
								
            $data = $this->ReadSettings();
			$currentPreAlertState = $data["PreAlertState"];
			if ($currentPreAlertState !== "" and $Value == false){
				return;
			}

			$arr = $this->GetListItems("actors");
			if ($arr){
				foreach($arr as $device){
					$this->SetObjectValue($device["InstanceID"], $ValueBool, $Value, $ValueFloat, false, false);
				}
			}
        }

		
		public function SetAlertState(bool $Value){
			IPS_SemaphoreEnter("SXGRP_AlertStateChange", 120 * 1000);
			
            $data = $this->ReadSettings();

			$currentPreAlertState = $data['PreAlertState'];
					
			if ($Value == true){
				if ($currentPreAlertState == ""){
					$result = $this->GetCurrentStateString();
                    $data["PreAlertState"] = $result;
				}
			
				$arr = $this->GetListItems("actors");
				if ($arr){
					foreach($arr as $device){
						$this->SetObjectValue($device["InstanceID"], $Value, 100, 1.0, false, false);
					}
				}

			}else{
				if ($currentPreAlertState !== ""){
					$this->SetCurrentStateString($currentPreAlertState);
                    $data["PreAlertState"] = "";
				}
			}

            $this->WriteSettings($data);
			// $this->RefreshStatus();
						
			SetValueBoolean($this->GetIDForIdent("AlertModeAktive"), $Value);
			
			if ($Value == false){
				$this->RefreshPresence();
			}
			
			IPS_SemaphoreLeave("SXGRP_AlertStateChange");
		}
		public function SetManualPresence(bool $Value){
			SetValueBoolean($this->GetIDForIdent("ManualPresence"), $Value);
			$this->RefreshPresence();
		}
		private function SetPresenceState(bool $Value){
			$enabled = GetValueBoolean(IPS_GetObjectIDByIdent("EnablePresenceDetection", $this->InstanceID));
			//IPS_SemaphoreEnter("SXGRP_AlertStateChange", 120 * 1000);
			SetValue($this->GetIDForIdent("PresenceDetected"), $Value);
			
			//$PresenceOffDelayScriptID = IPS_GetObjectIDByIdent("PresenceOffDelayScript", $this->InstanceID);
			$this->SetTimerInterval("PresenceOffDelayScript_Timer", 0);
			//IPS_SetScriptTimer($PresenceOffDelayScriptID, 0);
			
            $data = $this->ReadSettings();

			$currentPrePresenceState = $data['PrePresenceState'];
			$currentPreAlertState = $data['PreAlertState'];
			
			if ($currentPreAlertState !== ""){
				return;
			}
			
			// Keine weitere Ausführung, wenn Bewegungsmelder deaktiviert sind.
			if ($enabled == false){
				return;
			}
			
			$DeviceList = $this->GetListItems("actors");
			$PresenceTimeout = $this->ReadPropertyInteger("PresenceTimeout");
			//$PresenceTimeoutOffScriptID = IPS_GetObjectIDByIdent("PresenceTimeoutOff", $this->InstanceID);  
			//$PresenceResetID = IPS_GetObjectIDByIdent("ResetPresenceStateToTemplate", $this->InstanceID);  
			
			$PresenceResetToTemplateTimeout = $this->ReadPropertyInteger("PresenceResetToTemplateTimeout");

			if ($Value == false){
				if ($currentPrePresenceState == ""){
					$result = $this->GetCurrentStateString();
                    $data["PrePresenceState"] = $result;
				}
				
				$ProfileID3 = GetValueInteger(IPS_GetObjectIDByIdent("ProfileID3", $this->InstanceID)); 
				
				if ($ProfileID3 == -1 or $ProfileID3 == 0 or $ProfileID3 == -2){
					if ($PresenceTimeout > 0){
						$DimmLevel = $this->ReadPropertyInteger("PresenceDimmerOffPercent");
						
						if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], true, $DimmLevel, $DimmLevel / 100, true, false);
							}
						}
						
						$this->SetTimerInterval("PresenceTimeoutOff_Timer", $PresenceTimeout * 1000);
						// IPS_SetScriptTimer ($PresenceTimeoutOffScriptID, $PresenceTimeout );
					
					}else{
						if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], false, 0, 0, false, false);
							}
						}
					}
				
				}elseif($ProfileID3 == -3){
					if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], true, 100, 1.0, false, false);
							}
						}
					
				}elseif($ProfileID3 > 0){
						CallProfile(ProfileID3);
						
				}
				
				if ($PresenceResetToTemplateTimeout > 0 ){
					$this->SetTimerInterval("ResetPresenceStateToTemplate_Timer", ($PresenceTimeout + $PresenceResetToTemplateTimeout) * 1000);
					//IPS_SetScriptTimer ($PresenceResetID, $PresenceTimeout + $PresenceResetToTemplateTimeout);
				}
				
			}else{
				// Prüfe Helligkeit
				$IlluminationLevelMotion = GetValueFloat(IPS_GetObjectIDByIdent("IlluminationLevelMotion", $this->InstanceID));  
				if ($IlluminationLevelMotion > -1){
					$illumination = $this->GetIlluminationLevelMin();
					if ($illumination > $IlluminationLevelMotion){
						return; // Bewegung nicht als erkannt setzen, wenn Helligkeit höher als eingestellter Wert ist.
					}
				}
				
				$this->SetTimerInterval("PresenceTimeoutOff_Timer", 0);
				// IPS_SetScriptTimer($PresenceTimeoutOffScriptID, 0);
				$this->SetTimerInterval("ResetPresenceStateToTemplate_Timer", 0);
				//IPS_SetScriptTimer ($PresenceResetID, 0);
				
				$ProfileID2 = GetValueInteger(IPS_GetObjectIDByIdent("ProfileID2", $this->InstanceID));  
				
				if ($ProfileID2 == -1 or $ProfileID2 == 0){
					if ($currentPrePresenceState !== ""){
						$this->SetCurrentStateString($currentPrePresenceState);
					}
					
				}elseif($ProfileID2 == -2){
					if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], false, 0, 0, false, false);
							}
						}
					
					
				}elseif($ProfileID2 == -3){
					if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], true, 100, 1.0, false, false);
							}
						}
					
				}elseif($ProfileID2 > 0){
						$this->CallProfile($ProfileID2);
						
				}
				
				$data["PrePresenceState"] = "";
			}

            $this->WriteSettings($data);
			$this->RefreshStatus();
			
			//IPS_SemaphoreLeave("SXGRP_AlertStateChange");
		}
		private function PresenceTimeoutOff(){
			//IPS_SemaphoreEnter("SXGRP_AlertStateChange", 120 * 1000);
			
			//$PresenceTimeoutOffScriptID = IPS_GetObjectIDByIdent("PresenceTimeoutOff", $this->InstanceID);
			//$this->SetTimerInterval("PresenceTimeoutOff_Timer", 0);
			//IPS_SetScriptTimer($PresenceTimeoutOffScriptID, 0);
			
			$data = $this->ReadSettings();

			if (array_key_exists('PrePresenceState', $data) == false) {
				$data['PrePresenceState'] = "";
			}
			$currentPrePresenceState = $data['PrePresenceState'];
			
			if ($currentPrePresenceState !== ""){
				$arr = $this->GetListItems("actors");
				if ($arr){
					foreach($arr as $device){
						$this->SetObjectValue($device["InstanceID"], false, 0, 0, false, false);
					}
				}
			}
			//IPS_SemaphoreLeave("SXGRP_AlertStateChange");
		}
		public function ResetPresenceStateToTemplate(){
			//$PResetPresenceStateToTemplateScriptID = IPS_GetObjectIDByIdent("ResetPresenceStateToTemplate", $this->InstanceID);
			//IPS_SetScriptTimer($PResetPresenceStateToTemplateScriptID, 0);
			
			$data = $this->ReadSettings();
			
			$data['PrePresenceState'] = $data['PresenceStateTemplate'];
			
			$this->WriteSettings($data);
		}
		public function StoreCurrentAsPresenceStateTemplate(){
			$data = $this->ReadSettings();
			
			$State = $this->GetCurrentStateString();
			
			$data['PresenceStateTemplate'] = $State;
			$data['PrePresenceState'] = $State;
			
			$this->WriteSettings($data);
		}
		public function GetCurrentStateString(){
			$arr =array();
			$DeviceList = $this->GetListItems("actors");

			if ($DeviceList){
			foreach($DeviceList as $Device) {
				$TargetID = $Device["InstanceID"];				
				$itemObject = IPS_GetObject($TargetID);
				
				if (IPS_VariableExists($TargetID)){
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
			}
			
			
			

			return json_encode($arr);
		}
		public function SetCurrentStateString(string $State){
			$arr = json_decode($State, true);
			$DeviceList = $this->GetListItems("actors");
            $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

			if (!$DeviceList){ return; }
			
			foreach($DeviceList as $Device) {
				$key2 = $Device["InstanceID"];
				
				set_time_limit(30);

				$itemObject = IPS_GetObject($key2);
				$TargetID = $key2;
				
				if (IPS_VariableExists($TargetID)){
					$pID = IPS_GetParent($TargetID);
                    $VariableName = IPS_GetName($TargetID);
					$value = $arr[$TargetID];
					$var = IPS_GetVariable ($TargetID);
					$t = $var["VariableType"];
					$currentVal = GetValue($TargetID);

					if ($currentVal != $value){
						if (@IPS_RequestAction($pID, $VariableName, $value) == false){
							SetValue($TargetID, $value);
						}
					}
				}
			}
		}

		public function CallProfile(int $id){
			//IPS_SemaphoreEnter("SXGRP_AlertStateChange", 120 * 1000);
			
			$data = $this->ReadSettings();
			if (array_key_exists('Profile'.$id, $data)) {
				$this->SetCurrentStateString($data['Profile'.$id]);
			}
			SetValue($this->GetIDForIdent("ProfileID"), $id);

			//IPS_SemaphoreLeave("SXGRP_AlertStateChange");
		}
		public function UseProfileIDAsPresenceStateTeplate(int $id){
			$data = $this->ReadSettings();
			
			if (array_key_exists('Profile'.$id, $data)) {
				$data['PresenceStateTemplate'] = $data['Profile'.$id];
			}

			SetValue($this->GetIDForIdent("ProfileID2"), $id);
			
			$this->WriteSettings($data);
		}
		public function UseProfileIDAsPresenceStateTeplateAndApplyToCurrentStateIfPresent(int $id){
			$data = $this->ReadSettings();
			
			if (array_key_exists('Profile'.$id, $data)) {
				$data['PresenceStateTemplate'] = $data['Profile'.$id];
				$data['PrePresenceState'] = $data['Profile'.$id];
			}

			$this->WriteSettings($data);
			
			SetValue($this->GetIDForIdent("ProfileID"), $id);
			SetValue($this->GetIDForIdent("ProfileID2"), $id);
			
			$this->RefreshPresence();
		}
		public function StoreProfile(int $id){
			$data = $this->ReadSettings();
			
			$data['Profile'.$id] = $this->GetCurrentStateString();
			
			$this->WriteSettings($data);
		}
		public function StoreCurrentProfile(){
			$ProfileID = GetValue($this->GetIDForIdent("ProfileID"));
			$this->StoreProfile($ProfileID);
		}
		public function EnablePresenceDetection(){
			SetValueBoolean($this->GetIDForIdent("EnablePresenceDetection"), true);
			$this->RefreshPresence();
		}
		public function DisablePresenceDetection(){
			SetValueBoolean($this->GetIDForIdent("EnablePresenceDetection"), false);
			$this->RefreshPresence();
		}
		public function SetProfile(int $id){
			$this->CallProfile($id);
		}
		public function SetProfilePresent(int $id){
			SetValue($this->GetIDForIdent("ProfileID2"), $id);
			if ($id > 0){
				$this->UseProfileIDAsPresenceStateTeplateAndApplyToCurrentStateIfPresent($id);	
			}
		}
		public function SetProfileAbsent(int $id){
			SetValue($this->GetIDForIdent("ProfileID3"), $id);
			$this->RefreshPresence();
		}
		public function SetIlluminationLevelMotion(float $Value){
			SetValue($this->GetIDForIdent("IlluminationLevelMotion"), $Value);
			$this->RefreshIlluminationLevel();
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

			case "ProfileID":
				// Aktuelles Profil
				if ($Value > 0){
					$this->CallProfile($Value);
				}elseif($Value == 0){
					$this->StoreCurrentProfile();
				}
				break;
				
			case "ProfileID2":
			    // Profil Anwesend
				$this->SetProfilePresent($Value);
				break;
				
			case "ProfileID3":
				// Profil Abwesend
				$this->SetProfileAbsent($Value);
				break;
			
			case "EnablePresenceDetection":
				if ($Value == true){
					$this->EnablePresenceDetection();
				}else{
					$this->DisablePresenceDetection();
				}
				break;
			
			case "IlluminationLevelMotion":
				$this->SetIlluminationLevelMotion($Value);
				break;
			
			case "AlertModeAktive":
				$this->SetAlertState($Value);
				break;
			
			case "ManualPresence":
				$this->SetManualPresence($Value);
				break;
				
			case "TimerCallback":
				$this->TimerCallback($Value);
				break;
			
        	default:
	            throw new Exception("Invalid Ident");

    		}
 		}
		
		public function TimerCallback(string $TimerID){
			$this->SetTimerInterval($TimerID, 0);
				
				switch($TimerID){
					case "UpdatePresence_Timer":
						$this->RefreshPresence();
						break;
						
					case "PresenceTimeoutOff_Timer":
						$this->PresenceTimeoutOff();
						break;
						
					case "PresenceOffDelayScript_Timer":
						$this->SetPresenceState(false);
						break;
						
					case "ResetPresenceStateToTemplate_Timer":
						$this->ResetPresenceStateToTemplat();
						break;
				}				
		}
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			if ($Message == 10603){
				$this->DeviceStatusChanged($SenderID);
			}
		}
		
		
		private function SetObjectValue(int $key, bool $value, int $valueInteger, float $valueFloat, bool $lowerOnly, bool $higherOnly){
			set_time_limit(30);

				$itemObject = IPS_GetObject($key);
				$TargetID = $key;


				if (IPS_VariableExists($TargetID)){
					$pID = IPS_GetParent($TargetID);					
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

        private function WriteSettings($data){
			IPS_SemaphoreEnter("SXGRP_SettingAccess".$this->InstanceID,  2000);
            $fp = fopen(IPS_GetKernelDir().$this->InstanceID.'.settings.json', 'w');
            fwrite($fp, json_encode($data));
            fclose($fp);
			IPS_SemaphoreLeave("SXGRP_SettingAccess".$this->InstanceID);
        }
        private function ReadSettings(){
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
