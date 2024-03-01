<?php

declare(strict_types=1);

require_once(__DIR__ . "/../libs/autoload.php");

class EOprationMode {
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


class MillLocalAPI {
    use HttpRequest;

    private string $ipAddress;
    private bool $useSSL;

    private $name;
    private $customName;

    public function __construct(string $IpAddress, $UseSSL = False) {
        $this->ipAddress = $IpAddress;
        $this->useSSL = $UseSSL;

        $device = self::GetStatus();
        if($device!==false) {
            $this->name = $$device->name;
            $this->customName = $device->custom_name;
        }
    }

    public function Name() {
        return $this->name;
    }

    public function CustomName() {
        return $this->customName;
    }

    public function GetStatus() {
        return self::httpGet('/status');
    }

    public function GetControlStatus() {
        return self::httpGet('/control-status');
    }

    public function GetOperationMode() {
        return self::httpGet('/operation-mode');
    }

    public function SetOperationMode(string $OperationMode) {
        $params = array('mode' => $OperationMode);
        $jsonParams = json_encode($params);

        return self::httpPostJson('/operation-mode', $jsonParams);
    }

    public function Reboot() {
        self::httpPost('/reboot', False);
    }
}