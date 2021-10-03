<?php

namespace system\models;

use Cfg;

class Email
{

    private array $receivers, $headers;
    private string $subject, $message;

    public function to(array $receivers) {
        $this->receivers = $receivers;
        return $this;
    }

    public function setSubject(string $subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setMessage(string $message) {
        $this->message = $message;
        return $this;
    }

    private function setHeaders() {
        $this->headers['From'] = Cfg::$get->email['from'];
        $this->headers['Reply-To'] = Cfg::$get->email['from'];
        $this->headers['X-Mailer'] = 'PHP/' . phpversion();
        $this->headers['Content-Type'] = Cfg::$get->email['type'] . '; charset=' . Cfg::$get->email['charset'];;
    }

    public function send() {
        $this->setHeaders();
        foreach ($this->receivers as $receiver) {
            mail($receiver, $this->subject, $this->message, $this->headers);
        }
    }

}