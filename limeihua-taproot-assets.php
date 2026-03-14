<?php
/**
 * Plugin Name: LiMeiHua Create Taproot Assets
 * Plugin URI: https://github.com/kanutsanan1988/LiMeiHua-Create-Taproot-Assets-WP-Plugin
 * Description: สร้าง Taproot Assets Tokens บน Bitcoin Lightning Network ได้ง่ายๆ ผ่าน WordPress Admin Dashboard
 * Version: 1.0.0
 * Author: Mr. Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
 * Author URI: https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: limeihua-taproot-assets
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * ชุดซอฟต์แวร์ชุดนี้ มีไว้เพื่อเป็นโครงสร้างพื้นฐานทางการเงินยุคใหม่
 * เพื่อรองรับการไหลของเงินจำนวนมหาศาลของท่านผู้เฒ่าหลี่เหมยฮัว หรือ LiMeiHua Grand Mother
 * และ source code นี้สร้างโดย Mr.Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
 * URL: https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna
 */

// ป้องกันการเข้าถึงไฟล์โดยตรง
if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

// กำหนด constants
define('LIMEIHUA_TAPROOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LIMEIHUA_TAPROOT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LIMEIHUA_TAPROOT_PLUGIN_VERSION', '1.0.0');

/**
 * โหลดไฟล์ที่จำเป็น
 */
require_once LIMEIHUA_TAPROOT_PLUGIN_DIR . 'includes/class-limeihua-taproot.php';
require_once LIMEIHUA_TAPROOT_PLUGIN_DIR . 'includes/class-admin-page.php';
require_once LIMEIHUA_TAPROOT_PLUGIN_DIR . 'includes/class-taproot-api.php';
require_once LIMEIHUA_TAPROOT_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once LIMEIHUA_TAPROOT_PLUGIN_DIR . 'includes/class-widget.php';

/**
 * เริ่มต้น Plugin
 */
function limeihua_taproot_init() {
    // โหลด text domain สำหรับการแปลภาษา
    load_plugin_textdomain(
        'limeihua-taproot-assets',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );

    // เริ่มต้น main class
    LiMeiHua_Taproot::get_instance();
}
add_action('plugins_loaded', 'limeihua_taproot_init');

/**
 * เมื่อเปิดใช้งาน Plugin
 */
function limeihua_taproot_activate() {
    // สร้าง database tables
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // ตาราง tokens
    $tokens_table = $wpdb->prefix . 'limeihua_tokens';
    $sql_tokens = "CREATE TABLE IF NOT EXISTS $tokens_table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        name VARCHAR(255) NOT NULL,
        symbol VARCHAR(32) NOT NULL,
        initial_supply VARCHAR(255) NOT NULL,
        current_supply VARCHAR(255) NOT NULL,
        decimals INT NOT NULL DEFAULT 8,
        token_type ENUM('fixed', 'mintable', 'burnable') NOT NULL,
        owner_address VARCHAR(512) NOT NULL,
        asset_id VARCHAR(255),
        batch_txid VARCHAR(255),
        estimated_fee BIGINT,
        status ENUM('pending', 'minting', 'confirmed', 'failed') NOT NULL DEFAULT 'pending',
        metadata LONGTEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY user_id (user_id),
        KEY status (status)
    ) $charset_collate;";

    // ตาราง transactions
    $transactions_table = $wpdb->prefix . 'limeihua_token_transactions';
    $sql_transactions = "CREATE TABLE IF NOT EXISTS $transactions_table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        token_id BIGINT UNSIGNED NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        tx_type ENUM('create', 'mint', 'burn') NOT NULL,
        amount VARCHAR(255) NOT NULL,
        tx_hash VARCHAR(255),
        fee BIGINT,
        status ENUM('pending', 'confirmed', 'failed') NOT NULL DEFAULT 'pending',
        notes LONGTEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY token_id (token_id),
        KEY user_id (user_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_tokens);
    dbDelta($sql_transactions);

    // บันทึก version
    update_option('limeihua_taproot_version', LIMEIHUA_TAPROOT_PLUGIN_VERSION);
}
register_activation_hook(__FILE__, 'limeihua_taproot_activate');

/**
 * เมื่อปิดใช้งาน Plugin
 */
function limeihua_taproot_deactivate() {
    // ทำความสะอาด
    wp_clear_scheduled_hook('limeihua_taproot_hourly_check');
}
register_deactivation_hook(__FILE__, 'limeihua_taproot_deactivate');

/**
 * เมื่อลบ Plugin
 */
function limeihua_taproot_uninstall() {
    // ลบ database tables (ตัวเลือก)
    if (get_option('limeihua_taproot_delete_on_uninstall')) {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}limeihua_tokens");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}limeihua_token_transactions");
        delete_option('limeihua_taproot_version');
    }
}
register_uninstall_hook(__FILE__, 'limeihua_taproot_uninstall');
?>
