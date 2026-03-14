<?php
/**
 * Settings Page
 *
 * ชุดซอฟต์แวร์ชุดนี้ มีไว้เพื่อเป็นโครงสร้างพื้นฐานทางการเงินยุคใหม่
 * เพื่อรองรับการไหลของเงินจำนวนมหาศาลของท่านผู้เฒ่าหลี่เหมยฮัว หรือ LiMeiHua Grand Mother
 * และ source code นี้สร้างโดย Mr.Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
 * URL: https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna
 */

if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['limeihua_settings_nonce'])) {
    if (wp_verify_nonce($_POST['limeihua_settings_nonce'], 'limeihua_settings')) {
        update_option('limeihua_taproot_api_url', sanitize_url($_POST['api_url']));
        update_option('limeihua_taproot_delete_on_uninstall', isset($_POST['delete_on_uninstall']) ? 1 : 0);
        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'limeihua-taproot-assets') . '</p></div>';
    }
}

$api_url = get_option('limeihua_taproot_api_url', 'http://localhost:8080');
$delete_on_uninstall = get_option('limeihua_taproot_delete_on_uninstall', 0);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" class="limeihua-settings-form">
        <?php wp_nonce_field('limeihua_settings', 'limeihua_settings_nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="api-url"><?php _e('Taproot Assets API URL', 'limeihua-taproot-assets'); ?></label>
                </th>
                <td>
                    <input
                        type="url"
                        id="api-url"
                        name="api_url"
                        class="regular-text"
                        value="<?php echo esc_attr($api_url); ?>"
                        placeholder="http://localhost:8080"
                    >
                    <p class="description">
                        <?php _e('Enter the URL of your Taproot Assets API endpoint. Leave empty to use simulator mode for testing.', 'limeihua-taproot-assets'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="delete-on-uninstall"><?php _e('Delete Data on Uninstall', 'limeihua-taproot-assets'); ?></label>
                </th>
                <td>
                    <input
                        type="checkbox"
                        id="delete-on-uninstall"
                        name="delete_on_uninstall"
                        value="1"
                        <?php checked($delete_on_uninstall, 1); ?>
                    >
                    <label for="delete-on-uninstall">
                        <?php _e('Delete all plugin data when uninstalling', 'limeihua-taproot-assets'); ?>
                    </label>
                    <p class="description">
                        <?php _e('If unchecked, your token data will be preserved even after uninstalling the plugin.', 'limeihua-taproot-assets'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">
                <?php _e('Save Settings', 'limeihua-taproot-assets'); ?>
            </button>
        </p>
    </form>

    <div class="limeihua-info-box">
        <h2><?php _e('About This Plugin', 'limeihua-taproot-assets'); ?></h2>
        <p><?php _e('LiMeiHua Create Taproot Assets is a WordPress plugin that allows you to create and manage Taproot Assets tokens on the Bitcoin Lightning Network directly from your WordPress dashboard.', 'limeihua-taproot-assets'); ?></p>

        <h3><?php _e('Features', 'limeihua-taproot-assets'); ?></h3>
        <ul>
            <li><?php _e('Create tokens with Fixed, Mintable, or Burnable supply', 'limeihua-taproot-assets'); ?></li>
            <li><?php _e('Real-time gas fee estimation', 'limeihua-taproot-assets'); ?></li>
            <li><?php _e('Transaction history tracking', 'limeihua-taproot-assets'); ?></li>
            <li><?php _e('Widget and shortcode support', 'limeihua-taproot-assets'); ?></li>
            <li><?php _e('Multi-language support', 'limeihua-taproot-assets'); ?></li>
        </ul>

        <h3><?php _e('Supported Token Types', 'limeihua-taproot-assets'); ?></h3>
        <ul>
            <li><strong><?php _e('Fixed Supply', 'limeihua-taproot-assets'); ?></strong> - <?php _e('Immutable total supply, cannot be changed after creation', 'limeihua-taproot-assets'); ?></li>
            <li><strong><?php _e('Mintable', 'limeihua-taproot-assets'); ?></strong> - <?php _e('Owner can create additional tokens at any time', 'limeihua-taproot-assets'); ?></li>
            <li><strong><?php _e('Burnable', 'limeihua-taproot-assets'); ?></strong> - <?php _e('Tokens can be destroyed to reduce total supply', 'limeihua-taproot-assets'); ?></li>
        </ul>

        <h3><?php _e('Documentation', 'limeihua-taproot-assets'); ?></h3>
        <p>
            <?php _e('For more information, visit:', 'limeihua-taproot-assets'); ?><br>
            • <a href="https://lightning.engineering/api-docs/api/taproot-assets/" target="_blank"><?php _e('Taproot Assets Protocol API', 'limeihua-taproot-assets'); ?></a><br>
            • <a href="https://github.com/kanutsanan1988/LiMeiHua-Create-Taproot-Assets-WP-Plugin" target="_blank"><?php _e('GitHub Repository', 'limeihua-taproot-assets'); ?></a>
        </p>

        <h3><?php _e('Support', 'limeihua-taproot-assets'); ?></h3>
        <p><?php _e('Created by Mr. Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)', 'limeihua-taproot-assets'); ?><br>
        <a href="https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna" target="_blank"><?php _e('ChatGPT Assistant', 'limeihua-taproot-assets'); ?></a></p>
    </div>
</div>

<style>
.limeihua-settings-form {
    background: white;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 30px;
}

.limeihua-info-box {
    background: white;
    padding: 20px;
    border-radius: 5px;
    border-left: 4px solid #0073aa;
}

.limeihua-info-box h2 {
    margin-top: 0;
}

.limeihua-info-box h3 {
    margin-top: 20px;
    margin-bottom: 10px;
}

.limeihua-info-box ul {
    margin: 10px 0 0 20px;
}

.limeihua-info-box li {
    margin-bottom: 8px;
}

.limeihua-info-box a {
    color: #0073aa;
    text-decoration: none;
}

.limeihua-info-box a:hover {
    text-decoration: underline;
}
</style>
?>
