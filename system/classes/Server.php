<?php

namespace system\classes;

use Error;

class Server {
    
    /**
     * Определяет, были ли инициализированы сессионные переменные
     * @return bool Да / Нет.
    */
    public static function existsSession(): bool {
        return !empty($_SESSION);
    }
    
    /**
     * Определяет, была ли инициализирована определенная сессионная переменная
     * @param string $key Ключ переменной.
     * @return bool Да / Нет.
    */
    public static function issetSession(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Получить значение сессионной переменной по ключу
     * @param string $key Ключ переменной.
     * @return mixed Значение сессионной переменной
    */
    public static function getSession(string $key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        else {
            throw new Error("Сессионной переменной " . $key . " не существует");
        }
    }

    /**
     * Получить значение сессионной переменной по ключу и затем удалить ее
     * @param string $key Ключ переменной.
     * @return mixed Значение сессионной переменной
     */
    public static function extractSession(string $key) {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            self::unsetSession([$key]);
            return $value;
        } else {
            throw new Error("Сессионной переменной " . $key . " не существует");
        }
    }
    
    /**
     * Инициализировать сессионные переменные
     * @param array $values Массив типа ключ => значение.
    */
    public static function setSession(array $values) {
        foreach ($values as $key => $value) {
            $_SESSION[$key] = SafetyManager::filterString($value);
        }
    }
    
    /**
     * Удалить сессионные переменные
     * @param array $values Массив с ключами переменных.
    */
    public static function unsetSession(array $values) {
        foreach ($values as $key) {
            if (!isset($_SESSION[$key])) {
                throw new Error("Сессионной переменной " . $key . " не существует");
            }
        }
        foreach ($values as $key) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Очистить сессионный массив
    */
    public static function clearSession() {
        $_SESSION = array();
        //session_destroy();
    }
    
}
