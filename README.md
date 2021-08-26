# Telegram
Отправка сообщений от имени Телеграм бота

# Содержание

- [Общие понятия](#общие-понятия)
- [Установка](#Установка)
- [Настройка](#Настройка)
- [Описание работы](#описание-работы)
    - [Подключение файла класса](#Подключение-файла-класса)
    - [Инициализация класса](#Инициализация-класса)
    - [Установка метода взаимодействия с API Телеграм](#Установка-метода-взаимодействия-с-API-Телеграм)
        - [Поддерживаемые методы](#Поддерживаемые-методы:)
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

##Подключение файла класса
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
##Инициализация класса
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
##Установка метода взаимодействия с API Телеграм
```php
$telegram->setMethod('sendVideo');
```
###Поддерживаемые методы:
* 'sendMessage' - отправка текстового сообщения,
* 'sendPhoto' - отправка фотографии или изображения,
* 'sendDocument' - отправка документа как вложения,
* 'sendVideo' - отправка видио,
* 'sendAudio' - отправка звукового файла,
* 'sendVoice' - отправка голосового сообщения,
* 'sendAnimation' - отправка анимированного изображения.
---
##Пример отправки сообщения
```php
$token = "000000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"; // токен Телеграм бота
$chat_id = 000000000; // ID чата получателя
$telegram->setMethod('sendMessage');
if (!$answer = $telegram->sendToTelegram($message, $token, $chat_id)) Base::dump($telegram->getLogs());
else Base::dump($answer);
```
или отправка сообщения с использованием параметров по умолчанию
```php
$message = "Hi!";
if (!$answer = $telegram->sendToTelegram($message)) Base::dump($telegram->getLogs());
else Base::dump($answer);
```
---
##Пример отправки фотографии
```php
$token = "000000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"; // токен Телеграм бота
$chat_id = 000000000; // ID чата получателя
$caption = "It's my photo!";
$path_to_photo = "/home/images/my.jpeg";
$telegram->setMethod('sendPhoto');
if (!$answer = $telegram->sendToTelegram($caption, $path_to_photo, $token, $chat_id)) Base::dump($telegram->getLogs());
else Base::dump($answer);
```
имли отправка фотографии с использованием параметров по умолчанию
```php
$caption = "It's my photo!";
$path_to_photo = "/home/images/my.jpeg";
$telegram->setMethod('sendPhoto');
if (!$answer = $telegram->sendToTelegram($caption, $path_to_photo")) Base::dump($telegram->getLogs());
else Base::dump($answer);
```