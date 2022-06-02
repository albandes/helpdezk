<?php

/*********************************************************************
 *
 *             $HeadURL: syslog.php $
 * $LastChangedRevision: 1.1 $
 *             Language: PHP 4.x or higher
 *            Copyright: SysCo systèmes de communication sa
 *         CreationDate: 2005-11-05
 *            CreatedBy: SysCo/al
 *     $LastChangedDate: 2005-12-24 $
 *       $LastChangedBy: SysCo/al $
 *              WebSite: http://developer.sysco.ch/php/
 *                Email: developer [at] sysco [dot] ch
 *
 *
 * Description
 *
 *   The Syslog class is a syslog device implementation in PHP
 *   following the RFC 3164 rules.
 *
 *   Facility values:
 *      0 kernel messages
 *      1 user-level messages
 *      2 mail system
 *      3 system daemons
 *      4 security/authorization messages
 *      5 messages generated internally by syslogd
 *      6 line printer subsystem
 *      7 network news subsystem
 *      8 UUCP subsystem
 *      9 clock daemon
 *     10 security/authorization messages
 *     11 FTP daemon
 *     12 NTP subsystem
 *     13 log audit
 *     14 log alert
 *     15 clock daemon
 *     16 local user 0 (local0) (default value)
 *     17 local user 1 (local1)
 *     18 local user 2 (local2)
 *     19 local user 3 (local3)
 *     20 local user 4 (local4)
 *     21 local user 5 (local5)
 *     22 local user 6 (local6)
 *     23 local user 7 (local7)
 *
 *   Severity values:
 *     0 Emergency: system is unusable
 *     1 Alert: action must be taken immediately
 *     2 Critical: critical conditions
 *     3 Error: error conditions
 *     4 Warning: warning conditions
 *     5 Notice: normal but significant condition (default value)
 *     6 Informational: informational messages
 *     7 Debug: debug-level messages
 *
 *
 * Usage
 *
 *   require_once('syslog.php');
 *   $syslog = new Syslog($facility = 16, $severity = 5, $hostname = "", $fqdn= "", $ip_from = "", $process="", $content = "");
 *   $syslog->Send($server = "", $content = "", $timeout = 0);
 *
 *
 * Examples
 *
 *   Example 1
 *     <?php
 *         require_once('syslog.php');
 *         $syslog = new Syslog();
 *         $syslog->Send('192.168.0.12', 'My first PHP syslog message');
 *     ?>
 *
 *   Example 2
 *     <?php
 *         require_once('syslog.php');
 *         $syslog = new Syslog(23, 7, 'MYSERVER', 'myserver.mydomain.net', '192.168.0.1', 'webautomation');
 *         $syslog->Send('192.168.0.12', 'My second PHP syslog message');
 *     ?>
 *
 *   Example 3
 *     <?php
 *         require_once('syslog.php');
 *         $syslog = new Syslog();
 *         $syslog->SetFacility(23);
 *         $syslog->SetSeverity(7);
 *         $syslog->SetHostname('MYSERVER');
 *         $syslog->SetFqdn('myserver.mydomain.net');
 *         $syslog->SetIpFrom('192.168.0.1');
 *         $syslog->SetProcess('webautomation');
 *         $syslog->SetContent('My third PHP syslog message');
 *         $syslog->SetServer('192.168.0.12');
 *         $syslog->Send();
 *     ?>
 *
 *   Example 4
 *     <?php
 *         // Do not follow the conventions of the RFC
 *         // and send a customized MSG part instead of
 *         // the recommanded format "process fqdn ip content"
 *         require_once('syslog.php');
 *         $syslog = new Syslog();
 *         $syslog->SetFacility(23);
 *         $syslog->SetSeverity(7);
 *         $syslog->SetHostname('MYSERVER');
 *         $syslog->SetMsg('My customized MSG PHP syslog message');
 *         $syslog->SetServer('192.168.0.12');
 *         $syslog->Send();
 *     ?>
 *
 *
 * External file needed
 *
 *   none.
 *
 *
 * External file created
 *
 *   none.
 *
 *
 * Special issues
 *
 *   - Sockets support must be enabled.
 *     * In Linux and *nix environments, the extension is enabled at
 *       compile time using the --enable-sockets configure option
 *     * In Windows, PHP Sockets can be activated by un-commenting
 *       extension=php_sockets.dll in php.ini
 *
 *
 * Licence
 *
 *   Copyright (c) 2005, SysCo systèmes de communication sa
 *   SysCo (tm) is a trademark of SysCo systèmes de communication sa
 *   All rights reserved.
 *
 *   Redistribution and use in source and binary forms, with or without 
 *   modification, are permitted provided that the following conditions are met:
 *
 *   - Redistributions of source code must retain the above copyright notice, 
 *     this list of conditions and the following disclaimer. 
 *   - Redistributions in binary form must reproduce the above copyright notice, 
 *     this list of conditions and the following disclaimer in the documentation 
 *     and/or other materials provided with the distribution. 
 *   - Neither the name of SysCo systèmes de communication sa nor the names of its
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission. 
 *
 *   THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 *   EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES 
 *   OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT 
 *   SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
 *   SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT 
 *   OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
 *   HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR 
 *   TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
 *   EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * Change Log
 *
 *   2005-12-24 1.1 SysCo/al Generic release and documentation
 *   2005-11-05 1.0 SysCo/al Initial release
 *
 *********************************************************************/
 
    class Syslog //extends hdkCommon
    {
        var $_facility; // 0-23
        var $_severity; // 0-7
        var $_hostname; // no embedded space, no domain name, only a-z A-Z 0-9 and other authorized characters
        var $_fqdn;
        var $_ip_from;
        var $_process;
        var $_content;
        var $_msg;
        var $_server;   // Syslog destination server
        var $_port;     // Standard syslog port is 514
        var $_timeout;  // Timeout of the UDP connection (in seconds)

        public function __construct()
        {
            session_start();
        }

        function Syslog($facility = 6, $severity = 5, $hostname = "", $fqdn= "", $ip_from = "", $process="", $content = "")
        {
            $this->_msg      = '';
            $this->_server   = '127.0.0.1';
            $this->_port     = 514;
            $this->_timeout  = 10;
            $this->_facility = $facility;
            $this->_severity = $severity;
            //die('a: ' . $this->_facility);
            $this->_hostname = $hostname;
            if ($this->_hostname == "")
            {
                if (isset($_ENV["COMPUTERNAME"]))
                {
                    $this->_hostname = $_ENV["COMPUTERNAME"];
                }
                elseif (isset($_ENV["HOSTNAME"]))
                {
                    $this->_hostname = $_ENV["HOSTNAME"];
                }
                else
                {
                    $this->_hostname = "WEBSERVER";
                }
            }
            $this->_hostname = substr($this->_hostname, 0, strpos($this->_hostname.".", "."));
            
            $this->_fqdn = $fqdn;
            if ($this->_fqdn == "")
            {
                if (isset($_SERVER["SERVER_NAME"]))
                {
                    $this->_fqdn = $_SERVER["SERVER_NAME"];
                }
            }

            $this->_ip_from = $ip_from;
            if ($this->_ip_from == "")
            {
                if (isset($_SERVER["SERVER_ADDR"]))
                {
                    $this->_ip_from = $_SERVER["SERVER_ADDR"];
                }
            }

            $this->_process = $process;
            if ($this->_process == "")
            {
                $this->_process = "PHP";
            }

            $this->_content = $content;
            if ($this->_content == "")
            {
                $this->_content = "PHP generated message";
            }
            
        }

        // Pipegrep Functions --------------------

        function setLogStatus(){
            $logEmail = $_SESSION['LOG_EMAIL'];
            $logGeneral = $_SESSION['LOG_GENERAL'];

            if ($logEmail == 1 or $logGeneral == 1) {
                return true;
            } else {
                return false ;
            }
        }
        function setLogLevel(){
            return $_SESSION['LOG_LEVEL'];
        }
        function setLogHost(){
            return $_SESSION['LOG_HOST'];
        }
        function setLogRemoteServer(){
            return $_SESSION['LOG_REMOTE_SERVER'];
        }
        function setFacility($facility)
        {
            $facility = 18;
            $this->_facility = $facility;
        }
        function setLogFacility(){
            //return $_SESSION['LOG_REMOTE_SERVER'];
            //echo '<pre>';
            //print_r($_SESSION);
            return 6;
        }
        // ---------------------------------------
        
        function SetSeverity($severity)
        {
            $this->_severity = $severity;
        }
        
        
        function SetHostname($hostname)
        {
            $this->_hostname = $hostname;
        }
        
        
        function SetFqdn($fqdn)
        {
            $this->_fqdn = $fqdn;
        }
        
        
        function SetIpFrom($ip_from)
        {
            $this->_ip_from = $ip_from;
        }
        
        
        function SetProcess($process)
        {
            $this->_process = $process;
        }
        
        
        function SetContent($content)
        {
            $this->_content = $content;
        }
        
        
        function SetMsg($msg)
        {
            $this->_msg = $msg;
        }
        
        
        function SetServer($server)
        {
            $this->_server = $server;
        }
        
        
        function SetPort($port)
        {
            if ((intval($port) > 0) && (intval($port) < 65536))
            {
                $this->_port = intval($port);
            }
        }


        function SetTimeout($timeout)
        {
            if (intval($timeout) > 0)
            {
                $this->_timeout = intval($timeout);
            }
        }
        
        
        function Send($server = "", $content = "", $timeout = 0)
        {

            if ($server != "")
            {
                $this->_server = $server;
            }

            if ($content != "")
            {
                $this->_content = $content;
            }
            
            if (intval($timeout) > 0)
            {
                $this->_timeout = intval($timeout);
            }
            
            if ($this->_facility <  0) { $this->_facility =  0; }
            if ($this->_facility > 23) { $this->_facility = 23; }
            if ($this->_severity <  0) { $this->_severity =  0; }
            if ($this->_severity >  7) { $this->_severity =  7; }
            
            $this->_process = substr($this->_process, 0, 32);
            
            $actualtime = time();
            $month      = date("M", $actualtime);
            $day        = substr("  ".date("j", $actualtime), -2);
            $hhmmss     = date("H:i:s", $actualtime);
            $timestamp  = $month." ".$day." ".$hhmmss;

            $pri    = "<".($this->_facility*8 + $this->_severity).">";
            $header = $timestamp." ".$this->_hostname;
            
            if ($this->_msg != "")
            {
                $msg = $this->_msg;
            }
            else
            {
                $msg = $this->_process.": ".$this->_fqdn." ".$this->_ip_from." ".$this->_content;
            }
            
            $message = substr($pri.$header." ".$msg, 0, 1024);

            $fp = fsockopen("udp://".$this->_server, $this->_port, $errno, $errstr);
            if ($fp)
            {
                fwrite($fp, $message);
                fclose($fp);
                $result = $message;
            }
            else
            {
                $result = "ERROR: $errno - $errstr";
            }
            return $result;
        }
    }

?>