<?PHP

declare(strict_types=1);

class Errors {
    const UNEXPECTED = 'An unexpected error occured. The error was "%s"';
    const UNKNOWNROOM = 'Did not find the room specified (%s)';
    const NOTRESPONDING = 'The device %s is not responding (%s)';
    const MISSINGIP = "The device %s is missing information about it's ip address";
    const ROOMERROR = 'Unable to read room list in profile "%s" or invalid room value %d';
    const INVALIDDATA = 'The data received was invalid';
    const STOPLINK = 'Stopping link...';
    const INVALIDTYPE = 'Invalid type (%s)';
}

class Debug {
    const UPDATEALLLISTS = 'Starting scheduled updating of all lists...';
    const UPDATELINK = 'Starting scheduled updating the Link list...';
    const ESTABLISHLINK = 'Establishing link with room "%s"...';
    const UPDATINGVARIABLES = 'Updating variables...';
    const GETINFORMATION = 'Starting scheduled retrieving of information...';
    const STOPSTATUSUPDATED = 'Skipping processing status_updated!';
    const GETSTATUS = 'Retrieving status...';
    const STARTSTATUSUPDATED = 'Processing event status_updated...';
    const STOPPLAYINFO = 'Skipping processing play_info_updated!';
    const STARTPLAYINFO = 'Processing event play_info_updated...';
    const HANDLEPLAYINFO = 'Handling play_info_updated in own thread...';
    const HANDLESTATUSUPDATED = 'Handling status_updated in own thread...';
    const ALLLISTS = 'Updated all list(s)';
    const LINKLIST = 'Updated Link list';
    const GETPLAYINFO = 'Retrieving PlayInfo for "%s"';
    const STOPLINK = 'Stopping the link!';
    const SEARCHFORROOM = 'Seaching for value %d in profile "%s"';
}

class Properties {
}

class Timers {
    const UPDATE = 'Update';
    const UPDATELISTS = 'UpdateLists';
    const RESETFAVOURITE = 'ResetFavourite';
    const RESETMCPLAYLIST = 'ResetMCPLaylist';
    const RESETCONTROL = 'ResetControl';
}

class Profiles {
    const CONTROL = 'YMC.Control';
    const CONTROL_ICON = 'Execute';
    const INFORMATION = 'YMC.Information';
    const INFORMATION_ICON = 'Information';
    const MUTE = 'YMC.Mute';
    const MUTE_ICON = 'Speaker';
    const SLEEP = 'YMC.Sleep';
    const SLEEP_ICON = 'Sleep';
    const SLEEP_SUFIX = 'min.';
    const FAVORITES = 'YMC.%s.Favorites';
    const FAVORITES_ICON = 'Music';
    const MCPLAYLISTS = 'YMC.%s.Playlists';
    const MCPLAYLISTS_ICON = 'Music'; 
    const LINK = 'YMC.%s.Link'; 
    const LINK_ICON = 'Link'; 
    const POSITION = 'YMC.Position';
    const POSITION_ICON = 'Distance';
    const TIME = 'YMC.Time';
    const TIME_ICON = 'Hourglass';
    const MUSIC = 'YMC.Music';
    const MUSIC_ICON = 'Music';
}

class Variables {
}

class ResponseCodes {
    const SUCCESSFUL_REQUEST = 0;
    const INITIALIZING = 1;
    const INTERNAL_ERROR = 2;
    const INVALID_REQUEST = 3;
    const INVALID_PARAMETER = 4;
    const GUARDED = 5;
    const TIME_OUT = 6;
    const FIRMWARE_UPDATING = 99;
    const ACCESS_ERROR = 100;
    const OTHER_ERRORS = 101;
    const WRONG_USER_NAME = 102;
    const WRONG_PASSWORD = 103;
    const ACCOUNT_EXPIRED = 104;
    const ACCOUNT_DISCONNECTED = 105;
    const ACCOUNT_NUMBER_REACHED_LIMIT = 106;
    const SERVER_MAINTENANCE = 107;
    const INVALID_ACCOUNT = 108;
    const LICENSE_ERROR = 109;
    const READ_ONLY_MODE = 110;
    const MAX_STATIONS = 111;
    const ACCESS_DENIED = 112;

    public static function GetMessage($code) {
        switch ($code) {
            case self::SUCCESSFUL_REQUEST:
                return 'Successful request';
            case self::INITIALIZING:
                return 'Initializing';
            case self::INTERNAL_ERROR:
                return 'Internal Error';
            case self::INVALID_REQUEST:
                return 'Invalid Request (A method did not exist, a method wasn’t appropriate etc.)';
            case self::INVALID_PARAMETER:
                return 'Invalid Parameter (Out of range, invalid characters etc.)';
            case self::GUARDED:
                return 'Guarded (Unable to setup in current status etc.)';
            case self::TIME_OUT:
                return 'Time Out';
            case self::FIRMWARE_UPDATING:
                return 'Firmware Updating';
            case self::ACCESS_ERROR:
                return 'Access Error';
            case self::OTHER_ERRORS:
                return 'Other Errors';
            case self::WRONG_USER_NAME:
                return 'Wrong User Nam';
            case self::WRONG_PASSWORD:
                return 'Wrong Password';
            case self::ACCOUNT_EXPIRED:
                return 'Account Expired';
            case self::ACCOUNT_DISCONNECTED:
                return 'Account Disconnected/Gone Off/Shut Down';
            case self::ACCOUNT_NUMBER_REACHED_LIMIT:
                return 'Account Number Reached to the Limit';
            case self::SERVER_MAINTENANCE:
                return 'Server Maintenance';
            case self::INVALID_ACCOUNT:
                return 'Invalid Account';
            case self::LICENSE_ERROR:
                return 'License Error';
            case self::READ_ONLY_MODE:
                return 'Read Only Mode';
            case self::MAX_STATIONS:
                return 'Max Stations';
            case self::ACCESS_DENIED:
                return 'Access Denied';
            default:
                return 'Unknown error';
        }
    }
}
