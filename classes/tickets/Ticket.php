<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ticket
 *
 * @author Janno
 */
class Ticket {
    
    private $support_ticket_id;
    private $title;
    private $ticket_status;
    private $creation_date;
    
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
    
    function saveTicket(SQL $sql) {
        $query = Constants::$INSERT_QUERIES['INSERT_NEW_TICKET'];
        $params = array($this->title, $this->ticket_status);
        return $sql->query($query, $params);
    }
    
    function mapTicketToUsers(SQL $sql, $by_group_id = null, $by_user_id = null) {
        $query = "";
        $params = null;
        if ($by_group_id !== null) {
            $query = Constants::$INSERT_QUERIES['MAP_TICKET_TO_USERS_BY_GROUP_LARGER_THAN'];
            $params = array($this->support_ticket_id, $by_group_id);
        } else if ($by_user_id !== null) {
            $query = Constants::$INSERT_QUERIES['MAP_TICKET_TO_USER_BY_USER_ID'];
            $params = array($this->support_ticket_id, $by_user_id);
        }
        return $sql->query($query, $params);
    }
    
    function saveMessageToTicket(SQL $sql, $user_id, $user_ip, $message) {
        $query = Constants::$INSERT_QUERIES['ADD_TICKET_MESSAGE'];
        $params = array($this->support_ticket_id, $user_id, $user_ip, $message);
        return $sql->query($query, $params);
    }
    
    
    /**
     * Gets the tickets based on your selection.
     * @param SQL $sql The SQL handle
     * @param int $ticket_id The ticket ID
     * @param int $user_id The user ID
     * @param int $ticket_status The ticket status which to search for.
     * @param bool $ticket_status_smaller_than If true, it will search the tickets by the ticket status smaller or equal than the given value. Otherwise, it'll search for exactly the same status.
     * @param bool $include_messages If true, will get the ticket by id and mapped user id with messages.
     * @return array Returns the tickets.
     */
    static function getTickets(SQL $sql, $ticket_id, $user_id, $ticket_status = null, $ticket_status_smaller_than = false, $include_messages = false) {
        $query = "";
        $params = null;
        if ($include_messages) {
            $query = Constants::$SELECT_QUERIES['GET_TICKET_MESSAGES_WITH_USERS_WITH_MAP_WITH_TICKET_BY_TICKET_ID_USER_ID'];
            $params = array($ticket_id, $user_id);
        } else if ($ticket_status !== null && $ticket_status_smaller_than) {
            $query = Constants::$SELECT_QUERIES['GET_MAPPED_TICKETS_BY_STATUS_SMALLER_THAN'];
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
