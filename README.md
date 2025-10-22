# Easy Custom WP Login Register

A small WordPress plugin to style and modernize the login/register pages with a configurable animated gradient background, dark/light mode toggle, custom branding text, color pickers for buttons and inputs, and an admin settings page with live preview.

## Features

- Admin-configurable gradient background (4 color stops)
- Light-mode background color
- Toggleable gradient animation with adjustable speed
- Button color and input border color controls
- Custom branding footer text and login header text
- Initial login mode (Dark or Light) set by admin — users can toggle and their choice persists in their browser (localStorage)
- Admin settings include a live preview and AJAX save
- Inputs are sanitized server-side

## Installation

1. Copy the `easy-custom-wp-login-register` plugin folder to your site's `wp-content/plugins/` directory.
2. Activate the plugin from the WordPress admin under **Plugins** → **Installed Plugins**.
3. Go to **Settings** → **Easy Custom WP Login** to configure colors, animation, fonts, and the initial login mode.

## Settings

- Gradient color 1..4: The four color stops used in the animated background.
- Light mode background: Background color used when the page is in light mode.
- Button color: Primary button color on the login form.
- Input border color: Border color for username/password fields.
- Font family: A font-family stack used on the login page.
- Enable gradient animation: Toggle the animated gradient.
- Animation speed: Number of seconds for one animation cycle.
- Initial login mode: Admin sets the default mode (dark or light). Users can toggle on the front-end and that preference is stored locally in their browser.

Note: The Dark/Light toggle persists only in the user's browser using `localStorage`. No per-user data is stored server-side by default.

## Quick testing checklist

- Adjust settings in the admin and click **Save Settings**. The settings page uses AJAX; you should see a confirmation.
- Open `/wp-login.php` in a private/incognito window to ensure no cached localStorage affects results.
- Verify the initial mode corresponds to the admin "Initial login mode" setting.
- Toggle Dark/Light and refresh — the toggle choice should persist for that browser.

## Running the included unit test

A PHPUnit test skeleton for the sanitizer is included at `tests/TestSanitize.php`. It uses `WP_UnitTestCase` and requires the WordPress PHPUnit test environment (the WordPress test suite) to run.

To run tests locally:

1. Set up the WordPress PHPUnit testing environment following the official guide: https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/
2. Ensure you have a `phpunit.xml` configured to bootstrap the WP tests and point to your local test database.
3. From the plugin root, run PHPUnit (example):

```powershell
# From plugin directory
phpunit --filter Test_Sanitize_Settings
```

Note: On Windows, you may find it easier to run the tests inside WSL or a container where the WP test bootstrap is already configured.

## Uninstall

If you delete the plugin via the WordPress admin Plugins screen, the `uninstall.php` in the plugin root will remove the stored plugin options (`ecwpr_settings`) from the database.

## Troubleshooting

- If colors don't update, clear your browser cache and localStorage for the site.
- If the admin color picker isn't visible, ensure `wp-color-picker` is available (WordPress core provides it) and that you're on the plugin settings page.

## License

This plugin is released under the GPL-2.0+ license.

## Support

Open an issue or contact the maintainer if you need help configuring the plugin or integrating with custom themes.

## Advanced: theme integration and filters

The plugin provides a few filters themes can use to opt-in or customize behavior:

- `ecwpr_scoped_body_class` (string) — change the classname the plugin adds to the login body. Default: `'ecwpr-customized'`.
- `ecwpr_login_css_filename` (string) — change which CSS file inside the plugin is enqueued. Default: `'admin/css/ecwpr-login.css'`.
- `ecwpr_login_css_theme_filename` (string) — change the destination filename used when exporting CSS into the active theme. Default: `'ecwpr-login.css'`.

Example: add this to your theme's `functions.php` to opt-in and use a different scoping classname:

```php
add_filter('ecwpr_scoped_body_class', function($default){
	// Use a theme-specific class so you can style login page from your theme
	return 'mytheme-login-styles';
});

// Optional: change the file name used when exporting into the theme
add_filter('ecwpr_login_css_theme_filename', function($default){
	return 'mytheme-ecwpr-login.css';
});
```

When a theme opts in by changing the scoped class it can then provide its own CSS rules targeting that class and the plugin will not interfere unless `override_all_styles` is enabled.
