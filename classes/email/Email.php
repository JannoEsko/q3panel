<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Email
 *
 * @author Janno
 */
class Email {
    
    private $from;
    private $to;
    private $title;
    private $body;
    private $fromName;
    private $toName;
    
    function __construct($from, $to, $title, $body, $fromName, $toName) {
        $this->from = $from;
        $this->to = $to;
        $this->title = $title;
        $this->body = $body;
        $this->fromName = $fromName;
        $this->toName = $toName;
    }

    function getFromName() {
        return $this->fromName;
    }

    function getToName() {
        return $this->toName;
    }

    function setFromName($fromName) {
        $this->fromName = $fromName;
    }

    function setToName($toName) {
        $this->toName = $toName;
    }

    function getFrom() {
        return $this->from;
    }

    function getTo() {
        return $this->to;
    }

    function getTitle() {
        return $this->title;
    }

    function getBody() {
        return $this->body;
    }

    function setFrom($from) {
        $this->from = $from;
    }

    function setTo($to) {
        $this->to = $to;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setBody($body) {
        $this->body = $body;
    }

    /**
     * Function for sending e-mail. Uses either SendGrid or PHPMailer
     * @param boolean $useSendgrid Boolean value whether to use SendGrid or not (default false).
     * @param String $sendgrid_apikey The API key if you wish to send it with SendGrid.
     * @return Array Returns array, if it contains error key, there was a problem, if not, then the e-mail was sent successfully.
     */
    public function sendEmail($useSendgrid = false, $sendgrid_apikey = "") {
        if ($useSendgrid) {
            require_once __DIR__ . "/../sendgrid-php/sendgrid-php.php";
            return array();
        } else {
            require_once __DIR__ . "/../phpmailer/PHPMailerAutoload.php";
            return array();
        }
    }


    
}
