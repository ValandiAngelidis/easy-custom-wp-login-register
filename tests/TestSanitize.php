<?php
/**
 * PHPUnit tests for ecwpr_sanitize_settings
 */

require_once dirname(__DIR__) . '/easy-custom-wp-login-register.php';

class Test_Sanitize_Settings extends WP_UnitTestCase {

    public function test_color_sanitization() {
        $input = [
            'gradient_1' => '#ff0000',
            'gradient_2' => 'invalid-color',
            'button_color' => '#00ff00',
            'input_border' => 'not-a-color',
        ];

        $out = ecwpr_sanitize_settings($input);

        $this->assertEquals('#ff0000', $out['gradient_1']);
        $this->assertEquals('', $out['gradient_2']);
        $this->assertEquals('#00ff00', $out['button_color']);
        $this->assertEquals('', $out['input_border']);
    }

    public function test_numeric_and_text() {
        $input = [
            'enable_animation' => '1',
            'animation_speed' => '500',
            'branding_text' => '<script>alert(1)</script>Brand',
            'title_text' => 'Site <b>Title</b>',
        ];

        $out = ecwpr_sanitize_settings($input);

        $this->assertEquals(1, $out['enable_animation']);
        $this->assertEquals(500, $out['animation_speed']);

        // sanitized text should not contain angle brackets and should include plain text
        $this->assertStringNotContainsString('<', $out['branding_text']);
        $this->assertStringNotContainsString('>', $out['branding_text']);
        $this->assertStringContainsString('Brand', $out['branding_text']);
    }

    public function test_initial_mode() {
        $input = ['initial_mode' => 'dark'];
        $out = ecwpr_sanitize_settings($input);
        $this->assertEquals('dark', $out['initial_mode']);

        $input = ['initial_mode' => 'invalid'];
        $out = ecwpr_sanitize_settings($input);
        // fallback to 'light' when invalid
        $this->assertEquals('light', $out['initial_mode']);
    }
}
