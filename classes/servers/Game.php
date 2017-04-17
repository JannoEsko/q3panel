<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GAme
 *
 * @author Janno
 */
class Game {
    
    
    static function getGames(SQL $sql, $game_id = null) {
        $query = "";
        $params = null;
        if ($game_id === null) {
            $query = Constants::$SELECT_QUERIES['GET_ALL_GAMES'];
        } else {
            $query = Constants::$SELECT_QUERIES['GET_GAME_BY_ID'];
            $params = array($game_id);
        }
        return $sql->query($query, $params);
    }
    
    static function saveGame(SQL $sql, $game_name, $game_location, $startscript) {
        $query = Constants::$INSERT_QUERIES['ADD_NEW_GAME'];
        $params = array($game_name, $game_location, $startscript);
        return $sql->query($query, $params);
    }
}
