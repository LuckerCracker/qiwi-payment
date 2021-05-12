<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/../Helpers/Payment_Helper.php';
require_once __DIR__ . '/../Config.php';
require_once __DIR__ . '/MySQL_Model.php';

class Payment_Model extends MySQL_Model 
{

    public function __construct(){
        parent::__construct(Config::DATABASE);
    }

    /**
     * Получает платёж по billId
     */
    public function isPaymentExists($id)
    {
        return parent::get(Config::PAYMENTS['table'], '*', ['billId' => $id]) ? true : false;
    }

    /**
     * Создаёт платёж
     */
    public function createPayment($billId, $account, $amount, $date)
    {
        return parent::insert(Config::PAYMENTS['table'], [
            'billId' => $billId,
            'account' => $account,
            'amount' => $amount,
            'date' => $date
        ]);
    }

    public function isUserExists($account)
    {
        switch(Config::ENGINE){
            case 'dle':
                return parent::get('dle_users', '*', ['name' => $account]) ? true : false;
            default:
            break;
        }
        return false;
    }

    /**
     * Обновляет количество денег
     */
    public function updateAmount($account, $amount)
    {
        switch(Config::ENGINE){
            case 'dle':
                return parent::update('dle_users', [Config::MONEY_COLUMN => [Config::MONEY_COLUMN, '+', $amount]], ['name' => $account]);
            default:
            break;
        }
        return false;
    }
}

?>