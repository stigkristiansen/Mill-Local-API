<?php

declare(strict_types=1);

require_once(__DIR__ . "/../libs/autoload.php");

class Heater extends IPSModule {
	use Profile;

	public function Create() {
		//Never delete this line!
		parent::Create();

		$this->RegisterPropertyString(Properties::IPADDRESS, '');
		$this->RegisterPropertyString(Properties::CUSTOMNAME, '');
		$this->RegisterPropertyString(Properties::NAME, '');
		$this->RegisterPropertyBoolean(Properties::USESSL, False);

		$this->RegisterTimer(Timers::UPDATE . (string) $this->InstanceID, 0, 'IPS_RequestAction(' . (string)$this->InstanceID . ', "Update", 0);');

		$this->RegisterMessage(0, IPS_KERNELMESSAGE);
		
	}

	public function Destroy() {
		$module = json_decode(file_get_contents(__DIR__ . '/module.json'));
		if(count(IPS_GetInstanceListByModuleID($module->id))==0) {
			//$this->DeleteProfile(Profiles::CONTROL);
		}

		//Never delete this line!
		parent::Destroy();
	}

	public function ApplyChanges() {
		//Never delete this line!
		parent::ApplyChanges();

		if (IPS_GetKernelRunlevel() == KR_READY) {
            $this->SetTimers();
			
			$this->SetDeviceProperties();
        }
	}

	public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        if ($Message == IPS_KERNELMESSAGE && $Data[0] == KR_READY) {
			$this->SetDeviceProperties();
			
			$this->SetTimers();
		}
     }

	private function SetDeviceProperties() {
		try {
			$ipAddress = $this->ReadPropertyString(Properties::IPADDRESS);
			If(strlen($ipAddress)>0 && strlen($this->ReadPropertyString(Properties::NAME))==0) {
				$useSSL = $this->ReadPropertyBoolean(Properties::USESSL);

				$this->SendDebug(__FUNCTION__, 'Trying to retrive the device information...', 0);
				$this->SendDebug(__FUNCTION__, sprintf('The IP Address is %s', $ipAddress), 0);
				
				$device = new MillLocalAPI($ipAddress, $useSSL);
				$name = $device->Name();
				$customName = $device->CustomName();
				
				if(strlen($name)>0) {
					$this->SendDebug(__FUNCTION__, sprintf('Updating form...', $name), 0);
					
					IPS_SetProperty($this->InstanceID, Properties::NAME, $name);
					IPS_SetProperty($this->InstanceID, Properties::CUSTOMNAME, $customName);
					
					IPS_ApplyChanges($this->InstanceID);
				} else {
					$this->SendDebug(__FUNCTION__, sprintf('Failed to retrive device information!', $name), 0);
				}
			}
		} catch(Exception $e) {
			$msg = sprintf(Errors::UNEXPECTED, $e->getMessage());
			$this->LogMessage($msg, KL_ERROR);
			$this->SendDebug(__FUNCTION__, $msg, 0);
		} 
	}

	private function SetTimers() {
		$this->SetTimerInterval(Timers::UPDATE  . (string) $this->InstanceID, Timers::UPDATEINTERVAL);
	}

}