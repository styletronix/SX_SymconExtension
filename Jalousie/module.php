<?php
class JalousieControl extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();
            
            $this->UpdateVariableProfiles();
            
				$this->RegisterVariableFloat("LEVEL", "Höhe", "~Intensity.1");
				$this->EnableAction("LEVEL");
				
				$this->RegisterVariableFloat("POSITION", "Öffnung", "~Intensity.1");
				$this->EnableAction("POSITION");
				
				$id = $this->RegisterVariableInteger("StorageID", "Speicherplatz", "Speicherplatz.SX");
				SetValue($id, -1);
				$this->EnableAction("StorageID");
				
				$this->RegisterPropertyFloat("PositionDiff", 4.00);
				$this->RegisterPropertyInteger("RolladenLevelID", false);
				$this->RegisterPropertyInteger("ScriptTimeOut", 120);
				$this->RegisterPropertyBoolean("POSITION_umkehr", false);

				//$this->UpdateEvents();
        }
        
        public function ApplyChanges() {
            parent::ApplyChanges();

				$this->UpdateEvents();
				$this->SetStatus(102);
        }
        
        private function UpdateVariableProfiles(){
			if (!IPS_VariableProfileExists("Speicherplatz.SX")){
				IPS_CreateVariableProfile("Speicherplatz.SX", 1);
				IPS_SetVariableProfileValues("Speicherplatz.SX", -1, 10, 1);
				IPS_SetVariableProfileAssociation("Speicherplatz.SX", -1, "kein Speicherpl.", "", 16737330);
				IPS_SetVariableProfileAssociation("Speicherplatz.SX", 0, "%d", "", 16777215);
			}

		  }
        
        public function StorePositionToCurrentStorageID(){
         $storageID = GetValue($this->GetIDForIdent("StorageID"));
         if ($storageID < 0){
				throw new Exception('Positions ID kann nicht verwendet werden.');
				return false;
			}
         
         $this->StorePosition($storageID);
         return true;
        }
        
			public function StorePosition(int $StorageID){
			   $Umkehr = $this->ReadPropertyBoolean("POSITION_umkehr");
			   $PositionDiff = $this->ReadPropertyFloat("PositionDiff") / 100;
			   
				$LevelName = "Storage".$StorageID."_LEVEL";
				$PositionName = "Storage".$StorageID."_POSITION";
				
				$data = $this->ReadSettings();

				$data[$LevelName] = GetValue($this->GetIDForIdent("LEVEL"));
         	$data[$PositionName] = GetValue($this->GetIDForIdent("POSITION"));
         	
         	$LevelDiff = $data[$PositionName] * $PositionDiff;
         	if ($Umkehr == true){
         	   $data[$LevelName] = $data[$LevelName] + $LevelDiff;
				}else{
               $data[$LevelName] = $data[$LevelName] - $LevelDiff;
				}
         	
         	$this->WriteSettings($data);
         	SetValue($this->GetIDForIdent("StorageID"), $StorageID);
         	return true;
			}
			
			public function RecallPosition(int $StorageID){
				$LevelName = "Storage".$StorageID."_LEVEL";
				$PositionName = "Storage".$StorageID."_POSITION";

				$data = $this->ReadSettings();
				
				SetValue($this->GetIDForIdent("StorageID"), $StorageID);
				
				if (array_key_exists($LevelName,$data) and array_key_exists($PositionName,$data)){
					$Level = $data[$LevelName];
					$Position = $data[$PositionName];

					$this->GoToPosition($Level, $Position);

					return true;
				}else{

					return false;
				}
			}
			
        public function UpdateEvents(){
         $this->RegisterScript("StorePosition", "Position speichern","<?\n SXjc_StorePositionToCurrentStorageID(".$this->InstanceID."); \n?>");
        
         $RolladenLevelID = $this->ReadPropertyInteger("RolladenLevelID");
         if ($RolladenLevelID > 0){
         	$EventName = "TargetID ".$RolladenLevelID;
				@$EventID = IPS_GetEventIDByName($EventName, $this->InstanceID);
				if ($EventID === false){
					$EventID = IPS_CreateEvent(0);
					IPS_SetEventTrigger($EventID, 1, $RolladenLevelID);
					IPS_SetName($EventID, $EventName);
					IPS_SetParent($EventID, $this->InstanceID);
					IPS_SetEventScript($EventID, "SXjc_UpdateCurrentStatus(".$this->InstanceID.");");
					IPS_SetHidden($EventID, true);
					IPS_SetEventActive($EventID, true);
				}
			}
        }
        
        public function UpdateCurrentStatus(){
			//Liest den aktuellen Status der Jalousie und berechnet die Richtung,
			//Höhe und Winkel der Jalousie. Der Status wird in der Einstellungs-Datei gespeichert.
			
         $Umkehr = $this->ReadPropertyBoolean("POSITION_umkehr");
			$PositionDiff = $this->ReadPropertyFloat("PositionDiff") / 100;

			$RolladenLevelID = $this->ReadPropertyInteger("RolladenLevelID");
			$Level = GetValue($RolladenLevelID);

			$data = $this->ReadSettings();
			$LastLevel = $data["LEVEL"];
         $LastPosition = $data["POSITION"];
         $LastDirection = $contents["DIRECTION"];
         
         if ($Umkehr == true){
            $LevelDiff = $LastLevel - $Level;
         }else{
         	$LevelDiff = $Level - $LastLevel;
			}
			//LevelDiff in Prozentwert umrechnen
			$LevelDiff = $LevelDiff / $PositionDiff;
         
         if ($Level > $LastLevel){$contents["DIRECTION"] = "UP";}
			if ($Level < $LastLevel){$contents["DIRECTION"] = "DOWN";}
			
         $data["POSITION"] = $LastPosition + $LevelDiff;
         if ($data["POSITION"] < 0){$data["POSITION"] = 0;}
			if ($data["POSITION"] > 1.0){$data["POSITION"] = 1.0;}

         $data["LEVEL"] = $Level;

         $this->WriteSettings($data);
         
         SetValue($this->GetIDForIdent("POSITION"), $data["POSITION"]);
         SetValue($this->GetIDForIdent("LEVEL"), $data["LEVEL"]);
         
         $this->ExecutePositioning();
        }

		public function RequestAction($Ident, $Value) {
    		switch($Ident) {
				case "LEVEL":
			      $this->GoToNewLevel($Value);
					break;
					
				case "POSITION":
			      $this->GoToNewPosition($Value);
					break;
					
				case "StorageID":
					$this->RecallPosition($Value);
					break;
				
        		default:
	         	throw new Exception("Invalid Ident");

    		}
 		}
 		
 		private function ExecutePositioning(){
			// Daten lesen
 		   $data = $this->ReadSettings();

			 //Sonstige Paramter lesen
 			$Umkehr = $this->ReadPropertyBoolean("POSITION_umkehr");
 			$PositionDiff = $this->ReadPropertyFloat("PositionDiff") / 100;
 			$RolladenLevelID = $this->ReadPropertyInteger("RolladenLevelID");
 			
 		   //Zielposition lesen
			$NewLevel = $data["TargetLEVEL"];
			if ($NewLevel > 1.0){$NewLevel = 1.0;};
			if ($NewLevel < 0.0){$NewLevel = 0.0;};
			
 		   $NewPosition = $data["TargetPOSITION"];
			if ($NewPosition > 1.0){$NewPosition = 1.0;};
			if ($NewPosition < 0.0){$NewPosition = 0.0;};

   		//Aktuelle Position lesen
         $Level = $data["LEVEL"];

			//Mögliche Positionsabweichung wegen Winkel der Jalousie berechnen
			$Level1 = $Level + ($PositionDiff);
			$Level2 = $Level - ($PositionDiff);

         if ($NewLevel > $Level1 or $NewLevel < $Level2){
            // Fahre auf neue Position
            if ($data["LastTargetLEVEL"] <> $data["TargetLEVEL"]){
					if (@IPS_RequestAction(IPS_GetParent($RolladenLevelID), IPS_GetName($RolladenLevelID), $NewLevel) == false){
               	SetValue($RolladenLevelID, $NewLevel);
            	}
            	$data["LastTargetLEVEL"] = $data["TargetLEVEL"];
            	$data["LastTargetPOSITION"] = -1;
            	$this->WriteSettings($data);
            }
			}else{
			   // Stelle Winkel der Jalousie ein
			   
			  if ($data["LastTargetPOSITION"] <> $data["TargetPOSITION"]){
			   // Warte 2 Sekunden
			   IPS_Sleep(2000);
			   
			   // Aktuelle Position der Jalousie auslesen
			   $Position = GetValue($this->GetIDForIdent("POSITION"));
			   $Level = GetValue($this->GetIDForIdent("LEVEL"));
			   
         	//Drehrichtung anpassen und neue Position berechnen
         	if ($Umkehr){
               $PositionDifference = $Position - $NewPosition ;
            }else{
					$PositionDifference = $NewPosition - $Position;
				}
				
            $PositionDifference = $PositionDifference * $PositionDiff;
            $NewLevel = $Level + $PositionDifference;

            if ($NewLevel > 1.0){$NewLevel = 1.0;};
				if ($NewLevel < 0.0){$NewLevel = 0.0;};

				//Neue Position anfahren
				if (@IPS_RequestAction(IPS_GetParent($RolladenLevelID), IPS_GetName($RolladenLevelID), $NewLevel) == false){
               SetValue($RolladenLevelID, $NewLevel);
            }
            $data["LastTargetPOSITION"] = $data["TargetPOSITION"];
           	$this->WriteSettings($data);
			  }
			}
 		}
 		
		public function GoToPosition(float $NewLevel, float $NewPosition){
		   $data = $this->ReadSettings();

		   $data["TargetPOSITION"] = $NewPosition;
			$data["TargetLEVEL"] = $NewLevel;
			$data["LastTargetLEVEL"] = -1;
			$data["LastTargetPOSITION"] = -1;
			
			$this->WriteSettings($data);
			$this->ExecutePositioning();
		}
		public function GoToNewLevel(float $NewLevel){
         $data = $this->ReadSettings();
         
			$data["TargetLEVEL"] = $NewLevel;
			$data["LastTargetLEVEL"] = -1;
			
			$this->WriteSettings($data);
			$this->ExecutePositioning();
		}
		public function GoToNewPosition(float $NewPosition){
         $data = $this->ReadSettings();
         
		   $data["TargetPOSITION"] = $NewPosition;
			$data["LastTargetPOSITION"] = -1;
			
			$this->WriteSettings($data);
			$this->ExecutePositioning();
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
                $data = json_decode($contents,true);
            }else{
                $data = array();
            }
            
            if (!array_key_exists("LEVEL", $data)){$data["LEVEL"] = 1.0;};
            if (!array_key_exists("DIRECTION", $data)){$data["DIRECTION"] = "DOWN";};
            if (!array_key_exists("POSITION", $data)){$data["POSITION"] = 1.0;};
            if (!array_key_exists("TargetPOSITION", $data)){$data["TargetPOSITION"] = 1.0;};
            if (!array_key_exists("TargetLEVEL", $data)){$data["TargetLEVEL"] = 1.0;};
            if (!array_key_exists("LastTargetLEVEL", $data)){$data["LastTargetLEVEL"] = 1.0;};
            if (!array_key_exists("LastTargetPOSITION", $data)){$data["LastTargetPOSITION"] = 1.0;};
            
         	return $data;
   }
}
?>