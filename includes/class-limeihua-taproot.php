<?php
/**
 * Main Plugin Class
 *
 * ชุดซอฟต์แวร์ชุดนี้ มีไว้เพื่อเป็นโครงสร้างพื้นฐานทางการเงินยุคใหม่
 * เพื่อรองรับการไหลของเงินจำนวนมหาศาลของท่านผู้เฒ่าหลี่เหมยฮัว หรือ LiMeiHua Grand Mother
 * และ source code นี้สร้างโดย Mr.Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
 * URL: https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna
 */

if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

class LiMeiHua_Taproot {
    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * เริ่มต้น hooks
     */
    private function init_hooks() {
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));

        // AJAX handlers
        add_action('wp_ajax_limeihua_create_token', array($this, 'ajax_create_token'));
        add_action('wp_ajax_limeihua_list_tokens', array($this, 'ajax_list_tokens'));
        add_action('wp_ajax_limeihua_mint_token', array($this, 'ajax_mint_token'));
        add_action('wp_ajax_limeihua_burn_token', array($this, 'ajax_burn_token'));
        add_action('wp_ajax_limeihua_estimate_gas', array($this, 'ajax_estimate_gas'));

        // Register widgets
        add_action('widgets_init', array($this, 'register_widgets'));

        // Register shortcodes
        add_action('init', array($this, 'register_shortcodes'));
    }

    /**
     * เพิ่ม Admin Menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('LiMeiHua Taproot Assets', 'limeihua-taproot-assets'),
            __('Taproot Assets', 'limeihua-taproot-assets'),
            'manage_options',
            'limeihua-taproot-assets',
            array($this, 'render_admin_page'),
            'dashicons-bitcoin',
            30
        );

        add_submenu_page(
            'limeihua-taproot-assets',
            __('Create Token', 'limeihua-taproot-assets'),
            __('Create Token', 'limeihua-taproot-assets'),
            'manage_options',
            'limeihua-taproot-create',
            array($this, 'render_create_page')
        );

        add_submenu_page(
            'limeihua-taproot-assets',
            __('My Tokens', 'limeihua-taproot-assets'),
            __('My Tokens', 'limeihua-taproot-assets'),
            'manage_options',
            'limeihua-taproot-tokens',
            array($this, 'render_tokens_page')
        );

        add_submenu_page(
            'limeihua-taproot-assets',
            __('Settings', 'limeihua-taproot-assets'),
            __('Settings', 'limeihua-taproot-assets'),
            'manage_options',
            'limeihua-taproot-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        include LIMEIHUA_TAPROOT_PLUGIN_DIR . 'admin/dashboard.php';
    }

    /**
     * Render create token page
     */
    public function render_create_page() {
        include LIMEIHUA_TAPROOT_PLUGIN_DIR . 'admin/create-token.php';
    }

    /**
     * Render tokens list page
     */
    public function render_tokens_page() {
        include LIMEIHUA_TAPROOT_PLUGIN_DIR . 'admin/tokens-list.php';
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        include LIMEIHUA_TAPROOT_PLUGIN_DIR . 'admin/settings.php';
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'limeihua-taproot') === false) {
            return;
        }

        wp_enqueue_style(
            'limeihua-taproot-admin',
            LIMEIHUA_TAPROOT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            LIMEIHUA_TAPROOT_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'limeihua-taproot-admin',
            LIMEIHUA_TAPROOT_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            LIMEIHUA_TAPROOT_PLUGIN_VERSION,
            true
        );

        wp_localize_script('limeihua-taproot-admin', 'limeihuaTaproot', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('limeihua_taproot_nonce'),
        ));
    }

    /**
     * Enqueue frontend scripts
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            'limeihua-taproot-frontend',
            LIMEIHUA_TAPROOT_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            LIMEIHUA_TAPROOT_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'limeihua-taproot-frontend',
            LIMEIHUA_TAPROOT_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            LIMEIHUA_TAPROOT_PLUGIN_VERSION,
            true
        );

        wp_localize_script('limeihua-taproot-frontend', 'limeihuaTaprootFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('limeihua_taproot_frontend_nonce'),
        ));
    }

    /**
     * AJAX: Create token
     */
    public function ajax_create_token() {
        check_ajax_referer('limeihua_taproot_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $name = sanitize_text_field($_POST['name']);
        $symbol = sanitize_text_field($_POST['symbol']);
        $supply = sanitize_text_field($_POST['supply']);
        $decimals = intval($_POST['decimals']);
        $type = sanitize_text_field($_POST['type']);
        $owner = sanitize_text_field($_POST['owner']);

        // สร้าง token
        $api = new LiMeiHua_Taproot_API();
        $result = $api->create_token($name, $symbol, $supply, $decimals, $type, $owner);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result['message']);
        }
    }

    /**
     * AJAX: List tokens
     */
    public function ajax_list_tokens() {
        check_ajax_referer('limeihua_taproot_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        global $wpdb;
        $table = $wpdb->prefix . 'limeihua_tokens';
        $tokens = $wpdb->get_results("SELECT * FROM $table WHERE user_id = " . get_current_user_id());

        wp_send_json_success($tokens);
    }

    /**
     * AJAX: Mint token
     */
    public function ajax_mint_token() {
        check_ajax_referer('limeihua_taproot_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $token_id = intval($_POST['token_id']);
        $amount = sanitize_text_field($_POST['amount']);

        $api = new LiMeiHua_Taproot_API();
        $result = $api->mint_token($token_id, $amount);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result['message']);
        }
    }

    /**
     * AJAX: Burn token
     */
    public function ajax_burn_token() {
        check_ajax_referer('limeihua_taproot_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $token_id = intval($_POST['token_id']);
        $amount = sanitize_text_field($_POST['amount']);

        $api = new LiMeiHua_Taproot_API();
        $result = $api->burn_token($token_id, $amount);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result['message']);
        }
    }

    /**
     * AJAX: Estimate gas
     */
    public function ajax_estimate_gas() {
        check_ajax_referer('limeihua_taproot_nonce', 'nonce');

        $type = sanitize_text_field($_POST['type']);
        $has_metadata = isset($_POST['metadata']) ? true : false;

        $api = new LiMeiHua_Taproot_API();
        $result = $api->estimate_gas($type, $has_metadata);

        wp_send_json_success($result);
    }

    /**
     * Register widgets
     */
    public function register_widgets() {
        register_widget('LiMeiHua_Taproot_Widget');
    }

    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('limeihua_create_token', array('LiMeiHua_Taproot_Shortcode', 'render_create_form'));
        add_shortcode('limeihua_tokens_list', array('LiMeiHua_Taproot_Shortcode', 'render_tokens_list'));
    }
}
?>
