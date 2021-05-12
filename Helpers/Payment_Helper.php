<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_Helper 
{

    /**
     * Генерирует billId
     * для QIWI API
     */
    public static function generateId()
    {
        $bytes = '';
        for ($i = 1; $i <= 16; $i++) {
            $bytes .= chr(mt_rand(0, 255));
        }

        $hash = bin2hex($bytes);

        return sprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            str_pad(dechex(hexdec(substr($hash, 12, 4)) & 0x0fff & ~(0xf000) | 0x4000), 4, '0', STR_PAD_LEFT),
            str_pad(dechex(hexdec(substr($hash, 16, 2)) & 0x3f & ~(0xc0) | 0x80), 2, '0', STR_PAD_LEFT),
            substr($hash, 18, 2),
            substr($hash, 20, 12)
        );

    }

    /**
     * Округляет сумму
     * для QIWI API
     */
    public static function normalizeAmount($amount=0)
    {
        return intval($amount);

    }

    /**
     * Возвращает отформатированую дату
     * для QIWI API
     */
    public static function normalizeDate($date)
    {
        return $date->format('Y-m-d\TH:i:sP');

    }

    /**
     * Возвращает отформатированую дату 
     * по количеству дней
     * для QIWI API
     */
    public static function getLifetimeByDay($days=45)
    {
        $dateTime = new \DateTime();
        return static::normalizeDate($dateTime->modify('+'.max(1, $days).' days'));
    }

    /**
     * Сравнивает сигнатуры
     */
    public static function isValidSignature($signature, $data)
    {   
        if( empty($data) )
        {
            return false;
        }

        if( !isset($signature) )
        {
            return false;
        }

        $data = array_replace_recursive(
            [
                'bill' => [
                    'billId' => null,
                    'amount' => [
                        'value'    => null,
                        'currency' => null,
                    ],
                    'siteId' => null,
                    'status' => ['value' => null],
                ],
            ],
            $data
        );

        $processedData = [
            'billId'          => $data['bill']['billId'],
            'amount.value'    => $data['bill']['amount']['value'],
            'amount.currency' => $data['bill']['amount']['currency'],
            'siteId'          => $data['bill']['siteId'],
            'status'          => $data['bill']['status']['value'],
        ];

        if( $processedData['status'] !== 'PAID')
        {
            return false;
        }

        ksort($processedData);

        $invoice_parameters = join('|', $processedData);
        $hash = hash_hmac('sha256', $invoice_parameters, Config::PRIVATE_KEY);

        return $hash === $signature;
    }
}

?>