<?php
// @codingStandardsIgnoreStart
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'qrcode'.DS.'src'.DS.'Exceptions'.DS.'DataDoesntExistsException.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'qrcode'.DS.'src'.DS.'Exceptions'.DS.'FreeTypeLibraryMissingException.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'qrcode'.DS.'src'.DS.'Exceptions'.DS.'ImageFunctionFailedException.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'qrcode'.DS.'src'.DS.'Exceptions'.DS.'VersionTooLargeException.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'qrcode'.DS.'src'.DS.'Exceptions'.DS.'ImageSizeTooLargeException.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'qrcode'.DS.'src'.DS.'Exceptions'.DS.'ImageFunctionUnknownException.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'qrcode'.DS.'src'.DS.'QrCode.php');

require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'base32'.DS.'Base32.php');

require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'assert'.DS.'Assertion.php');

require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'otphp'.DS.'OTPInterface.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'otphp'.DS.'TOTPInterface.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'otphp'.DS.'ParameterTrait.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'otphp'.DS.'OTP.php');
require_once(Mage::getModuleDir('', 'MSP_TwoFactorAuth').DS.'libs'.DS.'otphp'.DS.'TOTP.php');
// @codingStandardsIgnoreEnd

class MSP_TwoFactorAuth_Helper_Totp extends Mage_Core_Helper_Abstract
{
    /**
     * Get session TOTP
     * @return \OTPHP\TOTP
     */
    public function getTotp()
    {
        $user = Mage::getSingleton('admin/session')->getUser();

        return new OTPHP\TOTP(
            $user->getEmail(),
            $user->getMspTfaSecret()
        );
    }

    /**
     * Get two factor auth provisioning URL
     * @return string
     */
    public function getProvisioningUrl()
    {
        $user = Mage::getSingleton('admin/session')->getUser();

        if (!$user->getMspTfaSecret()) {
            $secret = $this->_generateSecret();
            $user
                ->setMspTfaSecret($secret)
                ->save();
        }

        // @codingStandardsIgnoreStart
        $issuer = parse_url(Mage::getBaseUrl(), PHP_URL_HOST);
        // @codingStandardsIgnoreEnd

        $totp = $this->getTotp();
        $totp->setIssuer($issuer);

        return $totp->getProvisioningUri(true);
    }

    /**
     * Render QR code image
     */
    public function renderQrCode()
    {
        $qrCode = new Endroid\QrCode\QrCode();
        $qrCode
            ->setText($this->getProvisioningUrl())
            ->setSize(300)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16);

        $qrCode->render(null, 'png');
    }

    /**
     * Return true on token validation
     * @param $token
     * @return bool
     */
    public function verify($token)
    {
        $totp = $this->getTotp();
        $totp->now();

        return $totp->verify($token);
    }

    /**
     * Generate TFA secret
     * @return string
     */
    protected function _generateSecret()
    {
        $secret = mcrypt_create_iv(128, MCRYPT_RAND);
        return Base32\Base32::encode($secret);
    }
}
