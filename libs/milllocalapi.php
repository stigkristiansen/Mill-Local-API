<?php

declare(strict_types=1);

require_once(__DIR__ . "/../libs/autoload.php");

<?PHP 

declare(strict_types=1);

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

class LocalAPI {
    use HttpRequest;

    private string $ipAddress;
    private bool $useSSL;

    public function __construct(string $IpAddress, $UseSSL = $false) {
        $this->ipAddress = $IpAddress;
        $this->useSSL = $UseSSL;
    }

    public function GetStatus() {
        $status = self::httpGetJson($this->ipAddress, $this->GetScheme() . $this->ipAddress . '/status');
    }

    public function GetControlStatus() {
        $status = self::httpGetJson($this->ipAddress, $this->GetScheme() . $this->ipAddress . '/control-status');
    }

    public function GetOperationMode() {
        $status = self::httpGetJson($this->ipAddress, $this->GetScheme() . $this->ipAddress . '/operation-mode');
    }

    public function SetOperationMode(string $OperationMode) {
        $params = array('mode' => $OperationMode);
        $jsonParams = json_encode($params);

        $status = self::httpPostJson($this->ipAddress, $this->GetScheme() . $this->ipAddress . '/operation-mode', $jsonParams);
    }


    public function Reboot() {
        self::httpPost($this->ipAddress, $this->GetScheme() . $this->ipAddress . '/Reboot', $false);
    }

    private function GetScheme() {
        return $this->useSSL?'https://':'http://';
    }




}