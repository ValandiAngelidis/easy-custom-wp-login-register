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