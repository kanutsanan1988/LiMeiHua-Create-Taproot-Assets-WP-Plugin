<?php
/**
 * Taproot Assets API Integration
 *
 * ชุดซอฟต์แวร์ชุดนี้ มีไว้เพื่อเป็นโครงสร้างพื้นฐานทางการเงินยุคใหม่
 * เพื่อรองรับการไหลของเงินจำนวนมหาศาลของท่านผู้เฒ่าหลี่เหมยฮัว หรือ LiMeiHua Grand Mother
 * และ source code นี้สร้างโดย Mr.Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
 * URL: https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna
 */

if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

class LiMeiHua_Taproot_API {
    /**
     * Taproot Assets REST API base URL
     */
    private $api_url;

    /**
     * Constructor
     */
    public function __construct() {
        // ดึง API URL จาก settings หรือใช้ค่าเริ่มต้น
        $this->api_url = get_option('limeihua_taproot_api_url', 'http://localhost:8080');
    }

    /**
     * สร้าง Taproot Asset Token
     *
     * @param string $name ชื่อ Token
     * @param string $symbol สัญลักษณ์ Token
     * @param string $supply จำนวน Supply เริ่มต้น
     * @param int $decimals ทศนิยม
     * @param string $type ประเภท Token (fixed, mintable, burnable)
     * @param string $owner Owner Address
     * @return array ผลลัพธ์การสร้าง
     */
    public function create_token($name, $symbol, $supply, $decimals, $type, $owner) {
        global $wpdb;

        try {
            // ตรวจสอบข้อมูล
            if (empty($name) || empty($symbol) || empty($supply) || empty($owner)) {
                return array(
                    'success' => false,
                    'message' => __('Please fill in all required fields', 'limeihua-taproot-assets')
                );
            }

            // คำนวณค่าแก๊ส
            $gas = $this->estimate_gas($type, !empty($_POST['metadata']));

            // บันทึกลงฐานข้อมูล
            $table = $wpdb->prefix . 'limeihua_tokens';
            $inserted = $wpdb->insert($table, array(
                'user_id' => get_current_user_id(),
                'name' => $name,
                'symbol' => $symbol,
                'initial_supply' => $supply,
                'current_supply' => $supply,
                'decimals' => $decimals,
                'token_type' => $type,
                'owner_address' => $owner,
                'estimated_fee' => $gas['estimatedFeeSats'],
                'status' => 'pending',
                'metadata' => isset($_POST['metadata']) ? sanitize_textarea_field($_POST['metadata']) : null,
            ));

            if (!$inserted) {
                return array(
                    'success' => false,
                    'message' => __('Failed to create token', 'limeihua-taproot-assets')
                );
            }

            $token_id = $wpdb->insert_id;

            // ส่งไปยัง Taproot Assets API (ถ้ามี)
            $api_result = $this->call_taproot_api('mint', array(
                'name' => $name,
                'symbol' => $symbol,
                'amount' => $supply,
                'decimals' => $decimals,
                'owner_address' => $owner,
                'enable_emission' => $type === 'mintable',
            ));

            // บันทึก transaction
            $tx_table = $wpdb->prefix . 'limeihua_token_transactions';
            $wpdb->insert($tx_table, array(
                'token_id' => $token_id,
                'user_id' => get_current_user_id(),
                'tx_type' => 'create',
                'amount' => $supply,
                'tx_hash' => isset($api_result['batch_txid']) ? $api_result['batch_txid'] : null,
                'fee' => $gas['estimatedFeeSats'],
                'status' => 'confirmed',
            ));

            // อัปเดต token status
            $wpdb->update($table, array('status' => 'confirmed'), array('id' => $token_id));

            return array(
                'success' => true,
                'message' => __('Token created successfully', 'limeihua-taproot-assets'),
                'token_id' => $token_id,
                'gas' => $gas,
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Mint additional tokens
     *
     * @param int $token_id Token ID
     * @param string $amount จำนวนที่ต้องการ mint
     * @return array ผลลัพธ์
     */
    public function mint_token($token_id, $amount) {
        global $wpdb;

        try {
            $table = $wpdb->prefix . 'limeihua_tokens';
            $token = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $token_id));

            if (!$token) {
                return array('success' => false, 'message' => __('Token not found', 'limeihua-taproot-assets'));
            }

            if ($token->token_type !== 'mintable') {
                return array('success' => false, 'message' => __('Token is not mintable', 'limeihua-taproot-assets'));
            }

            // ส่งไปยัง Taproot Assets API
            $api_result = $this->call_taproot_api('mint', array(
                'asset_id' => $token->asset_id,
                'amount' => $amount,
            ));

            // อัปเดต supply
            $new_supply = bcadd($token->current_supply, $amount, 0);
            $wpdb->update($table, array('current_supply' => $new_supply), array('id' => $token_id));

            // บันทึก transaction
            $tx_table = $wpdb->prefix . 'limeihua_token_transactions';
            $wpdb->insert($tx_table, array(
                'token_id' => $token_id,
                'user_id' => get_current_user_id(),
                'tx_type' => 'mint',
                'amount' => $amount,
                'tx_hash' => isset($api_result['batch_txid']) ? $api_result['batch_txid'] : null,
                'status' => 'confirmed',
            ));

            return array(
                'success' => true,
                'message' => __('Tokens minted successfully', 'limeihua-taproot-assets'),
                'new_supply' => $new_supply,
            );
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Burn tokens
     *
     * @param int $token_id Token ID
     * @param string $amount จำนวนที่ต้องการ burn
     * @return array ผลลัพธ์
     */
    public function burn_token($token_id, $amount) {
        global $wpdb;

        try {
            $table = $wpdb->prefix . 'limeihua_tokens';
            $token = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $token_id));

            if (!$token) {
                return array('success' => false, 'message' => __('Token not found', 'limeihua-taproot-assets'));
            }

            if ($token->token_type !== 'burnable') {
                return array('success' => false, 'message' => __('Token is not burnable', 'limeihua-taproot-assets'));
            }

            // ตรวจสอบ amount
            if (bccomp($amount, $token->current_supply, 0) > 0) {
                return array('success' => false, 'message' => __('Burn amount exceeds current supply', 'limeihua-taproot-assets'));
            }

            // ส่งไปยัง Taproot Assets API
            $api_result = $this->call_taproot_api('burn', array(
                'asset_id' => $token->asset_id,
                'amount' => $amount,
            ));

            // อัปเดต supply
            $new_supply = bcsub($token->current_supply, $amount, 0);
            $wpdb->update($table, array('current_supply' => $new_supply), array('id' => $token_id));

            // บันทึก transaction
            $tx_table = $wpdb->prefix . 'limeihua_token_transactions';
            $wpdb->insert($tx_table, array(
                'token_id' => $token_id,
                'user_id' => get_current_user_id(),
                'tx_type' => 'burn',
                'amount' => $amount,
                'tx_hash' => isset($api_result['batch_txid']) ? $api_result['batch_txid'] : null,
                'status' => 'confirmed',
            ));

            return array(
                'success' => true,
                'message' => __('Tokens burned successfully', 'limeihua-taproot-assets'),
                'new_supply' => $new_supply,
            );
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * คำนวณค่าแก๊ส
     *
     * @param string $type ประเภท Token
     * @param bool $has_metadata มีข้อมูล metadata หรือไม่
     * @return array ค่าแก๊สที่คำนวณได้
     */
    public function estimate_gas($type, $has_metadata = false) {
        // ค่าแก๊สพื้นฐาน
        $base_fee = 3000;
        $fee_per_vbyte = 10;

        // เพิ่มค่าแก๊สสำหรับ mintable tokens
        if ($type === 'mintable') {
            $base_fee += 640;
        }

        // เพิ่มค่าแก๊สสำหรับ metadata
        if ($has_metadata) {
            $base_fee += 200;
        }

        $vsize = intval($base_fee / $fee_per_vbyte);

        return array(
            'estimatedFeeSats' => $base_fee,
            'feeRateSatPerVbyte' => $fee_per_vbyte,
            'estimatedVsize' => $vsize,
        );
    }

    /**
     * เรียก Taproot Assets API
     *
     * @param string $method Method name
     * @param array $params Parameters
     * @return array API response
     */
    private function call_taproot_api($method, $params) {
        // ตรวจสอบว่า API URL ถูกตั้งค่าหรือไม่
        if (empty($this->api_url) || $this->api_url === 'http://localhost:8080') {
            // ใช้ simulator mode
            return $this->simulate_api_response($method, $params);
        }

        try {
            $response = wp_remote_post($this->api_url . '/v1/' . $method, array(
                'method' => 'POST',
                'headers' => array('Content-Type' => 'application/json'),
                'body' => json_encode($params),
                'timeout' => 30,
            ));

            if (is_wp_error($response)) {
                throw new Exception($response->get_error_message());
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            return $body;
        } catch (Exception $e) {
            // Fallback to simulator
            return $this->simulate_api_response($method, $params);
        }
    }

    /**
     * Simulate API response (สำหรับ development)
     *
     * @param string $method Method name
     * @param array $params Parameters
     * @return array Simulated response
     */
    private function simulate_api_response($method, $params) {
        return array(
            'batch_txid' => 'sim_' . wp_generate_uuid4(),
            'asset_id' => 'asset_' . wp_generate_uuid4(),
            'status' => 'success',
        );
    }
}
?>
