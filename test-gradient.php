<?php
/**
 * Test gradient generation - verify colors match database
 */

// Load WordPress
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
    $maybe = realpath(__DIR__ . '/../../../../wp-load.php');
    if ($maybe && file_exists($maybe)) $wp_load = $maybe;
}
if (! $wp_load) {
    die('Could not locate wp-load.php');
}
require_once $wp_load;

if (!current_user_can('manage_options')) {
    die('Access denied');
}

// Get options the same way the plugin does
function ecwpr_default_options() {
    return array(
        'gradient1' => '#0b1120',
        'gradient2' => '#1e3a8a',
        'gradient3' => '#1e40af',
        'gradient4' => '#1e3a8a',
        'light_bg' => '#f3f4f6',
        'enable_animation' => 1,
        'animation_speed' => 20,
        'branding_text' => 'Powered by Stratonoakland',
        'title_text' => 'Pro Sales Team',
        'button_color' => '#3b82f6',
        'input_border' => '#cbd5e1',
        'font_family' => "Segoe UI, Roboto, sans-serif",
        'initial_mode' => 'dark',
        'logo' => '',
        'logo_mode' => 'contain',
        'logo_square_size' => 200,
        'disable_styling' => 0,
        'export_css_to_theme' => 0,
        'override_all_styles' => 0,
    );
}

$defaults = ecwpr_default_options();
$opts = get_option('ecwpr_settings', array());
$opts = wp_parse_args($opts, $defaults);

echo '<h1>Gradient Test</h1>';
echo '<pre>';

echo "Raw database values:\n";
echo "gradient1: " . $opts['gradient1'] . "\n";
echo "gradient2: " . $opts['gradient2'] . "\n";
echo "gradient3: " . $opts['gradient3'] . "\n";
echo "gradient4: " . $opts['gradient4'] . "\n\n";

$gradient = sprintf('linear-gradient(-45deg, %s, %s, %s, %s)', 
    esc_attr($opts['gradient1']), 
    esc_attr($opts['gradient2']), 
    esc_attr($opts['gradient3']), 
    esc_attr($opts['gradient4'])
);

echo "Generated gradient CSS:\n";
echo $gradient . "\n\n";

echo "Expected (from page source):\n";
echo "linear-gradient(-45deg, #c0dd49, #d62f8b, #e02667, #2687bf)\n\n";

echo "Match? " . ($gradient === 'linear-gradient(-45deg, #c0dd49, #d62f8b, #e02667, #2687bf)' ? 'YES ✓' : 'NO ✗') . "\n";

echo '</pre>';

// Show preview
echo '<div style="width:400px;height:300px;background:' . $gradient . ';background-size:400% 400%;animation:gradientShift 10s ease infinite;border-radius:12px;margin:20px 0;"></div>';
echo '<style>@keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}</style>';
