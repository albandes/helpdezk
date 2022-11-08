<?php

/*
 * PHP class that can retrieve data from AdSense account
 * It's based on PHP AdSense account monitor class (http://www.webtoolkit.info/php-adsense-account-monitor.html)
 * Copyright (C) 2009  Alex Polski
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * sys_get_temp_dir function for the proper work in PHP4
 * Based on http://www.php.net/sys_get_temp_dir
 */
if (!function_exists('sys_get_temp_dir')) {
  function sys_get_temp_dir() {
    if (!empty($_ENV['TMP'])) { return realpath($_ENV['TMP']); }
    if (!empty($_ENV['TMPDIR'])) { return realpath( $_ENV['TMPDIR']); }
    if (!empty($_ENV['TEMP'])) { return realpath( $_ENV['TEMP']); }
    $tempfile = tempnam(uniqid(rand(), TRUE), '');
    if (file_exists($tempfile)) {
      unlink($tempfile);
      return realpath(dirname($tempfile));
    }
  }
}

/**
 * PHP class that can retrieve data from AdSense account
 * @package AdSense
 */
class AdSense {
    /**
     * Stores curl connection handle
     * @var resource
     */
    var $curl = null;


    /**
     * AdSense::AdSense()
     * AdSense class constructor
     */
    function AdSense(){
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'cookie');

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7");
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookieFile);

        register_shutdown_function(array(&$this, '__destructor'));
    }


    /**
     * AdSense::__destructor()
     * AdSense class destructor
     */
    function __destructor(){
        @curl_close($this->curl);
        @unlink($this->cookieFile);
    }


    /**
     * AdSense::connect()
     * Connects to AdSense account using supplied credentials
     * Returns true on unsuccessful connection, false otherwise
     *
     * @param string $username AdSense username
     * @param string $password AdSense password
     * @return boolean
     */
    function connect($username, $password){
        // /adsense/
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_URL, 'https://www.google.com/adsense/');
        $content = curl_exec($this->curl);

        // /adsense/login-box.js
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_URL, 'https://www.google.com/adsense/login-box.js');
        $content = curl_exec($this->curl);
        $content = preg_replace(
          array("/\\\\75/", "/\\\\42/", "/\\\\46/", "/\\\\075/"),
          array('=', '"', '&', '='),
          $content);
        preg_match('/src="([^"]+)"/', $content, $match);
        $next_url = $match[1];
        $next_url = str_replace('&amp;', '&', $next_url);

        // /accounts/ServiceLoginBox
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_URL, $next_url);
        $content = curl_exec($this->curl);
        preg_match_all('<input type="hidden" name="(.*?)" value="(.*?)">', curl_exec($this->curl), $out);
        $params = array();
        foreach($out[1] as $key=>$name) {
          $params[] = $name . '=' . urlencode($out[2][$key]);
        }
        $params[] = 'Email=' . urlencode($username);
        $params[] = 'Passwd=' . urlencode($password);
        $params[] = 'null=' . urlencode('Sign in');

        // /accounts/ServiceLoginBoxAuth
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/accounts/ServiceLoginBoxAuth");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, join('&', $params));
        $content = curl_exec($this->curl);
        preg_match("/<a href=\"([^\"]+)\"[^>]*>click here to continue<\/a>/", $content, $match);
        $next_url = $match[1];
        $next_url = str_replace('&amp;', '&', $next_url);

        // /accounts/CheckCookie
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_URL, $next_url);
        $content = curl_exec($this->curl);
        preg_match('/location\.replace\("(.+?)"\)/', $content, $match);
        $next_url = $match[1];
        $next_url = preg_replace_callback("/\\\\x(..)/i", create_function('$match', 'return chr(hexdec($match[1]));'), $next_url);

        // /accounts/SetSID
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_URL, $next_url);
        $content = curl_exec($this->curl);

        // did we login ?
        if (eregi("<a href=\"/adsense/signout\">",  $content)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Log out
     */
    function log_out(){
        curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/signout");
        curl_exec($this->curl);
    }

    /**
     * AdSense::parse()
     * Parses AdSense page and gets all stats
     * Returns associative array with collected data
     *
     * @param string $content AdSense page content
     * @return array
     */
    function parse($content){
        preg_match_all('/<td nowrap valign="top" style="text-align:right" class="">(.*?)<\/td>/', $content, $matches);
        return array(
            "impressions" => $matches[1][0],
            "clicks" => $matches[1][1],
            "ctr" => $matches[1][2],
            "ecpm" => $matches[1][3],
            "earnings" => $matches[1][4]
        );

    }


    /**
     * AdSense::today()
     * Gets AdSense data for the period: today
     * Returns associative array with collected data
     *
     * @return array
     */
    function today(){
        curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/report/overview?timePeriod=today");
        return $this->parse(curl_exec($this->curl));
    }


    /**
     * AdSense::yesterday()
     * Gets AdSense data for the period: yesterday
     * Returns associative array with collected data
     *
     * @return array
     */
    function yesterday(){
        curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/report/overview?timePeriod=yesterday");
        return $this->parse(curl_exec($this->curl));
    }


    /**
     * AdSense::last7days()
     * Gets AdSense data for the period: last7days
     * Returns associative array with collected data
     *
     * @return array
     */
    function last7days(){
        curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/report/overview?timePeriod=last7days");
        return $this->parse(curl_exec($this->curl));
    }


    /**
     * AdSense::lastmonth()
     * Gets AdSense data for the period: lastmonth
     * Returns associative array with collected data
     *
     * @return array
     */
    function lastmonth(){
        curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/report/overview?timePeriod=lastmonth");
        return $this->parse(curl_exec($this->curl));
    }


    /**
     * AdSense::thismonth()
     * Gets AdSense data for the period: thismonth
     * Returns associative array with collected data
     *
     * @return array
     */
    function thismonth(){
        curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/report/overview?timePeriod=thismonth");
        return $this->parse(curl_exec($this->curl));
    }


    /**
     * AdSense::sincelastpayment()
     * Gets AdSense data for the period: sincelastpayment
     * Returns associative array with collected data
     *
     * @return array
     */
    function sincelastpayment(){
        curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/report/overview?timePeriod=sincelastpayment");
        return $this->parse(curl_exec($this->curl));
    }

	/**
	 * Get current report id by report name.
	 * Helps to avoid problems with report editing and ids.
	 *
	 * @param string $name
	 * @return int
	 */
	function get_report_id_from_name($name){
		curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/report/overview");
		$page = curl_exec($this->curl);

		preg_match('~<a href=".*reportId=([0-9]+)">'.$name.'</a>~i', $page, $matches);

		if(empty($matches[1])) return false;

		return $matches[1];
	}

    /**
     * Get csv data from custom report
	 * Warning: if you will edit report at Google Adsense page, its id will change!
     *
     * @param int $id
     * @param string $encoding
     * @return string
     */
    function get_report_as_csv($report_id, $encoding = 'UTF-8'){
        curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/report/view-custom.do?reportId=$report_id&outputFormat=TSV_EXCEL");

        //By default report is in UTF-16LE
        return iconv('UTF-16', $encoding, curl_exec($this->curl));
    }

	/**
	 * Get report data as array. Uses html parser. A bit slower than get_report_as_csv.
	 * Warning: if you will edit report at Google Adsense page, its id will change!
	 *
	 * @param int $report_id
	 * @return array
	 */
    function report($report_id){
      $result = array();
      curl_setopt($this->curl, CURLOPT_URL, "https://www.google.com/adsense/report/view-custom.do?reportId=$report_id");
      $content = curl_exec($this->curl);
      if (preg_match('/var\s+reportTable\s+=\s+new\s+AsyncReportTable\(([^\)]+)\);/si', $content, $matches)) {
        $params = array();
        foreach (explode(",\n", $matches[1]) as $v) {
          $params[] = trim($v, " \t'\"\n\r");
        }
        curl_setopt($this->curl, CURLOPT_URL,
          'https://www.google.com/adsense/report/online-stored-reporttable?storedReportId=' .
          $params[0] . '&reportUri=' . str_replace('?', '%3F', str_replace('/', '%2F', str_replace('\x', '%', $params[2]))) .
          '&formId=' . $params[3] . '&title=' . str_replace('+', '%20', urlencode($params[1])) . '&waitTime0');
        do {
          $content = curl_exec($this->curl);
          if (empty($content)) {
            sleep(1);
          }
        } while (empty($content));
        if (preg_match_all('/<tr\s+class="(odd|even)\s+datarow">\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<\/tr>/si', $content, $matches)) {
            foreach ($matches[2] as $k => $v) {
              $result[$k]['channel'] = preg_replace('/<.+>/', '', $v);
              $result[$k]['impressions'] = $matches[3][$k];
              $result[$k]['clicks'] = $matches[4][$k];
              $result[$k]['ctr'] = $matches[5][$k];
              $result[$k]['ecpm'] = $matches[6][$k];
              $result[$k]['earnings'] = $matches[7][$k];
            }
        }
      }
      return $result;
    }

    function quick_report($url){
      $result = array();
      curl_setopt($this->curl, CURLOPT_URL, $url);
      $content = curl_exec($this->curl);
      if (preg_match('/var\s+reportTable\s+=\s+new\s+AsyncReportTable\(([^\)]+)\);/si', $content, $matches)) {
        $params = array();
        foreach (explode(",\n", $matches[1]) as $v) {
          $params[] = trim($v, " \t'\"\n\r");
        }
        $reportUri = preg_replace(
          array('/&/', '/=/', '/\?/', '/\//'),
          array('%26', '%3D', '%3F', '%2F'),
          $params[2]);
        $reportUri = str_replace('\x', '%', $reportUri);
        curl_setopt($this->curl, CURLOPT_URL,
          'https://www.google.com/adsense/report/online-stored-reporttable?storedReportId=' .
          $params[0] . '&reportUri=' . $reportUri .
          '&formId=' . $params[3] . '&title=' . str_replace('+', '%20', urlencode($params[1])) . '&waitTime0');
        $tr = 0;
        do {
          $content = curl_exec($this->curl);
          if (empty($content) or $content === true) {
            sleep(1);
            if (++$tr > 10) {
              echo "Can't get the report data!\n";
              die;
            }
          }
        } while (empty($content) or $content === true);
        if (preg_match_all('/<tr\s+class="(odd|even)\s+datarow">\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<td[^>]*>(.*?)<\/td>\s*' .
          '<\/tr>/si', $content, $matches)) {
            $day = 0;
            $n = 0;
            foreach ($matches[3] as $k => $v) {
              if ($day === 0) {
                $day = date('Y-m-d', strtotime($matches[2][$k]));
              }
              if (isset($matches[2][$k - 1]) && $matches[2][$k - 1] != $matches[2][$k]) {
                $day = date('Y-m-d', strtotime($matches[2][$k]));
                $n = 0;
              }
              $result[$day][$n]['channel'] = preg_replace('/\s*<[^>]+>\s*/', '', $v);
              $result[$day][$n]['impressions'] = $matches[4][$k];
              $result[$day][$n]['clicks'] = $matches[5][$k];
              $result[$day][$n]['ctr'] = $matches[6][$k];
              $result[$day][$n]['ecpm'] = $matches[7][$k];
              $result[$day][$n]['earnings'] = $matches[8][$k];
              $n++;
            }
        }
      }
      return $result;
    }
}
