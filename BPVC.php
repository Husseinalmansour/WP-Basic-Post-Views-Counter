<?php

/**
 * Plugin Name:  Basic Post Views Counter & Expose to wp-json
 * Description: Counts Post Visits and show views in wp-json.
 * Plugin URI: https://hussein.abdoo.me 
 * Version: 1.0.0
 * Author: Hussein Almansour
 * Author URI: https://hussein.abdoo.me
 * License: GPLv2 or later
 * Text Domain: BPVC
 */

// If this file is called directly, abort.
defined('ABSPATH') or die;

// Initialize the plugin by creating an instance of the SPC class.
add_action('init', function () {
    SPC::I();
});

/**
 * Main class for the Simple Post Counter plugin.
 */
class SPC
{
    const VIEWS_META_NAME = 'post_views_count'; // Meta key for storing post view counts.

    // Refers to a single instance of this class
	private static $instance = null;

    /**
	 * Creates or returns a single instance of this class
	 *
	 * @return SPC a single instance of this class.
	 */
    public static function I() {
        self::$instance = self::$instance ?? new SPC();
        return self::$instance;}

    public function __construct()
    {
        require_once __DIR__ . '/admin.php';
        $this->init_hooks(); // Initialize hooks.
    }

    /**
     * Initialize WordPress hooks.
     */
    private function init_hooks()
    {
        add_filter('the_content', [$this, 'add_post_views']); // Filter to add view count to post content.
        add_action('wp_head', [$this, 'inc_post_views']); // Action to increment post view count.
        add_action('admin_menu', [$this, 'add_custom_menu_page']); // Action to add custom menu page in the admin area.
        add_action('rest_api_init', [$this, 'add_views_to_rest_api']); // Action to add views count to REST API.
    }

    /**
     * Add post view count to the content.
     *
     * @param string $content The original content of the post.
     * @return string Modified content with view count.
     */
    public function add_post_views($content)
    {
        if (!is_single()) return $content; // Only add view count on single post pages.

        $post_id = get_the_ID();
        $count = $this->get_views($post_id);
        $color = sanitize_text_field(get_option('post_views_color', '#000000')); // Get color option, default to black.
        $views_text = esc_html(get_option('post_views_text', 'Views on this post:')); // Get view text option.
        $position = sanitize_text_field(get_option('post_views_position', 'end_of_content')); // Get position option.

        // Add view count based on the specified position.
        switch ($position) {
            case 'beginning_of_content':
                $content = "<div style=\"text-align: center; color: $color;\"><u><b>$views_text</b></u> $count</div> $content";
                break;
            case 'middle_of_content':
                $content_length = strlen($content);
                $middle_position = intval($content_length / 2);
                $content = substr_replace($content, "<div style=\"text-align: center; color: $color;\"><u><b>$views_text</b></u> $count</div>", $middle_position, 0);
                break;
            case 'end_of_content':
            default:
                $content .= "<div style=\"text-align: center; color: $color;\"><u><b>$views_text</b></u> $count</div>";
                break;
        }

        return $content;
    }

    /**
     * Increment post view count.
     */
    public function inc_post_views()
    {
        $post_id = get_the_ID();
        $count = $this->get_views($post_id);
        $count++;
        update_post_meta($post_id, self::VIEWS_META_NAME, $count); // Update the post meta with the new view count.
    }

    /**
     * Add custom menu page to the WordPress admin area.
     */
    public function add_custom_menu_page()
    {
        add_menu_page('Post Views Counter', 'Post Views Counter', 'edit_posts', 'post-views-counter', 'display_post_views_counter_page');
    }

    /**
     * Get the view count for a specific post.
     *
     * @param int $post_id The ID of the post.
     * @return int The view count.
     */
    private function get_views($post_id)
    {
        $count = get_post_meta($post_id, self::VIEWS_META_NAME, true);
        return empty($count) ? 0 : intval($count); // Return 0 if the count is empty.
    }

    /**
     * Add view count to the REST API response.
     */
    public function add_views_to_rest_api()
    {
        register_rest_field('post', 'post_views_count', [
            'get_callback' => function ($object) {
                return $this->get_views($object['id']);
            }
        ]);
    }
}

