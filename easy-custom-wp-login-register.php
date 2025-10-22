<?php
/**
 * Plugin Name: Easy Custom WP Login Register
 * Plugin URI: https://valandiangelidis.com
 * License: GPL2+
 * Description: A modern animated WordPress login & register page with dark/light mode toggle, custom branding, and improved styling for iThemes reCAPTCHA and admin email verification screens.
 * Version: 1.0.0
 * Author: Valandi Angelidis
 * Author URI: https://valandiangelidis.com
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: easy-custom-wp-login-register
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/*
 * Defaults and option helpers
 */
function ecwpr_default_options() {
    return array(
        'gradient1' => '#0b1120',
        'gradient2' => '#1e3a8a',
        'gradient3' => '#1e40af',
        'gradient4' => '#1e3a8a',
        'light_bg' => '#f3f4f6',
        'enable_animation' => 1,
        'animation_speed' => 20,
        'title_text' => 'Pro Sales Team',
        'button_color' => '#3b82f6',
        'input_border' => '#cbd5e1',
        'font_family' => "Segoe UI, Roboto, sans-serif",
        'message_bg_color' => '#3b82f6',
        'error_bg_color' => '#ef4444',
        // admin-controlled initial mode for login page: 'dark' or 'light'
        'initial_mode' => 'dark',
        'logo' => '',
        // logo display: contain | cover | square
        'logo_mode' => 'contain',
        // size when creating square logo (px)
        'logo_square_size' => 200,
        // allow admin to disable plugin styling (only keep DOM changes like logo/title)
        'disable_styling' => 0,
        // hide scrollbar on login page
        'hide_scrollbar' => 0,
        // copy the login stylesheet into the active theme when enabled
        'export_css_to_theme' => 0,
        // aggressive mode: plugin should override all other login styles (use with caution)
        'override_all_styles' => 0,
    );
}

function ecwpr_get_options() {
    $defaults = ecwpr_default_options();
    $opts = get_option('ecwpr_settings', array());
    return wp_parse_args($opts, $defaults);
}

/* Admin settings page */
// Use named functions instead of closures for better PHP compatibility
add_action('admin_menu', 'ecwpr_admin_menu');
function ecwpr_admin_menu() {
    add_options_page(
        'Easy Custom WP Login',
        'Easy Custom WP Login',
        'manage_options',
        'ecwpr-settings',
        'ecwpr_settings_page'
    );
}

add_action('admin_init', 'ecwpr_admin_init');
function ecwpr_admin_init() {
    register_setting('ecwpr_settings_group', 'ecwpr_settings', 'ecwpr_sanitize_settings');
}

function ecwpr_sanitize_settings($input) {
    $defaults = ecwpr_default_options();
    $out = array();

    $out['gradient1'] = isset($input['gradient1']) ? sanitize_hex_color($input['gradient1']) : $defaults['gradient1'];
    $out['gradient2'] = isset($input['gradient2']) ? sanitize_hex_color($input['gradient2']) : $defaults['gradient2'];
    $out['gradient3'] = isset($input['gradient3']) ? sanitize_hex_color($input['gradient3']) : $defaults['gradient3'];
    $out['gradient4'] = isset($input['gradient4']) ? sanitize_hex_color($input['gradient4']) : $defaults['gradient4'];
    $out['light_bg'] = isset($input['light_bg']) ? sanitize_hex_color($input['light_bg']) : $defaults['light_bg'];
    $out['enable_animation'] = !empty($input['enable_animation']) ? 1 : 0;
    $out['animation_speed'] = isset($input['animation_speed']) ? intval($input['animation_speed']) : $defaults['animation_speed'];
    if ($out['animation_speed'] <= 0) $out['animation_speed'] = $defaults['animation_speed'];
    $out['title_text'] = isset($input['title_text']) ? sanitize_text_field($input['title_text']) : $defaults['title_text'];
    $out['button_color'] = isset($input['button_color']) ? sanitize_hex_color($input['button_color']) : $defaults['button_color'];
    $out['input_border'] = isset($input['input_border']) ? sanitize_hex_color($input['input_border']) : $defaults['input_border'];
    $out['font_family'] = isset($input['font_family']) ? sanitize_text_field($input['font_family']) : $defaults['font_family'];
    $out['message_bg_color'] = isset($input['message_bg_color']) ? sanitize_hex_color($input['message_bg_color']) : $defaults['message_bg_color'];
    $out['error_bg_color'] = isset($input['error_bg_color']) ? sanitize_hex_color($input['error_bg_color']) : $defaults['error_bg_color'];
    // initial_mode: only 'light' or 'dark' allowed
    $out['initial_mode'] = (isset($input['initial_mode']) && $input['initial_mode'] === 'light') ? 'light' : 'dark';
    $out['logo'] = isset($input['logo']) ? esc_url_raw($input['logo']) : $defaults['logo'];
    $out['logo_mode'] = (isset($input['logo_mode']) && in_array($input['logo_mode'], array('contain','cover','square')) ) ? $input['logo_mode'] : $defaults['logo_mode'];
    $out['logo_square_size'] = isset($input['logo_square_size']) ? intval($input['logo_square_size']) : $defaults['logo_square_size'];
    if ($out['logo_square_size'] < 32) $out['logo_square_size'] = $defaults['logo_square_size'];
    $out['disable_styling'] = !empty($input['disable_styling']) ? 1 : 0;
    $out['hide_scrollbar'] = !empty($input['hide_scrollbar']) ? 1 : 0;
    $out['export_css_to_theme'] = !empty($input['export_css_to_theme']) ? 1 : 0;
    $out['override_all_styles'] = !empty($input['override_all_styles']) ? 1 : 0;

    return $out;
}

// Admin settings UI moved to admin/ecwpr-admin.php

// Include admin file (separates UI and scripts)
require_once plugin_dir_path(__FILE__) . 'admin/ecwpr-admin.php';

// Per-user preference removed — admin controls login/register appearance only.

// AJAX handler to save plugin settings from admin page
add_action('wp_ajax_ecwpr_save_settings', 'ecwpr_ajax_save_settings');
function ecwpr_ajax_save_settings() {
    if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ecwpr_save_settings' ) ) {
        wp_send_json_error('invalid_nonce', 400);
    }
    if (!current_user_can('manage_options')) {
        wp_send_json_error('forbidden', 403);
    }
    $raw = isset($_POST['settings']) && is_array($_POST['settings']) ? $_POST['settings'] : array();
    // Sanitize by reusing sanitize callback
    $sanitized = ecwpr_sanitize_settings($raw);
    // If logo_mode is 'square' and logo is set, attempt to generate a square resized image from the attachment
    if (!empty($sanitized['logo']) && isset($sanitized['logo_mode']) && $sanitized['logo_mode'] === 'square') {
        $attachment_id = attachment_url_to_postid($sanitized['logo']);
        $size = isset($sanitized['logo_square_size']) ? intval($sanitized['logo_square_size']) : 200;
        if ($attachment_id && $size > 0) {
            $new_url = ecwpr_make_square_logo($attachment_id, $size);
            if ($new_url) {
                $sanitized['logo'] = esc_url_raw($new_url);
            }
        }
    }
    update_option('ecwpr_settings', $sanitized);

    $exported = false;
    $export_result = array('exported' => false, 'path' => '', 'override_written' => false, 'override_path' => '', 'error' => '');
    // If admin requested, export the login CSS to the active theme for easier overrides or theme-level control
    if (!empty($sanitized['export_css_to_theme'])) {
        $src = plugin_dir_path(__FILE__) . 'admin/css/ecwpr-login.css';
        // Allow filter to change destination filename in the theme
        $theme_file = apply_filters('ecwpr_login_css_theme_filename', 'ecwpr-login.css');
        $dst = trailingslashit(get_stylesheet_directory()) . $theme_file;
            if (file_exists($src)) {
                // If destination already exists, attempt a non-destructive backup first
                if (file_exists($dst)) {
                    $bak = $dst . '.bak-' . time();
                    $bcopy = @copy($dst, $bak);
                    if ($bcopy) {
                        $export_result['backup_path'] = $bak;
                    } else {
                        // record backup failure but continue trying to write (caller will see copy failure if it occurs)
                        $export_result['backup_error'] = 'backup_failed';
                    }
                }
                // attempt to copy (overwrite)
                $copied = @copy($src, $dst);
                if ($copied) {
                    @chmod($dst, 0644);
                    $export_result['exported'] = true;
                    $export_result['path'] = $dst;
                } else {
                    $export_result['error'] = 'copy_failed';
                }

            // If override_all_styles is requested, produce a variant that contains additional !important rules
            if (!empty($sanitized['override_all_styles'])) {
                $override_dst = trailingslashit(get_stylesheet_directory()) . preg_replace('/(\.css)$/', '-override$1', $theme_file);
                // generate a simple override variant by appending extra rules
                $contents = @file_get_contents($src);
                if ($contents !== false) {
                    $override_rules = "\n/* Aggressive override rules added by plugin */\n";
                    $override_rules .= 'body.login.' . apply_filters('ecwpr_scoped_body_class', 'ecwpr-customized') . ' .login .button-primary{ background-color: var(--ecwpr-button) !important; }\n';
                    $override_rules .= 'body.login.' . apply_filters('ecwpr_scoped_body_class', 'ecwpr-customized') . ' .login input[type="text"], body.login.' . apply_filters('ecwpr_scoped_body_class', 'ecwpr-customized') . ' .login input[type="password"]{ border-color: var(--ecwpr-input-border) !important; }\n';
                    $ok = @file_put_contents($override_dst, $contents . $override_rules);
                    if ($ok !== false) {
                        @chmod($override_dst, 0644);
                        $export_result['override_written'] = true;
                        $export_result['override_path'] = $override_dst;
                    } else {
                        $export_result['error'] = 'override_write_failed';
                    }
                } else {
                    $export_result['error'] = 'read_src_failed';
                }
            }
        } else {
            $export_result['error'] = 'src_missing';
        }
    }

    wp_send_json_success(array('status' => 'saved', 'export' => $export_result));
}

    // AJAX endpoint to check whether theme export target exists and is writable (preflight)
    add_action('wp_ajax_ecwpr_check_export_target', 'ecwpr_ajax_check_export_target');
    function ecwpr_ajax_check_export_target() {
        if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ecwpr_save_settings' ) ) {
            wp_send_json_error(array('error' => 'invalid_nonce'), 400);
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('error' => 'forbidden'), 403);
        }
        $theme_file = apply_filters('ecwpr_login_css_theme_filename', 'ecwpr-login.css');
        $dst = trailingslashit(get_stylesheet_directory()) . $theme_file;
        $exists = file_exists($dst);
        $writable = is_writable(dirname($dst));
        wp_send_json_success(array('dst' => $dst, 'exists' => $exists, 'writable' => $writable));
    }

/**
 * Create a square resized image from an attachment and register it as a new attachment.
 * Returns URL on success, false on failure.
 */
function ecwpr_make_square_logo($attachment_id, $size) {
    if (!function_exists('wp_get_image_editor')) return false;
    $file = get_attached_file($attachment_id);
    if (!$file || !file_exists($file)) return false;

    $editor = wp_get_image_editor($file);
    if (is_wp_error($editor)) return false;

    $editor->resize($size, $size, true);
    $res = $editor->save();
    if (is_wp_error($res) || empty($res['path'])) return false;

    $new_file = $res['path'];
    $upload_dir = wp_upload_dir();
    // compute relative path
    $new_rel = str_replace(trailingslashit($upload_dir['basedir']), '', $new_file);
    $new_url = trailingslashit($upload_dir['baseurl']) . $new_rel;

    // Register as attachment
    $filetype = wp_check_filetype( basename( $new_file ), null );
    $attachment = array(
        'post_mime_type' => $filetype['type'],
        'post_title' => sanitize_file_name( basename( $new_file ) ),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $new_file );
    if ( $attach_id && !is_wp_error($attach_id) ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $new_file );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        // Track generated square images per source attachment so we can cleanup old generated files
        $meta_key = '_ecwpr_generated_' . intval( $size );
        $prev_id = get_post_meta( $attachment_id, $meta_key, true );
        if ( $prev_id && intval( $prev_id ) && intval( $prev_id ) !== intval( $attach_id ) ) {
            // Delete previous generated attachment (force delete to remove from uploads)
            @wp_delete_attachment( intval( $prev_id ), true );
        }
        update_post_meta( $attachment_id, $meta_key, intval( $attach_id ) );

        return esc_url_raw( $new_url );
    }
    return false;
}

// AJAX endpoint to generate a square logo immediately
add_action('wp_ajax_ecwpr_generate_square_logo', 'ecwpr_ajax_generate_square_logo');
function ecwpr_ajax_generate_square_logo() {
    if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ecwpr_save_settings' ) ) {
        wp_send_json_error('invalid_nonce', 400);
    }
    if (!current_user_can('manage_options')) {
        wp_send_json_error('forbidden', 403);
    }
    $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
    $size = isset($_POST['size']) ? intval($_POST['size']) : 200;
    if (!$attachment_id || $size <= 0) {
        wp_send_json_error('invalid_params', 400);
    }
    $new = ecwpr_make_square_logo($attachment_id, $size);
    if ($new) {
        wp_send_json_success(array('url' => $new));
    } else {
        wp_send_json_error('failed', 500);
    }
}

// Per-user quick-toggle removed — admin controls global appearance only.


// Use saved options for the login header text
add_filter('login_headertext', 'ecwpr_login_headertext');
function ecwpr_login_headertext() {
    $opts = ecwpr_get_options();
    return $opts['title_text'];
}

// Inject custom styles using settings
// Use priority 100 to ensure our styles come AFTER WordPress default login styles
add_action('login_head', 'ecwpr_login_head', 100);
function ecwpr_login_head() {
    try {
        $opts = ecwpr_get_options();
        
        // Output critical CSS immediately to prevent FOUC (flash of unstyled content)
        echo '<style id="ecwpr-critical">';
        echo 'body.login.ecwpr-customized { opacity: 0; transition: opacity 0.2s ease-in; }';
        echo 'body.login.ecwpr-customized.ecwpr-loaded { opacity: 1; }';
        echo '</style>';
        echo '<script>document.addEventListener("DOMContentLoaded", function(){ document.body.classList.add("ecwpr-loaded"); });</script>';
        
        // Debug: log options to help troubleshoot
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ECWPR Options on login: ' . print_r($opts, true));
        }
        $gradient = sprintf('linear-gradient(-45deg, %s, %s, %s, %s)', esc_attr($opts['gradient1']), esc_attr($opts['gradient2']), esc_attr($opts['gradient3']), esc_attr($opts['gradient4']));
        $light_bg = esc_attr($opts['light_bg']);
        $animation = $opts['enable_animation'] ? sprintf('animation: gradientShift %ss ease infinite;', intval($opts['animation_speed'])) : 'animation: none;';

        // If admin chose to disable plugin styling, skip everything
        if ( ! empty( $opts['disable_styling'] ) ) {
            return;
        }

        // Register and enqueue the plugin login stylesheet (so themes can override if needed)
        $css_file = plugin_dir_path(__FILE__) . 'admin/css/ecwpr-login.css';
        $css_url = plugin_dir_url(__FILE__) . 'admin/css/ecwpr-login.css';
        // Use filemtime for cache busting to ensure browsers get the latest CSS
        $css_version = file_exists( $css_file ) ? filemtime( $css_file ) : time();
        if ( file_exists( $css_file ) ) {
            wp_register_style( 'ecwpr-login-css', $css_url, array(), $css_version );
            wp_enqueue_style( 'ecwpr-login-css' );
        }

        $button_color = esc_attr($opts['button_color']);
        $input_border = esc_attr($opts['input_border']);
        $font_family = esc_attr($opts['font_family']);
        $message_bg = esc_attr($opts['message_bg_color']);
        $error_bg = esc_attr($opts['error_bg_color']);

        // Build dynamic CSS variables and rules (use scoped class from filter)
        $scoped = apply_filters('ecwpr_scoped_body_class', 'ecwpr-customized');
        $dynamic = ':root { --ecwpr-bg-dark: ' . $gradient . '; --ecwpr-bg-light: ' . $light_bg . '; --ecwpr-button: ' . $button_color . '; --ecwpr-input-border: ' . $input_border . '; --ecwpr-font: ' . $font_family . '; --ecwpr-message-bg: ' . $message_bg . '; --ecwpr-error-bg: ' . $error_bg . '; }\n';
        // Use higher specificity to ensure our styles override WordPress defaults
        $dynamic .= 'body.login.' . $scoped . ' { background: var(--ecwpr-bg-dark) !important; background-size: 400% 400% !important; ' . $animation . ' color: #f1f5f9; font-family: ' . $font_family . '; }\n';
        $dynamic .= '@keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}\n';

        if (!empty($opts['logo'])) {
            $mode = isset($opts['logo_mode']) ? $opts['logo_mode'] : 'contain';
            $square_size = isset($opts['logo_square_size']) ? intval($opts['logo_square_size']) : 200;
            if ($mode === 'square') {
                $dynamic .= 'body.login.' . $scoped . ' .ecwpr-login-logo img{ width: ' . $square_size . 'px !important; height: ' . $square_size . 'px !important; object-fit: cover !important; }\n';
            }
        }

        // If aggressive override is enabled, append extra override rules
        if (!empty($opts['override_all_styles'])) {
            $dynamic .= 'body.login.' . $scoped . ' .login .button-primary{ background-color: var(--ecwpr-button) !important; }\n';
            $dynamic .= 'body.login.' . $scoped . ' .login input[type="text"], body.login.' . $scoped . ' .login input[type="password"]{ border-color: var(--ecwpr-input-border) !important; }\n';
        }

        // If hide_scrollbar is enabled, add scrollbar hiding CSS
        if (!empty($opts['hide_scrollbar'])) {
            $dynamic .= 'html { overflow-y: auto !important; scrollbar-width: none !important; -ms-overflow-style: none !important; }\n';
            $dynamic .= 'html::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }\n';
            $dynamic .= 'body.login.' . $scoped . ' { scrollbar-width: none !important; -ms-overflow-style: none !important; }\n';
            $dynamic .= 'body.login.' . $scoped . '::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }\n';
        }

        // Try to attach inline style to the registered stylesheet. Some environments
        // may not allow wp_add_inline_style to take effect (or the style handle
        // might be missing). Provide a safe fallback to echo a <style> block
        // directly into the login head so styling still appears.
        $attached = false;
        if ( did_action( 'wp_enqueue_scripts' ) || did_action( 'wp_print_styles' ) || did_action( 'login_enqueue_scripts' ) ) {
            // If the style was registered/enqueued above, attempt to add inline
            // style. wp_add_inline_style will return false if the handle doesn't
            // exist; it doesn't throw, so we capture by checking registered styles.
            if ( wp_style_is( 'ecwpr-login-css', 'registered' ) ) {
                wp_add_inline_style( 'ecwpr-login-css', $dynamic );
                $attached = true;
            }
        } else {
            // WordPress may not yet have run styles hooks; still try to add inline
            if ( wp_style_is( 'ecwpr-login-css', 'registered' ) ) {
                wp_add_inline_style( 'ecwpr-login-css', $dynamic );
                $attached = true;
            }
        }

        if ( ! $attached ) {
            // Fallback: echo a style tag directly into the head. This guarantees
            // the dynamic CSS is present on the login page even when enqueueing
            // does not work as expected (useful for unusual setups/local dev).
            echo "\n<style id=\"ecwpr-login-inline-fallback\">" . $dynamic . "</style>\n";
        }
        
        // ALWAYS output an additional high-priority style block to ensure our background
        // and core styles are applied even if wp_add_inline_style doesn't work properly
        // or gets loaded in the wrong order. This uses even higher specificity.
        echo "\n<style id=\"ecwpr-login-priority\">\n";
        echo $dynamic;
        echo "</style>\n";

        // Debug marker
        echo "\n<!-- ECWPR: Styles injected successfully (attached=" . ($attached ? 'true' : 'false') . ") -->\n";
    } catch (\Throwable $e) {
        error_log('ecwpr_login_head error: ' . $e->getMessage());
        echo "\n<!-- ECWPR ERROR: " . esc_html($e->getMessage()) . " -->\n";
        return;
    }
}

// Ensure a visible header/title appears above the login form
add_filter('login_message', 'ecwpr_login_message');
function ecwpr_login_message($message) {
    try {
        $opts = ecwpr_get_options();
        $out = '';
        if (!empty($opts['logo'])) {
            $out .= '<div class="ecwpr-login-logo"><img src="' . esc_url($opts['logo']) . '" alt="' . esc_attr($opts['title_text']) . '" /></div>';
        }
        if (!empty($opts['title_text'])) {
            $out .= '<h1 class="ecwpr-login-title" style="text-align:center;margin-bottom:12px;">' . esc_html($opts['title_text']) . '</h1>';
        }
        return $out . $message;
    } catch (\Throwable $e) {
        error_log('ecwpr_login_message error: ' . $e->getMessage());
        return $message;
    }
}
// end login_message

// Add initial mode class to the login body so the server-rendered page matches admin's initial mode
add_filter('login_body_class', 'ecwpr_login_body_class');
function ecwpr_login_body_class($classes) {
    try {
        $opts = ecwpr_get_options();
        // Normalize $classes to an array
        if (is_array($classes)) {
            $class_array = $classes;
        } else {
            $class_array = preg_split('/\s+/', trim((string) $classes));
            if (!is_array($class_array)) $class_array = array();
            // remove empty values
            $class_array = array_filter($class_array);
        }

        // Add light-mode when requested
        if (isset($opts['initial_mode']) && $opts['initial_mode'] === 'light') {
            if (!in_array('light-mode', $class_array, true)) {
                $class_array[] = 'light-mode';
            }
        }

        // Add hide-scrollbar class when enabled
        if (!empty($opts['hide_scrollbar'])) {
            if (!in_array('ecwpr-hide-scrollbar', $class_array, true)) {
                $class_array[] = 'ecwpr-hide-scrollbar';
            }
        }

        // Always add our scoping class (theme can change via filter)
        $scoped = apply_filters('ecwpr_scoped_body_class', 'ecwpr-customized');
        if ($scoped && !in_array($scoped, $class_array, true)) {
            $class_array[] = $scoped;
        }

        return $class_array;
    } catch (\Throwable $e) {
        error_log('ecwpr_login_body_class error: ' . $e->getMessage());
        return $classes;
    }
}

// Add JavaScript for toggle and layout fix (keeps behavior but doesn't override settings beyond styling)
add_action('login_footer', 'ecwpr_login_footer');
function ecwpr_login_footer() {
    try {
        $opts = ecwpr_get_options();
        $initial = isset($opts['initial_mode']) ? $opts['initial_mode'] : 'dark';

        ?>
        <script>
        (function(){
            function safeGetLS(key){ try{ return localStorage.getItem(key); }catch(e){return null;} }
            function safeSetLS(key,val){ try{ localStorage.setItem(key,val); }catch(e){} }

            document.addEventListener('DOMContentLoaded', function () {
                var nav = document.getElementById('nav'), back = document.getElementById('backtoblog');
                if (nav && back) {
                    var wrapper = document.createElement('div');
                    wrapper.className = 'link-row';
                    nav.parentNode.insertBefore(wrapper, nav);
                    wrapper.appendChild(back);
                    wrapper.appendChild(nav);
                }

                var toggle = document.getElementById('darkLightToggle');
                if (!toggle) {
                    toggle = document.createElement('button');
                    toggle.id = 'darkLightToggle';
                    toggle.type = 'button';
                    toggle.setAttribute('aria-pressed','false');
                    toggle.textContent = 'Toggle Light';
                    document.body.appendChild(toggle);
                }

                function applyMode(mode){
                    if(mode === 'light'){
                        document.body.classList.add('light-mode');
                        toggle.textContent = 'Dark Mode';
                        toggle.setAttribute('aria-pressed','true');
                    } else {
                        document.body.classList.remove('light-mode');
                        toggle.textContent = 'Light Mode';
                        toggle.setAttribute('aria-pressed','false');
                    }
                }

                var saved = safeGetLS('ecwpr_mode');
                var mode = (saved === 'light' || saved === 'dark') ? saved : <?php echo json_encode($initial); ?>;
                applyMode(mode);

                toggle.addEventListener('click', function(){
                    var current = document.body.classList.contains('light-mode') ? 'light' : 'dark';
                    var next = (current === 'light') ? 'dark' : 'light';
                    applyMode(next);
                    safeSetLS('ecwpr_mode', next);
                });
            });
        })();
        </script>
        <?php
    } catch (\Throwable $e) {
        error_log('ecwpr_login_footer error: ' . $e->getMessage());
        // fail silently to avoid breaking the page
    }
}
