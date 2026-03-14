# LiMeiHua Create Taproot Assets - WordPress Plugin

> ชุดซอฟต์แวร์ชุดนี้ มีไว้เพื่อเป็นโครงสร้างพื้นฐานทางการเงินยุคใหม่เพื่อรองรับการไหลของเงินจำนวนมหาศาลของท่านผู้เฒ่าหลี่เหมยฮัว หรือ LiMeiHua Grand Mother และ source code นี้สร้างโดย Mr.Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา) URL:https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna

A powerful WordPress plugin for creating and managing Taproot Assets tokens on the Bitcoin Lightning Network directly from your WordPress dashboard.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![WordPress](https://img.shields.io/badge/wordpress-5.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/php-7.4%2B-blue.svg)

## 🚀 Features

- **Easy Token Creation** - Create Taproot Assets tokens with just a few clicks
- **Multiple Token Types** - Support for Fixed Supply, Mintable, and Burnable tokens
- **Real-time Gas Estimation** - Automatic calculation of Bitcoin network fees
- **Transaction History** - Complete tracking of all token operations
- **Admin Dashboard** - Comprehensive management interface
- **Widget Support** - Display tokens in sidebar widgets
- **Shortcode Support** - Embed forms and lists anywhere on your site
- **Multi-language** - Supports 28+ languages
- **Bitcoin Lightning Integration** - Direct integration with Bitcoin Lightning Network
- **Taproot Assets Protocol** - Uses official Taproot Assets Protocol API

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Bitcoin Lightning Network wallet

## 💾 Installation

### Method 1: Manual Installation

1. Download the plugin from GitHub
2. Extract the files to `/wp-content/plugins/limeihua-taproot-assets/`
3. Activate the plugin through the WordPress admin panel
4. Go to Taproot Assets menu to configure settings

### Method 2: Upload via WordPress Admin

1. Go to Plugins → Add New
2. Click "Upload Plugin"
3. Select the plugin ZIP file
4. Click "Install Now"
5. Activate the plugin

## 🎯 Usage

### Creating a Token

1. Navigate to **Taproot Assets → Create Token**
2. Fill in the token details:
   - **Token Name** - The name of your token
   - **Token Symbol** - Unique identifier (e.g., MCT)
   - **Initial Supply** - Total number of tokens to create
   - **Decimals** - Number of decimal places (0-18)
   - **Token Type** - Choose from Fixed, Mintable, or Burnable
   - **Owner Address** - Your Bitcoin Lightning wallet address
   - **Description** - Optional metadata about your token

3. Review the estimated gas fees
4. Click "Create Token"

### Managing Tokens

1. Go to **Taproot Assets → My Tokens** to view all your tokens
2. For **Mintable** tokens, click "Mint" to create additional tokens
3. For **Burnable** tokens, click "Burn" to destroy tokens
4. Click "Details" to view transaction history

### Using Shortcodes

Display the token creation form on any page or post:

```
[limeihua_create_token]
```

Display a list of all your tokens:

```
[limeihua_tokens_list]
```

### Using Widgets

1. Go to **Appearance → Widgets**
2. Add the "LiMeiHua Taproot Assets" widget to your sidebar
3. Configure the widget title
4. Save

## ⚙️ Configuration

### API Settings

1. Go to **Taproot Assets → Settings**
2. Enter your Taproot Assets API URL (optional - leave blank for simulator mode)
3. Configure data retention options
4. Save settings

### Supported Token Types

| Type | Description | Use Case |
|------|-------------|----------|
| **Fixed Supply** | Immutable total supply | Limited edition tokens, collectibles |
| **Mintable** | Owner can create more tokens | Governance tokens, rewards |
| **Burnable** | Tokens can be destroyed | Deflationary tokens, buyback programs |

## 🔧 Database Schema

### Tokens Table

```sql
CREATE TABLE wp_limeihua_tokens (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Transactions Table

```sql
CREATE TABLE wp_limeihua_token_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    token_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    tx_type ENUM('create', 'mint', 'burn') NOT NULL,
    amount VARCHAR(255) NOT NULL,
    tx_hash VARCHAR(255),
    fee BIGINT,
    status ENUM('pending', 'confirmed', 'failed') NOT NULL DEFAULT 'pending',
    notes LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 📁 File Structure

```
limeihua-taproot-assets/
├── limeihua-taproot-assets.php       # Main plugin file
├── readme.txt                         # Plugin readme (28+ languages)
├── README.md                          # GitHub readme
├── LICENSE                            # MIT License
├── .gitignore                         # Git ignore file
├── includes/
│   ├── class-limeihua-taproot.php    # Main plugin class
│   ├── class-taproot-api.php         # Taproot Assets API integration
│   ├── class-shortcode.php           # Shortcode handlers
│   ├── class-widget.php              # Widget class
│   └── class-admin-page.php          # Admin page handler
├── admin/
│   ├── dashboard.php                 # Admin dashboard
│   ├── create-token.php              # Create token form
│   ├── tokens-list.php               # Tokens list page
│   └── settings.php                  # Settings page
├── assets/
│   ├── css/
│   │   ├── admin.css                 # Admin styles
│   │   └── frontend.css              # Frontend styles
│   └── js/
│       ├── admin.js                  # Admin scripts
│       └── frontend.js               # Frontend scripts
└── languages/
    └── limeihua-taproot-assets.pot   # Translation template
```

## 🌍 Supported Languages

The plugin includes support for 28+ languages:

- English
- ไทย (Thai)
- 中文 (Chinese Simplified)
- 中文繁體 (Chinese Traditional)
- 日本語 (Japanese)
- 한국어 (Korean)
- Español (Spanish)
- Français (French)
- Deutsch (German)
- Português (Portuguese)
- Русский (Russian)
- العربية (Arabic)
- हिन्दी (Hindi)
- Tiếng Việt (Vietnamese)
- Bahasa Indonesia (Indonesian)
- Bahasa Melayu (Malay)
- Türkçe (Turkish)
- Italiano (Italian)
- Nederlands (Dutch)
- Polski (Polish)
- Svenska (Swedish)
- Українська (Ukrainian)
- Čeština (Czech)
- Română (Romanian)
- Ελληνικά (Greek)
- עברית (Hebrew)
- বাংলা (Bengali)
- Filipino (Tagalog)
- Kiswahili (Swahili)

## 🔐 Security

- All user inputs are sanitized and validated
- Database queries use prepared statements to prevent SQL injection
- AJAX requests are protected with WordPress nonces
- Private keys are never stored by the plugin
- All data is encrypted in transit

## 🐛 Troubleshooting

### Plugin not showing in admin menu

- Make sure you have administrator privileges
- Try deactivating and reactivating the plugin
- Check that WordPress version is 5.0 or higher

### Tokens not creating

- Verify your Bitcoin Lightning wallet address is correct
- Check that the API URL is configured correctly (or leave blank for simulator mode)
- Review the estimated gas fees
- Check WordPress error logs

### Database errors

- Ensure MySQL version is 5.7 or higher
- Verify database user has CREATE TABLE permissions
- Try deactivating and reactivating the plugin to recreate tables

## 📞 Support

For support and questions:

- **GitHub Issues**: [Report a bug](https://github.com/kanutsanan1988/LiMeiHua-Create-Taproot-Assets-WP-Plugin/issues)
- **Documentation**: [Taproot Assets API](https://lightning.engineering/api-docs/api/taproot-assets/)
- **Author**: Mr. Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)
- **Assistant**: [ChatGPT Assistant](https://chatgpt.com/g/g-68d289535dec81919445deb9830f2d8e-kanutsanan-pongpanna)

## 📝 Changelog

### Version 1.0.0 (2026-03-14)

- Initial release
- Support for Fixed, Mintable, and Burnable tokens
- Admin dashboard and management interface
- Widget and shortcode support
- Multi-language support (28+ languages)
- Gas fee estimation
- Transaction history tracking
- Bitcoin Lightning Network integration
- Taproot Assets Protocol API integration

## 📄 License

This plugin is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## 👨‍💻 Credits

**Created by:** Mr. Kanutsanan Pongpanna (นายคณัสนันท์ พงษ์พันนา)

**Dedicated to:** LiMeiHua Grand Mother (ท่านผู้เฒ่าหลี่เหมยฮัว)

This software is designed to serve as a modern financial infrastructure foundation to support the massive flow of funds for LiMeiHua Grand Mother and was created by Mr. Kanutsanan Pongpanna.

---

**Repository:** [GitHub](https://github.com/kanutsanan1988/LiMeiHua-Create-Taproot-Assets-WP-Plugin)

**Version:** 1.0.0

**License:** MIT
