<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LifeCare ERP - Modern Cryptographic Helper
 * Provides authenticated AES-256 encryption and decryption for sensitive data
 */

if (!function_exists('app_encrypt')) {
    /**
     * Encrypt plaintext string using AES-256-CBC with HMAC-SHA256 authentication
     *
     * @param string $data Plain text to encrypt
     * @param string|null $custom_key Optional custom key (defaults to config encryption_key)
     * @return string Base64-encoded encrypted payload
     */
    function app_encrypt($data, $custom_key = null) {
        if ($data === null || $data === '') {
            return '';
        }

        $ci =& get_instance();
        $key = $custom_key !== null ? $custom_key : $ci->config->item('encryption_key');
        if (empty($key)) {
            $key = 'LifeCareHospitalERP_SecureKey_2026_9e8b7c6d5a4f3e2b1a0c9d8e7f6a5b4c';
        }

        $derived_key = hash('sha256', $key, true);
        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
        $iv = openssl_random_pseudo_bytes($iv_length);

        $ciphertext = openssl_encrypt((string)$data, 'AES-256-CBC', $derived_key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $iv . $ciphertext, $derived_key, true);

        return base64_encode($iv . $hmac . $ciphertext);
    }
}

if (!function_exists('app_decrypt')) {
    /**
     * Decrypt base64-encoded ciphertext payload and verify integrity
     *
     * @param string $payload Base64 encrypted payload
     * @param string|null $custom_key Optional custom key
     * @return string|false Decrypted plaintext or false on failure/tampering
     */
    function app_decrypt($payload, $custom_key = null) {
        if (empty($payload)) {
            return '';
        }

        $raw = base64_decode($payload, true);
        if ($raw === false) {
            return false;
        }

        $ci =& get_instance();
        $key = $custom_key !== null ? $custom_key : $ci->config->item('encryption_key');
        if (empty($key)) {
            $key = 'LifeCareHospitalERP_SecureKey_2026_9e8b7c6d5a4f3e2b1a0c9d8e7f6a5b4c';
        }

        $derived_key = hash('sha256', $key, true);
        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
        $hmac_length = 32; // sha256 raw bytes

        if (strlen($raw) < ($iv_length + $hmac_length)) {
            return false;
        }

        $iv = substr($raw, 0, $iv_length);
        $hmac = substr($raw, $iv_length, $hmac_length);
        $ciphertext = substr($raw, $iv_length + $hmac_length);

        $calculated_hmac = hash_hmac('sha256', $iv . $ciphertext, $derived_key, true);
        if (!hash_equals($hmac, $calculated_hmac)) {
            return false; // Integrity check failed / tampered
        }

        $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $derived_key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }
}
