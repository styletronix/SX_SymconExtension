<?
class KeyMatic extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
 
        }
		
        public function Create() {
            parent::Create();

			$this->RegisterVariableBoolean("AutoLock", "Automatisch verriegeln", "~Switch");
			$this->RegisterVariableBoolean("DoorLocked", "Türe verriegelt", "~Switch");
			$this->RegisterVariableBoolean("DoorClosed", "Türe geschlossen", "~Switch");

			$this->RegisterPropertyInteger("AutoLockDelay", 60);
			$this->RegisterPropertyFloat("DoorOpenerTime", 1.0);
			$this->RegisterPropertyFloat("MaxOpenTime", 1.0);
			$this->RegisterPropertyBoolean("UnlockOnly", false);

			$this->RegisterTimer("AutoLockDelay", 0, "SXSecKey_LockDoor(".$this->InstanceID.");");
			$this->RegisterTimer("MaxOpenTimeDelay", 0, "SXSecKey_CloseDoor(".$this->InstanceID.");");

			@$CategoryID = IPS_GetCategoryIDByName("Sec-Key", $this->InstanceID);
			if ($CategoryID == false){
				$CategoryID = IPS_CreateCategory();
				IPS_SetName($CategoryID, "Sec-Key");
				IPS_SetParent($CategoryID, $this->InstanceID);
			}

			@$CategoryID = IPS_GetCategoryIDByName("TuerOeffner", $this->InstanceID);
			if ($CategoryID == false){
				$CategoryID = IPS_CreateCategory();
				IPS_SetName($CategoryID, "TuerOeffner");
				IPS_SetParent($CategoryID, $this->InstanceID);
			}

			@$CategoryID = IPS_GetCategoryIDByName("TuerKontakt", $this->InstanceID);
			if ($CategoryID == false){
				$CategoryID = IPS_CreateCategory();
				IPS_SetName($CategoryID, "TuerKontakt");
				IPS_SetParent($CategoryID, $this->InstanceID);
			}

			@$CategoryID = IPS_GetCategoryIDByName("Schluessel", $this->InstanceID);
			if ($CategoryID == false){
				$CategoryID = IPS_CreateCategory();
				IPS_SetName($CategoryID, "Schluessel");
				IPS_SetParent($CategoryID, $this->InstanceID);
			}

			@$CategoryID = IPS_GetCategoryIDByName("Schluessel Dauer auf", $this->InstanceID);
			if ($CategoryID == false){
				$CategoryID = IPS_CreateCategory();
				IPS_SetName($CategoryID, "Schluessel Dauer auf");
				IPS_SetParent($CategoryID, $this->InstanceID);
			}

			@$CategoryID = IPS_GetCategoryIDByName("Alarmzonen", $this->InstanceID);
			if ($CategoryID == false){
				$CategoryID = IPS_CreateCategory();
				IPS_SetName($CategoryID, "Alarmzonen");
				IPS_SetParent($CategoryID, $this->InstanceID);
			}


			@$EventID = IPS_GetEventIDByName("AutoLockSchedule", $this->InstanceID);
			if ($EventID == false){
				$EventID = IPS_CreateEvent(2);
				IPS_SetName($EventID, "AutoLockSchedule");
				IPS_SetParent($EventID, $this->InstanceID);
				IPS_SetEventScheduleAction($EventID, 0, "Automatisch verriegeln", 0x00CC00, "SXSecKey_SetAutoLockState(".$this->InstanceID.", true);");
				IPS_SetEventScheduleAction($EventID, 1, "Nicht verriegeln", 0xFF0000, "SXSecKey_SetAutoLockState(".$this->InstanceID.", false);");
				IPS_SetEventScheduleGroup($EventID, 0, 1);
				IPS_SetEventScheduleGroup($EventID, 1, 2);
				IPS_SetEventScheduleGroup($EventID, 2, 4);
				IPS_SetEventScheduleGroup($EventID, 3, 8);
				IPS_SetEventScheduleGroup($EventID, 4, 16);
				IPS_SetEventScheduleGroup($EventID, 5, 32);
				IPS_SetEventScheduleGroup($EventID, 6, 64);
				IPS_SetEventActive($EventID, false);
			}
			

			$this->UpdateEvents();

			$this->EnableAction("AutoLock");
        }

        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->UpdateEvents();
			$this->SetStatus(102);
        }

		public function UpdateEvents(){
			$foundIDs = array();

            //$this->RegisterTimer("AutoLockDelay", 0, "SXSecKey_LockDoor(".$this->InstanceID.");");

			$SecKeyID = IPS_GetCategoryIDByName("Sec-Key", $this->InstanceID);
			$TuerOeffnerID = IPS_GetCategoryIDByName("TuerOeffner", $this->InstanceID);
			$SecKeyScript1ID = $this->RegisterScript("UpdateStatus", "UpdateStatus", "<?\n\nSXSecKey_UpdateStatus(".$this->InstanceID."); \n\n?>");		
			foreach(IPS_GetChildrenIDs($SecKeyID) as $LinkID){
				$itemObject = IPS_GetObject($LinkID);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($LinkID)["TargetID"];
				}else{
					$TargetID = $LinkID;
				}
				$TargetID = IPS_GetObjectIDByIdent("STATE", $TargetID);
				$EventName = "TargetID ".$TargetID;
				$foundIDs[] = $EventName;

				@$EventID = IPS_GetEventIDByName($EventName, $SecKeyScript1ID);
				if ($EventID == false){
					$EventID = IPS_CreateEvent(0);
					IPS_SetEventTrigger($EventID, 0, $TargetID);
					IPS_SetName($EventID, $EventName);
					IPS_SetParent($EventID, $SecKeyScript1ID);
					IPS_SetEventActive($EventID, true);
				}
			}



			$SchluesselID = IPS_GetCategoryIDByName("Schluessel", $this->InstanceID);
			$TuerOeffnerScriptID = $this->RegisterScript("OpenDoor", "OpenDoor", "<?\n\nSXSecKey_OpenDoor(".$this->InstanceID.", false); \n\n?>");
			foreach(IPS_GetChildrenIDs($SchluesselID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($key2)["TargetID"];
				}

				if ($TargetID > 0){
					$EventName = "TargetID ".$TargetID;
					$foundIDs[] = $EventName;

					@$EventID = IPS_GetEventIDByName($EventName, $TuerOeffnerScriptID);
					if ($EventID == false){
						$EventID = IPS_CreateEvent(0);
						IPS_SetEventTrigger($EventID, 0, $TargetID);
						IPS_SetName($EventID, $EventName);
						IPS_SetParent($EventID, $TuerOeffnerScriptID);
						IPS_SetEventActive($EventID, true);
					}
				}
			}
			foreach(IPS_GetChildrenIDs($TuerOeffnerScriptID) as $key2) {
				$EventName = IPS_GetName($key2);
				if (!in_array ($EventName, $foundIDs)){
					IPS_DeleteEvent($key2);
				}
			}



			$SchluesselID = IPS_GetCategoryIDByName("Schluessel Dauer auf", $this->InstanceID);
			$TuerOeffnerScriptID = $this->RegisterScript("OpenDoorMaxTime", "OpenDoorMaxTime", "<?\n\nSXSecKey_OpenDoor(".$this->InstanceID.", true); \n\n?>");
			foreach(IPS_GetChildrenIDs($SchluesselID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($key2)["TargetID"];
				}

				if ($TargetID > 0){
					$EventName = "TargetID ".$TargetID;
					$foundIDs[] = $EventName;

					@$EventID = IPS_GetEventIDByName($EventName, $TuerOeffnerScriptID);
					if ($EventID == false){
						$EventID = IPS_CreateEvent(0);
						IPS_SetEventTrigger($EventID, 0, $TargetID);
						IPS_SetName($EventID, $EventName);
						IPS_SetParent($EventID, $TuerOeffnerScriptID);
						IPS_SetEventActive($EventID, true);
					}
				}
			}
			foreach(IPS_GetChildrenIDs($TuerOeffnerScriptID) as $key2) {
				$EventName = IPS_GetName($key2);
				if (!in_array ($EventName, $foundIDs)){
					IPS_DeleteEvent($key2);
				}
			}





			// $AlarmzonenID = IPS_GetCategoryIDByName("Alarmzonen", $this->InstanceID);




			$TuerKontaktID = IPS_GetCategoryIDByName("TuerKontakt", $this->InstanceID);
			$TuerKontaktScriptID = $this->RegisterScript("SetDoorClosed", "SetDoorClosed", "<?\n\nSXSecKey_DoorClosed(".$this->InstanceID."); \n\n?>");
			
			foreach(IPS_GetChildrenIDs($TuerKontaktID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($key2)["TargetID"];
				}

				if ($TargetID > 0){
					$EventName = "TargetID ".$TargetID;
					$foundIDs[] = $EventName;

					@$EventID = IPS_GetEventIDByName($EventName, $TuerKontaktScriptID);
					if ($EventID == false){
						$EventID = IPS_CreateEvent(0);
						IPS_SetEventTrigger($EventID, 4, $TargetID);
						IPS_SetEventTriggerValue($EventID, true);
						IPS_SetName($EventID, $EventName);
						IPS_SetParent($EventID, $TuerKontaktScriptID);
						IPS_SetEventActive($EventID, true);
					}
				}
			}

			foreach(IPS_GetChildrenIDs($TuerKontaktScriptID) as $key2) {
				$EventName = IPS_GetName($key2);
				if (!in_array ($EventName, $foundIDs)){
					IPS_DeleteEvent($key2);
				}
			}


			foreach(IPS_GetChildrenIDs($TuerKontaktID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($key2)["TargetID"];
				}

				if ($TargetID > 0){
					$EventName = "TargetID ".$TargetID;
					$foundIDs[] = $EventName;

					@$EventID = IPS_GetEventIDByName($EventName, $SecKeyScript1ID);
					if ($EventID == false){
						$EventID = IPS_CreateEvent(0);
						IPS_SetEventTrigger($EventID, 0, $TargetID);
						IPS_SetName($EventID, $EventName);
						IPS_SetParent($EventID, $SecKeyScript1ID);
						IPS_SetEventActive($EventID, true);
					}
				}
			}

			foreach(IPS_GetChildrenIDs($SecKeyScript1ID) as $key2) {
				$EventName = IPS_GetName($key2);
				if (!in_array ($EventName, $foundIDs)){
					IPS_DeleteEvent($key2);
				}
			}


			$this->UpdateStatus();
		}
	       
		public function UpdateStatus(){
			SetValue($this->GetIDForIdent("DoorClosed"), $this->isDoorClosed());
			SetValue($this->GetIDForIdent("DoorLocked"), $this->isDoorLocked());
		}

		public function DoorClosed(){
			SetValue($this->GetIDForIdent("DoorClosed"), $this->isDoorClosed());

			$AutoLock = GetValue($this->GetIDForIdent("AutoLock"));
			if ($AutoLock == true){
                $EreignisID = IPS_GetEventIDByName ("AutoLockDelay", $this->InstanceID);
                IPS_SetEventCyclicTimeFrom($EreignisID, date('H'), date('i'), date('s'));
				
                $AutoLockDelay = $this->ReadPropertyInteger ("AutoLockDelay") * 1000;
				$this->SetTimerInterval ("AutoLockDelay", $AutoLockDelay);
			}
		}

		public function SetAutoLockState(boolean $value){
			SetValue($this->GetIDForIdent("AutoLock"), $value);
			if ($value == true){
				$this->LockDoor();
			}
		}

		public function LockDoor(){
			$this->CloseDoor();
			$this->SetTimerInterval ("AutoLockDelay", 0);

			$AutoLock = GetValue($this->GetIDForIdent("AutoLock"));

			if ($this->isDoorClosed() == false){
                $EreignisID = IPS_GetEventIDByName ("AutoLockDelay", $this->InstanceID);
                IPS_SetEventCyclicTimeFrom($EreignisID, date('H'), date('i'), date('s'));
				$this->SetTimerInterval ("AutoLockDelay", 5000);
				return;
			}

			$SecKeyID = IPS_GetCategoryIDByName("Sec-Key", $this->InstanceID);
			foreach(IPS_GetChildrenIDs($SecKeyID) as $LinkID){
				set_time_limit(30);
				$itemObject = IPS_GetObject($LinkID);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($LinkID)["TargetID"];
				}else{
					$TargetID = $LinkID;
				}

				//Abschliesen
				if (HM_WriteValueBoolean($TargetID, "STATE", false) !== true){
                    $EreignisID = IPS_GetEventIDByName ("AutoLockDelay", $this->InstanceID);
                    IPS_SetEventCyclicTimeFrom($EreignisID, date('H'), date('i'), date('s'));
					$this->SetTimerInterval ("AutoLockDelay", 5000);
				}
			}
		}
		
		public function OpenDoor(boolean $keepopen){
			set_time_limit(30);
			//Sicherstellen, dass Türe nicht per AutoTimer geschlossen wird
			$this->SetTimerInterval("AutoLockDelay", 0);
			$this->SetTimerInterval("MaxOpenTimeDelay", 0);

			$AutoLockDelay = $this->ReadPropertyInteger ("AutoLockDelay");
			$OpenTimer = $this->ReadPropertyFloat ("DoorOpenerTime");
			$MaxOpenTime = $this->ReadPropertyFloat ("MaxOpenTime");
			$UnlockOnly = $this->ReadPropertyBoolean ("UnlockOnly");

			if ($keepopen){
				$OpenTimer = $MaxOpenTime;
			}

			//Alarm deaktivieren
			$AlarmzonenID = IPS_GetCategoryIDByName("Alarmzonen", $this->InstanceID);
			foreach(IPS_GetChildrenIDs($AlarmzonenID) as $LinkID){
				set_time_limit(30);
				$itemObject = IPS_GetObject($LinkID);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($LinkID)["TargetID"];
				}else{
					$TargetID = $LinkID;
				}

				SetValue($TargetID, false);
			}

			//Türe aufschließen
			$SecKeyID = IPS_GetCategoryIDByName("Sec-Key", $this->InstanceID);
			foreach(IPS_GetChildrenIDs($SecKeyID) as $LinkID){
				set_time_limit(30);
				$itemObject = IPS_GetObject($LinkID);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($LinkID)["TargetID"];
				}else{
					$TargetID = $LinkID;
				}

				//Prüfe ob Türe gerade abgeschlossen wird
				HM_RequestStatus($TargetID, "DIRECTION");
				$KeyLockDIRECTIONID = IPS_GetObjectIDByIdent("DIRECTION", $TargetID);
				$count = 0;
				while ($count <= 20){
					set_time_limit(30);
					if (GetValueInteger($KeyLockDIRECTIONID) == 0){break;}
					$count++;
					IPS_Sleep(500);
				}

				//Aufschliesen
				if ($UnlockOnly == true){
					HM_WriteValueBoolean($TargetID, "STATE", true);
				}else{
					HM_WriteValueBoolean($TargetID, "OPEN", true);
				}

				//Sicherstellen, dass Türe nicht per AutoTimer geschlossen wird
				$this->SetTimerInterval("AutoLockDelay", 0);
				$this->SetTimerInterval("MaxOpenTimeDelay", 0);

				//Warte max 10 sekunden bis Türe aufgeschlossen ist.
				HM_RequestStatus($TargetID, "STATE");
				$KeyLockStateID = IPS_GetObjectIDByIdent("STATE", $TargetID);
				$count = 0;
				while ($count <= 20){
					set_time_limit(30);
					if (GetValueBoolean($KeyLockStateID) == true){break;}
					$count++;
					IPS_Sleep(500);
				}
			}
			
			//Sicherstellen, dass Türe nicht per AutoTimer geschlossen wird
			$this->SetTimerInterval("AutoLockDelay", 0);
			$this->SetTimerInterval("MaxOpenTimeDelay", 0);

			//Türöffner betätigen ()
			$TuerOeffnerID = IPS_GetCategoryIDByName("TuerOeffner", $this->InstanceID);
			foreach(IPS_GetChildrenIDs($TuerOeffnerID) as $LinkID) {
				set_time_limit(30);

				$itemObject = IPS_GetObject($LinkID);
				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($LinkID)["TargetID"];
				}else{
					$TargetID = $LinkID;
				}

				$pID = IPS_GetParent($TargetID);
				$inst = IPS_GetInstance($pID);
				$istHM = ($inst["ModuleInfo"]["ModuleID"] == "{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}");
				
				$var = IPS_GetVariable ($TargetID);
				if ($istHM){
					if (HM_WriteValueFloat($pID, "ON_TIME", $OpenTimer) == true){
						HM_WriteValueBoolean($pID, IPS_GetName($TargetID), true);
					}
                    $EreignisID = IPS_GetEventIDByName ("MaxOpenTimeDelay", $this->InstanceID);
                    IPS_SetEventCyclicTimeFrom($EreignisID, date('H'), date('i'), date('s'));
					$this->SetTimerInterval("MaxOpenTimeDelay", $OpenTimer * 1000);
				}else{
					SetValueBoolean($TargetID, true);
					if ($OpenTimer > 0){
                        $EreignisID = IPS_GetEventIDByName ("MaxOpenTimeDelay", $this->InstanceID);
                        IPS_SetEventCyclicTimeFrom($EreignisID, date('H'), date('i'), date('s'));
						$this->SetTimerInterval("MaxOpenTimeDelay", $OpenTimer * 1000);
					}
				}
			}
		}

		public function CloseDoor(){
			$this->SetTimerInterval("MaxOpenTimeDelay", 0);

			//Türöffner betätigen ()
			$TuerOeffnerID = IPS_GetCategoryIDByName("TuerOeffner", $this->InstanceID);
			foreach(IPS_GetChildrenIDs($TuerOeffnerID) as $LinkID) {
				set_time_limit(30);

				$itemObject = IPS_GetObject($LinkID);
				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($LinkID)["TargetID"];
				}else{
					$TargetID = $LinkID;
				}

				$pID = IPS_GetParent($TargetID);
				$inst = IPS_GetInstance($pID);
				$istHM = ($inst["ModuleInfo"]["ModuleID"] == "{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}");
				
				$var = IPS_GetVariable ($TargetID);
				if ($istHM){
					HM_WriteValueBoolean($pID, IPS_GetName($TargetID), false);
				}else{
					SetValueBoolean($TargetID, false);
				}
			}
		}

		public function isDoorClosed(){
			$TuerKontaktID = IPS_GetCategoryIDByName("TuerKontakt", $this->InstanceID);
			foreach(IPS_GetChildrenIDs($TuerKontaktID) as $LinkID){
				$itemObject = IPS_GetObject($LinkID);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($LinkID)["TargetID"];
				}else{
					$TargetID = $LinkID;
				}

				return !GetValue($TargetID);
			}
			return true;
		}

		public function isDoorLocked(){
			$SecKeyID = IPS_GetCategoryIDByName("Sec-Key", $this->InstanceID);
			foreach(IPS_GetChildrenIDs($SecKeyID) as $LinkID){
				$itemObject = IPS_GetObject($LinkID);

				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($LinkID)["TargetID"];
				}else{
					$TargetID = $LinkID;
				}

				return !GetValue(IPS_GetObjectIDByIdent("STATE", $TargetID));
			}
			return false;
		}
		
		public function RequestAction($Ident, $Value) {
    		switch($Ident) {
			case "AutoLock":
				SetAutoLockState($Value);
				break;
				
        	default:
	            throw new Exception("Invalid Ident");

    		}
 		}

    }
?>