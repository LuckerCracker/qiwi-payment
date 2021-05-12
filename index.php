<?php
define('BASEPATH', true);

require_once __DIR__ . '/Controllers/Payment_Controller.php';

/**
 * Тип запроса
 * create - Создать и получить форму с оплатой
 * callback - Исользуется для вызова callback'a qiwi
 */
$type = $_GET['type'];

/**
 * Ник игрока
 */
$account = $_POST['account'];

/**
 * Количество денег
 */
$amount = $_POST['amount'];

/**
 * Проверяем, на пустой запрос
 */
if( isset($type) )
{
    /**
    * Берём метод по типу запроса
    */
    switch($type) 
    {
        case 'create':
            /**
            * Проверяем, на пустые запросы
            */
            if( isset($account) && isset($amount) )
            {
                /**
                * Генерируем и редиректим пользователя на форму оплаты
                */
                header('Location: ' . \Payment_Controller::getInstance()->getPaymentForm($account, $amount));
            }
        break;
        case 'callback':
            /**
            * Получаем и обрабатываем callback от QIWI
            * Возвращаем true после успешной записи в бд
            */
            exit(\Payment_Controller::getInstance()->paymentCallBack($_SERVER, json_decode(file_get_contents('php://input'), true)) !== true ? 'NO' : 'OK');
        break;
        default:
        break;
    }
}
?>