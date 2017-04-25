<?php

/**
 * Generic class for games.
 * @author Janno
 */
class Game {
    
    private $game_id;
    private $game_name;
    private $game_location;
    private $startscript;
    
    /**
     * Constructs the game object.
     * @param int $game_id The ID of the game.
     * @param string $game_name The game name.
     * @param string $game_location The location of the game.
     * @param string $startscript The startscript of the game.
     */
    function __construct($game_id, $game_name, $game_location, $startscript) {
        $this->game_id = $game_id;
        $this->game_name = $game_name;
        $this->game_location = $game_location;
        $this->startscript = $startscript;
    }

    function getGame_id() {
        return $this->game_id;
    }

    function getGame_name() {
        return $this->game_name;
    }

    function getGame_location() {
        return $this->game_location;
    }

    function getStartscript() {
        return $this->startscript;
    }

    function setGame_id($game_id) {
        $this->game_id = $game_id;
    }

    function setGame_name($game_name) {
        $this->game_name = $game_name;
    }

    function setGame_location($game_location) {
        $this->game_location = $game_location;
    }

    function setStartscript($startscript) {
        $this->startscript = $startscript;
    }

        
    /**
     * Gets the games.
     * @param SQL $sql The SQL handle.
     * @param int $game_id [optional] If specified, it'll get the game by id, otherwise it will get all games.
     * @return array Returns the SQL response.
     */
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
    
    /**
     * Inserts the game into the database.
     * @param SQL $sql The SQL handle
     * @param string $game_name The game name.
     * @param string $game_location The location of the game.
     * @param string $startscript The startscript of the game.
     * @return array Returns the SQL response,
     */
    static function saveGame(SQL $sql, $game_name, $game_location, $startscript) {
        $query = Constants::$INSERT_QUERIES['ADD_NEW_GAME'];
        $params = array($game_name, $game_location, $startscript);
        return $sql->query($query, $params);
    }
    
    /**
     * Deletes the game from the database.
     * @param SQL $sql The SQL handle.
     * @param int $game_id The game ID.
     * @return array Returns the SQL response, or the array with the error key, including the error message.
     */
    static function deleteGame(SQL $sql, $game_id) {
        //first check that do we got any games deployed.
        $checkGames = Constants::$SELECT_QUERIES['GET_SERVERS_BY_GAME_ID'];
        $checkGamesParam = array($game_id);
        if (sizeof($sql->query($checkGames, $checkGamesParam)) === 0) {
            $query = Constants::$DELETE_QUERIES['DELETE_GAME_BY_ID'];
            $params = array($game_id);
            return $sql->query($query, $params);
        } else {
            return array("error" => Constants::$ERRORS['DELETE_GAME_HAS_SERVERS']);
        }
        
    }
    
    /**
     * Updates the game.
     * @param SQL $sql The SQL handle.
     * @param int $game_id The game ID
     * @param string $game_name [optional] The game name
     * @param string $game_location [optional] The game location.
     * @param string $startscript [optional] The startscript.
     * @return array Returns the SQL response.
     */
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
    
    /**
     * Gets the select-options for the games.
     * @param SQL $sql The SQL handle.
     * @return string Returns the games select-options.
     */
    static function getGamesSelect(SQL $sql) {
        $games = self::getGames($sql);
        $str = "";
        foreach ($games as $game) {
            $str .= "<option value='" . $game['game_id'] . "'>" . $game['game_name'] . "</option>";
        }
        return $str;
    }
}
