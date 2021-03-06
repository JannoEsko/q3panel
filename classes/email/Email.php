<?php

/**
 * Generic class for sending e-mails. Also handles some of the notifications.
 * @author Janno
 */
class Email {
    
    private $from;
    private $to;
    private $title;
    private $body;
    private $fromName;
    private $toName;
    
    /**
     * Constructs new Email object.
     * @param string $from The e-mail address from who to send the e-mail
     * @param string $to The e-mail address to whom to send the e-mail
     * @param string $title The title of the e-mail.
     * @param string $body The body of the e-mail.
     * @param string $fromName The name, from which the e-mail will be sent from (e.g. Q3Panel)
     * @param string $toName The username, to which the e-mail will be sent to. (just as e-mail to-name parameter).
     */
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
     * @param string $sendgrid_apikey The API key if you wish to send it with SendGrid.
     * @param SQL $sql The SQL handle, required only if the email preferences aren't set in the object itself.
     * @return array Returns array, if it contains error key, there was a problem, if not, then the e-mail was sent successfully.
     */
    public function sendEmail($useSendgrid = false, $sendgrid_apikey = "") {
        if ($useSendgrid) {
            require_once __DIR__ . "/../sendgrid-php/sendgrid-php.php";
            $sfrom = new SendGrid\Email($this->getFromName(), $this->getFrom());
            $sto = new SendGrid\Email($this->getToName(), $this->getTo());
            $content = new SendGrid\Content("text/html", $this->getBody());
            $mail = new SendGrid\Mail($sfrom, $this->title, $sto, $content);
            $sg = new \Sendgrid($sendgrid_apikey);
            $response = $sg->client->mail()->send()->post($mail);
            return array($response);
        } else {
            require_once __DIR__ . "/../phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer();
            $mail->setFrom($this->getFrom(), $this->getFromName(), 0);
            $mail->addAddress($this->getTo(), $this->getToName());
            $mail->Subject = $this->getTitle();
            $mail->Body = $this->getBody();
            $mail->IsHTML(true); 
            if (!$mail->send()) {
                return array("error" => $mail->ErrorInfo);
            } else {
                return array();
            }
            return array();
        }
    }


    /**
     * Saves the e-mail preferences to the database.
     * @param SQL $sql The SQL handle.
     * @param int $is_sendgrid 1 if it's SendGrid, 0 otherwise.
     * @param string $from_name The name, from which the e-mails will be sent from.
     * @param string $from_email The e-mail, from which the e-mails will be sent from.
     * @param string $api [optional] Sendgrid's API key.
     * @return array Returns array from the SQL query or an error otherwise.
     */
    public static function saveEmailPreferences(SQL $sql, $is_sendgrid, $from_name, $from_email, $api) {
        $query = Constants::$INSERT_QUERIES['ADD_EMAIL_SERVICE'];
        $params = array($is_sendgrid, $from_name, $from_email, $api);
        try {
            return $sql->query($query, $params);
        } catch (PDOException $ex) {
            return array("error" => $ex->getMessage());
        }
    }
    
    /**
     * Updates the e-mail preferences in the SQL database.
     * @param SQL $sql The SQL handle.
     * @param int $is_sendgrid 1 if it's SendGrid, 0 otherwise.
     * @param string $from_name The name, from which the e-mails will be sent from.
     * @param string $from_email The e-mail, from which the e-mails will be sent from.
     * @param string $api [optional] Sendgrid's API key.
     * @return array Returns array from the SQL query or an error otherwise.
     */
    public static function updateEmailPreferences(SQL $sql, $is_sendgrid, $from_name, $from_email, $api) {
        $query = Constants::$UPDATE_QUERIES['UPDATE_EMAIL_SERVICE'];
        $params = array($is_sendgrid, $from_name, $from_email, $api);
        try {
            return $sql->query($query, $params);
        } catch (PDOException $ex) {
            return array("error" => $ex->getMessage());
        }
    }
    
    /**
     * Gets the e-mail preferences from the SQL database.
     * @param SQL $sql The SQL handle.
     * @return array Returns the e-mail preferences.
     */
    public static function getEmailPreferences(SQL $sql) {
        $query = Constants::$SELECT_QUERIES['GET_EMAIL_PREFERENCES'];
        $data = $sql->query($query);
        return $data[0];
    }
    
    /**
     * This function notifies the users mapped to the server based on their group.
     * @param SQL $sql The SQL handle
     * @param int $group_id The group the user must have (>= $group)
     * @param string $title The title of the message
     * @param string $msg The message to send.
     * @param array $params Optional parameters to substitute in the message. Can be used with a template (e.g. Your server {server_name} where array is array("{server_name}", "theservername");, so it'll substitute it).
     */
    public static function notifyServerUsers(SQL $sql, $group_id, $title, $msg, $params = null) {
        if ($params !== null) {
            foreach($params as $key => $value) {
                $msg = str_replace($key, $value, $msg);
            }
        }
        //get users
        $users = User::getAllUsers($sql, $group_id);
        $emailPrefs = self::getEmailPreferences($sql);
        $from = $emailPrefs['from_email'];
        $fromName = $emailPrefs['from_name'];
        $is_sendgrid = intval($emailPrefs['is_sendgrid']);
        $msg = str_replace("{sender_name}", $fromName, $msg);
        if ($is_sendgrid === 1) {
            $is_sendgrid = true;
        } else {
            $is_sendgrid = false;
        }
        $api_key = $emailPrefs['api_key'];
        foreach ($users as $user) {
            $email = new static($from, $user['email'], $title, $msg, $fromName, $user['realName']);
            $email->sendEmail($is_sendgrid, $api_key);
        }
    }
    
}
