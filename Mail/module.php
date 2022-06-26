<?php
    class Mailer extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }
		
        public function Create() {
            parent::Create();

			// $this->RegisterVariableBoolean("Ergebnis_Boolean", "Ergebnis Boolean", "~Switch");
            // $this->EnableAction("Ergebnis_Boolean");

            // $this->RegisterVariableFloat("Ergebnis_Float", "Ergebnis Float", "~Intensity.1");
            // $this->EnableAction("Ergebnis_Float");

            // $this->RegisterVariableInteger("Ergebnis_Integer", "Ergebnis Integer", "~Intensity.100");
            // $this->EnableAction("Ergebnis_Integer");

            $this->RegisterPropertyInteger("VariablesToSend", 0);
            
            if ($this->ReadPropertyInteger("VariablesToSend") == 0){
                @$CategoryID = IPS_GetCategoryIDByName("VariablesToSend", $this->InstanceID);
                if ($CategoryID == false){
                    $CategoryID = IPS_CreateCategory();
                    IPS_SetName($CategoryID, "VariablesToSend");
                    IPS_SetParent($CategoryID, $this->InstanceID);
                }

                IPS_SetProperty($this->InstanceID, "VariablesToSend", $CategoryID);
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
			$ScriptID = $this->RegisterScript("PrepareSendMail", "PrepareSendMail", "<?\n\nSXMAIL_PrepareSendMail(".$this->InstanceID."); \n\n?>");
			$VariablesToSendID = $this->ReadPropertyInteger("VariablesToSend");

			$foundIDs = array();

            $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

			foreach(IPS_GetChildrenIDs($VariablesToSend) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = $key2;
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
		}

       public function PrepareSendMail(){
		   $ScriptID = $this->RegisterScript("SendMail", "SendMail", "<?\n\nSXMAIL_SendMail(".$this->InstanceID."); \n\n?>");
		   IPS_SetScriptTimer($ScriptID, 10);
	   }
	   
	   public function SendMail(){
		   $VariablesToSendID = $this->ReadPropertyInteger("VariablesToSend");
		   
		   
	   }
	   

		public function SetAlertState(bool $Value){
			IPS_SemaphoreEnter("SXGRP_AlertStateChange", 120 * 1000);
			
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
			
			IPS_SemaphoreLeave("SXGRP_AlertStateChange");
		}
		public function GetCurrentStateString(){
			$arr =array();
			$CategoryID =  $this->ReadPropertyInteger("DeviceCategory");
            $ignoreIDs = IPS_GetChildrenIDs($this->InstanceID);

			foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = $key2;
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
				@set_time_limit(30);

				$itemObject = IPS_GetObject($key2);
				$TargetID = $key2;
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
        	// case "Ergebnis_Boolean":
                // $this->SetState($Value);
				// break;

        	default:
	            throw new Exception("Invalid Ident");

    		}
 		}
    }
?>