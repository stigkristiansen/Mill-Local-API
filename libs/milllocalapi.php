<?php

declare(strict_types=1);

require_once(__DIR__ . "/../libs/autoload.php");

class MillLocalAPI {
    use HttpRequest;

    private string $ipAddress;
    private bool $useSSL;

    private $name;
    private $customName;
    private $operationMode;
    private $temperature;
    private $setpoint;
    private $humidity;
    
    public function __construct(string $IpAddress, $UseSSL = False) {
        $this->ipAddress = $IpAddress;
        $this->useSSL = $UseSSL;

        $device = self::GetStatus();
        if($device!==false) {
            $this->name = $device->name;
            $this->customName = $device->custom_name;
        }
        
        $status = self::GetControlStatus();
        if($status!==false) {
            $this->temperature = round($status->ambient_temperature,1);
            $this->setpoint = $status->set_temperature;
            $this->humidity = round($status->humidity,1);
            $this->operationMode = $status->operation_mode;
        }
    }

    public function Name() {
        return $this->name;
    }

    public function CustomName() {
        return $this->customName;
    }

    public function Temperature() {
        return $this->temperature;
    }

    public function Setpoint() {
        return $this->setpoint;
    }

    public function Humidity() {
        return $this->humidity;
    }

    public function OperationMode() {
        return $this->operationMode;
    }

    private function GetStatus() {
        return self::httpGet('/status');
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
}