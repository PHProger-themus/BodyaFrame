<?php

namespace system\interfaces;

interface ModelInterface {
    
    /**
     * Транслит русских символов на английские для генерации SEO-урлов.
     * @param string $text Строка с символами.
     * @return string Строка с транслированными символами.
     */
    public function generateSeoUrl(string $text): string;

//    /**
//     * Генерация пароля.
//     * @param string $password Строка с паролем.
//     * @return string Зашифрованный пароль.
//     */
//    public function encryptPassword(string $password): string;

//    /**
//     * Проверяет данные в базе данных.
//     * @param array $params Ассоциативный массив, ключ - колонка, значение - значение, которое требуется найти в колонке. Значение в колонке <b>password</b> автоматически шифруется.
//     * @return bool true, если хотя бы одна строка была найдена.
//     */
//    public function checkData(array $params): bool;

    /**
     * Проверяет данные из формы, пришедшие через метод POST, используя правила валидации, определенные в классе модели, обрабатывающей данные, в свойстве <b>$processors</b>.<br>
     * Свойство <b>$processors</b> задается как ассоциативный массив примерно такого вида:<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;[<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'email' => ['required', 'isEmail'],<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'name' => ['required' => ['routes' => ['user/register']], 'length' => ['values' => [2, 20]]],<br>&nbsp;&nbsp;&nbsp;&nbsp;]<br>
     * То есть ключи - это name-атрибуты формы, данные которой были переданы, в массиве указываются правила валидации типа ключ => значение, если у правила имеются дополнительные параметры, либо просто значение.<br>
     * Обратите внимание, что указание, например, только валидатора <b>isEmail</b> для поля не сделает его обязательным, потребуется дополнительный валидатор <b>required</b>. Это сделано для того, чтобы иметь необязательные поля, но с конкретным форматом ввода. Данное правило действует на все валидаторы.
     * @return bool Данные корректны или некорректны.
     */
    public function correct(): bool;

    /**
     * Говорит о том, пуст ли массив с ошибками.
     * @return bool Пуст или нет.
     */
    public function emptyErrors(): bool;

    /**
     * Возвращает массив ошибок валидации.
     * @return array Массив ошибок валидации.
     */
    public function getErrors(): array;

    /**
     * Загружает файл на сервер.
     * @param array $formFile Суперглобальный массив $_FILES из формы, содержащий файл.
     * @param string $path Путь, по которому требуется сохранить файл.
     * @return string Имя загруженного файла.
     */
    public function uploadFile(array $formFile, string $path, callable $generateName);
    
}
