<?php
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
});

add_action('admin_init', function () {
    register_setting('post_views_settings', 'post_views_color');
    register_setting('post_views_settings', 'post_views_text');
    register_setting('post_views_settings', 'post_views_position');
});

function display_post_views_counter_page()
{
?>

    <div class="wrap">
        <h2> <?php echo esc_html(__('Post Views Counter Settings', 'BPVC'))?> </h2>
        <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) : ?>
            <div id="message" class="updated notice is-dismissible">
                <p><?esc_html(translate('Changes saved.'))?></p>
            </div>
        <?php endif; ?>
        <form method="post" action="options.php">

            <?php settings_fields('post_views_settings'); ?>
            <?php do_settings_sections('post_views_settings'); ?>
            
            <table class="form-table">
            
                <tr valign="top">
                    <th scope="row"><?php echo esc_html(__('Views Text', 'BPVC'))?></th>
                    <td>
                        <?php $views_text = get_option('post_views_text', 'Views on this post:'); ?>
                        <input type="text" name="post_views_text" value="<?php echo $views_text; ?>" />
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php echo esc_html(__('Post views Count Color','BPVC'))?></th>
                    <td>
                        <?php $color = get_option('post_views_color', '#000000'); ?>
                        <input type="text" name="post_views_color" value="<?php echo $color; ?>" class="color-field" />
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php echo esc_html( __('Post views Position', 'BPVC') ); ?></th>
                    <td>
                        <?php $position = get_option('post_views_position', 'end_of_content'); ?>
                        <select name="post_views_position">
                        <option value="end_of_content" <?php selected($position, 'end_of_content'); ?>><?php echo esc_html(__('End of Content', 'BPVC')); ?></option>
                            <option value="beginning_of_content" <?php selected($position, 'beginning_of_content'); ?>><?php echo (__('Beginning of Content','BPVC'))?></option>
                            <option value="middle_of_content" <?php selected($position, 'middle_of_content'); ?>><?php echo (__('Middle of Content','BPVC'))?></option>
                        </select>
                    </td>
                </tr>
            
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <script>
            jQuery(document).ready(function($){
                $('.color-field').wpColorPicker();
            });
        </script>
<?php
}
