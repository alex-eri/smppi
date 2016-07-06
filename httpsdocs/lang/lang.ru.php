<?php

define("FIELDS_ERROR", "Не заполнены поля");
define("SEND_ERROR", "Ошибка отправки");

define("USER_FIELDS_ERROR", "Не заполнены поля");

define("MENU_SEND_SMS", "Отправить SMS");
define("MENU_MANAGE", "Управление");
define("THEAD_DATE", "Дата");
define("THEAD_PHONE", "Номер");
define("THEAD_MSG", "Сообщение");
define("THEAD_METHOD", "Канал");
define("THEAD_STATE", "Статус");
define("THEAD_LOGIN", "Логин");
define("THEAD_IP", "Разрешенные IP");
define("THEAD_INTERFACE", "Интерфейс");
define("THEAD_RIGHTS", "Права");
define("SENT_YES", "Отправлено");
define("SENT_NO", "Не отправлено");
define("IN_PROCESS", "В процессе");
define("DELIVERED", "Доставлено");
define("SENT_ERROR", "Ошибка отправки");
define("DELIVER_ERROR", "Не доставлено");
define("PC_SENDING_ON", "Отправка включена");
define("PC_SENDING_OFF", "Отправка выключена");
define("PC_RECEIVING_ON", "Прием включен");
define("PC_RECEIVING_OFF", "Прием выключен");
define("BTN_CREATE_USER", "Создать пользователя");

define("AUTHORIZATION", "Авторизация");
define("BTN_AUTH", "Войти");

define("NAVIGATION", "Навигация");
define("INCOMING", "Входящие");
define("OUTGOING", "Исходящие");
define("BTN_EXIT", "Выйти");

define("SEND_SMS", "Отправить SMS");
define("OPTION_GSM", "GSM Modem");
define("OPTION_SMPP", "SMPP Provider");
define("LABEL_METHOD", "Канал");
define("LABEL_PHONE", "Номер");
define("LABEL_MSG", "Сообщение");
define("LABEL_TRANSLIT", "Translit");
define("BTN_SEND", "Отправить");

define("LABEL_LOGIN", "Логин");
define("LABEL_PASSWORD", "Пароль");
define("LABEL_IP", "Разрешенные IP");
define("LABEL_INTERFACE", "Интерфейс");
define("LABEL_RIGHTS", "Права");
define("BTN_CLOSE", "Закрыть");
define("BTN_SAVE", "Сохранить изменения");

define("BTN_DELETE", "Удалить");

define("MODAL_USER_EDIT", "Редактировать пользователя");
define("MODAL_USER_CREATE", "Создать пользователя");

define("ACCESS_DENIED", "Нет доступа к интерфейсу");
define("WRONG_USERNAME_OR_PASSWORD", "Неправильный логин или пароль");
define("ACCESS_DENIED_BY_LOGIN", "Нет доступа к интерфейсу по логину");

$rights_descr = array(
		"SMS_ACCESS" => "Доступ к веб-интерфейсу SMS",
		"SMS_ADMIN" => "Доступ к интерфейсу управления",
		"SMS_APISEND" => "Доступ к отправке SMS через API",
		"SMS_WEBSEND" => "Доступ к отправке SMS через веб",
);
