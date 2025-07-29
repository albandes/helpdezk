<?php

namespace App\src;

use App\src\appServices;
use Exception;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeWithLogoProvider;


class mfaServices
{

    public function __construct()
    {
    }

    /**
     * authorized
     *
     * en_us Get authorized account data
     * pt_br Obtem os dados da conta autorizada
     *
     * @return void
     */
    public function generateQRCode()
    {

        $idperson = $_SESSION['SES_COD_USUARIO'];
        $signatureDAO = new \App\modules\main\dao\mysql\signatureDAO();
        $secret = $signatureDAO->hasAuthenticatorSecret($idperson);
        $qrcode = null;
        if (!$secret) {
            $qrcodeProvider = new EndroidQrCodeWithLogoProvider();
            $tfa = new TwoFactorAuth('helpdezk auth',6,30,'sha512',$qrcodeProvider);
            $secret = $tfa->createSecret();
            $qrcode = $tfa->getQRCodeImageAsDataUri('user-'.$idperson,$secret);
        }

        return [
            'qrCode' => $tfa ? $qrcode : null,
            'secret' => $secret
        ];
    }

    public function checkAuthCode($code, $secret)
    {
        $qrcodeProvider = new EndroidQrCodeWithLogoProvider();
        $tfa = new TwoFactorAuth('helpdezk auth',6,30,'sha512',$qrcodeProvider);
        $isValid = $tfa->verifyCode($secret, $code);
        return [
            'isValid' => $isValid
        ];
    }
}
