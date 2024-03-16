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
			//[OperationMode::OFF_ID, OperationMode::OFF_TEXT,  '', -1],
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

		$this->RegisterVariableFloat(Variables::TEMP_IDENT, Variables::TEMP_TEXT, '~Temperature', 3);

		$this->RegisterVariableFloat(Variables::SETPOINT_IDENT, Variables::SETPOINT_TEXT, '~Temperature.Room', 4);
		$this->EnableAction(Variables::SETPOINT_IDENT);

		$this->RegisterVariableFloat(Variables::HUMIDITY_IDENT, Variables::HUMIDITY_TEXT, '~Humidity.F', 5);
		
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
			$this->Update();
        }
	}

	public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        if ($Message == IPS_KERNELMESSAGE && $Data[0] == KR_READY) {
			$this->SetTimers();
			$this->Update();
		}
     }

	public function RequestAction($Ident, $Value) {
		$msg = sprintf(Debug::REQUESTACTIONCALLED, (string)$Ident, (string)$Value);
		$this->SendDebug(__FUNCTION__, $msg, 0);
		
		try {
			switch ($Ident) {
				case Variables::POWER_IDENT:
					$this->Power($Value);
					return;
				case Variables::OPMODE_IDENT:
					$this->SetOperationMode($Value);
					return;
				case Variables::SETPOINT_IDENT:
					$this->SetSetpoint($Value);
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

	private function SetDeviceProperties() {
		$this->SendDebug(__FUNCTION__, Debug::ENTERINGFUNCTION, 0);

		try {
			$ipAddress = $this->ReadPropertyString(Properties::IPADDRESS);
			If(strlen($ipAddress)>0) {
				$useSSL = $this->ReadPropertyBoolean(Properties::USESSL);

				$this->SendDebug(__FUNCTION__, Debug::RETRIVEINFO, 0);
				$this->SendDebug(__FUNCTION__, sprintf(Debug::IPADDRESS, $ipAddress), 0);
				
				$device = new MillLocalAPI($ipAddress, $useSSL);
				
				$name = $device->Name;
						
				if(strlen($name)>0) {
					$this->SendDebug(__FUNCTION__, sprintf(Debug::DEVICENAME, $name), 0);

					$customName = $device->CustomName;
					$this->SendDebug(__FUNCTION__, sprintf(Debug::DEVICECUSTOMNAME, $customName), 0);

					$this->SendDebug(__FUNCTION__, sprintf(Debug::UPDATINGFORM, $name), 0);
					
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
					$this->SendDebug(__FUNCTION__, sprintf(Debug::DEVICEINFOFAILED, $name), 0);
				}
			
			}
		} catch(Exception $e) {
			$msg = sprintf(Errors::UNEXPECTED, $e->getMessage());
			$this->LogMessage($msg, KL_ERROR);
			$this->SendDebug(__FUNCTION__, $msg, 0);
		} 
	}

	private function UpdateVariables() {
		$this->SendDebug(__FUNCTION__, Debug::ENTERINGFUNCTION, 0);

		try {
			$ipAddress = $this->ReadPropertyString(Properties::IPADDRESS);
			If(strlen($ipAddress)>0) {
				$this->SendDebug(__FUNCTION__, Debug::RETRIVEINFO, 0);
				$this->SendDebug(__FUNCTION__, sprintf(Debug::IPADDRESS, $ipAddress), 0);
				
				$useSSL = $this->ReadPropertyBoolean(Properties::USESSL);
				$device = new MillLocalAPI($ipAddress, $useSSL);
				
				$operationMode = MillLocalAPI::MapOperationModeToInt($device->OperationMode);
						
				if($operationMode>0) {
					$this->SendDebug(__FUNCTION__, sprintf(Debug::OPERATIONMODE, $device->OperationMode, $operationMode), 0);
					
					if($operationMode==OperationMode::OFF_ID) {
						$this->SetValueEx(Variables::POWER_IDENT, false);
						$this->DisableAction(Variables::OPMODE_IDENT);
						$this->DisableAction(Variables::SETPOINT_IDENT);
					} else {
						$this->SetValueEx(Variables::OPMODE_IDENT, $operationMode);
						$this->SetValueEx(Variables::POWER_IDENT, true);
						$this->EnableAction(Variables::OPMODE_IDENT);
						if($operationMode!=OperationMode::WEEKLYPROGRAM_ID) {
							$this->EnableAction(Variables::SETPOINT_IDENT);
						}
					}
				} else {
					$this->SendDebug(__FUNCTION__, sprintf(Debug::DEVICEINFOFAILED, $name), 0);
					return;
				}

				$this->SendDebug(__FUNCTION__, sprintf(Debug::TEMPERATURE, $device->Temperature), 0);
				$this->SetValueEx(Variables::TEMP_IDENT, $device->Temperature);

				$this->SendDebug(__FUNCTION__, sprintf(Debug::SETPOINT, $device->Setpoint), 0);
				$this->SetValueEx(Variables::SETPOINT_IDENT, $device->Setpoint);
				
				$this->SendDebug(__FUNCTION__, sprintf(Debug::HUMIDITY, $device->Humidity), 0);
				$this->SetValueEx(Variables::HUMIDITY_IDENT, $device->Humidity);
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

	public function Power(bool $State) {
		$this->SendDebug(__FUNCTION__, Debug::ENTERINGFUNCTION, 0);

		$this->SetValueEx(Variables::POWER_IDENT, $State);

		if($State) {
			$operationMode = $this->GetValue(Variables::OPMODE_IDENT);
			$this->SetOperationMode($operationMode);
			$this->EnableAction(Variables::OPMODE_IDENT);
			
			if($operationMode!=OperationMode::WEEKLYPROGRAM_ID) {
				$this->EnableAction(Variable::SETPOINT_IDENT);
			}	
		} else {
			$this->SetOperationMode(OperationMode::OFF_ID);
			$this->DisableAction(Variables::OPMODE_IDENT);
			$this->DisableAction(Variables::SETPOINT_IDENT);	
		}
	}

	public function SetOperationMode(int $Mode) {
		$this->SendDebug(__FUNCTION__, Debug::ENTERINGFUNCTION, 0);

		try {
			$ipAddress = $this->ReadPropertyString(Properties::IPADDRESS);
			If(strlen($ipAddress)>0) {
				$useSSL = $this->ReadPropertyBoolean(Properties::USESSL);

				$this->SendDebug(__FUNCTION__, Debug::SETOPERATIONMODE, 0);
				$this->SendDebug(__FUNCTION__, sprintf(Debug::IPADDRESS, $ipAddress), 0);
				
				$device = new MillLocalAPI($ipAddress, $useSSL);
				
				$operationMode = MillLocalAPI::MapOperationModeToString($Mode);
						
				if($operationMode!==false) {
					$device->SetOperationMode($operationMode);
					$this->SendDebug(__FUNCTION__, sprintf(Debug::SELECTEDOPERATIONMODE, $operationMode), 0);
					if($Mode!=OperationMode::OFF_ID) {
						$this->SetValueEx(Variables::OPMODE_IDENT, $Mode);
					}
				} else {
					$this->SendDebug(__FUNCTION__, sprintf(Debug::OPERATIONMODEFAILED, $ipAddress, $Mode), 0);
				}
			}
		} catch(Exception $e) {
			$msg = sprintf(Errors::UNEXPECTED, $e->getMessage());
			$this->LogMessage($msg, KL_ERROR);
			$this->SendDebug(__FUNCTION__, $msg, 0);
		} 

	}

	public function SetSetpoint(float $Temperature) {
		$this->SendDebug(__FUNCTION__, Debug::ENTERINGFUNCTION, 0);

		try {
			$ipAddress = $this->ReadPropertyString(Properties::IPADDRESS);
			If(strlen($ipAddress)>0) {
				$useSSL = $this->ReadPropertyBoolean(Properties::USESSL);

				$this->SendDebug(__FUNCTION__, Debug::ADJUSTSETPOINT, 0);
				$this->SendDebug(__FUNCTION__, sprintf(Debug::IPADDRESS, $ipAddress), 0);
				
				$device = new MillLocalAPI($ipAddress, $useSSL);
						
				if($device->OperationMode!=EOperationMode::WeeklyProgram) {
					$device->SetSetpoint($Temperature);
					$this->SendDebug(__FUNCTION__, sprintf(Debug::NEWSETPOINT, $Temperature), 0);
					$this->SetValueEX(Variables::SETPOINT_IDENT, $Temperature);
				} else {
					throw new Exception(Errors::INVALIDOPERATIONMODE);
				}
			}
		} catch(Exception $e) {
			$msg = sprintf(Errors::UNEXPECTED, $e->getMessage());
			$this->LogMessage($msg, KL_ERROR);
			$this->SendDebug(__FUNCTION__, $msg, 0);
		} 
	}

	private function Update() {
		$this->SendDebug(__FUNCTION__, Debug::ENTERINGFUNCTION, 0);

		$this->SetDeviceProperties();
		$this->UpdateVariables();
	}
}