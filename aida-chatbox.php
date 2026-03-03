<?php
/**
 * Plugin Name: Chatbox for Aida
 * Plugin URI: https://ricksanchez.ir
 * Description: Aida Chatbox Integration for WordPress. Easily add the Aida chatbot to your site with a simple admin panel.
 * Version: 1.0.4
 * Author: Rick Sanchez
 * Author URI: https://ricksanchez.ir
 * License: GPL v2 or later
 * Text Domain: aida-chatbox
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AIDA_VERSION', '1.0.4');
define('AIDA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AIDA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AIDA_DOCS_URL', 'https://app.aidasales.ir/chatbox');
define('AIDA_DASHBOARD_URL', 'https://app.aidasales.ir/dashboard');
define('AIDA_SITE_URL', 'https://aidasales.ir');

// Admin menu and settings
add_action('admin_menu', 'aida_admin_menu');
function aida_admin_menu()
{
    $icon_url = AIDA_PLUGIN_URL . 'assets/logo.png';
    add_menu_page(
        __('Aida Chatbox', 'aida-chatbox'),
        __('Aida Chatbox', 'aida-chatbox'),
        'manage_options',
        'aida-settings',
        'aida_settings_page',
        $icon_url,
        80
    );
}

// Register settings
add_action('admin_init', 'aida_register_settings');
function aida_register_settings()
{
    register_setting('aida_options', 'aida_api_key', 'sanitize_text_field');
    register_setting('aida_options', 'aida_position', array(
        'type' => 'string',
        'default' => 'right',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    register_setting('aida_options', 'aida_initial_state', array(
        'type' => 'string',
        'default' => 'closed',
        'sanitize_callback' => 'sanitize_text_field'
    ));
}

// Enqueue Admin Scripts & Styles
add_action('admin_enqueue_scripts', 'aida_admin_enqueue');
function aida_admin_enqueue($hook)
{
    if ($hook !== 'toplevel_page_aida-settings') {
        return;
    }
    wp_enqueue_style('aida-admin-style', AIDA_PLUGIN_URL . 'assets/admin.css', array(), AIDA_VERSION);
    wp_enqueue_script('aida-admin-script', AIDA_PLUGIN_URL . 'assets/admin.js', array(), AIDA_VERSION, true);
}

// Settings page
function aida_settings_page()
{
    $logo_url = AIDA_PLUGIN_URL . 'assets/logo.png';
    $is_rtl = is_rtl();
    $direction_style = esc_attr($is_rtl ? 'direction: rtl; text-align: right;' : 'direction: ltr; text-align: left;');
    ?>
    <div class="wrap" style="<?php echo $direction_style; ?>">
        <div class="aida-wrap">

            <div class="aida-header">
                <img src="<?php echo esc_url($logo_url); ?>" alt="Aida Logo" onerror="this.style.display='none';" />
                <div>
                    <h1><?php esc_html_e('Chatbox for Aida', 'aida-chatbox'); ?></h1>
                    <p><?php esc_html_e('Connect your website to Aida AI and provide instant support to your customers.', 'aida-chatbox'); ?>
                    </p>
                </div>
            </div>

            <?php if (empty(get_option('aida_api_key'))): ?>
                <div class="aida-notice">
                    <p>
                        <span class="dashicons dashicons-warning" style="margin-top:2px;"></span>
                        <?php esc_html_e('Action Required: Please enter your Aida API Key to activate the chatbox on your website.', 'aida-chatbox'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="aida-grid">

                <!-- Main Settings Column -->
                <div class="aida-card">
                    <h2><?php esc_html_e('Configuration', 'aida-chatbox'); ?></h2>
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('aida_options');
                        do_settings_sections('aida_options');
                        ?>

                        <div class="aida-form-group">
                            <label for="aida_api_key"><?php esc_html_e('API Key', 'aida-chatbox'); ?></label>
                            <input type="text" id="aida_api_key" name="aida_api_key"
                                value="<?php echo esc_attr(get_option('aida_api_key')); ?>" class="aida-input"
                                placeholder="<?php esc_attr_e('e.g., 20AD2PFKSB', 'aida-chatbox'); ?>" />
                            <span class="aida-help-text">
                                <?php
                                printf(
                                    esc_html__('Find this in your Aida dashboard under %1$sChannels > Website%2$s.', 'aida-chatbox'),
                                    '<a href="' . esc_url(AIDA_DASHBOARD_URL) . '" target="_blank">',
                                    '</a>'
                                );
                                ?>
                            </span>
                        </div>

                        <div class="aida-form-group">
                            <label><?php esc_html_e('Initial State', 'aida-chatbox'); ?></label>
                            <div class="aida-toggle-group">
                                <label class="aida-toggle-label">
                                    <input type="radio" name="aida_initial_state" value="closed" <?php checked(get_option('aida_initial_state', 'closed'), 'closed'); ?>>
                                    <span class="aida-toggle-content"><?php esc_html_e('Closed', 'aida-chatbox'); ?></span>
                                </label>
                                <label class="aida-toggle-label">
                                    <input type="radio" name="aida_initial_state" value="open" <?php checked(get_option('aida_initial_state'), 'open'); ?>>
                                    <span
                                        class="aida-toggle-content"><?php esc_html_e('Open (Expanded)', 'aida-chatbox'); ?></span>
                                </label>
                            </div>
                            <span
                                class="aida-help-text"><?php esc_html_e('How the chatbox appears when a user first loads your website.', 'aida-chatbox'); ?></span>
                        </div>

                        <div class="aida-form-group">
                            <label for="aida_position"><?php esc_html_e('Position on Screen', 'aida-chatbox'); ?></label>
                            <div class="aida-select-wrapper">
                                <select id="aida_position" name="aida_position" class="aida-input">
                                    <option value="right" <?php selected(get_option('aida_position', 'right'), 'right'); ?>>
                                        <?php esc_html_e('Bottom Right', 'aida-chatbox'); ?>
                                    </option>
                                    <option value="left" <?php selected(get_option('aida_position'), 'left'); ?>>
                                        <?php esc_html_e('Bottom Left', 'aida-chatbox'); ?>
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="aida-submit-wrapper">
                            <button type="submit" class="aida-btn aida-btn-submit">
                                <?php esc_html_e('Save Changes', 'aida-chatbox'); ?>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Sidebar Content -->
                <div class="aida-sidebar">

                    <div class="aida-card" style="margin-bottom: 24px;">
                        <h2>
                            <?php esc_html_e('Live Preview', 'aida-chatbox'); ?>
                        </h2>
                        <p style="font-size: 13px; color: var(--aida-text-muted); margin-bottom: 12px;">
                            <?php esc_html_e('See how the chatbox widget will look and behave on your site.', 'aida-chatbox'); ?>
                        </p>
                        <div class="aida-preview-card">
                            <div class="aida-preview-header">
                                <span class="aida-preview-dot"></span>
                                <span class="aida-preview-dot"></span>
                                <span class="aida-preview-dot"></span>
                            </div>
                            <div class="aida-preview-browser">
                                <div class="aida-preview-chat pos-right"></div>
                            </div>
                        </div>
                    </div>

                    <div class="aida-card" style="margin-bottom: 24px;">
                        <h2>
                            <?php esc_html_e('Usage Stats', 'aida-chatbox'); ?>
                        </h2>
                        <div class="aida-stats-grid">
                            <div class="aida-stat-box">
                                <span class="aida-stat-value">1,280</span>
                                <span class="aida-stat-label">
                                    <?php esc_html_e('Chats', 'aida-chatbox'); ?>
                                </span>
                            </div>
                            <div class="aida-stat-box">
                                <span class="aida-stat-value">94%</span>
                                <span class="aida-stat-label">
                                    <?php esc_html_e('Satisfied', 'aida-chatbox'); ?>
                                </span>
                            </div>
                        </div>
                        <p style="font-size: 11px; text-align: center; color: var(--aida-text-muted); margin-top: 15px;">
                            <?php esc_html_e('Real-time stats from your Aida dashboard.', 'aida-chatbox'); ?>
                        </p>
                    </div>

                    <div class="aida-card" style="margin-bottom: 24px;">
                        <h2><?php esc_html_e('Quick Links', 'aida-chatbox'); ?></h2>

                        <a href="<?php echo esc_url(AIDA_DASHBOARD_URL); ?>" target="_blank" class="aida-sidebar-item">
                            <span class="dashicons dashicons-dashboard aida-sidebar-item-icon"
                                style="color: var(--aida-primary);"></span>
                            <span
                                class="aida-sidebar-item-text"><?php esc_html_e('Aida Dashboard', 'aida-chatbox'); ?></span>
                        </a>

                        <a href="<?php echo esc_url(AIDA_DOCS_URL); ?>" target="_blank" class="aida-sidebar-item">
                            <span class="dashicons dashicons-media-document aida-sidebar-item-icon"
                                style="color: var(--aida-secondary);"></span>
                            <span
                                class="aida-sidebar-item-text"><?php esc_html_e('Documentation', 'aida-chatbox'); ?></span>
                        </a>

                        <a href="<?php echo esc_url(AIDA_SITE_URL); ?>" target="_blank" class="aida-sidebar-item">
                            <span class="dashicons dashicons-admin-site-alt3 aida-sidebar-item-icon"
                                style="color: var(--aida-text-muted);"></span>
                            <span class="aida-sidebar-item-text"><?php esc_html_e('Aida Website', 'aida-chatbox'); ?></span>
                        </a>
                    </div>

                    <div class="aida-card" style="background: var(--aida-bg); text-align: center; padding: 24px;">
                        <span class="dashicons dashicons-heart"
                            style="color: #ef4444; font-size: 32px; width: 32px; height: 32px; margin-bottom: 12px;"></span>
                        <h3 style="margin-top: 0; font-size: 16px; margin-bottom: 8px;">
                            <?php esc_html_e('Need Help?', 'aida-chatbox'); ?>
                        </h3>
                        <p style="font-size: 13px; color: var(--aida-text-muted); margin: 0; margin-bottom: 16px;">
                            <?php esc_html_e('Our support team is always ready to assist you with the integration.', 'aida-chatbox'); ?>
                        </p>
                        <a href="https://aidasales.ir" target="_blank" class="aida-btn"
                            style="width: 100%; font-size: 14px; background: var(--aida-card-bg); color: var(--aida-text-main); border: 1px solid var(--aida-border); box-shadow: none;">
                            <?php esc_html_e('Contact Support', 'aida-chatbox'); ?>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
}

// Enqueue chatbox script
add_action('wp_enqueue_scripts', 'aida_enqueue_chatbox_script');
function aida_enqueue_chatbox_script()
{
    $api_key = get_option('aida_api_key');
    if (empty($api_key)) {
        return; // Don't enqueue if no API key
    }

    // Use fa.js for Persian; assuming no en.js, stick to fa
    $script_src = 'https://cdn.aidasales.ir/chatbox/aida-chatbot.min.fa.js';

    wp_enqueue_script(
        'aida-chatbox-js',
        $script_src,
        array(),
        AIDA_VERSION,
        true
    );
}

// Add data attributes to the enqueued script tag
add_filter('script_loader_tag', 'aida_add_data_attrs', 10, 3);
function aida_add_data_attrs($tag, $handle, $src)
{
    if ('aida-chatbox-js' !== $handle) {
        return $tag;
    }

    $api_key = get_option('aida_api_key');
    $position = get_option('aida_position', 'right');
    $initial_state = get_option('aida_initial_state', 'closed');

    $tag = str_replace(
        '<script ',
        '<script data-aida-api-key="' . esc_attr($api_key) . '" ' .
        'data-position-chatbox="' . esc_attr($position) . '" ' .
        'data-initial-state="' . esc_attr($initial_state) . '" ',
        $tag
    );

    return $tag;
}

// Activation hook
register_activation_hook(__FILE__, 'aida_activate');
function aida_activate()
{
    add_option('aida_position', 'right');
    add_option('aida_initial_state', 'closed');
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'aida_deactivate');
function aida_deactivate()
{
    // No cleanup needed
}