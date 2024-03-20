<?PHP

declare(strict_types=1);

class Errors {
    const UNEXPECTED = 'An unexpected error occured. The error was "%s"';
    const REQUESTRETURNED = '%s returned: error %s:';
    const INVALDJSON = '%s returned invalid JSON. The returned value was \"%s\"';
    const HOSTNOTRESPONDING = 'Host %s is not responding';
    const REQUESTFAILED = '\"%s\" failed. The error was \"%s\"';
    const INVALIDOPERATIONMODE = 'Operation Mode cannot be \"Weekely program\"!';
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
    const TEMPERATURE = 'Tempearature is: %.1f';
    const SETPOINT = 'Setpoint is: %.1f';
    const HUMIDITY = 'Humidity is: %.1f';
    const SETOPERATIONMODE = 'Trying to set operation mode...';
    const SELECTEDOPERATIONMODE = 'Selected Operation Mode: %s';
    const OPERATIONMODEFAILED = 'Failed to set operation mode for %s. The mode was %d';
    const ADJUSTSETPOINT = 'Trying to adjust the setpoint...';
    const NEWSETPOINT = 'New setpoint is: %.1f';
    const PROGRAMMEDASETPOINT = 'Programmed setpoint is: %.1f';
    const SWITCHEDON = 'Heating status is: %s';
    const PROGRAMMEDTEMPTYPE = 'Programmed temperature type is: %s';
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
    const SWITCHEDON = 'Mill.SwitchedOn';
    const SWITCHEDON_ICON = 'Temperature';
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
    const PROGRAMMEDSETPOINT_IDENT = 'progsetpoint';
    const PROGRAMMEDSETPOINT_TEXT = 'Prog. setpoint';
    const PROGRAMMEDTEMPTYPE_IDENT = 'programmedtemptype';
    const PROGRAMMEDTEMPTYPE_TEXT = 'Prog. temp. type';
    const SWITCHEDON_IDENT = 'switchedon';
    const SWITCHEDON_TEXT = 'Status';
}



