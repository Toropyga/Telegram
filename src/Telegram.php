<?php
/**
 * Отправка сообщений в Телеграм бот
 *
 * @author Yuri Frantsevich (FYN)
 * Date: 20/08/2011
 * Time: 08:46
 * @version 2.2.0
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
     * ID чата в телеграмм (кому отправляем сообщение)
     * ИД чата telegram получаем через запрос https://api.telegram.org/bot{token}/getUpdates
     * @var integer
     */
    private $telegram_id = 0;

    /**
     * Метод отправки сообщений в Телеграм
     * ('sendMessage', 'sendPhoto', 'sendDocument', 'sendVideo', 'sendAudio', 'sendVoice', 'sendAnimation')
     * @var string
     */
    private $method = 'sendMessage';

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
        if (defined("TELEGRAM_TOKEN")) $this->telegram_token = TELEGRAM_TOKEN;
        if (defined("TELEGRAM_ID")) $this->telegram_id = TELEGRAM_ID;
        if (defined("TELEGRAM_DEBUG")) $this->debug = TELEGRAM_DEBUG;
        if (defined("TELEGRAM_LOG_NAME")) $this->log_file = TELEGRAM_LOG_NAME;
        $this->net = $net;
        $this->net->setType('CURL');
        $this->net->setMethod('POST');
    }

    /**
     * Влючение отладочных функций класса
     * @param bool $debug
     */
    public function setDebug ($debug = false) {
        $this->debug = $debug;
        if ($this->debug) $this->logs[] = "Telegram debug: ON";
    }

    /**
     * Установка метода отправки сообщений в Телеграм
     * Поддерживаемые методы:
     *   'sendMessage' - отправка текстового сообщения,
     *   'sendPhoto' - отправка фотографии или изображения,
     *   'sendDocument' - отправка документа как вложения,
     *   'sendVideo' - отправка видио,
     *   'sendAudio' - отправка звукового файла,
     *   'sendVoice' - отправка голосового сообщения,
     *   'sendAnimation' - отправка анимированного изображения.
     * @param string $method
     */
    public function setMethod ($method = 'sendMessage') {
        $available_methods = array('sendMessage', 'sendPhoto', 'sendDocument', 'sendVideo', 'sendAudio', 'sendVoice', 'sendAnimation');
        if (in_array($method, $available_methods)) {
            if ($this->debug) $this->logs[] = "Telegram method set to ".$method;
            $this->method = $method;
        }
    }

    /**
     * Установка токена
     * @param string $token - токен от телеграмм бота
     */
    public function setToken ($token) {
        $this->telegram_token = $token;
        if ($this->debug) $this->logs[] = "Telegram set token: ".$token;
    }

    /**
     * Установка ID чата в телеграм (кому отправляем сообщение)
     * @param integer $chatId - ID чата в телеграм
     */
    public function setChatID ($chat_id) {
        $this->telegram_id = $chat_id;
        if ($this->debug) $this->logs[] = "Telegram set Chat ID: ".$chat_id;
    }

    /**
     * Установка параметров сервера обработки запросов (Webhook-сервер)
     * @param string $webhook_url - адрес Webhook-сервера
     * @param string $certificate - путь к персональному сертификату (необязательный параметр)
     * @param string $ip_address - IP-адрес , который будет использоваться для отправки запросов Webhook вместо IP-адреса, полученного через DNS (необязательный параметр)
     * @param int $max_connections - максимальное количество одновременных подключений к Webhook-серверу [1-100] (необязательный параметр)
     * @return bool|mixed
     */
    public function setWebhook ($webhook_url, $certificate = '', $ip_address = '', $max_connections = 0) {
        if (!$webhook_url) {
            $this->logs[] = "Wrong Telegram parameters!";
            $this->logs[] = "Webhook url not defined!";
            return false;
        }
        $url = "https://api.telegram.org/bot" . $this->telegram_token . "/setWebhook";
        if ($this->debug) $this->logs[] = "Telegram URL: ".$url;
        $file_curl = [];
        if ($certificate) {
            if (file_exists($certificate)) {
                if ($this->debug) $this->logs[] = "Telegram certificate: " . $certificate;
                $file_curl = new \CURLFile(realpath($certificate));
            }
            else {
                $this->logs[] = "Certificate not found";
                return false;
            }
        }
        $data = array();
        $data['url'] = $webhook_url;
        if ($certificate && $file_curl) $data['certificate'] = $file_curl;
        if ($ip_address) $data['ip_address'] = $ip_address;
        if ($max_connections && (int) $max_connections > 0 && (int) $max_connections <= 100) $data['max_connections'] = (int) $max_connections;

        $t_bot = json_decode($this->net->getContent($url, 5, $data), true);
        if (!isset($t_bot['ok'])) {
            $t_bot = false;
            $this->logs[] = "Telegram SEND ERROR!";
        }
        elseif (isset($t_bot['error_code']) && $t_bot['error_code']) {
            $this->logs[] = $t_bot['error_code'].": ".$t_bot['description'];
            $t_bot = false;
        }
        if ($this->debug && is_array($t_bot)) $this->logs[] = "Telegram send status: ".$t_bot['ok'];
        elseif ($this->debug) $this->logs[] = "Telegram send status: ".$t_bot;
        return $t_bot;
    }

    /**
     * Удаление Webhook-сервера для бота
     * @return bool|mixed
     */
    public function deleteWebhook () {
        $url = "https://api.telegram.org/bot" . $this->telegram_token . "/deleteWebhook";
        if ($this->debug) $this->logs[] = "Telegram URL: ".$url;
        $t_bot = json_decode($this->net->getContent($url, 5), true);
        if (!isset($t_bot['ok'])) {
            $t_bot = false;
            $this->logs[] = "Telegram SEND ERROR!";
        }
        elseif (isset($t_bot['error_code']) && $t_bot['error_code']) {
            $this->logs[] = $t_bot['error_code'].": ".$t_bot['description'];
            $t_bot = false;
        }
        if ($this->debug && is_array($t_bot)) $this->logs[] = "Telegram send status: ".$t_bot['ok'];
        elseif ($this->debug) $this->logs[] = "Telegram send status: ".$t_bot;
        return $t_bot;
    }

    /**
     * Получение информации о Webhook-сервере бота
     * @return mixed
     */
    public function getWebhookInfo () {
        $url = "https://api.telegram.org/bot" . $this->telegram_token . "/getWebhookInfo";
        if ($this->debug) $this->logs[] = "Telegram URL: ".$url;
        return json_decode($this->net->getContent($url, 5), true);
    }

    /**
     * Отправка сообщения в Телеграм Бот
     * @param string $message - сообщение
     * @param string $file - отправляемый файл
     * @param integer $replay_to_id - ID сообщения на которое отвечаем
     * @param int $chat_id - идентификатор чата (человека, которому посылаем сообщение)
     * @return bool|false|string
     */
    public function sendToTelegram ($message = '', $file = '', $replay_to_id = 0, $chat_id = 0) {
        if ($this->debug) $this->logs[] = "Function sendToTelegram started";
        if (!$chat_id) $chat_id = $this->telegram_id;
        if ($this->debug) $this->logs[] = "Telegram Chat ID: ".$chat_id;
        if (!$this->telegram_token || !$chat_id) {
            $this->logs[] = "Wrong Telegram parameters!";
            if (!$this->telegram_token) $this->logs[] = "Token not defined!";
            else $this->logs[] = "Chat ID not defined!";
            return false;
        }
        $message = Base::convertLine($message);
        if ($this->debug) $this->logs[] = "Telegram message: ".$message;

        if ((!$message && !$file) || (!$message && $this->method == 'sendMessage')) {
            $this->logs[] = "Empty data to send";
            return false;
        }

        $file_curl = [];
        if ($file) {
            if (file_exists($file)) {
                if ($this->debug) $this->logs[] = "Telegram file: " . $file;
                $file_curl = new \CURLFile(realpath($file));
            }
            else {
                $this->logs[] = "File not found";
                return false;
            }
        }

        switch ($this->method) {
            case 'sendPhoto':
                if ($replay_to_id) $data = array("chat_id"=>$chat_id, "photo" => $file_curl, "reply_to_message_id" => $replay_to_id, "caption"=>$message);
                else $data = array("chat_id"=>$chat_id, "photo" => $file_curl, "caption"=>$message);
                break;
            case 'sendDocument':
                if ($replay_to_id) $data = array("chat_id"=>$chat_id, "photo" => $file_curl, "reply_to_message_id" => $replay_to_id, "caption"=>$message);
                else $data = array("chat_id"=>$chat_id, "document" => $file_curl, "caption"=>$message);
                break;
            case 'sendVideo':
                if ($replay_to_id) $data = array("chat_id"=>$chat_id, "photo" => $file_curl, "reply_to_message_id" => $replay_to_id, "caption"=>$message);
                else $data = array("chat_id"=>$chat_id, "video" => $file_curl, "caption"=>$message);
                break;
            case 'sendAudio':
                if ($replay_to_id) $data = array("chat_id"=>$chat_id, "photo" => $file_curl, "reply_to_message_id" => $replay_to_id, "caption"=>$message);
                else $data = array("chat_id"=>$chat_id, "audio" => $file_curl, "caption"=>$message);
                break;
            case 'sendVoice':
                if ($replay_to_id) $data = array("chat_id"=>$chat_id, "photo" => $file_curl, "reply_to_message_id" => $replay_to_id, "caption"=>$message);
                else $data = array("chat_id"=>$chat_id, "voice" => $file_curl, "caption"=>$message);
                break;
            case 'sendAnimation':
                if ($replay_to_id) $data = array("chat_id"=>$chat_id, "photo" => $file_curl, "reply_to_message_id" => $replay_to_id, "caption"=>$message);
                else $data = array("chat_id"=>$chat_id, "animation" => $file_curl, "caption"=>$message);
                break;
            default:
                if ($replay_to_id) $data = array("chat_id"=>$chat_id, "photo" => $file_curl, "reply_to_message_id" => $replay_to_id, "caption"=>$message);
                else $data = array("chat_id"=>$chat_id, "text"=>$message);
        }

        $url = "https://api.telegram.org/bot" . $this->telegram_token . "/".$this->method;
        if ($this->debug) $this->logs[] = "Telegram URL: ".$url;

        $t_bot = json_decode($this->net->getContent($url, 5, $data), true);
        if (!isset($t_bot['ok'])) {
            $t_bot = false;
            $this->logs[] = "Telegram SEND ERROR!";
        }
        elseif (isset($t_bot['error_code']) && $t_bot['error_code']) {
            $this->logs[] = $t_bot['error_code'].": ".$t_bot['description'];
            $t_bot = false;
        }
        if ($this->debug && is_array($t_bot)) $this->logs[] = "Telegram send status: ".$t_bot['ok'];
        elseif ($this->debug) $this->logs[] = "Telegram send status: ".$t_bot;
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