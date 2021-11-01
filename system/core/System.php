<?php

namespace system\core;

use system\classes\Server;

class System {
    
    public static function renderError($filename, $vars = [])
    {
        extract($vars);

        ob_start();
        require SYSTEM_DIR . "/content/$filename.php";
        $content = ob_get_clean();

        require SYSTEM_DIR . "/content/Wrapper.php";
        
    }

    public static function renderBlock($filename, $vars = [], $return = false)
    {
        extract($vars);
        if ($return) {
            return require SYSTEM_DIR . "/content/$filename.php";
        }
        require SYSTEM_DIR . "/content/$filename.php";
    }

    private static function renderPopup($vars = [])
    {
        $message = Server::getSession('popupMessage');
        extract($vars);
        require SYSTEM_DIR . "/content/Popup.php";
    }

    public static function successPopup() {
        self::renderPopup([
            'color' => '#009200',
            'colorBack' => '#bee4be'
        ]);
    }

    public static function warningPopup() {
        self::renderPopup([
            'color' => '#c19800',
            'colorBack' => '#ffea9a'
        ]);
    }

    public static function errorPopup() {
        self::renderPopup([
            'color' => '#d60000',
            'colorBack' => '#ffb0b0'
        ]);
    }

    public static function noticePopup() {
        self::renderPopup([
            'color' => '#737373',
            'colorBack' => '#dedede'
        ]);
    }

}
