<?PHP

declare(strict_types=1);

class Errors {
    const UNEXPECTED = 'An unexpected error occured. The error was "%s"';
}

class Debug {
    const ENTERINGFUNCTION = 'Entering function..';
    const REQUESTACTIONCALLED = 'RequestAction was called: %s:%s';
    const RETRIVEINFO = 'Trying to retrive the device information...';
    const IPADDRESS = 'IP Address: %s';
    const DEVICENAME = 'Device name: %s';
    const DEVICECUSTOMNAME = 'Device Custom Name: %s';
    const UPDATINGFORM = 'Updating form...';
    const DEVICEINFOFAILED = 'Failed to retrive device information! %s';
    const OPERATIONMODE = 'Operation Mode: %s (%d)';
    const TEMPERATURE = 'Tempearature is: %f';
    const SETPOINT = 'Setpoint is: %f';
    const HUMIDITY = 'Humidity is: %f';
    const SETOPERATIONMODE = 'Trying to set operation mode...';
    const SELECTEDOPERATIONMODE = 'Selected Operation Mode: %s';
    const OPERATIONMODEFAILED = 'Failed to set operation mode for %s. The mode was %d';
    const ADJUSTSETPOINT = 'Trying to adjust the setpoint...';
    const NEWSETPOINT = 'New setpoint is: %f';
}


class Properties {
    const NAME = 'Name';
    const CUSTOMNAME = 'CustomName';
    const IPADDRESS = 'IPAddress';
    const USESSL = 'UseSSL';
}

class Timers {
    const UPDATE = 'Update';
    const UPDATEINTERVAL = 10000;
}

class Profiles {
    const OPMODE = 'Mill.OperationMode';
    const OPMODE_ICON = 'Gear';
    

}

class Variables {
    const POWER_IDENT = 'power';
    const POWER_TEXT = 'Power';
    const OPMODE_IDENT = 'opmode';
    const OPMODE_TEXT = 'Mode';
    const SETPOINT_IDENT = 'setpoint';
    const SETPOINT_TEXT = 'Setpoint';
    const TEMP_IDENT = 'temperature';
    const TEMP_TEXT = 'Temperature';
    const HUMIDITY_IDENT = 'humidity';
    const HUMIDITY_TEXT = 'Humidity';
}

class ResponseCodes {
    public static function GetMessage($code) {
    }
}

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

