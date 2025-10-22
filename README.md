<<<<<<< HEAD
=======
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
=======
>>>>>>> 021909a8b705441eb6784172693f41c29ed2f455
# 🧩 Easy Custom WP Login Register Plugin

**Easy Custom WP Login Register** is a lightweight yet powerful **WordPress plugin** that modernizes and styles the **native WordPress Login, Register, and Password Reset pages** — with live previews and zero coding required.

It provides gradient backgrounds, dark/light mode toggles, color controls, branding options, and a live admin preview — all built with clean WordPress standards and no external dependencies.

---

## 🚀 Features

- 🎨 **Admin-configurable animated gradient background** (4 color stops)
- 🌗 **Dark/Light mode toggle** with user preference stored in localStorage
- 💡 **Custom branding** — add your own footer and header text
- 🎛️ **Color pickers** for buttons and input borders
- 🧑‍💻 **Font customization** and theme-aware design
- ⚙️ **Live preview in admin settings** for instant visual feedback
- 🔐 **Works with native WordPress login/register/reset pages**
- ⚡ **No shortcodes, no redirects, no page builders required**
- 🛡️ **Inputs sanitized server-side** for security and stability
- 🔄 **Lightweight and responsive** — minimal impact on performance

---

## 🧠 Why Use It?

The WordPress login page is the first impression many users get — it should reflect your brand, not WordPress’s defaults.  
This plugin gives you complete visual and functional control while keeping the **core WordPress authentication system** intact.

---

## ⚙️ Installation

1. Copy the `easy-custom-wp-login-register` folder into your site’s `wp-content/plugins/` directory.  
2. Activate the plugin from **Plugins → Installed Plugins**.  
3. Open **Settings → Easy Custom WP Login** to customize colors, gradients, fonts, and initial mode.  

---

## 🧰 Settings Overview

| Setting | Description |
|----------|-------------|
| **Gradient Color 1–4** | Defines the four color stops for the animated background. |
| **Light Mode Background** | Color used when the page is in light mode. |
| **Button Color** | Primary color for login buttons. |
| **Input Border Color** | Border color for username/password fields. |
| **Font Family** | Custom font stack for login page typography. |
| **Enable Gradient Animation** | Toggle gradient motion on/off. |
| **Animation Speed** | Duration (seconds) per animation cycle. |
| **Initial Login Mode** | Default mode (dark or light) for first load. Users can toggle and their choice persists locally. |

> Note: Dark/Light toggle preference is stored only in the browser using `localStorage`. No per-user server data is stored.

---

## 🧾 Quick Testing Checklist

- Change settings in the admin and click **Save Settings** (AJAX save confirmation should appear).  
- Open `/wp-login.php` in a private/incognito window to test without cached data.  
- Verify the default mode matches the “Initial login mode.”  
- Toggle dark/light mode and refresh — it should remember your preference.  

---

## 🧪 Unit Testing

A PHPUnit test skeleton for sanitization is included at `tests/TestSanitize.php`.  
To run it:

1. Set up the WordPress PHPUnit testing environment:  
   [WordPress Automated Testing Guide](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/)
2. Configure `phpunit.xml` to use your local test database.
3. Run from the plugin root:

```powershell
phpunit --filter Test_Sanitize_Settings
🧼 Uninstall
When deleted via the WordPress admin Plugins screen, uninstall.php will remove all stored plugin options (ecwpr_settings) from the database.

🧩 Advanced Integration & Filters
The plugin provides filters that themes or developers can use to extend functionality:

Filter	Purpose
ecwpr_scoped_body_class	Change the class name added to the login <body> (default: ecwpr-customized).
ecwpr_login_css_filename	Change which CSS file inside the plugin is enqueued.
ecwpr_login_css_theme_filename	Customize the filename used when exporting CSS into the theme (default: ecwpr-login.css).

Example:

php
Copy code
add_filter('ecwpr_scoped_body_class', function() {
    return 'mytheme-login-styles';
});

add_filter('ecwpr_login_css_theme_filename', function() {
    return 'mytheme-ecwpr-login.css';
});
🛠 Troubleshooting
If colors don’t update: clear your browser cache and localStorage.

If color pickers don’t appear: ensure wp-color-picker is enqueued (WordPress core provides it).

📄 License
Released under the GPL-2.0+ license.
You are free to use, modify, and distribute this plugin under the same license.

👨‍💻 Author
Valandi Angelidis
🌐 Official Website
💼 GitHub
📧 valandiangelidis@.com

“With WordPress, creativity has no limits — from automation and AI integrations to complete digital ecosystems.”
— Valandi Angelidis