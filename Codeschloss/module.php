<?php
class Codeschloss extends IPSModule {
    public $PinCache = "";
        
    public function __construct($InstanceID) {
            // Diese Zeile nicht löschen
            parent::__construct($InstanceID);
 
            // Selbsterstellter Code
        }
		
    public function Create() {
            parent::Create();

			$this->RegisterVariableBoolean("Active", "Aktiv", "~Switch");
            $this->RegisterVariableInteger("FailureCount", "Fehlerzähler");
            $this->RegisterVariableString("Input", "Input");
            $this->RegisterVariableString("LastAction", "Letzte Aktion");
            $this->RegisterVariableString("CodeList", "Code-Liste");

            $this->EnableAction("Active");

            $this->RegisterTimer("AutoReset", 0, "SXCode_Reset(".$this->InstanceID.");");

            @$CategoryID = IPS_GetCategoryIDByName("Skript", $this->InstanceID);
            if ($CategoryID == false){
                $CategoryID = IPS_CreateCategory();
                IPS_SetName($CategoryID, "Skript");
                IPS_SetParent($CategoryID, $this->InstanceID);
            }

            //$this->UpdateEvents();
    }
    public function ApplyChanges() {
            parent::ApplyChanges();

			$this->SetStatus(102);
        }

    public function RequestAction($Ident, $Value) {
    		switch($Ident) {
                case "Active":
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;

        	    default:
	                throw new Exception("Invalid Ident");
    		}
 		}

    public function KeyPressed(string $Key){
        $this->SetTimerInterval ("AutoReset", 0);
        $EreignisID = IPS_GetEventIDByName ("AutoReset", $this->InstanceID);
        IPS_SetEventCyclicTimeFrom($EreignisID, date('H'), date('i'), date('s')); 
        $this->SetTimerInterval ("AutoReset", 10000);

        $PinCache = GetValue($this->GetIDForIdent("Input"));
        $PinCache = $PinCache.$Key;

        SetValue($this->GetIDForIdent("Input"), $PinCache);
    }

    public function Reset(){
        $this->SetTimerInterval ("AutoReset", 0);
        SetValue($this->GetIDForIdent("Input"), "");
    }

    public function SetActiveState(bool $Active){
        $PinCache = GetValue($this->GetIDForIdent("Input"));
        $Benutzer = "unbekannt";

        if ($this->IsPinValid($PinCache)){
            $Benutzer = $this->GetUserName($PinCache);
            SetValue($this->GetIDForIdent("Active"), $Active);
            SetValue($this->GetIDForIdent("LastAction"), $Benutzer." - SetActiveState(".$Active.")");
        }else{
            SetValue($this->GetIDForIdent("LastAction"), "unbekannt - SetActiveState(".$Active.") - falsches Kennwort");
        }
        SetValue($this->GetIDForIdent("Input"), "");
    }
    public function ExecuteSkript(int $SkriptID){
        $PinCache = GetValue($this->GetIDForIdent("Input"));
        $Benutzer = "unbekannt";

        if ($this->IsPinValid($PinCache)){
            $Benutzer = $this->GetUserName($PinCache);
            SetValue($this->GetIDForIdent("LastAction"), $Benutzer." - ExecuteSkript(".$SkriptID.")");
            IPS_RunScript($SkriptID);
        }else{
            SetValue($this->GetIDForIdent("LastAction"), "unbekannt - ExecuteSkript(".$SkriptID.") - falsches Kennwort");
        }
        SetValue($this->GetIDForIdent("Input"), "");
    }

    public function GetUserName(string $Code){
        $list = explode(";", GetValue($this->GetIDForIdent("CodeList")));
       
        foreach ($list as $value) {
            $user = explode(",", $value);
            if ($user[1] == $Code){
                return $user[0];   
            }
        }

        return "unbekannt";
    }
    public function IsPinValid(string $pin){
        $list = explode(";", GetValue($this->GetIDForIdent("CodeList")));
        $FailureCount = GetValue($this->GetIDForIdent("FailureCount"));


        foreach ($list as $value) {
            $user = explode(",", $value);
            if ($user[1] == $pin){
                SetValue($this->GetIDForIdent("FailureCount"), 0);
                return true;
            }
        }

        SetValue($this->GetIDForIdent("FailureCount"), $FailureCount + 1);
        return false;
    }
}
?>