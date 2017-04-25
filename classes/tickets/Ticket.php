<?php

/**
 * Generic class for tickets management.
 * @author Janno
 */
class Ticket {
    
    private $support_ticket_id;
    private $title;
    private $ticket_status;
    private $creation_date;
    
    /**
     * Constructs a new ticket.
     * @param int $support_ticket_id The ticket ID
     * @param string $title The title of the ticket.
     * @param int $ticket_status The current status of the ticket.
     * @param string $creation_date The creation date of the ticket.
     */
    function __construct($support_ticket_id, $title, $ticket_status, $creation_date) {
        $this->support_ticket_id = $support_ticket_id;
        $this->title = $title;
        $this->ticket_status = $ticket_status;
        $this->creation_date = $creation_date;
    }
    
    function getSupport_ticket_id() {
        return $this->support_ticket_id;
    }

    function getTitle() {
        return $this->title;
    }

    function getTicket_status() {
        return $this->ticket_status;
    }

    function getCreation_date() {
        return $this->creation_date;
    }

    function setSupport_ticket_id($support_ticket_id) {
        $this->support_ticket_id = $support_ticket_id;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setTicket_status($ticket_status) {
        $this->ticket_status = $ticket_status;
    }

    function setCreation_date($creation_date) {
        $this->creation_date = $creation_date;
    }
    
    /**
     * Writes a new ticket into the database.
     * @param SQL $sql The SQL handle.
     * @return array Returns the SQL output.
     */
    function saveTicket(SQL $sql) {
        $query = Constants::$INSERT_QUERIES['INSERT_NEW_TICKET'];
        $params = array($this->title, $this->ticket_status);
        return $sql->query($query, $params);
    }
    
    /**
     * Maps the ticket to specific users.
     * @param SQL $sql The SQL handle.
     * @param int $by_group_id [optional] If specified, it maps the ticket to all users who are in a group equal or larger than the given group.
     * @param int $by_user_id [optional] If specified, it maps the ticket to the user ID specified.
     * @param string $host_url [optional] If specified, it tries to edit the email template to add the host URL into it.
     * @return array Returns the SQL output.
     */
    function mapTicketToUsers(SQL $sql, $by_group_id = null, $by_user_id = null, $host_url = null) {
        $query = "";
        $params = null;
        $emailData = Email::getEmailPreferences($sql);
        if ($by_group_id !== null) {
            $query = Constants::$INSERT_QUERIES['MAP_TICKET_TO_USERS_BY_GROUP_LARGER_THAN'];
            
            $params = array($this->support_ticket_id, $by_group_id);
            $users = User::getAllUsers($sql, Constants::$PANEL_ADMIN);
            foreach ($users as $user) {
                $email = new Email($emailData['from_email'], $user['email'], Constants::$EMAIL_TEMPLATE['NEW_TICKET_TEMPLATE_TITLE'], str_replace("{ticket_name}", $this->title, str_replace("{HOST_URL}", $host_url, str_replace("{sender_name}", $emailData['from_name'], Constants::$EMAIL_TEMPLATE['NEW_TICKET_TEMPLATE']))), $emailData['from_name'], $user['realName']);
                $email->sendEmail(int2bool($emailData['is_sendgrid']), $emailData['api_key']);
            }
        } else if ($by_user_id !== null) {
            $query = Constants::$INSERT_QUERIES['MAP_TICKET_TO_USER_BY_USER_ID'];
            $params = array($this->support_ticket_id, $by_user_id);
            $user = User::getUserById($sql, $by_user_id);
            $email = new Email($emailData['from_email'], $user['email'], Constants::$EMAIL_TEMPLATE['NEW_TICKET_TEMPLATE_TITLE'], str_replace("{ticket_name}", $this->title, str_replace("{HOST_URL}", $host_url, str_replace("{sender_name}", $emailData['from_name'], Constants::$EMAIL_TEMPLATE['NEW_TICKET_TEMPLATE']))), $emailData['from_name'], $user['realName']);
            $email->sendEmail(int2bool($emailData['is_sendgrid']), $emailData['api_key']);
        }
        return $sql->query($query, $params);
    }
    
    /**
     * Saves a new message to the ticket.
     * @param SQL $sql The SQL handle.
     * @param int $user_id The user ID
     * @param string $user_ip The IP of the user.
     * @param string $message The message which to add.
     * @return array Returns the SQL output of the query.
     */
    function saveMessageToTicket(SQL $sql, $user_id, $user_ip, $message) {
        $query = Constants::$INSERT_QUERIES['ADD_TICKET_MESSAGE'];
        $params = array($this->support_ticket_id, $user_id, $user_ip, $message);
        return $sql->query($query, $params);
    }
    
    /**
     * Updates a ticket status.
     * @param SQL $sql The SQL handle.
     * @param int $ticket_status The ticket status.
     * @return array Returns the SQL output of the query.
     */
    function setStatus(SQL $sql, $ticket_status) {
        $this->ticket_status = intval($ticket_status);
        $query = Constants::$UPDATE_QUERIES['UPDATE_TICKET_STATUS'];
        $params = array($ticket_status, $this->support_ticket_id);
        return $sql->query($query, $params);
    }
    
    /**
     * Notifies all mapped users.
     * @param SQL $sql The SQL handle.
     * @param string $title The title of the e-mail message.
     * @param string $message The message of the e-mail.
     */
    function notifyMappedUsers(SQL $sql, $title, $message) {
        $mappedUsers = Constants::$SELECT_QUERIES['GET_TICKET_MAP_BY_TICKET_ID'];
        $params = array($this->support_ticket_id);
        $data = $sql->query($mappedUsers, $params);
        $emailPrefs = Email::getEmailPreferences($sql);
        foreach ($data as $row) {
            $user_id = $row['mapped_user_id'];
            $user = User::getUserById($sql, $user_id);
            $email = new Email($emailPrefs['from_email'], $user['email'], $title, str_replace("{ticket_name}", $this->title, str_replace("{sender_name}", $emailPrefs['from_name'], $message)), $emailPrefs['from_name'], $user['realName']);
            $email->sendEmail(int2bool($emailPrefs['is_sendgrid']), $emailPrefs['api_key']);
        }
    }
    
    /**
     * Checks whether the user is mapped to the ticket.
     * @param SQL $sql The SQL handle.
     * @param int $ticket_id The ticket ID which to check.
     * @param int $user_id The user ID which to check.
     * @return bool Returns true, if the user is mapped to the ticket, false otherwise.
     */
    static function isUserMappedToTicket(SQL $sql, $ticket_id, $user_id) {
        $query = Constants::$SELECT_QUERIES['GET_TICKET_MAP_BY_TICKET_ID_USER_ID'];
        $params = array($ticket_id, $user_id);
        return sizeof($sql->query($query, $params) === 1);
    }
    
    /**
     * Gets a ticket from the database.
     * @param SQL $sql The SQL handle.
     * @param int $ticket_id The ticket ID which to get.
     * @return \Ticket Returns new Ticket object.
     */
    static function getTicket(SQL $sql, $ticket_id) {
        $query = Constants::$SELECT_QUERIES['GET_TICKET_DATA_BY_TICKET_ID'];
        $params = array($ticket_id);
        $data = $sql->query($query, $params);
        if (sizeof($data) === 1) {
            $data = $data[0];
            return new static($data['support_ticket_id'], $data['title'], $data['ticket_status'], $data['creation_date']);
        } else {
            return new static();
        }
    }
    
    
    /**
     * Gets the tickets based on your selection.
     * @param SQL $sql The SQL handle
     * @param int $ticket_id The ticket ID
     * @param int $user_id The user ID
     * @param int $ticket_status The ticket status which to search for.
     * @param bool $ticket_status_larger_than If true, it will search the tickets by the ticket status larger than the given value. Otherwise, it'll search for exactly the same status.
     * @param bool $include_messages If true, will get the ticket by id and mapped user id with messages.
     * @return array Returns the tickets.
     */
    static function getTickets(SQL $sql, $ticket_id, $user_id, $ticket_status = null, $ticket_status_larger_than = false, $include_messages = false) {
        $query = "";
        $params = null;
        if ($include_messages) {
            $query = Constants::$SELECT_QUERIES['GET_TICKET_MESSAGES_WITH_USERS_WITH_MAP_WITH_TICKET_BY_TICKET_ID_USER_ID'];
            $params = array($ticket_id, $user_id);
        } else if ($ticket_status !== null && $ticket_status_larger_than) {
            $query = Constants::$SELECT_QUERIES['GET_MAPPED_TICKETS_BY_STATUS_LARGER_THAN'];
            $params = array($ticket_status, $user_id);
        } else if ($ticket_status !== null) {
            $query = Constants::$SELECT_QUERIES['GET_MAPPED_TICKETS_BY_STATUS'];
            $params = array($ticket_status, $user_id);
        } else {
            $query = Constants::$SELECT_QUERIES['GET_MAPPED_TICKETS'];
            $params = array($user_id);
        }
        return $sql->query($query, $params);
    }
}
