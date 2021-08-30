# Telegram
Отправка сообщений от имени Телеграм бота

![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)
![Version](https://img.shields.io/badge/version-v2.2.0-blue.svg)
![PHP](https://img.shields.io/badge/php-v5.5_--_v8-blueviolet.svg)

# Содержание

- [Общие понятия](#общие-понятия)
- [Установка](#Установка)
- [Настройка](#Настройка)
- [Описание работы](#описание-работы)
    - [Подключение файла класса](#Подключение-файла-класса)
    - [Инициализация класса](#Инициализация-класса)
    - [Установка метода взаимодействия с API Телеграм](#Установка-метода-взаимодействия-с-API-Телеграм-при-отправке-сообщений)
        - [Поддерживаемые методы](#Поддерживаемые-методы)
    - [Установка токена телеграм бота](#Установка-токена-телеграм-бота)
    - [Установка параметров Webhook-сервера](#Установка-параметров-сервера-Webhook)
    - [Удаление сервера Webhook](#Удаление-сервера-Webhook)
    - [Получение информации о сервере Webhook](#Получение-информации-о-сервере-Webhook)
    - [Установка идентификатора получателя](#Установка-идентификатора-получателя)
    - [Включение отладки](#Включение-отладки)
    - [Получение логов](#Получение-логов)
    - [Пример отправки сообщения](#Пример-отправки-сообщения)
    - [Пример отправки фотографии](#Пример-отправки-фотографии)

# Общие понятия

Класс Telegram предназначен для отправки сообщений от имени Телеграм бота.

Для работы необходимо наличие PHP версии 5.5 и выше, библиотеки PHP php-json и php-curl.
Также используются дополниельные библиотеки [Base](https://github.com/Toropyga/Base) и [NetContent](https://github.com/Toropyga/NetContent)

# Установка

Рекомендуемый способ установки библиотеки NetContent с использованием [Composer](http://getcomposer.org/):

```bash
composer require toropyga/telegram
```

# Настройка
Предварительная настройка параметров по умолчанию может осуществлятся или непосредственно в самом классе, или с помощью именованных констант.
Именованные константы при необходимости обявляются до вызова класса, например, в конфигурационном файле, и определяют параметры по умолчанию.
* TELEGRAM_TOKEN - [токен Телеграм бота](https://core.telegram.org/bots/api#authorizing-your-bot);
* TELEGRAM_ID - [идентификатор чата](https://t.me/username_to_id_bot) (человека) которому посылаем сообщение
* TELEGRAM_DEBUG - включение или отключение параметров отладки;
* TELEGRAM_LOG_NAME - имя файла логов;

# Описание работы

## Подключение файла класса
```php
require_once("NetContent.php");
require_once("Base.php");
require_once("Telegram.php");
```
или с использованием composer
```php
require_once("vendor/autoload.php");
```
---
## Инициализация класса
```php
use \FYN\Base;
$net = new FYN\NetContent();
$telegram = new FYN\Telegram($net);
```
или с автоматическим подключением класса NetContent
```php
use \FYN\Base;
$telegram = new FYN\Telegram();
```
---
## Установка метода взаимодействия с API Телеграм при отправке сообщений
```php
$telegram->setMethod('sendVideo');
```
### Поддерживаемые методы
* ['sendMessage'](https://core.telegram.org/bots/api#sendmessage) - отправка текстового сообщения,
* ['sendPhoto'](https://core.telegram.org/bots/api#sendphoto) - отправка фотографии или изображения,
* ['sendDocument'](https://core.telegram.org/bots/api#senddocument) - отправка документа как вложения,
* ['sendVideo'](https://core.telegram.org/bots/api#sendvideo) - отправка видео файла,
* ['sendAudio'](https://core.telegram.org/bots/api#sendaudio) - отправка звукового файла,
* ['sendVoice'](https://core.telegram.org/bots/api#sendvoice) - отправка голосового сообщения,
* ['sendAnimation'](https://core.telegram.org/bots/api#sendanimation) - отправка анимированного изображения.
---
## Установка токена телеграм бота
```php
$telegram->setToken("000000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
```

---
## Установка параметров сервера Webhook
```php
$webhook_url = 'https://your_server_path'; // адрес Webhook-сервера
$certificate = '/home/user/ssl/my_ssl'; // путь к персональному сертификату (необязательный параметр)
$ip_address = '000.000.000.000'; // IP-адрес , который будет использоваться для отправки запросов Webhook вместо IP-адреса, полученного через DNS (необязательный параметр)
$max_connections = 40; // максимальное количество одновременных подключений к Webhook-серверу [1-100] (необязательный параметр)

$telegram->setWebhook ($webhook_url, $certificate, $ip_address, $max_connections);
```

---
## Удаление сервера Webhook
```php
$telegram->deleteWebhook();
```

---
## Получение информации о сервере Webhook
```php
$info = $telegram->getWebhookInfo();
```

---
## Установка идентификатора получателя
```php
$telegram->setChatID("000000000");
```

---
## Включение отладки
```php
$telegram->setDebug(true);
```
Если включен, то логируются все действия, в противном случае - только конечный результат.

---
## Получение логов
```php
$telegram->getLogs();
```
Возвращается массив действий и ошибок, и имя файла логов

---
## Пример отправки сообщения
```php
$token = "000000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"; // токен Телеграм бота
$chat_id = 000000000; // ID чата получателя
$telegram->setMethod('sendMessage');

$telegram->setToken($token);
$telegram->setChatID($chat_id);
if (!$answer = $telegram->sendToTelegram($message)) Base::dump($telegram->getLogs());
else Base::dump($answer);
```
или отправка сообщения с использованием параметров по умолчанию
```php
$message = "Hi!";

if (!$answer = $telegram->sendToTelegram($message)) Base::dump($telegram->getLogs());
else Base::dump($answer);
```
---
## Пример отправки фотографии
```php
$token = "000000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"; // токен Телеграм бота
$chat_id = 000000000; // ID чата получателя
$reply_message_id = 0000; // ID сообщения на которое даём ответ, по умолчанию - 0;
$caption = "It's my photo!";
$path_to_photo = "/home/images/my.jpeg";

$telegram->setDebug(true);
$telegram->setToken($token);
$telegram->setChatID($chat_id);
$telegram->setMethod('sendPhoto');
if (!$answer = $telegram->sendToTelegram($caption, $path_to_photo, $reply_message_id)) Base::dump($telegram->getLogs());
else Base::dump($answer);
```
отправка документа пользователю
```php
$chat_id = 111111111; // ID чата получателя
$caption = "It's my photo!";
$path_to_photo = "/home/images/my.jpeg";

$telegram->setMethod('sendPhoto');
if (!$answer = $telegram->sendToTelegram($caption, $path_to_photo, 0, $chat_id)) Base::dump($telegram->getLogs());
else Base::dump($answer);
```
или отправка фотографии с использованием параметров по умолчанию
```php
$caption = "It's my photo!";
$path_to_photo = "/home/images/my.jpeg";

$telegram->setMethod('sendPhoto');
if (!$answer = $telegram->sendToTelegram($caption, $path_to_photo)) Base::dump($telegram->getLogs());
else Base::dump($answer);
```