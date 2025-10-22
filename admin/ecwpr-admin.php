<?php
if (!defined('ABSPATH')) exit;

/* Admin UI for Easy Custom WP Login */

function ecwpr_admin_enqueue($hook) {
    // Only load for our settings page
    if ($hook !== 'settings_page_ecwpr-settings') return;
    wp_enqueue_style('wp-color-picker');
    // Professional admin styles (versioned by filemtime)
    $css_file = plugin_dir_path(__FILE__) . 'css/ecwpr-admin.css';
    $css_url = plugin_dir_url(__FILE__) . 'css/ecwpr-admin.css';
    $css_ver = file_exists($css_file) ? filemtime($css_file) : false;
    wp_enqueue_style('ecwpr-admin-css', $css_url, array(), $css_ver);

    wp_enqueue_script('wp-color-picker');
    // Enqueue the main admin JS (versioned by filemtime)
    $js_file = plugin_dir_path(__FILE__) . 'js/ecwpr-admin-main.js';
    $js_url = plugin_dir_url(__FILE__) . 'js/ecwpr-admin-main.js';
    $js_ver = file_exists($js_file) ? filemtime($js_file) : false;
    wp_enqueue_script('ecwpr-admin-js', $js_url, array('jquery', 'wp-color-picker'), $js_ver, true);

    // Localize current options for preview
    $opts = ecwpr_get_options();
    wp_localize_script('ecwpr-admin-js', 'ecwprOptions', $opts);
    // Provide AJAX endpoint and nonce for saving settings
    wp_localize_script('ecwpr-admin-js', 'ecwprAdmin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ecwpr_save_settings')
    ));

    // Enqueue media scripts so we can use the WP media uploader for logo
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'ecwpr_admin_enqueue');

// The settings page HTML is kept in this file to keep the main plugin file smaller
function ecwpr_settings_page() {
    if (!current_user_can('manage_options')) return;
    $opts = ecwpr_get_options();
    ?>
    <div class="wrap">
        <h1>Easy Custom WP Login Settings</h1>

    <!-- Admin styles are loaded from admin/css/ecwpr-admin.css for better maintainability -->

        <div class="ecwpr-admin-container">
            <div class="ecwpr-admin-left">
                <form id="ecwpr-settings-form" method="post" action="options.php">
                    <?php settings_fields('ecwpr_settings_group');
                    $name = 'ecwpr_settings'; ?>

                    <div class="ecwpr-settings-scroll">

                    <!-- Collapsible: Colors -->
                    <div class="ecwpr-section" data-section="colors">
                        <h3 class="ecwpr-section-title"><button type="button" class="ecwpr-section-toggle" aria-expanded="true">Colors</button></h3>
                        <div class="ecwpr-section-body" data-body="colors">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="gradient1">Gradient color 1</label></th>
                                    <td><input class="ecwpr-color" type="text" id="gradient1" name="<?php echo $name; ?>[gradient1]" value="<?php echo esc_attr($opts['gradient1']); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="gradient2">Gradient color 2</label></th>
                                    <td><input class="ecwpr-color" type="text" id="gradient2" name="<?php echo $name; ?>[gradient2]" value="<?php echo esc_attr($opts['gradient2']); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="gradient3">Gradient color 3</label></th>
                                    <td><input class="ecwpr-color" type="text" id="gradient3" name="<?php echo $name; ?>[gradient3]" value="<?php echo esc_attr($opts['gradient3']); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="gradient4">Gradient color 4</label></th>
                                    <td><input class="ecwpr-color" type="text" id="gradient4" name="<?php echo $name; ?>[gradient4]" value="<?php echo esc_attr($opts['gradient4']); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="light_bg">Light mode background</label></th>
                                    <td><input class="ecwpr-color" type="text" id="light_bg" name="<?php echo $name; ?>[light_bg]" value="<?php echo esc_attr($opts['light_bg']); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="button_color">Button color</label></th>
                                    <td><input class="ecwpr-color" type="text" id="button_color" name="<?php echo $name; ?>[button_color]" value="<?php echo esc_attr($opts['button_color']); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="input_border">Input border color</label></th>
                                    <td><input class="ecwpr-color" type="text" id="input_border" name="<?php echo $name; ?>[input_border]" value="<?php echo esc_attr($opts['input_border']); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="message_bg_color">Success message color</label></th>
                                    <td><input class="ecwpr-color" type="text" id="message_bg_color" name="<?php echo $name; ?>[message_bg_color]" value="<?php echo esc_attr($opts['message_bg_color']); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="error_bg_color">Error message color</label></th>
                                    <td><input class="ecwpr-color" type="text" id="error_bg_color" name="<?php echo $name; ?>[error_bg_color]" value="<?php echo esc_attr($opts['error_bg_color']); ?>" /></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Collapsible: Typography -->
                    <div class="ecwpr-section" data-section="typography">
                        <h3 class="ecwpr-section-title"><button type="button" class="ecwpr-section-toggle" aria-expanded="false">Typography</button></h3>
                        <div class="ecwpr-section-body" data-body="typography" hidden>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="font_family">Font family</label></th>
                                    <td><input type="text" id="font_family" name="<?php echo $name; ?>[font_family]" value="<?php echo esc_attr($opts['font_family']); ?>" class="regular-text" /></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Collapsible: Animation -->
                    <div class="ecwpr-section" data-section="animation">
                        <h3 class="ecwpr-section-title"><button type="button" class="ecwpr-section-toggle" aria-expanded="false">Animation</button></h3>
                        <div class="ecwpr-section-body" data-body="animation" hidden>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="enable_animation">Enable gradient animation</label></th>
                                    <td><input type="checkbox" id="enable_animation" name="<?php echo $name; ?>[enable_animation]" value="1" <?php checked(1, $opts['enable_animation']); ?> /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="animation_speed">Animation speed (seconds)</label></th>
                                    <td><input type="number" id="animation_speed" name="<?php echo $name; ?>[animation_speed]" value="<?php echo esc_attr($opts['animation_speed']); ?>" min="1" /></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Collapsible: Texts -->
                    <div class="ecwpr-section" data-section="texts">
                        <h3 class="ecwpr-section-title"><button type="button" class="ecwpr-section-toggle" aria-expanded="false">Texts</button></h3>
                        <div class="ecwpr-section-body" data-body="texts" hidden>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="logo">Logo</label></th>
                                    <td>
                                        <div class="ecwpr-logo-upload-wrapper">
                                            <input type="text" id="logo" name="<?php echo $name; ?>[logo]" value="<?php echo esc_attr($opts['logo']); ?>" class="regular-text" />
                                            <button type="button" class="button" id="ecwpr-upload-logo">Upload</button>
                                            <button type="button" class="button" id="ecwpr-remove-logo">Remove</button>
                                            <button type="button" class="button" id="ecwpr-generate-square">Generate thumbnail</button>
                                        </div>
                                        <div id="ecwpr-logo-hint" style="margin-top:8px;color:#6b7280;font-size:0.95rem;">Recommended size: at least 400x400px for good results in square mode.</div>
                                        <div id="ecwpr-logo-notice" style="margin-top:6px;display:none;padding:8px;border-radius:6px;background:#fff4e5;border:1px solid #ffd8a8;color:#92400e;"></div>
                                        <div id="ecwpr-logo-preview" style="margin-top:8px;">
                                            <?php if (!empty($opts['logo'])): ?><img src="<?php echo esc_url($opts['logo']); ?>" alt="Logo" style="max-width:200px;height:auto;border-radius:6px;" /><?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="logo_mode">Logo display</label></th>
                                    <td>
                                        <select id="logo_mode" name="<?php echo $name; ?>[logo_mode]">
                                            <option value="contain" <?php selected($opts['logo_mode'], 'contain'); ?>>Contain (default)</option>
                                            <option value="cover" <?php selected($opts['logo_mode'], 'cover'); ?>>Cover (fill)</option>
                                            <option value="square" <?php selected($opts['logo_mode'], 'square'); ?>>Square (generate square thumbnail)</option>
                                        </select>
                                        <p class="description">Choose how to display the logo on the login header. Square will attempt to generate a square thumbnail using server-side resizing.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="logo_square_size">Square logo size (px)</label></th>
                                    <td>
                                        <input type="number" id="logo_square_size" name="<?php echo $name; ?>[logo_square_size]" value="<?php echo esc_attr($opts['logo_square_size']); ?>" min="32" />
                                        <p class="description">When using "Square", the image will be resized to this dimension (px).</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="title_text">Login header text</label></th>
                                    <td><input type="text" id="title_text" name="<?php echo $name; ?>[title_text]" value="<?php echo esc_attr($opts['title_text']); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="disable_styling">Disable plugin styling</label></th>
                                    <td>
                                        <label><input type="checkbox" id="disable_styling" name="<?php echo $name; ?>[disable_styling]" value="1" <?php checked(1, $opts['disable_styling']); ?> /> Only inject logo/title, do not add CSS</label>
                                        <p class="description">Enabling this will keep the DOM changes (logo and title) but avoid injecting plugin CSS so your theme controls the appearance.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="hide_scrollbar">Hide scrollbar</label></th>
                                    <td>
                                        <label><input type="checkbox" id="hide_scrollbar" name="<?php echo $name; ?>[hide_scrollbar]" value="1" <?php checked(1, $opts['hide_scrollbar']); ?> /> Hide scrollbar on login page</label>
                                        <p class="description">Hides the scrollbar for a cleaner appearance while keeping scroll functionality.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="export_css_to_theme">Export CSS to theme</label></th>
                                    <td>
                                        <label><input type="checkbox" id="export_css_to_theme" name="<?php echo $name; ?>[export_css_to_theme]" value="1" <?php checked(1, $opts['export_css_to_theme']); ?> /> Copy plugin login CSS into the active theme folder</label>
                                        <p class="description">This copies a file into your active theme so theme authors can further customize the login styles. Requires writable theme folder.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="override_all_styles">Aggressive override</label></th>
                                    <td>
                                        <label><input type="checkbox" id="override_all_styles" name="<?php echo $name; ?>[override_all_styles]" value="1" <?php checked(1, $opts['override_all_styles']); ?> /> Let plugin try to override all other login styles</label>
                                        <p class="description">When enabled the plugin will increase specificity and use !important on key properties to ensure its styles take precedence. Use only when necessary.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Styling note</th>
                                    <td>
                                        <div id="ecwpr-disable-note" style="display:none;padding:8px;border-radius:6px;background:#fff8f0;border:1px solid #ffd8a8;color:#92400e;">Plugin styling is disabled — only logo and title DOM are injected. Your theme's styles will apply.</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="initial_mode">Initial login mode</label></th>
                                    <td>
                                        <select id="initial_mode" name="<?php echo $name; ?>[initial_mode]">
                                            <option value="dark" <?php selected($opts['initial_mode'], 'dark'); ?>>Dark</option>
                                            <option value="light" <?php selected($opts['initial_mode'], 'light'); ?>>Light</option>
                                        </select>
                                        <p class="description">Set the default mode for the login page; users can still toggle and their choice is saved locally in their browser.</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <p><?php submit_button('Save Settings'); ?></p>
                    </div><!-- .ecwpr-settings-scroll -->
                </form>
            </div>

            <div class="ecwpr-admin-right">
                <div class="ecwpr-preview-sticky">
                    <div id="ecwpr-preview" aria-live="polite" aria-label="Login preview">
                        <div class="ecwpr-preview-toprow">
                            <div class="ecwpr-preview-links"><a href="#" onclick="return false;" aria-hidden="true">&larr; Back to site</a></div>
                            <div class="ecwpr-preview-toggle-visual" role="button" aria-pressed="false">Light</div>
                        </div>
                        <div class="ecwpr-preview-title"><?php echo esc_html($opts['title_text']); ?></div>
                        <div class="ecwpr-preview-logo"><div class="logo-box" aria-hidden="true"></div></div>
                        <form class="ecwpr-preview-form" aria-hidden="true">
                            <label class="screen-reader-text" for="ecwpr-preview-username">Username</label>
                            <input id="ecwpr-preview-username" placeholder="Username" aria-label="Username preview" />
                            <label class="screen-reader-text" for="ecwpr-preview-password">Password</label>
                            <input id="ecwpr-preview-password" placeholder="Password" aria-label="Password preview" />
                            <button type="button" aria-label="Preview login button">Log In</button>
                        </form>
                    </div>
                    <h2 style="margin-top:20px;text-align:center;">Preview</h2>
                    <p style="text-align:center;color:#666;font-size:14px;margin-top:10px;">Live preview updates as you change colors. This preview does not affect the real login until you save.</p>
                    <p style="text-align:center;color:#888;font-size:13px;margin-top:8px;">Note: The Dark/Light toggle on the live login page is controlled separately. This preview only simulates the appearance.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Confirmation modal for exporting CSS to theme -->
    <div id="ecwpr-export-confirm-modal" class="hidden" style="display:none;">
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
            <div style="background:#fff;padding:20px;border-radius:8px;max-width:520px;width:90%;box-shadow:0 8px 24px rgba(0,0,0,0.2);">
                <h2 style="margin-top:0;margin-bottom:8px;">Export login CSS to theme</h2>
                <p id="ecwpr-export-confirm-message">A file already exists in your theme. Exporting will back it up and overwrite it. Proceed?</p>
                <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;">
                    <button type="button" class="button" id="ecwpr-export-cancel">Cancel</button>
                    <button type="button" class="button button-primary" id="ecwpr-export-confirm">Proceed and Backup</button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
