<?php
/**
 * Create Token Page
 *
 * ชุดซอฟต์แวร์ชุดนี้ มีไว้เพื่อเป็นโครงสร้างพื้นฐานทางการเงินยุคใหม่
 * เพื่อรองรับการไหลของเงินจำนวนมหาศาลของท่านผู้เฒ่าหลี่เหมยฮัว หรือ LiMeiHua Grand Mother
 * และ source code นี้สร้างโดย Mr.Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
 * URL: https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna
 */

if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="limeihua-create-token">
        <form id="limeihua-token-form" class="limeihua-form">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="token-name"><?php _e('Token Name', 'limeihua-taproot-assets'); ?> *</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="token-name"
                            name="name"
                            class="regular-text"
                            required
                            placeholder="<?php _e('e.g., My Custom Token', 'limeihua-taproot-assets'); ?>"
                        >
                        <p class="description"><?php _e('The name of your token (max 255 characters)', 'limeihua-taproot-assets'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="token-symbol"><?php _e('Token Symbol', 'limeihua-taproot-assets'); ?> *</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="token-symbol"
                            name="symbol"
                            class="regular-text"
                            required
                            placeholder="<?php _e('e.g., MCT', 'limeihua-taproot-assets'); ?>"
                            maxlength="32"
                        >
                        <p class="description"><?php _e('Unique identifier for your token (max 32 characters)', 'limeihua-taproot-assets'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="token-supply"><?php _e('Initial Supply', 'limeihua-taproot-assets'); ?> *</label>
                    </th>
                    <td>
                        <input
                            type="number"
                            id="token-supply"
                            name="supply"
                            class="regular-text"
                            required
                            placeholder="<?php _e('e.g., 1000000', 'limeihua-taproot-assets'); ?>"
                            min="1"
                        >
                        <p class="description"><?php _e('Total number of tokens to create initially', 'limeihua-taproot-assets'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="token-decimals"><?php _e('Decimals', 'limeihua-taproot-assets'); ?></label>
                    </th>
                    <td>
                        <input
                            type="number"
                            id="token-decimals"
                            name="decimals"
                            class="regular-text"
                            value="8"
                            min="0"
                            max="18"
                        >
                        <p class="description"><?php _e('Number of decimal places (0-18, default: 8)', 'limeihua-taproot-assets'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="token-type"><?php _e('Token Type', 'limeihua-taproot-assets'); ?> *</label>
                    </th>
                    <td>
                        <select id="token-type" name="type" required class="regular-text">
                            <option value=""><?php _e('-- Select Type --', 'limeihua-taproot-assets'); ?></option>
                            <option value="fixed"><?php _e('Fixed Supply', 'limeihua-taproot-assets'); ?></option>
                            <option value="mintable"><?php _e('Mintable (can create more)', 'limeihua-taproot-assets'); ?></option>
                            <option value="burnable"><?php _e('Burnable (can destroy)', 'limeihua-taproot-assets'); ?></option>
                        </select>
                        <p class="description">
                            <?php _e('Choose how your token supply will be managed:', 'limeihua-taproot-assets'); ?><br>
                            • <?php _e('Fixed: Cannot change supply after creation', 'limeihua-taproot-assets'); ?><br>
                            • <?php _e('Mintable: You can create more tokens later', 'limeihua-taproot-assets'); ?><br>
                            • <?php _e('Burnable: You can destroy tokens to reduce supply', 'limeihua-taproot-assets'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="token-owner"><?php _e('Owner Address', 'limeihua-taproot-assets'); ?> *</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="token-owner"
                            name="owner"
                            class="regular-text"
                            required
                            placeholder="<?php _e('Bitcoin Lightning Network Address', 'limeihua-taproot-assets'); ?>"
                        >
                        <p class="description"><?php _e('Your Bitcoin Lightning Network wallet address', 'limeihua-taproot-assets'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="token-metadata"><?php _e('Description (Optional)', 'limeihua-taproot-assets'); ?></label>
                    </th>
                    <td>
                        <textarea
                            id="token-metadata"
                            name="metadata"
                            class="large-text"
                            rows="5"
                            placeholder="<?php _e('Describe your token...', 'limeihua-taproot-assets'); ?>"
                        ></textarea>
                        <p class="description"><?php _e('Additional information about your token', 'limeihua-taproot-assets'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Estimated Gas Fee', 'limeihua-taproot-assets'); ?></th>
                    <td>
                        <div id="gas-estimate" class="gas-estimate">
                            <p><?php _e('Select token type to see estimated fees', 'limeihua-taproot-assets'); ?></p>
                        </div>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" class="button button-primary" id="submit-btn">
                    <?php _e('Create Token', 'limeihua-taproot-assets'); ?>
                </button>
                <span id="loading" style="display:none; margin-left: 10px;">
                    <span class="spinner" style="float: none; visibility: visible;"></span>
                    <?php _e('Creating...', 'limeihua-taproot-assets'); ?>
                </span>
            </p>

            <div id="result-message"></div>
        </form>
    </div>
</div>

<style>
.limeihua-create-token {
    background: white;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
}

.gas-estimate {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 3px;
    border-left: 4px solid #0073aa;
}

.gas-estimate .gas-item {
    display: inline-block;
    margin-right: 30px;
    margin-bottom: 10px;
}

.gas-estimate .gas-label {
    display: block;
    font-size: 12px;
    color: #666;
    margin-bottom: 3px;
}

.gas-estimate .gas-value {
    display: block;
    font-size: 18px;
    font-weight: bold;
    color: #0073aa;
}

#result-message {
    margin-top: 20px;
    padding: 15px;
    border-radius: 3px;
    display: none;
}

#result-message.success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    display: block;
}

#result-message.error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    display: block;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Update gas estimate when type changes
    $('#token-type').on('change', function() {
        var type = $(this).val();
        if (!type) {
            $('#gas-estimate').html('<p><?php _e('Select token type to see estimated fees', 'limeihua-taproot-assets'); ?></p>');
            return;
        }

        $.ajax({
            url: limeihuaTaproot.ajaxUrl,
            type: 'POST',
            data: {
                action: 'limeihua_estimate_gas',
                nonce: limeihuaTaproot.nonce,
                type: type,
                metadata: $('#token-metadata').val() ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    var gas = response.data;
                    var html = '<div class="gas-item">' +
                        '<span class="gas-label"><?php _e('Estimated Fee', 'limeihua-taproot-assets'); ?></span>' +
                        '<span class="gas-value">' + gas.estimatedFeeSats + ' sats</span>' +
                        '</div>' +
                        '<div class="gas-item">' +
                        '<span class="gas-label"><?php _e('Fee Rate', 'limeihua-taproot-assets'); ?></span>' +
                        '<span class="gas-value">' + gas.feeRateSatPerVbyte + ' sat/vB</span>' +
                        '</div>' +
                        '<div class="gas-item">' +
                        '<span class="gas-label"><?php _e('Size', 'limeihua-taproot-assets'); ?></span>' +
                        '<span class="gas-value">' + gas.estimatedVsize + ' vB</span>' +
                        '</div>';
                    $('#gas-estimate').html(html);
                }
            }
        });
    });

    // Submit form
    $('#limeihua-token-form').on('submit', function(e) {
        e.preventDefault();

        var $submitBtn = $('#submit-btn');
        var $loading = $('#loading');
        var $resultMsg = $('#result-message');

        $submitBtn.prop('disabled', true);
        $loading.show();
        $resultMsg.removeClass('success error').hide();

        $.ajax({
            url: limeihuaTaproot.ajaxUrl,
            type: 'POST',
            data: {
                action: 'limeihua_create_token',
                nonce: limeihuaTaproot.nonce,
                name: $('#token-name').val(),
                symbol: $('#token-symbol').val(),
                supply: $('#token-supply').val(),
                decimals: $('#token-decimals').val(),
                type: $('#token-type').val(),
                owner: $('#token-owner').val(),
                metadata: $('#token-metadata').val()
            },
            success: function(response) {
                $loading.hide();
                $submitBtn.prop('disabled', false);

                if (response.success) {
                    $resultMsg.addClass('success').html(
                        '<strong><?php _e('Success!', 'limeihua-taproot-assets'); ?></strong> ' + response.data.message
                    ).show();
                    $('#limeihua-token-form')[0].reset();
                    $('#gas-estimate').html('<p><?php _e('Select token type to see estimated fees', 'limeihua-taproot-assets'); ?></p>');
                } else {
                    $resultMsg.addClass('error').html(
                        '<strong><?php _e('Error!', 'limeihua-taproot-assets'); ?></strong> ' + response.data
                    ).show();
                }
            },
            error: function() {
                $loading.hide();
                $submitBtn.prop('disabled', false);
                $resultMsg.addClass('error').html(
                    '<strong><?php _e('Error!', 'limeihua-taproot-assets'); ?></strong> <?php _e('An error occurred. Please try again.', 'limeihua-taproot-assets'); ?>'
                ).show();
            }
        });
    });
});
</script>
?>
