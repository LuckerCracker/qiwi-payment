<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/../Models/Payment_Model.php';
require_once __DIR__ . '/../Config.php';

class Payment_Controller 
{
    /**
     * @var /Models/Payment_Model.php
     */
    protected $paymentModel;
    private static $instance = null;

    public function __construct()
    {
        $this->paymentModel = new Payment_Model();
    }

    /**
     * Возвращает текщий класс
     */
    public static function getInstance(): Payment_Controller
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Создаёт и возвращает форму оплаты
     */
    public function getPaymentForm($account, $amount)
    {
        $billId = $this->generateId();
        $amount = \Payment_Helper::normalizeAmount($amount);
        $lifetime = \Payment_Helper::getLifetimeByDay(Config::LIFE_TIME);

        $payUrlData = [
            'amount'       => $amount,
            'account'      => $account,
            'publicKey'    => Config::PUBLIC_KEY,
            'billId'       => $billId,
            'successUrl'   => Config::SUCCESS_URL,
            'comment'      => str_replace('%account%', $account, Config::COMMENT),
            'lifetime'     =>  $lifetime,
            'customFields' => [
                'paySourcesFilter' => Config::PAY_SOURCES_FILTER,
                'themeCode'        => Config::THEME_CODE
            ],
        ];
        return CONFIG::CREATE_URL . '?' . http_build_query($payUrlData, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Обрабатывет callback с QIWI
     */
    public function paymentCallBack($headers, $data)
    {
        if(Payment_Helper::isValidSignature($headers['HTTP_X_API_SIGNATURE_SHA256'], $data))
        {
            $billId = $data['bill']['billId'];

            if( $this->paymentModel->isPaymentExists($billId) ){
                return true;
            }

            $account = $data['bill']['customer']['account'];
            $amount = \Payment_Helper::normalizeAmount($data['bill']['amount']['value']);
            $date = $data['bill']['status']['changedDateTime'];

            if( !$this->paymentModel->createPayment($billId, $account, $amount, $date) ){
                return false;
            }

            if( !$this->paymentModel->isUserExists($account) ){
                return true;
            }

            return $this->paymentModel->updateAmount($account, $amount);
        }
        
        return false;
    }

    /**
     * Проверяет и генерирует billId
     * для платежа
     */
    private function generateId()
    {
        $billId = \Payment_Helper::generateId();
        return !$this->paymentModel->isPaymentExists($billId) ? $billId : $this->generateId();
    }
}

?>