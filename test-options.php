<?php
/**
 * Quick test script to check if options are saved correctly
 * Access via: /wp-content/plugins/easy-custom-wp-login-register/test-options.php
 * 
 * NOTE: This is a debugging file. Delete after testing.
 */

// Load WordPress by walking up folders to reliably locate wp-load.php in various environments
$wp_load = false;
$dir = __DIR__;
for ($i = 0; $i < 8; $i++) {
    $maybe = $dir . str_repeat(DIRECTORY_SEPARATOR . '..', $i) . DIRECTORY_SEPARATOR . 'wp-load.php';
    $maybe = realpath($maybe);
    if ($maybe && file_exists($maybe)) {
        $wp_load = $maybe;
        break;
    }
}
if (! $wp_load) {
    // fallback to common parent locations
    $maybe = realpath(__DIR__ . '/../../../../wp-load.php');
    if ($maybe && file_exists($maybe)) $wp_load = $maybe;
}
if (! $wp_load) {
    die('Could not locate wp-load.php. Adjust the test script path or run from within WordPress.');
}
require_once $wp_load;

if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

echo '<h1>ECWPR Settings Debug</h1>';
echo '<pre>';

$opts = get_option('ecwpr_settings', array());
echo "Current settings in database:\n";
print_r($opts);

echo "\n\n";
echo "Expected keys:\n";
$defaults = array(
    'gradient1', 'gradient2', 'gradient3', 'gradient4',
    'light_bg', 'enable_animation', 'animation_speed',
    'branding_text', 'title_text', 'button_color', 'input_border',
    'font_family', 'initial_mode', 'logo', 'logo_mode',
    'logo_square_size', 'disable_styling', 'export_css_to_theme',
    'override_all_styles'
);

foreach ($defaults as $key) {
    $exists = isset($opts[$key]) ? '✓' : '✗';
    $value = isset($opts[$key]) ? $opts[$key] : 'NOT SET';
    echo "$exists $key = $value\n";
}

echo '</pre>';
