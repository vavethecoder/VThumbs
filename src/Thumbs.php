<?php

class Thumbs {

    public function __construct() {
        add_shortcode('thumbs', array($this, 'showThumbs'));
        add_action('wp_ajax_vote', array($this, 'updateThumbs'));
        add_filter('the_content', array($this, 'getThumbsWithContent'));
    }

    public function showThumbs() {
        $post = get_post()->ID;
        return $this->getUpVotes($post) . '- <a href="' . admin_url('admin-ajax.php?action=vote&type=up&post_id=' . $post) . '"><img src="' . WP_PLUGIN_URL . '/v-thumbs/src/images/up.jpg" alt="Thumbs Up" width="20"></a> '
                . $this->getDownVotes($post) . '- <a href="' . admin_url('admin-ajax.php?action=vote&type=down&post_id=' . $post) . '"><img src="' . WP_PLUGIN_URL . '/v-thumbs/src/images/down.jpg" alt="Thumbs Down" width="20"></a>';
    }
    
    public function getThumbsWithContent() {
        $content = get_post()->post_content;
        return $content . '<br />' . $this->showThumbs();
    }

    public function getUpVotes($post) {
        $thumbs_up = get_post_meta($post, "thumbs_up", true);
        return ($thumbs_up == '') ? 0 : $thumbs_up;
    }

    public function getDownVotes($post) {
        $thumbs_down = get_post_meta($post, "thumbs_down", true);
        return ($thumbs_down == '') ? 0 : $thumbs_down;
    }

    public function updateThumbs() {
        if ("up" == $_REQUEST["type"]) {
            $thumbs_up = get_post_meta($_REQUEST["post_id"], "thumbs_up", true);
            $thumbs_up = ($thumbs_up == '') ? 0 : $thumbs_up;
            $thumbs_count = $thumbs_up + 1;
            $thumbs = update_post_meta($_REQUEST["post_id"], "thumbs_up", $thumbs_count);
        } elseif ("down" == $_REQUEST["type"]) {
            $thumbs_down = get_post_meta($_REQUEST["post_id"], "thumbs_down", true);
            $thumbs_down = ($thumbs_down == '') ? 0 : $thumbs_down;
            $thumbs_count = $thumbs_down + 1;
            $thumbs = update_post_meta($_REQUEST["post_id"], "thumbs_down", $thumbs_count);
        }
        if ($thumbs === false) {
            $result['type'] = "error";
            $result['vote_count'] = $thumbs_count;
        } else {
            $result['type'] = "success";
            $result['vote_count'] = $thumbs_count;
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        exit();
    }

}
