<?php
defined('BASEPATH') OR exit('No direct script access allowed');

final class Config 
{

    /**
     * Приватный ключ 
     */
    const PRIVATE_KEY = '';

    /** 
     * Публичный ключ 
     */
    const PUBLIC_KEY = '';

    /** Данные от базы данных */
    const DATABASE = [
        'host'     => 'lucker5r.beget.tech',
        'login' => 'lucker5r_qiwi',
        'password' => '',
        'name'     => 'lucker5r_qiwi',
        'port'     => 3306
    ];

    /**
     * Логи платежей после успешной оплаты 
     * (Используется для исключения повторных начислений на баланс)
     */
    const PAYMENTS = [
        'table'   => 'qiwi_payments',
        'engine'  => 'InnoDB',
        'charset' => 'utf8',
        /**
         * Колонки не менять
         * или меняй Models/Payment_Model.php
         * вместе с ними
         */
        'colums'  => [
            'billId'  => 'varchar(36)',
            'account' => 'varchar(18)',
            'amount'  => 'int',
            'date'    => 'datetime'
        ]
    ];

    /**
     * Колонка с деньгами пользователей
     */
    const MONEY_COLUMN = 'money';

    /** Движок сайта (Делал поддержку только для dle) */
    const ENGINE = 'dle';

    /**
     * Ссылка, на которую будет 
     * перенаправлен пользователь 
     * после успешной оплаты
     */
    const SUCCESS_URL = 'http://site.ru';

    /**
     * Комментарий платежа на вашей кошелке 
     * %account% - Ник игрока
     */
    const COMMENT = 'Платёж от аккаунта %account%';

    /**
     * Код от вашей формы
     */
    const THEME_CODE = '';

    /**
     * Ссылка для создания формы оплаты
     */
    const CREATE_URL = 'https://oplata.qiwi.com/create';

    /**
     * Способы перевода:
     * qw - QIWI Кошелек
     * card - банковская карта
     * Можно указать через запятую: qw,card
     */
    const PAY_SOURCES_FILTER = 'qw';

    /**
     * Количество дней, в течении которых будет
     * действовать платёж (Мб оно не работает)
     */
    const LIFE_TIME = 1;
    
}

?>