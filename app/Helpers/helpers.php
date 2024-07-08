<?php

use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;

if (!function_exists('notifyValidation')) {
    function notifyValidation($message, $body = '', $type = 'danger', $arg = 'admin')
    {
        Notification::make()
            ->title($message)
            ->body($body)
            ->{$type}()
            ->send();
        if (in_array($type, ['danger', 'warning']) && $arg == 'admin') throw new Halt();
    }
}

if (!function_exists('formattedDate')) {
    function formattedDate($data, $arg = IntlDateFormatter::FULL): string
    {
        setlocale(LC_TIME, 'id_ID');
        $dateTimestamp = strtotime($data);
        $dateFormatter = new IntlDateFormatter('id_ID', $arg, IntlDateFormatter::NONE);
        return $dateFormatter->format($dateTimestamp);
    }
}

if (!function_exists('aes_encrypt')) {
    function aes_encrypt($data, $password)
    {
        $method = 'aes-256-cbc'; // Encryption method
        $key = hash('sha256', $password, true); // Generate a 256-bit key from the password
        $iv = openssl_random_pseudo_bytes(16); // Generate a random initialization vector

        $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted); // Return the IV and the encrypted data combined, encoded in base64
    }
}

if (!function_exists('aes_decrypt')) {
    function aes_decrypt($data, $password)
    {
        $method = 'aes-256-cbc'; // Encryption method
        $key = hash('sha256', $password, true); // Generate a 256-bit key from the password

        $data = base64_decode($data);
        $iv = substr($data, 0, 16); // Extract the initialization vector from the data
        $encrypted = substr($data, 16); // Extract the encrypted data

        return openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv);
    }
}
