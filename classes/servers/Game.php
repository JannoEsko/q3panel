<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Game
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
    
    static function deleteGame(SQL $sql, $game_id) {
        $query = Constants::$DELETE_QUERIES['DELETE_GAME_BY_ID'];
        $params = array($game_id);
        return $sql->query($query, $params);
    }
    
    static function updateGame(SQL $sql, $game_id, $game_name = null, $game_location = null, $startscript = null) {
        $query = "UPDATE q3panel_games SET";
        $params = array();
        if ($game_name !== null) {
            $query .= " game_name = ?,";
            array_push($params, $game_name);
        }
        if ($game_location !== null) {
            $query .= " game_location = ?,";
            array_push($params, $game_location);
        }
        if ($startscript !== null) {
            $query .= " startscript = ?,";
            array_push($params, $startscript);
        }
        $query = rtrim($query, ",");
        $query .= " WHERE game_id = ?";
        array_push($params, $game_id);
        return $sql->query($query, $params);
    }
}
