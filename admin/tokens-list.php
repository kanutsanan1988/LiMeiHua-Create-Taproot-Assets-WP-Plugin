<?php
/**
 * Tokens List Page
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
$table = $wpdb->prefix . 'limeihua_tokens';
$tokens = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC",
    get_current_user_id()
));
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="limeihua-tokens-list">
        <?php if (empty($tokens)): ?>
            <div class="notice notice-info">
                <p><?php _e('No tokens created yet. ', 'limeihua-taproot-assets'); ?>
                    <a href="<?php echo admin_url('admin.php?page=limeihua-taproot-create'); ?>">
                        <?php _e('Create your first token', 'limeihua-taproot-assets'); ?>
                    </a>
                </p>
            </div>
        <?php else: ?>
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Name', 'limeihua-taproot-assets'); ?></th>
                        <th><?php _e('Symbol', 'limeihua-taproot-assets'); ?></th>
                        <th><?php _e('Supply', 'limeihua-taproot-assets'); ?></th>
                        <th><?php _e('Decimals', 'limeihua-taproot-assets'); ?></th>
                        <th><?php _e('Type', 'limeihua-taproot-assets'); ?></th>
                        <th><?php _e('Status', 'limeihua-taproot-assets'); ?></th>
                        <th><?php _e('Created', 'limeihua-taproot-assets'); ?></th>
                        <th><?php _e('Actions', 'limeihua-taproot-assets'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tokens as $token): ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($token->name); ?></strong>
                                <?php if ($token->metadata): ?>
                                    <br><small><?php echo esc_html(substr($token->metadata, 0, 50)); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><code><?php echo esc_html($token->symbol); ?></code></td>
                            <td><?php echo esc_html(number_format($token->current_supply)); ?></td>
                            <td><?php echo esc_html($token->decimals); ?></td>
                            <td>
                                <span class="badge badge-<?php echo esc_attr($token->token_type); ?>">
                                    <?php echo esc_html(ucfirst($token->token_type)); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status status-<?php echo esc_attr($token->status); ?>">
                                    <?php echo esc_html(ucfirst($token->status)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($token->created_at))); ?></td>
                            <td>
                                <?php if ($token->token_type === 'mintable' && $token->status === 'confirmed'): ?>
                                    <button class="button button-small mint-btn" data-token-id="<?php echo esc_attr($token->id); ?>">
                                        <?php _e('Mint', 'limeihua-taproot-assets'); ?>
                                    </button>
                                <?php endif; ?>

                                <?php if ($token->token_type === 'burnable' && $token->status === 'confirmed'): ?>
                                    <button class="button button-small burn-btn" data-token-id="<?php echo esc_attr($token->id); ?>">
                                        <?php _e('Burn', 'limeihua-taproot-assets'); ?>
                                    </button>
                                <?php endif; ?>

                                <button class="button button-small view-btn" data-token-id="<?php echo esc_attr($token->id); ?>">
                                    <?php _e('Details', 'limeihua-taproot-assets'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<style>
.badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
}

.badge-fixed {
    background: #e7f3ff;
    color: #0073aa;
}

.badge-mintable {
    background: #fff3cd;
    color: #856404;
}

.badge-burnable {
    background: #f8d7da;
    color: #721c24;
}

.status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.status-confirmed {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-minting {
    background: #cfe2ff;
    color: #084298;
}

.status-failed {
    background: #f8d7da;
    color: #721c24;
}

.button.button-small {
    padding: 3px 8px;
    font-size: 12px;
    margin-right: 5px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Mint button
    $(document).on('click', '.mint-btn', function() {
        var tokenId = $(this).data('token-id');
        var amount = prompt('<?php _e('Enter amount to mint:', 'limeihua-taproot-assets'); ?>');
        if (amount) {
            $.ajax({
                url: limeihuaTaproot.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'limeihua_mint_token',
                    nonce: limeihuaTaproot.nonce,
                    token_id: tokenId,
                    amount: amount
                },
                success: function(response) {
                    if (response.success) {
                        alert('<?php _e('Tokens minted successfully!', 'limeihua-taproot-assets'); ?>');
                        location.reload();
                    } else {
                        alert('<?php _e('Error:', 'limeihua-taproot-assets'); ?> ' + response.data);
                    }
                }
            });
        }
    });

    // Burn button
    $(document).on('click', '.burn-btn', function() {
        var tokenId = $(this).data('token-id');
        var amount = prompt('<?php _e('Enter amount to burn:', 'limeihua-taproot-assets'); ?>');
        if (amount) {
            $.ajax({
                url: limeihuaTaproot.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'limeihua_burn_token',
                    nonce: limeihuaTaproot.nonce,
                    token_id: tokenId,
                    amount: amount
                },
                success: function(response) {
                    if (response.success) {
                        alert('<?php _e('Tokens burned successfully!', 'limeihua-taproot-assets'); ?>');
                        location.reload();
                    } else {
                        alert('<?php _e('Error:', 'limeihua-taproot-assets'); ?> ' + response.data);
                    }
                }
            });
        }
    });

    // View details button
    $(document).on('click', '.view-btn', function() {
        var tokenId = $(this).data('token-id');
        alert('<?php _e('Token ID:', 'limeihua-taproot-assets'); ?> ' + tokenId);
    });
});
</script>
?>
