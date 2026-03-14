<?php
/**
 * Shortcode Handler
 *
 * ชุดซอฟต์แวร์ชุดนี้ มีไว้เพื่อเป็นโครงสร้างพื้นฐานทางการเงินยุคใหม่
 * เพื่อรองรับการไหลของเงินจำนวนมหาศาลของท่านผู้เฒ่าหลี่เหมยฮัว หรือ LiMeiHua Grand Mother
 * และ source code นี้สร้างโดย Mr.Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
 * URL: https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna
 */

if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

class LiMeiHua_Taproot_Shortcode {
    /**
     * Render create token form
     * Usage: [limeihua_create_token]
     */
    public static function render_create_form() {
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return '<p>' . __('You do not have permission to create tokens', 'limeihua-taproot-assets') . '</p>';
        }

        ob_start();
        ?>
        <div class="limeihua-create-token-form">
            <h2><?php _e('Create Taproot Asset Token', 'limeihua-taproot-assets'); ?></h2>
            <form id="limeihua-create-form">
                <div class="form-group">
                    <label><?php _e('Token Name', 'limeihua-taproot-assets'); ?> *</label>
                    <input type="text" name="name" required placeholder="<?php _e('e.g., My Token', 'limeihua-taproot-assets'); ?>">
                </div>

                <div class="form-group">
                    <label><?php _e('Token Symbol', 'limeihua-taproot-assets'); ?> *</label>
                    <input type="text" name="symbol" required placeholder="<?php _e('e.g., MYTOKEN', 'limeihua-taproot-assets'); ?>">
                </div>

                <div class="form-group">
                    <label><?php _e('Initial Supply', 'limeihua-taproot-assets'); ?> *</label>
                    <input type="number" name="supply" required placeholder="<?php _e('e.g., 1000000', 'limeihua-taproot-assets'); ?>">
                </div>

                <div class="form-group">
                    <label><?php _e('Decimals', 'limeihua-taproot-assets'); ?></label>
                    <input type="number" name="decimals" value="8" min="0" max="18">
                </div>

                <div class="form-group">
                    <label><?php _e('Token Type', 'limeihua-taproot-assets'); ?> *</label>
                    <select name="type" required>
                        <option value="fixed"><?php _e('Fixed Supply', 'limeihua-taproot-assets'); ?></option>
                        <option value="mintable"><?php _e('Mintable', 'limeihua-taproot-assets'); ?></option>
                        <option value="burnable"><?php _e('Burnable', 'limeihua-taproot-assets'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label><?php _e('Owner Address', 'limeihua-taproot-assets'); ?> *</label>
                    <input type="text" name="owner" required placeholder="<?php _e('Bitcoin Lightning Address', 'limeihua-taproot-assets'); ?>">
                </div>

                <div class="form-group">
                    <label><?php _e('Description (optional)', 'limeihua-taproot-assets'); ?></label>
                    <textarea name="metadata" rows="4" placeholder="<?php _e('Token description', 'limeihua-taproot-assets'); ?>"></textarea>
                </div>

                <button type="submit" class="button button-primary"><?php _e('Create Token', 'limeihua-taproot-assets'); ?></button>
            </form>
            <div id="limeihua-create-result"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render tokens list
     * Usage: [limeihua_tokens_list]
     */
    public static function render_tokens_list() {
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return '<p>' . __('You do not have permission to view tokens', 'limeihua-taproot-assets') . '</p>';
        }

        global $wpdb;
        $table = $wpdb->prefix . 'limeihua_tokens';
        $tokens = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC",
            get_current_user_id()
        ));

        ob_start();
        ?>
        <div class="limeihua-tokens-list">
            <h2><?php _e('My Tokens', 'limeihua-taproot-assets'); ?></h2>

            <?php if (empty($tokens)): ?>
                <p><?php _e('No tokens created yet', 'limeihua-taproot-assets'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat striped">
                    <thead>
                        <tr>
                            <th><?php _e('Name', 'limeihua-taproot-assets'); ?></th>
                            <th><?php _e('Symbol', 'limeihua-taproot-assets'); ?></th>
                            <th><?php _e('Supply', 'limeihua-taproot-assets'); ?></th>
                            <th><?php _e('Type', 'limeihua-taproot-assets'); ?></th>
                            <th><?php _e('Status', 'limeihua-taproot-assets'); ?></th>
                            <th><?php _e('Created', 'limeihua-taproot-assets'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tokens as $token): ?>
                            <tr>
                                <td><?php echo esc_html($token->name); ?></td>
                                <td><code><?php echo esc_html($token->symbol); ?></code></td>
                                <td><?php echo esc_html(number_format($token->current_supply)); ?></td>
                                <td><span class="badge badge-<?php echo esc_attr($token->token_type); ?>"><?php echo esc_html($token->token_type); ?></span></td>
                                <td><span class="status status-<?php echo esc_attr($token->status); ?>"><?php echo esc_html($token->status); ?></span></td>
                                <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($token->created_at))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>
