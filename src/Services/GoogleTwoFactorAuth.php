<?php

namespace GP\Support\Services;

use GP\Support\Models\GaAuth;
use PHPGangsta_GoogleAuthenticator;

class GoogleTwoFactorAuth
{
    protected $secret;

    protected $qrCode;

    protected $backupCodes;

    const NAME = 'Burger Prints CMS';

    protected $gaAuthModel;

    protected $googleAuthenticator;

    /**
     * GoogleTwoFactorAuth
     *
     * @param GaAuth $gaAuthModel
     * @param PHPGangsta_GoogleAuthenticator $googleAuthenticator
     */
    public function __construct(GaAuth $gaAuthModel, PHPGangsta_GoogleAuthenticator $googleAuthenticator)
    {
        $this->gaAuthModel = $gaAuthModel;
        $this->googleAuthenticator = $googleAuthenticator;
    }

    /**
     * Verify Code
     *
     * @param string $secret
     * @param string $code
     *
     * @return bool
     */
    public function verifyCode($secret, $code)
    {
        $checkResult = $this->googleAuthenticator->verifyCode($secret, $code, 2);

        if (!$checkResult) {
            throw new \RuntimeException('Invalid two-factor code');
        }

        return true;
    }

    /**
     * Setup
     *
     * @param string $name
     *
     * @return void
     */
    public function setup($name = null)
    {
        $name = $name === null ? self::NAME : $name . '@' . self::NAME;
        $this->secret = $this->googleAuthenticator->createSecret();
        $this->qrCode = $this->googleAuthenticator->getQRCodeGoogleUrl($name, $this->secret);
        $this->generateBackupCodes();

        return $this;
    }

    /**
     * Generate Backup Codes
     *
     * @return void
     */
    public function generateBackupCodes()
    {
        $aCodes = [];
        for ($index = 9; $index > 0; $index--) {
            $aCodes[] = \rand(100000000, 900000000);
        }
        $this->backupCodes = $aCodes;
        return $this;
    }

    /**
     * Get Backup Codes
     *
     * @return string
     */
    public function getBackupCodes()
    {
        return $this->backupCodes;
    }

    /**
     * Get Secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Get QrCode
     *
     * @return string
     */
    public function getQrCode()
    {
        return $this->qrCode;
    }

    /**
     * Undocumented function
     *
     * @param uuid $userId
     * @param char $secret
     * @param char $keys
     *
     * @return GaAuth
     */
    public function store($userId, $secret, $keys)
    {
        $data = [
            'user_id' => $userId,
            'secret' => $secret,
            'backup_code' => $keys
        ];

        return $this->gaAuthModel->updateOrCreate(['user_id' => $userId], $data);
    }
}
