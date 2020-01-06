<?php

/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 05/01/2020
 * Time: 14:15
 */

  class trello {
      private $key;
      private $secret;
      private $token;

      public function __construct ($key, $secret, $token) {
          $this->key = $key;
          $this->secret = $secret;
          $this->token = $token;
      }
       public function getCards($listId)
      {
          $data = $this->request('GET', ('/1/lists/'.$listId.'/cards?fields=id,name'));
          return $data;
      }
      public function getLists($boardId)
      {
          $data = $this->request('GET', ('/1/boards/'.$boardId.'/lists?cards=open&card_fields=id,name&filter=open&fields=name'));
          return $data;

      }
      public function getBoards()
      {
          $data = $this->request('GET', ('/1/members/me/boards/?fields=name'));
          return $data;
      }

      public function getUserId($username)
      {

          $data = $this->request('GET', ('/1/members/' . $username . '?fields=id'));
          return $data['id'] ;
      }

      public function request ($type, $request, $args = false) {
          if (!$args) {
              $args = array();
          } elseif (!is_array($args)) {
              $args = array($args);
          }

          if (strstr($request, '?')) {
              $url = 'https://api.trello.com' . $request . '&key=' . $this->key . '&token=' . $this->token;
          } else {
              $url = 'https://api.trello.com' . $request . '?key=' . $this->key . '&token=' . $this->token;
          }

          $c = curl_init();
          curl_setopt($c, CURLOPT_HEADER, 0);
          curl_setopt($c, CURLOPT_VERBOSE, 0);
          curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($c, CURLOPT_URL, $url);

          if (count($args)) curl_setopt($c, CURLOPT_POSTFIELDS , http_build_query($args));

          switch ($type) {
              case 'POST':
                  curl_setopt($c, CURLOPT_POST, 1);
                  break;
              case 'GET':
                  curl_setopt($c, CURLOPT_HTTPGET, 1);
                  break;
              default:
                  curl_setopt($c, CURLOPT_CUSTOMREQUEST, $type);
          }

          $data = curl_exec($c);

          if(!curl_errno($c)) {
              $info = curl_getinfo($c);
              if ($info['http_code'] == 401) {
                  $message = 'Got error, http code: ' . $info['http_code'] . ' - ' . $this->getHttpErrorCode($info['http_code']) ;
                  $arrayRet = array('success' => false, 'message' => $message, 'return' => '');
              } else {
                  $arrayRet = array('success' => true, 'message' => '', 'return' => json_decode($data,true));
              }

          } else {
                $message = 'Error making trello request, curl error: ' . $this->getCurlErrorCode(curl_error($c));
                $arrayRet = array('success' => false, 'message' => $message, 'return' => '');
          }

          curl_close($c);

          return $arrayRet;

      }

      public function getHttpErrorCode($code)
      {
          $http_codes = array(
              100 => 'Continue',
              101 => 'Switching Protocols',
              102 => 'Processing',
              103 => 'Checkpoint',
              200 => 'OK',
              201 => 'Created',
              202 => 'Accepted',
              203 => 'Non-Authoritative Information',
              204 => 'No Content',
              205 => 'Reset Content',
              206 => 'Partial Content',
              207 => 'Multi-Status',
              300 => 'Multiple Choices',
              301 => 'Moved Permanently',
              302 => 'Found',
              303 => 'See Other',
              304 => 'Not Modified',
              305 => 'Use Proxy',
              306 => 'Switch Proxy',
              307 => 'Temporary Redirect',
              400 => 'Bad Request',
              401 => 'Unauthorized',
              402 => 'Payment Required',
              403 => 'Forbidden',
              404 => 'Not Found',
              405 => 'Method Not Allowed',
              406 => 'Not Acceptable',
              407 => 'Proxy Authentication Required',
              408 => 'Request Timeout',
              409 => 'Conflict',
              410 => 'Gone',
              411 => 'Length Required',
              412 => 'Precondition Failed',
              413 => 'Request Entity Too Large',
              414 => 'Request-URI Too Long',
              415 => 'Unsupported Media Type',
              416 => 'Requested Range Not Satisfiable',
              417 => 'Expectation Failed',
              418 => 'I\'m a teapot',
              422 => 'Unprocessable Entity',
              423 => 'Locked',
              424 => 'Failed Dependency',
              425 => 'Unordered Collection',
              426 => 'Upgrade Required',
              449 => 'Retry With',
              450 => 'Blocked by Windows Parental Controls',
              500 => 'Internal Server Error',
              501 => 'Not Implemented',
              502 => 'Bad Gateway',
              503 => 'Service Unavailable',
              504 => 'Gateway Timeout',
              505 => 'HTTP Version Not Supported',
              506 => 'Variant Also Negotiates',
              507 => 'Insufficient Storage',
              509 => 'Bandwidth Limit Exceeded',
              510 => 'Not Extended'
          );
          return $http_codes[$code];
      }

      public function getCurlErrorCode($code)
      {
          $curl_error_codes = array (
              0 => 'CURLE_OK',
              1 => 'CURLE_UNSUPPORTED_PROTOCOL',
              2 => 'CURLE_FAILED_INIT',
              3 => 'CURLE_URL_MALFORMAT',
              4 => 'CURLE_NOT_BUILT_IN',
              5 => 'CURLE_COULDNT_RESOLVE_PROXY',
              6 => 'CURLE_COULDNT_RESOLVE_HOST',
              7 => 'CURLE_COULDNT_CONNECT',
              8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
              9 => 'CURLE_REMOTE_ACCESS_DENIED',
              10 => 'CURLE_FTP_ACCEPT_FAILED',
              11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
              12 => 'CURLE_FTP_ACCEPT_TIMEOUT',
              13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
              14 => 'CURLE_FTP_WEIRD_227_FORMAT',
              15 => 'CURLE_FTP_CANT_GET_HOST',
              17 => 'CURLE_FTP_COULDNT_SET_TYPE',
              18 => 'CURLE_PARTIAL_FILE',
              19 => 'CURLE_FTP_COULDNT_RETR_FILE',
              21 => 'CURLE_QUOTE_ERROR',
              22 => 'CURLE_HTTP_RETURNED_ERROR',
              23 => 'CURLE_WRITE_ERROR',
              25 => 'CURLE_UPLOAD_FAILED',
              26 => 'CURLE_READ_ERROR',
              27 => 'CURLE_OUT_OF_MEMORY',
              28 => 'CURLE_OPERATION_TIMEDOUT',
              30 => 'CURLE_FTP_PORT_FAILED',
              31 => 'CURLE_FTP_COULDNT_USE_REST',
              33 => 'CURLE_RANGE_ERROR',
              34 => 'CURLE_HTTP_POST_ERROR',
              35 => 'CURLE_SSL_CONNECT_ERROR',
              36 => 'CURLE_BAD_DOWNLOAD_RESUME',
              37 => 'CURLE_FILE_COULDNT_READ_FILE',
              38 => 'CURLE_LDAP_CANNOT_BIND',
              39 => 'CURLE_LDAP_SEARCH_FAILED',
              41 => 'CURLE_FUNCTION_NOT_FOUND',
              42 => 'CURLE_ABORTED_BY_CALLBACK',
              43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
              45 => 'CURLE_INTERFACE_FAILED',
              47 => 'CURLE_TOO_MANY_REDIRECTS',
              48 => 'CURLE_UNKNOWN_OPTION',
              49 => 'CURLE_TELNET_OPTION_SYNTAX',
              51 => 'CURLE_PEER_FAILED_VERIFICATION',
              52 => 'CURLE_GOT_NOTHING',
              53 => 'CURLE_SSL_ENGINE_NOTFOUND',
              54 => 'CURLE_SSL_ENGINE_SETFAILED',
              55 => 'CURLE_SEND_ERROR',
              56 => 'CURLE_RECV_ERROR',
              58 => 'CURLE_SSL_CERTPROBLEM',
              59 => 'CURLE_SSL_CIPHER',
              60 => 'CURLE_SSL_CACERT',
              61 => 'CURLE_BAD_CONTENT_ENCODING',
              62 => 'CURLE_LDAP_INVALID_URL',
              63 => 'CURLE_FILESIZE_EXCEEDED',
              64 => 'CURLE_USE_SSL_FAILED',
              65 => 'CURLE_SEND_FAIL_REWIND',
              66 => 'CURLE_SSL_ENGINE_INITFAILED',
              67 => 'CURLE_LOGIN_DENIED',
              68 => 'CURLE_TFTP_NOTFOUND',
              69 => 'CURLE_TFTP_PERM',
              70 => 'CURLE_REMOTE_DISK_FULL',
              71 => 'CURLE_TFTP_ILLEGAL',
              72 => 'CURLE_TFTP_UNKNOWNID',
              73 => 'CURLE_REMOTE_FILE_EXISTS',
              74 => 'CURLE_TFTP_NOSUCHUSER',
              75 => 'CURLE_CONV_FAILED',
              76 => 'CURLE_CONV_REQD',
              77 => 'CURLE_SSL_CACERT_BADFILE',
              78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
              79 => 'CURLE_SSH',
              80 => 'CURLE_SSL_SHUTDOWN_FAILED',
              81 => 'CURLE_AGAIN',
              82 => 'CURLE_SSL_CRL_BADFILE',
              83 => 'CURLE_SSL_ISSUER_ERROR',
              84 => 'CURLE_FTP_PRET_FAILED',
              85 => 'CURLE_RTSP_CSEQ_ERROR',
              86 => 'CURLE_RTSP_SESSION_ERROR',
              87 => 'CURLE_FTP_BAD_FILE_LIST',
              88 => 'CURLE_CHUNK_FAILED',
              89 => 'CURLE_NO_CONNECTION_AVAILABLE'
          );
          return $curl_error_codes[$code];
      }
  }
?>