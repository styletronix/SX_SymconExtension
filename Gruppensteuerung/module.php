<?php
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
			
			$this->RegisterVariableString("statusString", "Status");
			
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
			
			$this->RegisterVariableInteger("ProfileID4", "Profil Alarmbeleuchtung", "SXGRP.Profiles2");
            $this->EnableAction("ProfileID4");

			$this->RegisterVariableFloat("IlluminationLevelMotion", "Helligkeitsgrenze für Bewegungsmelder", "SXGRP.Brightness");
            $this->EnableAction("IlluminationLevelMotion");
			
			$this->RegisterPropertyInteger("PresenceTimeout", 10);
			$this->RegisterPropertyInteger("PresenceOffDelay", 0);
			$this->RegisterPropertyInteger("PresenceDimmerOffPercent", 10);
			$this->RegisterPropertyInteger("PresenceRefreshTimeout", 300);
			$this->RegisterPropertyInteger("PresenceResetToTemplateTimeout", 0);
			$this->RegisterPropertyInteger("BrightnessSegmentationLevel", 0);
			$this->RegisterPropertyInteger("ManualPresenceResetTimeout", 0);
			$this->RegisterPropertyInteger("AlertTimeout", 0);
			$this->RegisterPropertyInteger("PresenceDetectionOffTimeout", 0);		
			$this->RegisterPropertyBoolean("ResetManualPresenceOnManualTrigger", false);
			$this->RegisterPropertyInteger("IsVersion", 0);
			

            $this->RegisterPropertyInteger("DeviceCategory", 0); // Veraltet
			$this->RegisterPropertyString("actors", "");
			
			$this->RegisterPropertyInteger("PresenceCategory", 0); // Veraltet
			$this->RegisterPropertyString("sensors", "");
			
			$this->RegisterPropertyInteger("IlluminationCategory", 0); // Veraltet
			$this->RegisterPropertyString("brightness", "");			
			
			$ScriptID = $this->RegisterScript("StoreCurrentAsPresenceStateTemplate", "Als Vorlage für Anwesenheit speichern", "<?\n\nSXGRP_StoreCurrentAsPresenceStateTemplate(".$this->InstanceID."); \n\n?>");			
							
			$this->RegisterTimer("UpdatePresence_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "UpdatePresence_Timer");');			
			$this->RegisterTimer("PresenceTimeoutOff_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "PresenceTimeoutOff_Timer");');			
			$this->RegisterTimer("PresenceOffDelayScript_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "PresenceOffDelayScript_Timer");');
			$this->RegisterTimer("ResetPresenceStateToTemplate_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "ResetPresenceStateToTemplate_Timer");');
			$this->RegisterTimer("ManualPresenceReset_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "ManualPresenceReset_Timer");');
			$this->RegisterTimer("AlertTimeout_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "AlertTimeout_Timer");');
			$this->RegisterTimer("PresenceDetectionOffTimeout_Timer",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "PresenceDetectionOffTimeout_Timer");');
						
            if ($ApplyChanges == true){
				IPS_ApplyChanges($this->InstanceID);
			}
			
			//$this->UpdateEvents();
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->UpgradeToNewVersion();
			$this->UpdateEvents();
			$this->CheckTimerIntervals();
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
				$resetManual = $this->ReadPropertyBoolean("ResetManualPresenceOnManualTrigger");	
				foreach($arr as $key1) {
					if($key1["InstanceID"] == $DeviceID){
						if($key1["typ"] == 0){
							// Motion
							$this->RefreshPresence();
						}
						if($key1["typ"] == 1){
							// Button (depends on brightness)
							$this->RefreshPresence();
						}
						if($key1["typ"] == 2){
							// Button manual presence on
							$this->SetManualPresence(true);
						}
						if($key1["typ"] == 3){
							// Button manual presence off
							$this->SetManualPresence(false);
						}
						if($key1["typ"] == 4){
							// Button disable presence detection
							$this->DisablePresenceDetection();
						}
						if($key1["typ"] == 5){
							// Button enable presence detection
							$this->EnablePresenceDetection();
						}
						if($key1["typ"] == 6){
							// Button alert mode on
							$this->SetAlertState(true);
						}
						if($key1["typ"] == 7){
							// Button alert mode off
							$this->SetAlertState(false);
						}
						if($key1["typ"] == 8){
							// Button on
							if ($resetManual == true){
								$this->SetManualPresence(false);
							}
							$this->SetPresenceState(true, true);
						}
						
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
				$this->SetPresenceState(true, false);
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
			
			if ($PresenceDeviceList){
				foreach($PresenceDeviceList as $Device) {
					if($Device["typ"] < 0 or $Device["typ"] > 1){
						// Skip Buttons
						continue;
					}
						
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
			}
			
			
			$this->SetTimerInterval("PresenceOffDelayScript_Timer", 0);
			
						
			if ($PresenceDetectorsExist == true){
				if ($enabled == false){$result = false;}	//Setze Anwesenheit auf FALSCH wenn Bewegungsmelder deaktiviert wurden.
				
				if ($result == true){
				   	$this->SetTimerInterval("UpdatePresence_Timer", $PresenceRefreshTimeout * 1000);
					$this->SetPresenceState($result, false);
					
				}else{
					$this->SetTimerInterval("UpdatePresence_Timer", 0);
					
					if ($PresenceOffDelay <= 0){
						$this->SetPresenceState($result, false);
					}else{
						$this->SetTimerInterval("PresenceOffDelayScript_Timer", $PresenceOffDelay * 1000);
					}
				}
			}else{
				$this->SetPresenceState($result, false);
			}
		}
		
        private function RefreshStatus() {
			$result = false;
            $resultFloat = 0.0;
			$ActorDeviceList = $this->GetListItems("actors");
			
			if (!$ActorDeviceList){ return; }
			
			foreach($ActorDeviceList as $device) {
				$key2 = $device["InstanceID"];				
				$deviceStatus = $this->GetObjectValuePercent($key2);
				if ($deviceStatus > $resultFloat){
					$resultFloat = $deviceStatus;
				}
			}	
			
			if($resultFloat > 0.0){
				$result = true;
			}else{
				$result = false;
			}
		
			SetValue($this->GetIDForIdent("Ergebnis_Boolean"), $result);
			SetValue($this->GetIDForIdent("Ergebnis_Float"),	$resultFloat);
			SetValue($this->GetIDForIdent("Ergebnis_Integer"), $resultFloat * 100);
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
                    //$ValueInteger = 100;
                    $ValueFloat = 1.0;
                }else{
                    //$ValueInteger = 0;
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
					$this->SetObjectValuePercent($device["InstanceID"], $ValueFloat, false, false);
				}
			}
		}
	
        public function SetStateFloat(float $Value){								
            $data = $this->ReadSettings();
			$currentPreAlertState = $data["PreAlertState"];
			if ($currentPreAlertState !== "" and $Value == false){
				return;
			}

			$arr = $this->GetListItems("actors");
			if ($arr){
				foreach($arr as $device){
					$this->SetObjectValuePercent($device["InstanceID"], $Value, false, false);
				}
			}
        }
        public function SetStateInteger(int $Value){
			$ValueFloat = $Value / 100;
								
            $data = $this->ReadSettings();
			$currentPreAlertState = $data["PreAlertState"];
			if ($currentPreAlertState !== "" and $Value == false){
				return;
			}

			$arr = $this->GetListItems("actors");
			if ($arr){
				foreach($arr as $device){
					$this->SetObjectValuePercent($device["InstanceID"], $ValueFloat, false, false);
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
			
				$ProfileID4 = GetValueInteger(IPS_GetObjectIDByIdent("ProfileID4", $this->InstanceID));  
				if ($ProfileID4 > 0){
					// Load Profile if set
					$this->CallProfile($ProfileID4);
				}elseif($ProfileID3 == -2){
					// Switch off on Alert
					$arr = $this->GetListItems("actors");
					if ($arr){
						foreach($arr as $device){
							$this->SetObjectValuePercent($device["InstanceID"], 0.0, false, false);
						}
					}
				}else{
					// Switch on in Alert (default)
					$arr = $this->GetListItems("actors");
					if ($arr){
						foreach($arr as $device){
							$this->SetObjectValuePercent($device["InstanceID"], 1.0, false, false);
						}
					}
				}

			}else{
				if ($currentPreAlertState !== ""){
					$this->SetCurrentStateString($currentPreAlertState);
                    $data["PreAlertState"] = "";
				}
			}

            $this->WriteSettings($data);
						
			SetValueBoolean($this->GetIDForIdent("AlertModeAktive"), $Value);
			
			if ($Value == false){
				$this->SetTimerInterval("AlertTimeout_Timer", 0);
				$this->RefreshPresence();
			}else{
				$timer = $this->ReadPropertyInteger("AlertTimeout");	
				$this->SetTimerInterval("AlertTimeout_Timer",  $timer * 1000);
			}
			
			IPS_SemaphoreLeave("SXGRP_AlertStateChange");
		}
		public function SetManualPresence(bool $Value){
			if ($Value){
				$timer = $this->ReadPropertyInteger("ManualPresenceResetTimeout");			
				$this->SetTimerInterval("ManualPresenceReset_Timer", $timer * 1000);
			}else{
				$this->SetTimerInterval("ManualPresenceReset_Timer", 0);
			}
			
			SetValueBoolean($this->GetIDForIdent("ManualPresence"), $Value);
			$this->RefreshPresence();
		}
		public function TriggerPresenceDetected(){
			$this->SetPresenceState(true, false);
		}

		private function SetPresenceState(bool $Value, bool $ignoreIllumination){
			$enabled = GetValueBoolean(IPS_GetObjectIDByIdent("EnablePresenceDetection", $this->InstanceID));
			SetValue($this->GetIDForIdent("PresenceDetected"), $Value);
			
			$this->SetTimerInterval("PresenceOffDelayScript_Timer", 0);
			
            $data = $this->ReadSettings();

			$currentPrePresenceState = $data['PrePresenceState'];
			$currentPreAlertState = $data['PreAlertState'];
			
			if ($currentPreAlertState !== ""){
				$this->SetValue("statusString", "Alarmmodus ist aktiv");
				return;
			}
			
			// Keine weitere Ausführung, wenn Bewegungsmelder deaktiviert sind.
			if ($enabled == false){
				$this->SetValue("statusString", "Bewegungsmelder sind deaktiviert");
				return;
			}
			
			$DeviceList = $this->GetListItems("actors");
			$PresenceTimeout = $this->ReadPropertyInteger("PresenceTimeout");

			$PresenceResetToTemplateTimeout = $this->ReadPropertyInteger("PresenceResetToTemplateTimeout");

			if ($Value == false){
				if ($currentPrePresenceState == ""){
					$result = $this->GetCurrentStateString();
                    $data["PrePresenceState"] = $result;
				}
				
				$ProfileID3 = GetValueInteger(IPS_GetObjectIDByIdent("ProfileID3", $this->InstanceID)); 
				
				if ($ProfileID3 == -1 or $ProfileID3 == 0 or $ProfileID3 == -2){
					if ($PresenceTimeout > 0){
						$this->SetValue("statusString", "Vorwarnung vor abwesenheit...");
						$DimmLevel = $this->ReadPropertyInteger("PresenceDimmerOffPercent");
						
						if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], true, $DimmLevel, $DimmLevel / 100, true, false);
							}
						}
						
						$this->SetTimerInterval("PresenceTimeoutOff_Timer", $PresenceTimeout * 1000);
						
					}else{
						$this->SetValue("statusString", "Abwesend (Aus)");
						if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], false, 0, 0, false, false);
							}
						}
					}
				
				}elseif($ProfileID3 == -3){
					$this->SetValue("statusString", "Abwesend (An)");
					if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], true, 100, 1.0, false, false);
							}
						}
					
				}elseif($ProfileID3 > 0){
					$this->SetValue("statusString", "Abwesend (Profil ". $ProfileID3 .")");
					$this->CallProfile($ProfileID3);
						
				}
				
				if ($PresenceResetToTemplateTimeout > 0 ){
					$this->SetTimerInterval("ResetPresenceStateToTemplate_Timer", ($PresenceTimeout + $PresenceResetToTemplateTimeout) * 1000);
				}
				
			}else{
				// Prüfe Helligkeit
				if ($ignoreIllumination == false){
				$IlluminationLevelMotion = GetValueFloat(IPS_GetObjectIDByIdent("IlluminationLevelMotion", $this->InstanceID));  
				if ($IlluminationLevelMotion > -1){
					$illumination = $this->GetIlluminationLevelMin();
					if ($illumination > $IlluminationLevelMotion){
						$this->SetValue("statusString", "Bewegung wegen Helligkeit ignoriert");
						return; // Bewegung nicht als erkannt setzen, wenn Helligkeit höher als eingestellter Wert ist.
					}
				}
				}
				
				$this->SetTimerInterval("PresenceTimeoutOff_Timer", 0);
				$this->SetTimerInterval("ResetPresenceStateToTemplate_Timer", 0);
				
				$ProfileID2 = GetValueInteger(IPS_GetObjectIDByIdent("ProfileID2", $this->InstanceID));  
				
				if ($ProfileID2 == -1 or $ProfileID2 == 0){
					if ($currentPrePresenceState !== ""){
						$this->SetValue("statusString", "Anwesend (Automatik)");
						$this->SetCurrentStateString($currentPrePresenceState);
					}
					
				}elseif($ProfileID2 == -2){
					$this->SetValue("statusString", "Anwesend (Aus)");
					if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], false, 0, 0, false, false);
							}
						}
					
					
				}elseif($ProfileID2 == -3){
					$this->SetValue("statusString", "Anwesend (An)");					
					if ($DeviceList){
							foreach($DeviceList as $device){
								$this->SetObjectValue($device["InstanceID"], true, 100, 1.0, false, false);
							}
						}
					
				}elseif($ProfileID2 > 0){
					$this->SetValue("statusString", "Anwesend (Profil ". $ProfileID2 .")");
					$this->CallProfile($ProfileID2);
						
				}
				
				$data["PrePresenceState"] = "";
			}

            $this->WriteSettings($data);
			$this->RefreshStatus();
		}
		private function ApplyPresenceStateAfterProfileChange(){
			$this->SetPresenceState(GetValueBoolean($this->GetIDForIdent("PresenceDetected")), false);
		}
		private function PresenceTimeoutOff(){			
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
		}
		public function ResetPresenceStateToTemplate(){
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
				
				if (IPS_VariableExists($TargetID)){
					$itemObject = IPS_GetObject($TargetID);
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
				$TargetID = $key2;
				
				set_time_limit(30);

				if (IPS_VariableExists($TargetID)){
					$itemObject = IPS_GetObject($key2);
					$pID = IPS_GetParent($TargetID);
                    $VariableName = IPS_GetName($TargetID);
					if (array_key_exists($TargetID, $arr)) {
						$value = $arr[$TargetID];
						$currentVal = GetValue($TargetID);
						
						if ($currentVal != $value){
							$this->SetObjectValue($TargetID, $value, $value, $value, false, false);
						}
					}
				}
			}
		}

		public function CallProfile(int $id){
			$data = $this->ReadSettings();
			if (array_key_exists('Profile'.$id, $data)) {
				$this->SetCurrentStateString($data['Profile'.$id]);
			}
			SetValue($this->GetIDForIdent("ProfileID"), $id);
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
			$this->SetTimerInterval("PresenceDetectionOffTimeout_Timer",  0);
			
			SetValueBoolean($this->GetIDForIdent("EnablePresenceDetection"), true);
			$this->RefreshPresence();
		}
		public function DisablePresenceDetection(){
			$timer = $this->ReadPropertyInteger("PresenceDetectionOffTimeout");	
			$this->SetTimerInterval("PresenceDetectionOffTimeout_Timer",  $timer * 1000);
			
			SetValueBoolean($this->GetIDForIdent("EnablePresenceDetection"), false);
			$this->RefreshPresence();
		}
		public function SetProfile(int $id){
			$this->CallProfile($id);
		}
		public function SetProfilePresent(int $id){
			SetValue($this->GetIDForIdent("ProfileID2"), $id);
			
			if (GetValueBoolean($this->GetIDForIdent("PresenceDetected")) == true){
				$this->ApplyPresenceStateAfterProfileChange();
			} else {
				$this->RefreshPresence();
			}
			
			// if ($id > 0){
				// $this->UseProfileIDAsPresenceStateTeplateAndApplyToCurrentStateIfPresent($id);	
			// }
		}
		public function SetProfileAbsent(int $id){
			SetValue($this->GetIDForIdent("ProfileID3"), $id);
			
			if (GetValueBoolean($this->GetIDForIdent("PresenceDetected")) == false){
				$this->ApplyPresenceStateAfterProfileChange();
			} else {
				$this->RefreshPresence();
			}
			
			//$this->RefreshPresence();
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
		
		private function CheckTimerIntervals(){
			//$this->SetTimerInterval("PresenceTimeoutOff_Timer", $this->ReadPropertyInteger("PresenceTimeout") * 1000);
			//$this->SetTimerInterval("PresenceOffDelayScript_Timer", $this->ReadPropertyInteger("PresenceOffDelay") * 1000);
			
			if (GetValue($this->GetIDForIdent("PresenceDetected") == false)){
				$this->SetTimerInterval("ResetPresenceStateToTemplate_Timer", $this->ReadPropertyInteger("PresenceResetToTemplateTimeout") * 1000);	
			}else{
				$this->SetTimerInterval("UpdatePresence_Timer", $this->ReadPropertyInteger("PresenceRefreshTimeout") * 1000);
			}
		
			if (GetValueBoolean($this->GetIDForIdent("ManualPresence") == true)){
				$this->SetTimerInterval("ManualPresenceReset_Timer", $this->ReadPropertyInteger("ManualPresenceResetTimeout") * 1000);
			}
			
			if (GetValueBoolean($this->GetIDForIdent("AlertModeAktive") == true)){
				$this->SetTimerInterval("AlertTimeout_Timer",  $this->ReadPropertyInteger("AlertTimeout") * 1000);
			}
			
			if (GetValueBoolean($this->GetIDForIdent("EnablePresenceDetection") == false)){
				$this->SetTimerInterval("PresenceDetectionOffTimeout_Timer",  $this->ReadPropertyInteger("PresenceDetectionOffTimeout") * 1000);
			}		
		}
		
		private function TimerCallback(string $TimerID){
			$this->SetTimerInterval($TimerID, 0);
				
				switch($TimerID){
					case "UpdatePresence_Timer":
						$this->RefreshPresence();
						break;
						
					case "PresenceTimeoutOff_Timer":
						$this->PresenceTimeoutOff();
						break;
						
					case "PresenceOffDelayScript_Timer":
						$this->SetPresenceState(false, false);
						break;
						
					case "ResetPresenceStateToTemplate_Timer":
						$this->ResetPresenceStateToTemplat();
						break;
						
					case "ManualPresenceReset_Timer":
						$this->SetManualPresence(false);
						break;
						
					case "AlertTimeout_Timer":
						$this->SetAlertState(false);
						break;
						
					case "PresenceDetectionOffTimeout_Timer":
						$this->EnablePresenceDetection();
						break;
				}				
		}
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			if ($Message == 10603){
				$this->DeviceStatusChanged($SenderID);
			}
		}
		
		private function GetObjectValuePercent(int $TargetID){
			if (IPS_VariableExists($TargetID)){
				$variable = IPS_GetVariable($TargetID);										
				$t = $variable["VariableType"];
				$currentVal = GetValue($TargetID);				
				$profileName = $this->GetProfileName($variable);
				
				if ($t == 0){
					if ($currentVal == true){
						return 1.0;
					}else{
						return 0.0;
					}
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
				
				return (($currentVal - $MinValue) / ($MaxValue - $MinValue));
			}else{
				return 0.0;
			}
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
		
		private function SetObjectValue(int $TargetID, bool $value, int $valueInteger, float $valueFloat, bool $lowerOnly, bool $higherOnly){
			set_time_limit(30);

				if (IPS_VariableExists($TargetID)){
					$object = IPS_GetObject($TargetID);
					$variable = IPS_GetVariable($TargetID);
					$actionID = $this->GetProfileAction($variable);
										
					$t = $variable["VariableType"];
					$currentVal = GetValue($TargetID);
					
					if ($t == 0){
						if ($currentVal != $value){
						  if (($lowerOnly == true and $value < $currentVal) or ($higherOnly == true and $value > $currentVal) or ($higherOnly == false and $lowerOnly == false)){
							if(IPS_InstanceExists($actionID)){
								IPS_RequestAction($actionID, $object['ObjectIdent'], $value);
							} else if(IPS_ScriptExists($actionID)) {
								echo IPS_RunScriptWaitEx($actionID, Array("VARIABLE" => $TargetID, "VALUE" => $value));
							} else {
								SetValue($TargetID, $value);
							}		
						  }
						}
					}
					if ($t == 1){
						if ($currentVal != $valueInteger){
						if (($lowerOnly == true and $valueInteger < $currentVal) or ($higherOnly == true and $valueInteger > $currentVal) or ($higherOnly == false and $lowerOnly == false)){
							if(IPS_InstanceExists($actionID)){
								IPS_RequestAction($actionID, $object['ObjectIdent'], $valueInteger);
							} else if(IPS_ScriptExists($actionID)) {
								echo IPS_RunScriptWaitEx($actionID, Array("VARIABLE" => $TargetID, "VALUE" => $valueInteger));
							} else {
								SetValue($TargetID, $valueInteger);
							}
						}
						}
					}
					if ($t == 2){
						if ($currentVal != $valueFloat){
						if (($lowerOnly == true and $valueFloat < $currentVal ) or ($higherOnly == true and $valueFloat > $currentVal ) or ($higherOnly == false and $lowerOnly == false)){
							if(IPS_InstanceExists($actionID)){
								IPS_RequestAction($actionID, $object['ObjectIdent'], $valueFloat);
							} else if(IPS_ScriptExists($actionID)) {
								echo IPS_RunScriptWaitEx($actionID, Array("VARIABLE" => $TargetID, "VALUE" => $valueFloat));
							} else {
								SetValue($TargetID, $valueFloat);
							}
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
