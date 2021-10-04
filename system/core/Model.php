<?php

namespace system\core;

use system\classes\ArrayHolder;
use system\classes\SafetyManager;
use system\interfaces\ModelInterface;
use app\user\models\UserInputParser;

class Model implements ModelInterface
{

    private array $errors = [];
    public ArrayHolder $form;

    public function __construct(ArrayHolder $data = null)
    {
        if (!is_null($data)) {
            $this->form = $data;
        }
    }

    public function generateSeoUrl(string $text): string
    {
        $text = mb_strtolower($text);
        $rus = array('кс', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ');
        $lat = array('x', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya', '-');
        return str_replace($rus, $lat, $text);
    }

    public function notServiceField(string $field)
    {
        return !in_array($field, ['_csrfToken', '_formName']);
    }

    public function saveForm()
    {
        foreach ($this->form as $input => $value) {
            if ($this->notServiceField($input) && !is_array($value)) {
                Cfg::$get->server->setSession([$this->form->_formName . '_' . $input => $value]);
            }
        }
    }

    public function correct(): bool
    {
        $this->checkCSRF();
        $this->addFilesToForm();

        if (method_exists($this, 'rules')) {
            $parser = new UserInputParser($this);
            $rules = $this->rules();
            foreach ($this->form as $key => $value) {
                if ($this->notServiceField($key) && array_key_exists($key, $rules)) {
                    $rule = $rules[$key];
                    $this->makeAssocUnique($rule);
                    $this->processInputByRules($parser, $rule, $key, $value);
                }
            }
            if (!$this->emptyErrors()) {
                $this->saveForm();
                return false;
            }
        }

        $this->destroyFormSession();
        return true;

    }

    private function checkCSRF()
    {
        if (Cfg::$get->safety['csrfProtection']) {
            if (!isset($this->form->_csrfToken) || !Cfg::$get->server->issetSession('csrfToken') || $this->form->_csrfToken != Cfg::$get->server->getSession('csrfToken')) {
                Errors::code(419);
            }
        }
    }

    private function addFilesToForm()
    {
        foreach($_FILES as $name => $file) {
            if ($file['error']) {
                $file = array();
            }
            $this->form->$name = $file;
        }
    }

    private function destroyFormSession()
    {
        $sessionToDelete = [];
        foreach ($this->form as $input => $value) {
            $inputName = $this->form->_formName . '_' . $input;
            if ($this->notServiceField($input) && Cfg::$get->server->issetSession($inputName)) {
                $sessionToDelete[] = $inputName;
            }
        }
        Cfg::$get->server->unsetSession($sessionToDelete);
    }

    /**
     * <i>Данная функция используется функцией <b>correct()</b>.</i><br><br>
     * Унифицирует значения массива правил валидации. Убирает повторяющиеся значения массива, повторяющиеся по ключам исключаются автоматически, а в случае присутствия правила по ключу и по значению одновременно приоритетом будет правило по значению - по ключу удаляется.
     * @param array $array Массив с правилами валидации.
     */
    private function makeAssocUnique(array &$arr): void
    {
        $arr = array_unique($arr, SORT_REGULAR);
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $repeating_rule = $key;
            } else {
                $repeating_rule = $value;
            }
            if (array_key_exists($repeating_rule, $arr) && array_search($repeating_rule, $arr) !== false) {
                unset($arr[$repeating_rule]);
            }
        }

    }

    /**
     * <i>Данный метод используется методом <b>correct()</b>.</i><br><br>
     * Обрабатывает каждое правило валидации. Если значение - массив, значит правило содержит параметры, иначе же значение воспринимается как единое правило,<br>действующее на всех страницах с полем данного <b>name</b>, и где подключена модель-обработчик.
     * @param array $input Массив валидаторов.
     * @param string $key Название поля из атрибута <b>name</b>.
     * @param string|array $value Значение этого поля, которое будет валидироваться.
     */
    private function processInputByRules(UserInputParser $parser, array $input, string $key, string|array $value): void
    {
        foreach ($input as $rule_key => $rule) {
            if (isset($this->errors[$key])) {
                break;
            }
            $rule_method = (is_array($rule) ? $rule_key : $rule);
            if ($this->routesSpecified($rule) && $this->conditionSpecified($rule)) {
                $parser->$rule_method($value, $key, $this->errors, ($rule['values'] ?? null));
            }
        }
    }

    private function routesSpecified($routes)
    {
        return !isset($routes['routes']) || in_array(Cfg::$get->route->getController() . '/' . Cfg::$get->route->getAction(), $routes['routes']);
    }

    private function conditionSpecified($condition)
    {
        return !isset($condition['if']) || $condition['if']();
    }

    public function emptyErrors(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function uploadFile(array $formFile, string $path, callable $generateName = null)
    {
        if (!empty($formFile) && $formFile['error'] == 0) {
            $new_file_name =
                (is_null($generateName) ?
                    SafetyManager::generateRandomString(15) :
                    $generateName())
                . '.'
                . pathinfo($formFile['name'], PATHINFO_EXTENSION);
            $path = $path . $new_file_name;
            move_uploaded_file($formFile['tmp_name'], $path);
            return $new_file_name;
        }
        return false;
    }

    public function rules() {
        return [];
    }

    public function fields() {
        return [];
    }

}
