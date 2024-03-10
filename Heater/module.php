<?php

declare(strict_types=1);

require_once(__DIR__ . "/../libs/autoload.php");

class Heater extends IPSModule {
	use Profile;
	Use Utils;

	public function Create() {
		//Never delete this line!
		parent::Create();

		$this->RegisterProfileIntegerEx(Profiles::OPMODE, Profiles::OPMODE_ICON, '', '', [
			[OperationMode::OFF_ID, OperationMode::OFF_TEXT,  '', -1],
			[OperationMode::WEEKLYPROGRAM_ID, OperationMode::WEEKLYPROGRAM_TEXT,  '', -1],
			[OperationMode::INDEPENDENTDEVICE_ID, OperationMode::INDEPENDENTDEVICE_TEXT,  '', -1],
			[OperationMode::CONTROLINDIVIDUALLY_ID, OperationMode::CONTROLINDIVIDUALLY_TEXT, '', -1]
		]);

		$this->RegisterPropertyString(Properties::IPADDRESS, '');
		$this->RegisterPropertyString(Properties::CUSTOMNAME, '');
		$this->RegisterPropertyString(Properties::NAME, '');
		$this->RegisterPropertyBoolean(Properties::USESSL, False);

		$this->RegisterVariableBoolean(Variables::POWER_IDENT, Variables::POWER_TEXT, '~Switch', 1);
		$this->EnableAction(Variables::POWER_IDENT);

		$this->RegisterVariableInteger(Variables::OPMODE_IDENT, Variables::OPMODE_TEXT, Profiles::OPMODE, 2);
		$this->EnableAction(Variables::OPMODE_IDENT);
		
		$this->RegisterTimer(Timers::UPDATE . (string) $this->InstanceID, 0, 'IPS_RequestAction(' . (string)$this->InstanceID . ', "Update", 0);');

		$this->RegisterMessage(0, IPS_KERNELMESSAGE);
		
	}

	public function Destroy() {
		$module = json_decode(file_get_contents(__DIR__ . '/module.json'));
		if(count(IPS_GetInstanceListByModuleID($module->id))==0) {
			$this->DeleteProfile(Profiles::OPMODE);
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

	public function RequestAction($Ident, $Value) {
		$msg = sprintf('RequestAction was called: %s:%s', (string)$Ident, (string)$Value);
		$this->SendDebug(__FUNCTION__, $msg, 0);
		
		try {
			switch ($Ident) {
				case Variables::POWER_IDENT:
					$this->Power($Value);
					return;
				case Variables::OPMODE_IDENT:
					$this->SetOperationMode($Value);
					return;
				case 'Update':
					$this->Update();
					return;
			}
		} catch(Exception $e) {
			$msg = sprintf(Errors::UNEXPECTED,  $e->getMessage());
			$this->LogMessage($msg, KL_ERROR);
			$this->SendDebug(__FUNCTION__, $msg, 0);
		}
	}

	private function MapOperationModeToInt(string $OperationMode) {
		switch (strtolower($OperationMode)) {
			case strtolower(EOperationMode::Off):
				return 1;
			case strtolower(EOperationMode::WeeklyProgram):
				return 2;
			case strtolower(EOperationMode::IndependentDevice):
				return 3;
			case strtolower(EOperationMode::ControlIndividually):
				return 4;
			default:
				return 0;
		}
	}

	private function MapOperationModeToString(int $OperationMode) {
		switch ($OperationMode) {
			case 1:
				return EOperationMode::Off;
			case 2:
				return EOperationMode::WeeklyProgram;
			case 3:
				return EOperationMode::IndependentDevice;
			case 4:
				return EOperationMode::ControlIndividually;
			default:
				return false;
		}
	}

	private function SetDeviceProperties() {
		$this->SendDebug(__FUNCTION__, 'Entering function..', 0);

		try {
			$ipAddress = $this->ReadPropertyString(Properties::IPADDRESS);
			If(strlen($ipAddress)>0) {
				$useSSL = $this->ReadPropertyBoolean(Properties::USESSL);

				$this->SendDebug(__FUNCTION__, 'Trying to retrive the device information...', 0);
				$this->SendDebug(__FUNCTION__, sprintf('The IP Address is %s', $ipAddress), 0);
				
				$device = new MillLocalAPI($ipAddress, $useSSL);
				
				$name = $device->Name();
						
				if(strlen($name)>0) {
					$this->SendDebug(__FUNCTION__, sprintf('Device name: %s', $name), 0);

					$customName = $device->CustomName();
					$this->SendDebug(__FUNCTION__, sprintf('Device Custom Name: %s', $customName), 0);

					$this->SendDebug(__FUNCTION__, sprintf('Updating form...', $name), 0);
					
					$orgName = $this->ReadPropertyString(Properties::NAME);
					$orgCustomName = $this->ReadPropertyString(Properties::CUSTOMNAME);

					$apply =false;
					if($name!=$orgName) {
						IPS_SetProperty($this->InstanceID, Properties::NAME, $name);
						$apply = true;
					}

					if($customName!=$orgCustomName) {
						IPS_SetProperty($this->InstanceID, Properties::CUSTOMNAME, $customName);
						$apply = true;
					}
						
					if($apply)
						IPS_ApplyChanges($this->InstanceID);

				} else {
					$this->SendDebug(__FUNCTION__, sprintf('Failed to retrive device information! %s', $name), 0);
				}
			
			}
		} catch(Exception $e) {
			$msg = sprintf(Errors::UNEXPECTED, $e->getMessage());
			$this->LogMessage($msg, KL_ERROR);
			$this->SendDebug(__FUNCTION__, $msg, 0);
		} 
	}

	private function UpdateVariables() {
		try {
			$ipAddress = $this->ReadPropertyString(Properties::IPADDRESS);
			If(strlen($ipAddress)>0) {
				$useSSL = $this->ReadPropertyBoolean(Properties::USESSL);

				$this->SendDebug(__FUNCTION__, 'Trying to retrive the device information...', 0);
				$this->SendDebug(__FUNCTION__, sprintf('The IP Address is %s', $ipAddress), 0);
				
				$device = new MillLocalAPI($ipAddress, $useSSL);
				
				$operationMode = self::MapOperationModeToInt($device->OperationMode());
						
				if($operationMode>0) {
					$this->SendDebug(__FUNCTION__, sprintf('Operation Mode: %s', $device->OperationMode()), 0);
					$this->SetValueEx(Variables::OPMODE_IDENT, $operationMode);
				} else {
					$this->SendDebug(__FUNCTION__, sprintf('Failed to retrive device information from %s', $ipAddress), 0);
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

	private function Power(bool $State) {
		if($State) {

		} else {
			$this->SetOperationMode(OperationMode::OFF_ID);
		}
	}

	private function SetOperationMode($Mode) {
		try {
			$ipAddress = $this->ReadPropertyString(Properties::IPADDRESS);
			If(strlen($ipAddress)>0) {
				$useSSL = $this->ReadPropertyBoolean(Properties::USESSL);

				$this->SendDebug(__FUNCTION__, 'Trying to set operation mode...', 0);
				$this->SendDebug(__FUNCTION__, sprintf('The IP Address is %s', $ipAddress), 0);
				
				$device = new MillLocalAPI($ipAddress, $useSSL);
				
				$operationMode = self::MapOperationModeToString($Mode);
						
				if($operationMode!==false) {
					$this->SendDebug(__FUNCTION__, sprintf('Selected Operation Mode: %s', $operationMode), 0);
					$device->SetOperationMode($operationMode);
					$this->SetValue(Variables::OPMODE_IDENT, $Mode);
				} else {
					$this->SendDebug(__FUNCTION__, sprintf('Failed to set operation mode for %s. The mode was %d', $ipAddress, $Mode), 0);
				}
			}
		} catch(Exception $e) {
			$msg = sprintf(Errors::UNEXPECTED, $e->getMessage());
			$this->LogMessage($msg, KL_ERROR);
			$this->SendDebug(__FUNCTION__, $msg, 0);
		} 

	}

	private function Update() {
		$this->SetDeviceProperties();
		$this->UpdateVariables();
	}

}