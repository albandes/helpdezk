<?php
/*
abstract class AuthStatus
{
    const FAIL = "Authentication failed";
    const OK = "Authentication OK";
    const SERVER_FAIL = "Unable to connect to LDAP server";
    const ANONYMOUS = "Anonymous log on";
}
*/
//class ldapClass {
class ldapClass extends System {
    private $server = "127.0.0.1";
    private $domain = "localhost";
    private $admin = "admin";
    private $password = "";

    public function __construct($server, $domain, $admin = "", $password = "")
    {
        parent::__construct();
        $this->server = $server;
        $this->domain = $domain;
        $this->admin = $admin;
        $this->password = $password;
        $this->keep = array("samaccountname",
                            "distinguishedname",
                            "mail",
                            "telephonenumber",
                            "mobile",
                            "company",
                            "department"
                            );
    }


    public function get_users($dn)
    {

        ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);

        if(!($ldap = ldap_connect($this->server))) return false;

        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (!($ldapbind = ldap_bind($ldap, $this->admin."@".$this->domain, $this->password))) return false ;

        $query = "(&(objectClass=user)(objectCategory=person))";

        $results = ldap_search($ldap,$dn,$query);

        $entries = ldap_get_entries($ldap, $results);

        array_shift($entries);         // Remove first entry (it's always blank)

        $i = 0;
        // Build output array
        foreach($entries as $u) {
            foreach($this->keep as $x) {
                // Check for attribute
                if(isset($u[$x][0])) $attrval = $u[$x][0]; else $attrval = NULL;
                // Append attribute to output array
                $output[$i][$x] = $attrval;
            }
            $i++;
        }
        ldap_close($ldap);
        return $output;
    }
    // Authenticate the against server the domain\username and password combination.
    public function authenticate($type,$user,$password, $dn='')
    {
        if(!($ldap = ldap_connect($this->server))) return false;

        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

        if($type == 2) {
            $bind = @ldap_bind($ldap, $user."@".$this->domain, $password);
        }
        elseif($type == 1) {
            $bind = @ldap_bind($ldap, $dn, "$pass");
        }

        if($bind ) {
            $result = 'OK';
        } else {
            /* Login failed. Return false, together with the error code and text from
            ** the LDAP server. The common error codes and reasons are listed below :
            ** (for iPlanet, other servers may differ)
            ** 19 - Account locked out (too many invalid login attempts)
            ** 32 - User does not exist
            ** 49 - Wrong username or password
            ** 53 - Account inactive (manually locked out by administrator)
            */
            $ldapErrorCode = ldap_errno($ldap);
            $ldapErrorText = ldap_error($ldap);
            $result = "[LDAP] Error: " . $ldapErrorCode . " - " . $ldapErrorText ;
        }

        ldap_close($ldap);
        return $result ;

    }

    public function getUserInfo($dn,$userFind, $object)
    {
        ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);

        if(!($ldap = ldap_connect($this->server))) return false;

        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (!($ldapbind = ldap_bind($ldap, $this->admin."@".$this->domain, $this->password))) return false ;

        $results = ldap_search($ldap,$dn,"($object=$userFind)");
        $entries = ldap_get_entries($ldap, $results);

        array_shift($entries);         // Remove first entry (it's always blank)

        $i = 0;
        // Build output array
        foreach($entries as $u) {
            foreach($this->keep as $x) {
                if(isset($u[$x][0])) $attrval = $u[$x][0]; else $attrval = NULL;
                $output[$i][$x] = $attrval;
            }
            $i++;
        }
        ldap_close($ldap);
        return $output;
    }
}