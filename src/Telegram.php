<?php
/**
 * Отправка сообщений в Телеграм бот
 *
 * @author Yuri Frantsevich (FYN)
 * Date: 20/08/2011
 * Time: 08:46
 * @version 1.0.1
 * @copyright 2021
 */

namespace FYN;

use FYN\Base;
use FYN\NetContent;

class Telegram {

    /**
     * Токен от телеграмм бота
     * @var string
     */
    private $telegram_token = '';

    /**
     * ID чата в телеграмм
     * ИД чата telegram получаем через запрос https://api.telegram.org/bot{token}/getUpdates
     * @var integer
     */
    private $telegram_id = 0;

    /**
     * Класс получения данных из интернета
     * @var \FYN\NetContent
     */
    public $net;

    /**
     * Включить | выключить режим отладки
     * всё записываем в файл
     * @var bool
     */
    private $debug = true;

    /**
     * Имя файла в который сохраняется лог
     * @var string
     */
    private $log_file = 'telegram.log';

    /**
     * Лог событий
     * @var array
     */
    private $logs = array();

    /**
     * Telegram constructor.
     * @param \FYN\NetContent $net
     */
    public function __construct(NetContent $net = null) {
        if ($net === null) $net = new \FYN\NetContent();
        $this->net = $net;
    }

    /**
     * Влючение отладочных функций класса
     * @param bool $debug
     */
    public function setDebug ($debug = false) {
        $this->debug = $debug;
    }

    /**
     * Отправка сообщения в Телеграм Бот
     * @param $message - сообщение
     * @param int $token - ключ к Теллеграм
     * @param int $chat_id - идентификатор чата
     * @return bool|false|string
     */
    public function sendToTelegram ($message, $token = 0, $chat_id = 0) {
        if ($this->debug) $this->logs[] = "Function sendToTelegram started";
        if (!$token) $token = $this->telegram_token;
        if (!$chat_id) $chat_id = $this->telegram_id;
        if ($this->debug) $this->logs[] = "Telegram token: ".$token;
        if ($this->debug) $this->logs[] = "Telegram Chat ID: ".$chat_id;
        if (!$token || !$chat_id) {
            $this->logs[] = "Wrong Telegram parameters!";
            if (!$token) $this->logs[] = "Token not defined!";
            else $this->logs[] = "Chat ID not defined!";
            return false;
        }
        $message = Base::convertLine($message);
        if ($this->debug) $this->logs[] = "Telegram message: ".$message;
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($message);
        if ($this->debug) $this->logs[] = "Telegram URL: ".$url;
        $t_bot = json_decode($this->net->getContent($url, 5), true);
        if (!isset($t_bot['ok'])) {
            $t_bot = false;
            $this->logs[] = "Telegram SEND ERROR!";
        }
        elseif (isset($t_bot['error_code']) && $t_bot['error_code']) {
            $this->logs[] = $t_bot['error_code'].": ".$t_bot['description'];
        }
        if ($this->debug) $this->logs[] = "Telegram send status: ".$t_bot;
        return $t_bot;
    }

    /**
     * Возвращает логи
     * @return array
     */
    public function getLogs () {
        $return['log'] = $this->logs;
        $return['file'] = $this->log_file;
        return $return;
    }
}