<?php

use system\classes\FormHelper;

FormHelper::addInput('text', 'login', ['value' => FormHelper::getValue('name', 'register')]);
FormHelper::addInput('password', 'password', ['value' => FormHelper::getValue('password', 'register')]);
FormHelper::addSubmit('click', 'Go');
FormHelper::createForm('register');

FormHelper::addInput('file', 'name');
FormHelper::addInput('text', 'lastname');
FormHelper::addSubmit('upload', 'Загрузить на сервер');
FormHelper::createForm('fileform');

?>
