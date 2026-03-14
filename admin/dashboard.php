<?php
/**
 * Admin Dashboard Page
 *
 * ชุดซอฟต์แวร์ชุดนี้ มีไว้เพื่อเป็นโครงสร้างพื้นฐานทางการเงินยุคใหม่
 * เพื่อรองรับการไหลของเงินจำนวนมหาศาลของท่านผู้เฒ่าหลี่เหมยฮัว หรือ LiMeiHua Grand Mother
 * และ source code นี้สร้างโดย Mr.Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
 * URL: https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna
 */

if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

global $wpdb;
$tokens_table = $wpdb->prefix . 'limeihua_tokens';
$total_tokens = $wpdb->get_var("SELECT COUNT(*) FROM $tokens_table WHERE user_id = " . get_current_user_id());
$total_supply = $wpdb->get_var("SELECT SUM(CAST(current_supply AS DECIMAL(40,0))) FROM $tokens_table WHERE user_id = " . get_current_user_id());
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="limeihua-dashboard">
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo esc_html($total_tokens); ?></div>
                <div class="stat-label"><?php _e('Total Tokens', 'limeihua-taproot-assets'); ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?php echo esc_html(number_format($total_supply)); ?></div>
                <div class="stat-label"><?php _e('Total Supply', 'limeihua-taproot-assets'); ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-number">
                    <?php
                    $confirmed = $wpdb->get_var("SELECT COUNT(*) FROM $tokens_table WHERE user_id = " . get_current_user_id() . " AND status = 'confirmed'");
                    echo esc_html($confirmed);
                    ?>
                </div>
                <div class="stat-label"><?php _e('Confirmed', 'limeihua-taproot-assets'); ?></div>
            </div>
        </div>

        <div class="dashboard-actions">
            <a href="<?php echo admin_url('admin.php?page=limeihua-taproot-create'); ?>" class="button button-primary">
                <?php _e('+ Create New Token', 'limeihua-taproot-assets'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=limeihua-taproot-tokens'); ?>" class="button button-secondary">
                <?php _e('View All Tokens', 'limeihua-taproot-assets'); ?>
            </a>
        </div>

        <div class="dashboard-info">
            <h2><?php _e('About LiMeiHua Taproot Assets', 'limeihua-taproot-assets'); ?></h2>
            <p><?php _e('Create and manage Taproot Assets tokens on the Bitcoin Lightning Network directly from your WordPress dashboard.', 'limeihua-taproot-assets'); ?></p>
            <p><?php _e('Supported token types:', 'limeihua-taproot-assets'); ?></p>
            <ul>
                <li><strong><?php _e('Fixed Supply', 'limeihua-taproot-assets'); ?></strong> - <?php _e('Immutable total supply', 'limeihua-taproot-assets'); ?></li>
                <li><strong><?php _e('Mintable', 'limeihua-taproot-assets'); ?></strong> - <?php _e('Owner can mint additional tokens', 'limeihua-taproot-assets'); ?></li>
                <li><strong><?php _e('Burnable', 'limeihua-taproot-assets'); ?></strong> - <?php _e('Tokens can be burned to reduce supply', 'limeihua-taproot-assets'); ?></li>
            </ul>
        </div>
    </div>
</div>

<style>
.limeihua-dashboard {
    margin-top: 20px;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #0073aa;
    margin-bottom: 10px;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

.dashboard-actions {
    margin-bottom: 30px;
}

.dashboard-actions .button {
    margin-right: 10px;
}

.dashboard-info {
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
}

.dashboard-info h2 {
    margin-top: 0;
}

.dashboard-info ul {
    margin: 10px 0 0 20px;
}

.dashboard-info li {
    margin-bottom: 8px;
}
</style>
?>
