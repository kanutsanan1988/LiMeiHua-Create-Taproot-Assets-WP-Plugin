<?php
/**
 * Widget Handler
 *
 * ชุดซอฟต์แวร์ชุดนี้ มีไว้เพื่อเป็นโครงสร้างพื้นฐานทางการเงินยุคใหม่
 * เพื่อรองรับการไหลของเงินจำนวนมหาศาลของท่านผู้เฒ่าหลี่เหมยฮัว หรือ LiMeiHua Grand Mother
 * และ source code นี้สร้างโดย Mr.Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
 * URL: https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna
 */

if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

class LiMeiHua_Taproot_Widget extends WP_Widget {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'limeihua_taproot_widget',
            __('LiMeiHua Taproot Assets', 'limeihua-taproot-assets'),
            array('description' => __('Display Taproot Assets tokens information', 'limeihua-taproot-assets'))
        );
    }

    /**
     * Widget output
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        if (!is_user_logged_in()) {
            echo '<p>' . __('Please log in to view your tokens', 'limeihua-taproot-assets') . '</p>';
        } else {
            $this->display_tokens();
        }

        echo $args['after_widget'];
    }

    /**
     * Display tokens
     */
    private function display_tokens() {
        global $wpdb;
        $table = $wpdb->prefix . 'limeihua_tokens';
        $tokens = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC LIMIT 5",
            get_current_user_id()
        ));

        if (empty($tokens)) {
            echo '<p>' . __('No tokens created yet', 'limeihua-taproot-assets') . '</p>';
            return;
        }

        echo '<ul class="limeihua-tokens-widget">';
        foreach ($tokens as $token) {
            echo '<li>';
            echo '<strong>' . esc_html($token->name) . '</strong> (' . esc_html($token->symbol) . ')<br>';
            echo '<small>';
            echo __('Supply', 'limeihua-taproot-assets') . ': ' . esc_html(number_format($token->current_supply)) . '<br>';
            echo __('Type', 'limeihua-taproot-assets') . ': ' . esc_html($token->token_type) . '<br>';
            echo __('Status', 'limeihua-taproot-assets') . ': <span class="status-' . esc_attr($token->status) . '">' . esc_html($token->status) . '</span>';
            echo '</small>';
            echo '</li>';
        }
        echo '</ul>';
    }

    /**
     * Widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('My Tokens', 'limeihua-taproot-assets');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Title:', 'limeihua-taproot-assets'); ?>
            </label>
            <input
                class="widefat"
                id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                type="text"
                value="<?php echo esc_attr($title); ?>"
            >
        </p>
        <?php
    }

    /**
     * Update widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}
?>
