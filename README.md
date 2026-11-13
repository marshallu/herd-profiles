Herd Profiles
===
Herd Profiles is a WordPress plugin developed by the Marketing and Communications Office for use on Marshall University websites.

## Using the Plugin
Please review the documentation at https://www.marshall.edu/marsha/herd-profiles/

## Developer Notes
1. Clone the repo
2. Run `npm install` to install the Git hooks
3. Run `composer install` to install the required composer dependencies

Styles live in `css/herd-profiles.css`, which WordPress loads as is, so there is no build step. Class names follow [BEM](https://getbem.com/naming/) (`block__element--modifier`), and every block starts with `herd-profiles-` so it cannot clash with a theme or another plugin, for example `herd-profiles-card__name` or `herd-profiles-row--profile`.

PHP files are checked with WordPress VIP Coding Standards through `composer lint`. Run `composer format` to apply safe automatic fixes before committing. The project-local VIP standards are installed by `composer install`.

`tests/email-visibility-test.php` checks the Show Email Address setting, including sites where Display Settings has never been saved. Run it with WP-CLI on a site with ACF and this plugin active: `wp eval-file tests/email-visibility-test.php`.

## Settings
This repo contains a default settings file for VS Code to ensure the code is developed following [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).
