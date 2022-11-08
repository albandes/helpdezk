<?php
/**
 * Created by PhpStorm.
 * User: rogerio.albandes
 * Date: 28/08/2019
 * Time: 10:04
 *
 *  https://mandrillapp.com/api/docs/messages.php.html
 */


class sendMail {

    public $sender;
    public $token;

    public function __construct($sender,$token=null) {
        $this->sender = $sender;
        $this->token = $token;
    }

    public function sendEmail($params){
        switch ($this->sender){
            case 'mandrill':
                $msg = $this->makeMessageMandrill($params);
                return $this->sendMandrill($msg);
                break;
            default:
                return $this->sendSMTP($params);
                break;
        }
    }

    function makeMessageMandrill($params){

        $arrAtt = array();
        foreach($params['attachments'] as $key=>$value){
            $bus = array(
                'type' => mime_content_type($value['filepath']),
                'name' => $value['filename'],
                'content' => base64_encode(file_get_contents($value['filepath']))
            );

            array_push($arrAtt,$bus);
        }

        $message = array(
            'html' => $params['body'],
            'subject' => $params['subject'],
            'from_email' => $params['sender'],
            'from_name' => $params['senderName'],
            'to' => $params['to'],
            'headers' => $params['extra_headers'],
            'important' => false,
            'track_opens' => null,
            'track_clicks' => null,
            'auto_text' => null,
            'auto_html' => null,
            'inline_css' => null,
            'url_strip_qs' => null,
            'preserve_recipients' => null,
            'view_content_link' => null,
            'tracking_domain' => null,
            'signing_domain' => null,
            'return_path_domain' => null,
            'merge' => true,
            'merge_language' => 'mailchimp',
            'global_merge_vars' => $params['global_merge_vars'],
            'merge_vars' => $params['merge_vars'],
            'tags' => $params['tags'],
            'google_analytics_domains' => $params['analytics_domains'],
            'google_analytics_campaign' => 'teste',
            'metadata' => $params['metadata'],
            'recipient_metadata' => $params['recipient_metadata'],
            'attachments' => $arrAtt,
            'images' => $params['images']
        );

        return $message;
    }

    function sendMandrill($message){
        $endPoint = 'https://mandrillapp.com/api/1.0/messages/send.json';
        $token = $this->token;
        $params = array(
            "key" => $token,
            "message" => $message
        );
        $headers = [
            "Content-Type: application/json"
        ];
        $ch = curl_init();
        $ch_options = [
            CURLOPT_URL => $endPoint,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST    => 1,
            CURLOPT_HEADER  => 0,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($params)
        ];
        curl_setopt_array($ch,$ch_options);
        $callback = curl_exec($ch);
        $result   = (($callback) ? json_decode($callback,true) : curl_error($ch));
        $aRet = array('status' => $result['status'],'result' => $result);
        return $aRet;
    }

    function sendSMTP($params){
        $path_parts = pathinfo(dirname(__FILE__));
        $cron_path = $path_parts['dirname'] ;
        $phpMailerDir = $cron_path . '/phpMailer/class.phpmailer.php';

        if (!file_exists($phpMailerDir)) {
            die ('ERROR: ' .$phpMailerDir . ' , does not exist  !!!!') ;
        }

        require_once($phpMailerDir);

        $mail = new phpmailer();
        //$mail->SMTPDebug = 4;

        $mail->CharSet = 'utf-8';
        $mail->From = $params['sender'];
        $mail->FromName = $params['sender_name'];

        $mail->Host = $params['server']['host'];
        if (isset($params['server']['port']) AND !empty($params['server']['port'])) {
            $mail->Port = $params['server']['port'];
        }

        $mail->Mailer = $params['server']['method'];
        $mail->SMTPAuth = $params['server']['auth'];
        if (strpos($params['server']['username'],'gmail') !== false) {
            $mail->SMTPSecure = "tls";
        }
        $mail->Username = $params['server']['username'];
        $mail->Password = $params['server']['password'];

        $mail->AltBody 	= "HTML";
        $mail->Subject 	= '=?UTF-8?B?'.base64_encode($params['subject']).'?=';

        foreach ($params['extra_header'] as $key=>$v){
            $mail->addCustomHeader($key . ':' . $v);
        }

        if(sizeof($params['attachments']) > 0){
            foreach($params['attachments'] as $key=>$value){
                $mail->AddAttachment($value['filepath'], $value['filename']);  // optional name
            }
        }

        $mail->AddAddress($params['to']);

        $mail->Body = $params['body'];

        if(!$mail->Send()){
            $aRet = array('status' => 'error','result' => array("message"=>"Error: ".$mail->ErrorInfo));
        }else{
            $aRet = array('status' => 'sent','result' => array("message"=>"Message sent."));
        }

        $mail->ClearAddresses();
        $mail->ClearAttachments();

        return $aRet;
    }




}