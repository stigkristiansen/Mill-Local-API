<?PHP

declare(strict_types=1);

class Errors {
    const UNEXPECTED = 'An unexpected error occured. The error was "%s"';
}

class Debug {
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
}

class ResponseCodes {
    public static function GetMessage($code) {
    }
}

class OperationMode {
	const OFF_ID = 'Off';
	const OFF_TEXT = EOpertionMode::Off;
	const WEEKLYPROGRAM_ID = 'WeeklyProgram';
	const WEEKLYPROGRAM_TEXT = EOperationMode::WeeklyProgram;
	const INDEPENDENTDEVICE_ID = 'IndependentDevice';
	const INDEPENDENTDEVICE_TEXT = EOperationMode::IndependentDevice;
	const CONTROLINDIVIDUALLY_ID = 'ControlIndividually';
	const CONTROLINDIVIDUALLY_TEXT = EOPerationMode::ControlIndividually;
}

