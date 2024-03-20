<?php

declare(strict_types=1);

require_once(__DIR__ . "/../libs/autoload.php");

class EOperationMode {
    const Off = 'Off';
    const WeeklyProgram = 'Weekly program';
    const IndependentDevice = 'Independent device';
    const ControlIndividually = 'Control individually';
    const Invalid = 'Invalid';
}

class ETemperatureType {
    const Off = 'Off';
    const Normal = 'Normal';
    const Comfort = 'Comfort';
    const Sleep = 'Sleep';
    const Away = 'Away';
    const AlwaysHeating = 'AlwaysHeating';
}

class OperationMode {
	const OFF_ID = 1;
	const OFF_TEXT = EOperationMode::Off;
	const WEEKLYPROGRAM_ID = 2;
	const WEEKLYPROGRAM_TEXT = EOperationMode::WeeklyProgram;
	const INDEPENDENTDEVICE_ID = 3;
	const INDEPENDENTDEVICE_TEXT = EOperationMode::IndependentDevice;
	const CONTROLINDIVIDUALLY_ID = 4;
	const CONTROLINDIVIDUALLY_TEXT = EOPerationMode::ControlIndividually;
}

class MillLocalAPI {
    use HttpRequest;

    public $Name;
    public $CustomName;
    public $OperationMode;
    public $Temperature;
    public $Setpoint;
    public $Humidity;
    public $ProgrammedSetpoint;
    public $SwitchedOn;
     public $CurrentTemperatureType;
    
    public function __construct(string $IpAddress, $UseSSL = False) {
        $this->IpAddress = $IpAddress;
        $this->useSSL = $UseSSL;

        $device = self::GetStatus();
        if($device!==false) {
            $this->Name = $device->name;
            $this->CustomName = $device->custom_name;
        }
        
        $status = self::GetControlStatus();
        if($status!==false) {
            $this->Temperature = round($status->ambient_temperature,1);
            $this->ProgrammedSetpoint = $status->set_temperature;
            $this->Humidity = round($status->humidity,1);
            $this->OperationMode = $status->operation_mode;
            $this->SwitchedOn = $status->switched_on;
        }

        $setpoint = self::GetSetpoint();
        if($setpoint!==false) {
            $this->Setpoint = $setpoint->value;
        }

        $weeklyProgram = self::GetWeeklyProgram();
        if($weeklyProgram!==false) {
            $timers = json_decode($weeklyProgram, true)['timers'];

        }
    }

    
    private function GetStatus() {
        return self::httpGet('/status');
    }

    private function GetSetpoint() {
        $params = array('type' => ETemperatureType::Normal);

        $jsonParams = json_encode($params);

        return self::HttpGetJson('/set-temperature', $jsonParams);
    }

    private function GetControlStatus() {
        return self::httpGet('/control-status');
    }

    private function GetOperationMode() {
        return self::httpGet('/operation-mode');
    }

    public function SetOperationMode(string $OperationMode) {
        $params = array('mode' => $OperationMode);
        
        $jsonParams = json_encode($params);

        return self::httpPostJson('/operation-mode', $jsonParams);
    }

    public function SetSetpoint(float $Temperature) {
        $params = array('type' => ETemperatureType::Normal,
                        'value' => $Temperature);
        
        $jsonParams = json_encode($params);

        return self::httpPostJson('/set-temperature', $jsonParams);
    }

    public function Reboot() {
        self::httpPost('/reboot', False);
    }

    private function GetWeeklyProgram() {
        return self::httpGet('/weekly-program');
    }

    public static function MapOperationModeToInt(string $OperationMode) : int {
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

	public static function MapOperationModeToString(int $OperationMode) : string | bool {
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

    private Static function MinutesSinceMonday() : int {
        $secsPerWeek = 604800;
        $oldMonday = 	1710716400;
    
        $now = time();
        $elapsed = $now-$oldMonday;
    
        $secsSinceMonday = $elapsed%$secsPerWeek;
        
        return intval($secsSinceMonday/60);
    }

    private function GetProgrammedTemperatureType(array $Timers) : string {
        $size = sizeof($Timers)-1;
        $minutesSinceMonday = self::MinutesSinceMonday();
        
        for($i=0;$i<$size;$i++) {
            $timer1 = $Timers[$i]['timestamp'];
            $timer2 = $Timers[$i+1]['timestamp'];
            
            if($minutesSinceMonday>=$timer1 && $minutesSinceMonday<$timer2) {
                return $Timers[$i]['name'];
            }
        }
    
        return $Timers[$size-1]['name'];
    }
}