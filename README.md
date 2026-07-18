MU Profiles
===
MU Profiles is a WordPress plugin developed by the Marketing and Communications Office for use on Marshall University websites.

## Using the Plugin
Please review the documentation at https://www.marshall.edu/marsha/mu-profiles/

## Developer Notes
1. Clone the repo
2. Run `npm install` to install the required npm dependencies
3. Run `composer install` to install the required composer dependencies
4. While developing local run `npm run dev` to start the Tailwind CSS watcher.
5. When ready to push to production run `npm run build`

PHP files are checked with WordPress VIP Coding Standards through `composer lint`. Run `composer format` to apply safe automatic fixes before committing. The project-local VIP standards are installed by `composer install`.

## Settings
This repo contains a default settings file for VS Code to ensure the code is developed following [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).
