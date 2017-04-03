<?php

/**
 * Generic class for IPS REST API requests.
 *
 * @author Janno
 */
class IPSAPI {
    
    private $communityURL;
    private $apiKey;
    
    /**
     * Constructs the IPSAPI object. 
     * @uses cURL to communicate with IPS API.
     * @param string $communityURL The URL of your community page.
     * @param string $apiKey The REST API key of your community page.
     */
    function __construct($communityURL, $apiKey) {
        $this->communityURL = $communityURL;
        $this->apiKey = $apiKey;
        if ($this->endsWith($this->communityURL, "/")) {
            $this->communityURL .= "api/";
        } else {
            $this->communityURL .= "/api/";
        }
    }
    
    private function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length === 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }
    
    /**
     * Deletes a post.
     * @param int $post The post ID.
     * @return string Returns string seen in the API reference.
     * @throws BadFunctionCallException Throws exception if param $post is empty or its integer value is 0.
     */
    function deletePost($post) {
        if (intval($post) === 0) {
            throw new BadFunctionCallException("The parameter post's integer value cannot be 0 (yours is $post)");
        }
        $api_endpoint = $this->communityURL . "forums/posts/$post";
        $method = "DELETE";
        return $this->request($api_endpoint, $method);
    }
    
    /**
     * Creates a new topic.
     * @param int $forum The ID number of the forum the topic should be created in.
     * @param int $author The ID number of the member creating the topic (0 for guest).
     * @param string $title The topic title.
     * @param string $post The post content as HTML (e.g. "<p>This is a post.</p>").
     */
    function createTopic($forum, $author, $title, $post) {
        $api_endpoint = $this->communityURL . "forums/topics";
        $method = "POST";
        $bindParams = array(
            "forum" => $forum,
            "author" => $author,
            "title" => $title,
            "post" => $post
        );
        return $this->request($api_endpoint, $method, $bindParams);
    }
    
    /**
     * Creates a post in a topic.
     * @param int $topic The ID number of the topic the post should be created in.
     * @param int $author The ID number of the member making the post (0 for guest).
     * @param string $post The post content as HTML (e.g. "<p>This is a post.</p>").
     */
    function createPost($topic, $author, $post) {
        $api_endpoint = $this->communityURL . "forums/posts";
        $method = "POST";
        $bindParams = array(
            "topic" => $topic,
            "author" => $author,
            "post" => $post
        );
        return $this->request($api_endpoint, $method, $bindParams);
    }
    
    /**
     * Edits the post by its ID.
     * @param int $post_id The post ID.
     * @param string $post The post content as HTML (e.g. "<p>This is a post</p>").
     * @throws BadFunctionCallException Throws exception when the post_id parameter is empty/integer value of it is 0.
     */
    function editPost($post_id, $post) {
        if (intval($post_id) === 0) {
            throw new BadFunctionCallException("The integer value of post_id cannot be 0 (yours is $post_id)!");
        }
        $api_endpoint = $this->communityURL . "forums/posts/$post_id";
        $method = "POST";
        $bindParams = array(
            "post" => $post
        );
        return $this->request($api_endpoint, $method, $bindParams);
    }
    
    /**
     * Closes the topic by its ID.
     * @param int $topic The topic ID.
     */
    function closeTopic($topic) {
        
        if (intval($topic) === 0) {
            throw new BadFunctionCallException("Parameter topic's integer value cannot be null (you entered $topic).");
        }
        
        $bindParams = array(
            "locked" => 1
        );
        
        return $this->editTopic($topic, $bindParams);
    }
    
    /**
     * Moves the topic.
     * @param int $topic Topic ID.
     * @param int $whereToMove The forum ID where to move.
     * @return type See API reference.
     */
    function moveTopic($topic, $whereToMove) {
        
        $bindParams = array(
            "forum" => $whereToMove
        );
        return $this->editTopic($topic, $bindParams);
    }
    
    /**
     * Generic edit topic function.
     * @param int $topic Topic ID
     * @param Array $params Array of parameters (key => value).
     * @return See API reference.
     * @throws BadFunctionCallException If $topic is 0 or when $params is empty.
     */
    function editTopic($topic, $params) {
        if (intval($topic) === 0) {
            throw new BadFunctionCallException("Parameter topic's integer value cannot be null (you entered $topic).");
        }
        if (sizeof($params) === 0) {
            
            throw new BadFunctionCallException("Parameter params size value cannot be null.");
        }
        $api_endpoint = $this->communityURL . "forums/topics/$topic";
        $method = "POST";
        
        return $this->request($api_endpoint, $method, $params);
    }
    
    function getCommunityURL() {
        return $this->communityURL;
    }

    function getApiKey() {
        return $this->apiKey;
    }

    function getAuthorId() {
        return $this->authorId;
    }

    function setCommunityURL($communityURL) {
        $this->communityURL = $communityURL;
    }

    function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    function setAuthorId($authorId) {
        $this->authorId = $authorId;
    }


    private function request($api_endpoint, $method, $bindParams = NULL) {
        $ch = curl_init($api_endpoint);
        if ($method === "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($bindParams !== NULL) {
                $postFields = "";
                foreach($bindParams as $key => $value) {
                    $postFields .= "$key=";
                    $postFields .= curl_escape($ch, $value) . "&";
                }
                $postFields = rtrim($postFields, "&");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }
        } else if ($method === "DELETE") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->apiKey
        ));
        $response = json_decode(curl_exec($ch), true);
        return $response;
    }

}
