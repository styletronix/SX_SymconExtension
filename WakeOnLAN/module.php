<?php
class WakeOnLAN extends IPSModule {
        public function __construct($InstanceID) {
            // Diese Zeile nicht löschen
            parent::__construct($InstanceID);
 
            // Selbsterstellter Code
        }
		
        public function Create() {
            parent::Create();

            $this->RegisterPropertyString("IPAddress", "");
            $this->RegisterPropertyString("MACAddress", "");
            $this->RegisterPropertyString("BroadcastAddress", "");
            $this->RegisterPropertyInteger("TestInterval", "60");

			$this->RegisterVariableBoolean("IsAwake", "Erreichbar", "~Switch");
            $this->EnableAction("IsAwake");

            $this->RegisterTimer("TestIntervalTimer", 0, "SXwol_UpdateStatus(".$this->InstanceID.");");
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

            $this->SetTimerInterval("TestIntervalTimer", $this->ReadPropertyInteger("TestInterval") * 1000);

            //$mac = $this->ReadPropertyString("MACAddress");
            //$host = $this->ReadPropertyString("IPAddress");

            //if ($mac == ""){
            //    $mac = GetMacFromIP($host);
            //    if ($mac !== false){
                        
            //    }
            //}

			$this->SetStatus(102);
        }

		public function RequestAction($Ident, $Value) {
    		switch($Ident) {
                case "IsAwake":
                    if ($Value == true){
                        $this->WakeOnLan();
                    }else{
                        $this->Standby();
                    }
                    break;

        	default:
	            throw new Exception("Invalid Ident");

    		}
 		}

        public function UpdateStatus() {
            $host = $this->ReadPropertyString("IPAddress");
            $timeout = 1;

            /* ICMP ping packet with a pre-calculated checksum */
            $package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
            $socket  = socket_create(AF_INET, SOCK_RAW, 1);
            socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
            socket_connect($socket, $host, null);
            $ts = microtime(true);
            socket_send($socket, $package, strLen($package), 0);
            if (socket_read($socket, 255)) {
                $result = microtime(true) - $ts;
            } else {
                $result = false;
            }
            socket_close($socket);

            SetValue($this->GetIDForIdent("IsAwake"), $result);
        }

        public function WakeOnLan()
        {
            $macAddressHexadecimal = $this->ReadPropertyString("MACAddress");
            $broadcastAddress = $this->ReadPropertyString("BroadcastAddress");

            $macAddressHexadecimal = str_replace(':', '', $macAddressHexadecimal);
            $macAddressHexadecimal = str_replace('-', '', $macAddressHexadecimal);

            // check if $macAddress is a valid mac address
            if (!ctype_xdigit($macAddressHexadecimal)) {
                throw new \Exception('Mac address invalid, only 0-9 and a-f are allowed');
            }

            $macAddressBinary = pack('H12', $macAddressHexadecimal);

            $magicPacket = str_repeat(chr(0xff), 6).str_repeat($macAddressBinary, 16);

            if (!$fp = fsockopen('udp://' . $broadcastAddress, 7, $errno, $errstr, 2)) {
                throw new \Exception("Cannot open UDP socket: {$errstr}", $errno);
            }
            fputs($fp, $magicPacket);
            fclose($fp);
        }
        public function Shutdown(){
            $Host = $this->ReadPropertyString("IPAddress");
            $homepage = file_get_contents("http://".$Host.":498/command/shutdown");
        }
        public function Standby(){
            $Host = $this->ReadPropertyString("IPAddress");
            $homepage = file_get_contents("http://".$Host.":498/command/suspend");
        }
        
        public static function GetMacFromIP(string $ipAddress){
            $macAddr=false;

            #run the external command, break output into lines
            $arp="arp -a ".$ipAddress;
            $lines=explode("\n", $arp);

            #look for the output line describing our IP address
            foreach($lines as $line)
            {
                $cols=preg_split('/\s+/', trim($line));
                if ($cols[0]==$ipAddress)
                {
                    $macAddr=$cols[1];
                }
            }
            return $macAddr;
        }
    }
?>