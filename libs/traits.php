<?PHP

declare(strict_types=1);

trait HttpRequest {
    protected function GetScheme() {
        return $this->useSSL?'https://':'http://';
    }

    protected function HttpGet(string $DeltaUrl, bool $ReturnResult=True) {
		if(self::Ping($this->ipAddress)) {
            $url = $this->GetScheme() . $this->ipAddress . $DeltaUrl;

			$result = self::request ('get', $url);
            
            if($ReturnResult) {
                $originalResult = $result;
                $result = json_decode($result);
                
                if($result!==null) {
                    if(isset($result->status) && $result->status=='ok') {
                        return $result;
                    } else if(isset($result->status)) {
                        throw new Exception(sprintf("%s returned: error %s:", $url, $result->status));
                    }
                } else
                    return False;

                throw new Exception(sprintf("%s returned invalid JSON. The returned value was %s", $url, $originalResult));
            }
		} else
			throw new Exception(sprintf('Host %s is not responding', $this->ipAddress));
    }

    protected function HttpPost(string $DeltaUrl, bool $ReturnResult=True) {
        if(self::Ping($this->ipAddress)) {
			$url = $this->GetScheme() . $this->ipAddress . $DeltaUrl;

			$result = self::request('post', $url);

            if($ReturnResult) {
                $originalResult = $result;
                $result = json_decode($result);
                
                if($result!==null) {
                    if(isset($result->status) && $result->status=='ok') {
                        return $result;
                    } else if(isset($result->status)) {
                        throw new Exception(sprintf("%s returned: error %s:", $url, $result->status));
                    }
                } else
                    return False;

                throw new Exception(sprintf("%s returned invalid JSON. The returned value was %s", $url, $originalResult));
            }
		} else
			throw new Exception(sprintf('Host %s is not responding', $this->ipAddress));	    
    }

    
    protected function HttpPostJson(string $DeltaUrl, string $JsonParams, bool $ReturnResult=True) {
	    if(self::Ping($this->ipAddress)) {
            $url = $this->GetScheme() . $this->ipAddress . $DeltaUrl;

			$result = self::request('post', $url, $JsonParams);

            if($ReturnResult) {
                $originalResult = $result;
                $result = json_decode($result);
                
                if($result!==null) {
                    if(isset($result->status) && $result->status=='ok') {
                        return $result;
                    } else if(isset($result->status)) {
                        throw new Exception(sprintf("%s returned: error %s:", $url, $result->status));
                    }
                } else
                    return False;

                throw new Exception(sprintf("%s returned invalid JSON. The returned value was %s", $url, $originalResult));
            }
		} else
			throw new Exception(sprintf('Host %s is not responding', $this->ipAddress));	
    }

    protected function Ping(string $IpAddress) {
        $wait = 500;
        for($count=0;$count<3;$count++) {
            if(Sys_Ping($IpAddress, $wait))
                return true;
            $wait*=2;
        }

        return false;
    }

    protected function request($Type, $Url, $Data=NULL) {
		$ch = curl_init();
		
		switch(strtolower($Type)) {
			case "put":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				break;
			case "post":
				curl_setopt($ch, CURLOPT_POST, 1 );
				break;
			case "get":
				// Get is default for cURL
				break;
		}

        $headers = array(
            'User-Agent:Symcon',
            'Accept:application/json'
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $Url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		
		if($Data!=NULL)
			curl_setopt($ch, CURLOPT_POSTFIELDS, $Data); 
		
		$result=curl_exec($ch);

		if($result!==false)
            return $result;
		else
            throw new Exception(sprintf("\"%s\" failed. The error was \"%s\"", $Url, curl_error($ch)));
	}
}


trait Profile {
    protected function DeleteProfile($Name) {
        if(IPS_VariableProfileExists($Name))
            IPS_DeleteVariableProfile($Name);
    }

    protected function RegisterProfileString($Name, $Icon, $Prefix, $Suffix) {

        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, 3);
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 3) {
                throw new Exception('Variable profile type (string) does not match for profile ' . $Name);
            }
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
    }

    protected function RegisterProfileStringEx($Name, $Icon, $Prefix, $Suffix, $Associations) {
        
        $this->RegisterProfileString($Name, $Icon, $Prefix, $Suffix);

        foreach ($Associations as $association) {
            IPS_SetVariableProfileAssociation($Name, $association[0], $association[1], $association[2], $association[3]);
        }
        
        // Remove assiciations that is not specified in $Associations
        $profileAssociations = IPS_GetVariableProfile($Name)['Associations'];
        foreach($profileAssociations as $profileAssociation) {
            $found = false;
            foreach($Associations as $association) {
                if($profileAssociation['Value']==$association[0]) {
                    $found = true;
                    break;
                }
            }

            if(!$found)
                IPS_SetVariableProfileAssociation($Name, $profileAssociation['Value'], '', '', -1);    
        }
    }


    protected function RegisterProfileBoolean($Name, $Icon, $Prefix, $Suffix) {

        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, 0);
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 0) {
                throw new Exception('Variable profile type (boolean) does not match for profile ' . $Name);
            }
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
    }

    protected function RegisterProfileBooleanEx($Name, $Icon, $Prefix, $Suffix, $Associations) {
        
        $this->RegisterProfileBoolean($Name, $Icon, $Prefix, $Suffix);

        foreach ($Associations as $association) {
            IPS_SetVariableProfileAssociation($Name, $association[0], $association[1], $association[2], $association[3]);
        }
        
        // Remove assiciations that is not specified in $Associations
        $profileAssociations = IPS_GetVariableProfile($Name)['Associations'];
        foreach($profileAssociations as $profileAssociation) {
            $found = false;
            foreach($Associations as $association) {
                if($profileAssociation['Value']==$association[0]) {
                    $found = true;
                    break;
                }
            }

            if(!$found)
                IPS_SetVariableProfileAssociation($Name, $profileAssociation['Value'], '', '', -1);    
        }
    }
    
    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize) {
        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, 1);
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 1) {
                throw new Exception('Variable profile type (integer) does not match for profile ' . $Name);
            }
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
    }

    protected function RegisterProfileIntegerEx($Name, $Icon, $Prefix, $Suffix, $Associations) {
        
        if (count($Associations) === 0) {
            $MinValue = 0;
            $MaxValue = 0;
        } else {
            $MinValue = $Associations[0][0];
            $MaxValue = $Associations[count($Associations) - 1][0];
        }

        $this->RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, 0);

        foreach ($Associations as $association) {
            //$this->LogMessage($Name . ':' . $association[0] . ':' . $association[1] . ':' . $association[2] . ':' . $association[3], KL_MESSAGE);
            IPS_SetVariableProfileAssociation($Name, $association[0], $association[1], $association[2], $association[3]);
        }
        
        // Remove assiciations that is not specified in $Associations
        $profileAssociations = IPS_GetVariableProfile($Name)['Associations'];
        foreach($profileAssociations as $profileAssociation) {
            $found = false;
            foreach($Associations as $association) {
                if($profileAssociation['Value']==$association[0]) {
                    $found = true;
                    break;
                }
            }

            if(!$found)
                IPS_SetVariableProfileAssociation($Name, $profileAssociation['Value'], '', '', -1);    
        }
    }

    protected function CreateProfileAssosiationList($List) {
        $count = 0;
        foreach($List as $value) {
            $assosiations[] = [$count, $value,  '', -1];
            $count++;
        }

        return $assosiations;
    }

    protected function CreateProfileAssosiationLinkList($List) {
        $assosiations[] = [0, 'None',  '', -1];

        $instanceIds = IPS_GetInstanceListByModuleID('{5B66102A-96ED-DF96-0B89-54E37501F997}');
        foreach($List as $value) {
            foreach($instanceIds as $instanceId) {
                if(strtolower(IPS_GetProperty($instanceId, Properties::NAME))==strtolower($value)) {
                    $assosiations[] = [$instanceId, $value,  '', -1];
                    break;        
                }
            }
        }

        return $assosiations;
    }

    protected function GetProfileAssosiationName($ProfileName, $Index) {
        $profile = IPS_GetVariableProfile($ProfileName);
    
        if($profile!==false) {
            foreach($profile['Associations'] as $association) {
                if($association['Value']==$Index)
                    return $association['Name'];
            }
        } 
    
        return false;
    
    }
}

trait Buffer {
    protected function Lock(string $Id) {
		for ($count=0;$count<10;$count++) {
			if (IPS_SemaphoreEnter(get_class() . (string) $this->InstanceID . $Id, 1000)) {
				return true;
			} else {
				IPS_Sleep(mt_rand(1, 5));
			}
		}

		return false;
	}

	protected function Unlock(string $Id) {
		IPS_SemaphoreLeave(get_class() . (string) $this->InstanceID . $Id);
	}

}




trait Utils {
    protected function SecondsToString(float $Seconds, bool $ShowSeconds=true) {
		if($Seconds>=0) {
			$s = $Seconds%60;
			$m = floor(($Seconds%3600)/60);
			$h = floor(($Seconds%86400)/3600);
			
			if($ShowSeconds) {
				return sprintf('%02d:%02d:%02d', $h, $m, $s);
			} else {
				return sprintf('%02d:%02d', $h, $m);
			}
		} else {
			return 'N/A';
		}
	}

    protected function SetValueEx(string $Ident, $Value) {
		$oldValue = $this->GetValue($Ident);
		if($oldValue!=$Value)
			$this->SetValue($Ident, $Value);
	}

}