<?php
namespace AppBundle\Entity;

class ErrorMessage {
    public $title;
    public $message;
    public $errors;
    public $exception;

    public function __construct($title, $message, $errors = null, $exception = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->errors = $errors;
        $this->exception = $exception;
    }

    public static function fromException(\Exception $e) {
        return new self("An uncaught exception has occurred", $e->getMessage(), null, $e);
    }
}