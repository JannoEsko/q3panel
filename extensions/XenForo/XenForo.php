<?php
require_once __DIR__ . "/Ip/Ip.php";

/**
 * Description of XenForo
 *
 * @author Janno
 */
class XenForo {
    static $DEFAULT_USER_ID = 350;
    static $SQL_QUERIES = array(
          "GET_MAX_POS_BY_THREAD_ID" => "SELECT MAX(position) AS next_pos FROM xf_post WHERE thread_id = ?"
        , "CREATE_POST" => "INSERT INTO xf_post (thread_id, user_id, username, post_date, message, ip_id, message_state, position, like_users, embed_metadata) VALUES (?, ?, ?, UNIX_TIMESTAMP(), ?, ?, 'visible', ?, 'a:0:{}', '[]')"
        , "ADD_IP" => "INSERT INTO xf_ip (user_id, content_type, content_id, action, ip, log_date) VALUES (?, 'post', ?, '', ?, UNIX_TIMESTAMP())"
        , "LINK_IP_TO_POST" => "UPDATE xf_post SET ip_id = ? WHERE post_id = ?"
        , "UPDATE_THREAD_AFTER_POST" => "UPDATE xf_thread SET reply_count = reply_count + 1, last_post_date = UNIX_TIMESTAMP(), last_post_id = ?, last_post_user_id = ?, last_post_username = ? WHERE thread_id = ?"
        , "UPDATE_USER_POST_COUNT" => "UPDATE xf_user SET message_count = message_count + 1 WHERE user_id = ?"
        , "GENERATE_SEARCH_INDEX" => "INSERT INTO xf_search_index (content_type, content_id, title, message, metadata, user_id, item_date, discussion_id) VALUES (?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), ?)"
        , "GET_THREAD_NODE_ID" => "SELECT node_id FROM xf_thread WHERE thread_id = ?"
        , "GET_USERNAME_BY_ID" => "SELECT username FROM xf_user WHERE user_id = ?"
        , "ADD_POST_COUNT_TO_THREAD_USER_POST" => "UPDATE xf_thread_user_post SET post_count = post_count + 1 WHERE thread_id = ? AND user_id = ?"
        , "CHECK_POST_COUNT_THREAD" => "SELECT post_count FROM xf_thread_user_post WHERE thread_id = ? AND user_id = ?"
        , "INSERT_POST_COUNT_TO_THREAD" => "INSERT INTO xf_thread_user_post (thread_id, user_id, post_count) VALUES (?, ?, 1)"
        , "INSERT_THREAD" => "INSERT INTO xf_thread (node_id, title, reply_count, view_count, user_id, username, post_date, sticky, discussion_state, discussion_open, first_post_id, first_post_likes, last_post_date, last_post_id, last_post_user_id, last_post_username, prefix_id, tags, custom_fields) VALUES (?, ?, 0, 0, ?, ?, UNIX_TIMESTAMP(), 0, 'visible', 1, 0, 0, 0, 0, 0, 'a', 0, 'a:0:{}', 'a:0:{}')"
        , "UPDATE_THREAD_AFTER_FIRST_POST" => "UPDATE xf_thread SET first_post_id = last_post_id WHERE thread_id = ?"
        , "UPDATE_THREAD_AFTER_POSTING_THREAD" => "UPDATE xf_thread SET reply_count = 0, last_post_date = UNIX_TIMESTAMP(), last_post_id = ?, last_post_user_id = ?, last_post_username = ? WHERE thread_id = ?"
        , "CLOSE_THREAD" => "UPDATE xf_thread SET discussion_open = 0 WHERE thread_id = ?"
        , "DELETE_POST" => "UPDATE xf_post SET message_state = 'deleted' WHERE post_id = ?"
        , "SET_DELETE_POST_MODERATOR_LOG" => "INSERT INTO xf_moderator_log (log_date, user_id, ip_address, content_type, content_id, content_user_id, content_username, content_title, content_url, discussion_content_type, discussion_content_id, action, action_params) VALUES (UNIX_TIMESTAMP(), ?, ?, 'post', ?, ?, ?, ?, ?, 'thread', ?, 'delete_soft', '{\"reason\": \"Deleted with API\"}')"
        , "SET_POSITIONS_AFTER_DELETE" => "UPDATE xf_post SET position = position - 1 WHERE thread_id = ? AND post_id >= ?"
        , "GET_THREAD_ID_FROM_POST" => "SELECT thread_id FROM xf_post WHERE post_id = ?"
        , "GET_THREAD_TITLE_FROM_THREAD" => "SELECT title FROM xf_thread WHERE thread_id = ?"
        , "SET_THREAD_REPLY_COUNT_MINUS_1" => "UPDATE xf_thread SET reply_count = reply_count - 1 WHERE thread_id = ?"
        , "GET_MAX_POST_ID_FROM_VISIBLE_POSTS" => "SELECT MAX(post_id) AS max_post FROM xf_post WHERE thread_id = ? AND message_state = 'visible'"
        , "GET_POST_DATA" => "SELECT user_id, username, post_date FROM xf_post WHERE post_id = ?"
        , "UPDATE_THREAD_AFTER_DELETE" => "UPDATE xf_thread SET last_post_id = ?, last_post_user_id = ?, last_post_username = ?, last_post_date = ? WHERE thread_id = ?"
        , "SET_DELETION_LOG_ENTRY" => "INSERT INTO xf_deletion_log (content_type, content_id, delete_date, delete_user_id, delete_username, delete_reason) VALUES ('post', ?, UNIX_TIMESTAMP(), ?, ?, 'Deleted with API')"
        , "EDIT_POST_DATA" => "UPDATE xf_post SET message = ?, last_edit_date = UNIX_TIMESTAMP(), last_edit_user_id = ?, edit_count = edit_count + 1 WHERE post_id = ?"
        , "EDIT_POST_DATA_LOG" => "INSERT INTO xf_edit_history (content_type, content_id, edit_user_id, edit_date, old_text) SELECT 'post' AS content_type, ? AS content_id, ? AS edit_user_id, UNIX_TIMESTAMP() AS edit_date, message FROM xf_post WHERE post_id = ?"
        , "EDIT_POST_DATA_DIRTY" => "UPDATE xf_post SET message = ? WHERE post_id = ?"
        , "UPDATE_SEARCH_INDEX_MESSAGE_POST" => "UPDATE xf_search_index SET message = ? WHERE content_id = ?"
        , "MOVE_THREAD_GET_SEARCH_METADATA" => "SELECT content_type, content_id, user_id FROM xf_search_index WHERE discussion_id = ?"
        , "MOVE_THREAD_UPDATE_METADATA" => "UPDATE xf_search_index SET metadata = ? WHERE content_type = ? AND content_id = ? AND user_id = ? AND discussion_id = ?"
        , "MOVE_THREAD_CHANGE_NODE" => "UPDATE xf_thread SET node_id = ? WHERE thread_id = ?"
        , "GET_USER_DATA_BY_USERNAME_EMAIL" => "SELECT user_id, username, user_group_id FROM xf_user WHERE username = ? OR email = ?"
        , "GET_USER_AUTH_DATA" => "SELECT * FROM xf_user_authenticate WHERE user_id = ?"
        , "GET_USER_DATA_BY_NAME" => "SELECT user_id, username, email, user_group_id, secondary_group_ids FROM xf_user WHERE username LIKE ?"
        , "GET_THREAD_TITLE_BY_POST" => "SELECT xf_thread.title FROM xf_post INNER JOIN xf_thread ON (xf_thread.thread_id = xf_post.thread_id) WHERE xf_post.post_id = ?"
        , "UPDATE_XF_FORUM_AFTER_POST" => "UPDATE xf_forum SET discussion_count = discussion_count + 1, message_count = message_count + 1, last_post_id = ?, last_post_date = ?, last_post_user_id = ?, last_post_username = ?, last_thread_title = ? WHERE node_id = ?"
        , "UPDATE_XF_FORUM_AFTER_POST_WO_DISCUSSION" => "UPDATE xf_forum SET message_count = message_count + 1, last_post_id = ?, last_post_date = ?, last_post_user_id = ?, last_post_username = ?, last_thread_title = ? WHERE node_id = ?"
        , "GET_THREAD_REPLY_COUNT" => "SELECT reply_count FROM xf_thread WHERE thread_id = ?"
        , "UPDATE_XF_FORUM" => "UPDATE xf_forum SET discussion_count = discussion_count + ?, message_count = message_count + ?, last_post_id = ?, last_post_date = ?, last_post_user_id = ?, last_post_username = ?, last_thread_title = ? WHERE node_id = ?"
        , "GET_MAX_THREAD_DATA_BY_NODE" => "SELECT title, last_post_id, last_post_date, last_post_username, last_post_user_id FROM xf_thread WHERE last_post_id IN (SELECT MAX(last_post_id) FROM xf_thread WHERE node_id = ? AND discussion_state = 'visible')"
    );
    
    static function getUserDataByName(SQL $sql, $username) {
        $username = "%" . $username . "%";
        return $sql->query(self::$SQL_QUERIES['GET_USER_DATA_BY_NAME'], array($username));
    }
    
    static function getMaxThreadDataByNode(SQL $sql, $node_id) {
        $data = $sql->query(self::$SQL_QUERIES['GET_MAX_THREAD_DATA_BY_NODE'], array($node_id));
        if (sizeof($data) === 1) {
            return $data[0];
        }
        return false;
    }
    
    static function authenticate(SQL $sql, $username, $password) {
        //requirement - has to be password_verify'able.
        //get user id by name/email.
        $user_data = $sql->query(self::$SQL_QUERIES['GET_USER_DATA_BY_USERNAME_EMAIL'], array($username, $username));
        if (sizeof($user_data) === 1) {
            $user_data = $user_data[0];
        } else {
            return false;
        }
        $username = $user_data['username'];
        $user_id = intval($user_data['user_id']);
        $user_group = intval($user_data['user_group_id']);
        if ($user_id === 0) {
            return false;
        }
        $authdata = $sql->query(self::$SQL_QUERIES['GET_USER_AUTH_DATA'], array($user_id))[0];
        //we got authdata, means we can now check the password.
        $passwordData = unserialize($authdata['data']);
        $passwordHash = $passwordData['hash'];
        if (password_verify($password, $passwordHash)) {
            return array("name" => $username, "member_id" => $user_id, "member_group_id" => $user_group);
        }
        return false;
    }
    
    static function moveThread(SQL $sql, $thread_id, $new_node) {
        //get current data so you can replace the metadata easily.
        $search_index_data = $sql->query(self::$SQL_QUERIES['MOVE_THREAD_GET_SEARCH_METADATA'], array($thread_id));
        foreach ($search_index_data as $data) {
            $content_type = $data['content_type'];
            $content_id = $data['content_id'];
            $user_id = $data['user_id'];
            $newMetadata = self::generateMetadata($user_id, $content_type, $new_node, $thread_id);
            $sql->query(self::$SQL_QUERIES['MOVE_THREAD_UPDATE_METADATA'], array($newMetadata, $content_type, $content_id, $user_id, $thread_id));
        }
        //metadata set, move thread.
        //move thread needs to update xf_forum as well...
        //get old node so we can perform some changes.
        //need old node before doing shit.
        $node_id = self::getThreadNodeID($sql, $thread_id);
        $message_count = self::getPostCountFromThread($sql, $thread_id);
        if ($node_id === false) {
            throw new ErrorException("Node ID was not found, which shouldn't have happened");
        }
        
        $moveThread = $sql->query(self::$SQL_QUERIES['MOVE_THREAD_CHANGE_NODE'], array($new_node, $thread_id));
        
        
        //now update both old and new.
        $oldNodeData = self::getMaxThreadDataByNode($sql, $node_id);
        $newNodeData = self::getMaxThreadDataByNode($sql, $new_node);
        if ($oldNodeData === false) {
            self::updateForumData($sql, $node_id, -1, $message_count * -1);
        } else {
            self::updateForumData($sql, $node_id, -1, $message_count * -1, $oldNodeData['last_post_id'], $oldNodeData['last_post_date'], $oldNodeData['last_post_user_id'], $oldNodeData['last_post_username'], $oldNodeData['title']);
        }
        if ($newNodeData === false) {
            self::updateForumData($sql, $new_node, 1, $message_count);
        } else {
            self::updateForumData($sql, $new_node, 1, $message_count, $newNodeData['last_post_id'], $newNodeData['last_post_date'], $newNodeData['last_post_user_id'], $newNodeData['last_post_username'], $newNodeData['title']);
        }
        
        return $moveThread;
    }
    
    static function updateForumData(SQL $sql, $node_id, $discussion_count, $message_count = 0, $last_post_id = 0, $last_post_date = 0, $last_post_user_id = 0, $last_post_username = "", $last_thread_title = "") {
        //discussion count -1, 1
        if ($discussion_count !== 1 && $discussion_count !== -1) {
            throw new BadFunctionCallException("Discussion count can only be 1 or -1, adding or deducting.");
        }
        return $sql->query(self::$SQL_QUERIES['UPDATE_XF_FORUM'], array($discussion_count, $message_count, $last_post_id, $last_post_date, $last_post_user_id, $last_post_username, $last_thread_title, $node_id));
    }
    
    //static function getPostCountByNode
    
    static function getPostCountFromThread(SQL $sql, $thread_id) {
        $data = $sql->query(self::$SQL_QUERIES['GET_THREAD_REPLY_COUNT'], array($thread_id));
        if (sizeof($data) === 1) {
            $data = $data[0];
            return intval($data['reply_count']) + 1;
        } 
        throw new BadFunctionCallException("Couldn't find thread with id " . $thread_id);
    }
    
    static function getThreadNodeID(SQL $sql, $thread_id) {
        $data = $sql->query(self::$SQL_QUERIES['GET_THREAD_NODE_ID'], array($thread_id));
        if (sizeof($data) === 1) {
            $data = $data[0];
            return intval($data['node_id']);
        }
        return false;
    }
    
    static function editPostDirty(SQL $sql, $post_id, $new_post) {
        return $sql->query(self::$SQL_QUERIES['EDIT_POST_DATA_DIRTY'], array($new_post, $post_id));
    }
    
    static function editPost(SQL $sql, $post_id, $new_post, $editor_id = null) {
        if ($editor_id === null) {
            $editor_id = self::$DEFAULT_USER_ID;
        }
        //edit post : xf_post
        // message ?, last_edit_date UNIX_TIMESTAMP(), last_edit_user_id ?, edit_count = edit_count + 1, post id ?
        //log - INSERT INTO xf_edit_history (content_type, content_id, edit_user_id, edit_date, 
        //old_text) SELECT 'post' AS content_type, ? AS content_id, ? AS edit_user_id, 
        //UNIX_TIMESTAMP() AS edit_date, message FROM xf_post WHERE post_id = ?
        $sql->query(self::$SQL_QUERIES['EDIT_POST_DATA_LOG'], array($post_id, $editor_id, $post_id));
        $sql->query(self::$SQL_QUERIES['EDIT_POST_DATA'], array($new_post, $editor_id, $post_id));
        //update search index
        $sql->query(self::$SQL_QUERIES['UPDATE_SEARCH_INDEX_MESSAGE_POST'], array($new_post, $post_id));
    }
    
    static function deletePost(SQL $sql, $post_id, $deleted_by_user_id, $deleted_by_username, $ip_addr) {
        //message state to 'deleted' so it's a soft delete.
        //first "delete" it.
        $sql->query(self::$SQL_QUERIES['DELETE_POST'], array($post_id));
        //get the thread ID as we need it.
        $thread_id = $sql->query(self::$SQL_QUERIES['GET_THREAD_ID_FROM_POST'], array($post_id))[0]['thread_id'];
        //set all the positions to -1 if thread id is same but post id is larger.
        $sql->query(self::$SQL_QUERIES['SET_POSITIONS_AFTER_DELETE'], array($thread_id, $post_id));
        //set thread reply_count to -1 and if the post we deleted was the last post, set last_post_id, last_post_user_id, last_post_username, last_post_date to previous one.
        $sql->query(self::$SQL_QUERIES['SET_THREAD_REPLY_COUNT_MINUS_1'], array($thread_id));
        //get max
        $max_post = intval($sql->query(self::$SQL_QUERIES['GET_MAX_POST_ID_FROM_VISIBLE_POSTS'], array($thread_id))[0]['max_post']);
        if ($max_post === 0) {
            throw new UnexpectedValueException("Max post after post soft delete is 0, shouldn't have happened");
        }
        //get post data so we can use it to update thread.
        $postData = $sql->query(self::$SQL_QUERIES['GET_POST_DATA'], array($max_post))[0];
        $last_post_user_id = $postData['user_id'];
        $last_post_username = $postData['username'];
        $last_post_date = $postData['post_date'];
        //get post data as we need that for the moderator log as well.
        $deletedPostData = $sql->query(self::$SQL_QUERIES['GET_POST_DATA'], array($post_id))[0];
        $post_user_id = $deletedPostData['user_id'];
        $post_username = $deletedPostData['username'];
        $sql->query(self::$SQL_QUERIES['UPDATE_THREAD_AFTER_DELETE'], array($max_post, $last_post_user_id, $last_post_username, $last_post_date, $thread_id));
        //get thread title
        $title = $sql->query(self::$SQL_QUERIES['GET_THREAD_TITLE_FROM_THREAD'], array($thread_id))[0]['title'];
        //Set moderator log.
        $sql->query(self::$SQL_QUERIES['SET_DELETE_POST_MODERATOR_LOG'], array($deleted_by_user_id, Ip::convertIpStringToBinary($ip_addr), $post_id, $post_user_id, $post_username, $title, "posts/$post_id/", $thread_id));
        $sql->query(self::$SQL_QUERIES['SET_DELETION_LOG_ENTRY'], array($post_id, $deleted_by_user_id, $deleted_by_username));
        //delete post needs to update xf_forum as well to the latest post.
        //maybe it actually doesn't...
    
    }
    
    static function addPostCountToThread(SQL $sql, $thread_id, $user_id) {
        //check if there is a row already, if not, add it.
        $post_count = $sql->query(self::$SQL_QUERIES['CHECK_POST_COUNT_THREAD'], array($thread_id, $user_id));
        if (sizeof($post_count) === 0) {
            return $sql->query(self::$SQL_QUERIES['INSERT_POST_COUNT_TO_THREAD'], array($thread_id, $user_id));
        } else {
            return $sql->query(self::$SQL_QUERIES['ADD_POST_COUNT_TO_THREAD_USER_POST'], array($thread_id, $user_id));
        }
    }
    
    static function getUsernameByID(SQL $sql, $user_id) {
        $username = trim($sql->query(self::$SQL_QUERIES['GET_USERNAME_BY_ID'], array($user_id))[0]['username']);
        if (strlen($username) > 0) {
            return $username;
        }
        throw new BadFunctionCallException("No user data was found");
    }
    
    static function addIP(SQL $sql, $user_id, $post_id, $xf_ip) {
        return $sql->query(self::$SQL_QUERIES['ADD_IP'], array($user_id, $post_id, $xf_ip))['last_insert_id'];
    }
    

    
    static function addPost(SQL $sql, $user_id, $message, $thread, $ip_addr, $creatingThread = false) {
        //Lets get the username at the start, people might change it or whatever, ID's stay the same.
        $username = self::getUsernameByID($sql, $user_id);
        //Lets turn the IP address into the "wonderful" XenForo format.
        $xf_ip = Ip::convertIpStringToBinary($ip_addr);
        //First you need the max position in the thread.
        if ($creatingThread) {
            $next_pos = 0;
        } else {
            $next_pos = intval($sql->query(self::$SQL_QUERIES['GET_MAX_POS_BY_THREAD_ID'], array($thread))[0]['next_pos']) + 1;
        }
        
        //Now lets create the initial post without the IP data, because we do not have it yet.
        $post_id = $sql->query(self::$SQL_QUERIES['CREATE_POST'], array($thread, $user_id, $username, $message, 0, $next_pos))['last_insert_id'];
        //Post created, time to add IP and link it to the post.
        $ip_id = self::addIP($sql, $user_id, $post_id, $xf_ip);
        $sql->query(self::$SQL_QUERIES['LINK_IP_TO_POST'], array($ip_id, $post_id));
        //Update the thread to show the correct post count.
        if (!$creatingThread) {
            $sql->query(self::$SQL_QUERIES['UPDATE_THREAD_AFTER_POST'], array($post_id, $user_id, $username, $thread));
        } else {
            $sql->query(self::$SQL_QUERIES['UPDATE_THREAD_AFTER_POSTING_THREAD'], array($post_id, $user_id, $username, $thread));
        }
        
        //Update the users post count as well.
        $sql->query(self::$SQL_QUERIES['UPDATE_USER_POST_COUNT'], array($user_id));
        //Get the node ID so we can use it in the metadata for the search index.
        
        //Add the post to the search index so it would pop up everywhere.
        $node_id = self::getThreadNodeID($sql, $thread);
        self::generateSearchIndex($sql, "post", $post_id, "", $message, $user_id, $thread, $node_id);
        
        self::addPostCountToThread($sql, $thread, $user_id);
        self::addPostDataToForumNode($sql, $node_id, $post_id, $user_id);
        return $post_id;
    }
    
    static function closeThread(SQL $sql, $thread_id) {
        return $sql->query(self::$SQL_QUERIES['CLOSE_THREAD'], array($thread_id));
    }
    
    static function generateSearchIndex(SQL $sql, $content_type, $content_id, $title, $message, $user_id, $thread, $node) {
        $metadata = self::generateMetadata($user_id, $content_type, $node, $thread);
        return $sql->query(self::$SQL_QUERIES['GENERATE_SEARCH_INDEX'], array($content_type, $content_id, $title, $message, $metadata, $user_id, $thread))['last_insert_id'];
    }
    
    private static function generateMetadata($user_id, $type, $node, $thread) {
        return "_md_user_$user_id _md_content_$type _md_node_$node _md_thread_$thread";
    }
    
    static function addThread(SQL $sql, $user_id, $title, $message, $node, $ip_addr) {
        //required:
        //node_id, title, reply count 0, view count 0, user_id, username, post_date, sticky, discussion_state
        //discussion_open, first_post_id, first_post_likes 0, last_post_date, last_post_user_id
        //last_post_username, prefix_id 0, tags a:0:{}, custom_fields a:0:{}
        
        //need node_id, title, poster id, poster username
        
        $thread_id = $sql->query(self::$SQL_QUERIES['INSERT_THREAD'], array($node, $title, $user_id, self::getUsernameByID($sql, $user_id)))['last_insert_id'];
        self::generateSearchIndex($sql, "thread", $thread_id, $title, $message, $user_id, $thread_id, $node);
        $post_id = self::addPost($sql, $user_id, $message, $thread_id, $ip_addr, true);
        $sql->query(self::$SQL_QUERIES['UPDATE_THREAD_AFTER_FIRST_POST'], array($thread_id));
        self::addPostDataToForumNode($sql, $node, $post_id, $user_id, $title);
        return $thread_id;
    }
    
    static function addPostDataToForumNode($sql, $node_id, $post_id, $user_id, $title = null, $last_post_date = null) {
        //first get title if it's null.
        $setDiscussionCount = true;
        if ($title === null) {
            $setDiscussionCount = false;
        }
        if ($title === null) {
            $title = $sql->query(self::$SQL_QUERIES['GET_THREAD_TITLE_BY_POST'], array($post_id))[0]['title'];
            if (strlen($title) === 0) {
                return false;
            }
        }
        if ($last_post_date === null) {
            $last_post_date = time();
        }
        //we got title, time to insert some shit.
        //last_post_id = ?, last_post_user_id = ?, last_post_username = ?, last_thread_title = ? WHERE node_id = ?
        $username = self::getUsernameByID($sql, $user_id);
        if ($setDiscussionCount) {
            return $sql->query(self::$SQL_QUERIES['UPDATE_XF_FORUM_AFTER_POST'], array($post_id, $last_post_date, $user_id, $username, $title, $node_id));
        }
        return $sql->query(self::$SQL_QUERIES['UPDATE_XF_FORUM_AFTER_POST_WO_DISCUSSION'], array($post_id, $last_post_date, $user_id, $username, $title, $node_id));
    }
}
