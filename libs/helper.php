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

}

class Variables {
}

class ResponseCodes {
    public static function GetMessage($code) {
    }
}
