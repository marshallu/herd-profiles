# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Herd Profiles is a WordPress plugin for managing employee/faculty profiles at Marshall University. It registers a custom post type (`employee`) and taxonomy (`department`), integrates with Advanced Custom Fields Pro (ACF), and provides multiple display templates via shortcodes.

## Commands

```bash
# Install dependencies
npm install
composer install

# CSS development (Tailwind watcher)
npm run dev

# CSS production build (minified)
npm run build

# PHP linting (WordPress VIP Coding Standards)
composer lint

# PHP auto-fix formatting
composer format
```

A pre-commit hook (via Lefthook) automatically runs `composer lint` before each commit.

## Architecture

### Core Files

- `herd-profiles.php` — Plugin entry point; registers the `employee` post type, `department` taxonomy, and enqueues assets
- `acf-fields.php` — All ACF field group definitions (1600+ lines); fields are keyed with `field_*` identifiers for portability
- `shortcodes.php` — Defines `[herd_employee]`, `[herd_profiles]`, and `[herd_profile]` shortcodes with attributes: `ids`, `department`, `layout`, `site`, `per_row`; the legacy `[mu_*]` aliases remain supported
- `display-custom.php` — WordPress query hooks: alphabetical sorting on department archives, removes pagination
- `editor.php` — Admin UI customizations: dashboard columns, title placeholder, auto-generates full name from ACF fields

### Templates (`/templates/`)

Each template is a standalone PHP file loaded by the shortcode based on the `layout` attribute:

| Template | Layout |
|---|---|
| `basic.php` | Simple list with image and text |
| `card.php` | Card grid (3 per row default) |
| `table.php` | Tabular layout |
| `grid.php` | Grid layout |
| `enhanced.php` | Featured/enhanced layout |
| `full-profile.php` | Complete profile display |
| `icon-card.php` | Cards with icon elements |
| `row.php` | Horizontal row layout |
| `single-employee.php` | Single employee page (most complex) |
| `taxonomy-department.php` | Department archive page |

### CSS

Tailwind CSS v4 source is at `source/css/herd-profiles.css`; compiled output goes to `css/herd-profiles.css`. The `source/` and `css/` directories are excluded from PHP linting.

### Multi-site Support

The `site` shortcode attribute allows displaying profiles from other sites in a WordPress network by switching context with `switch_to_blog()`.

### ACF Fields

Fields are organized into groups covering: name components (title, first, middle, last), contact info (phone auto-formatted via `herd_profiles_format_phone()`), headshot, bio, pronouns, service information, office hours, and display control toggles.

## WordPress VIP Coding Standards

PHP files must follow WPVIP’s `WordPress-VIP-Go`, WordPress Extra/Docs, and PHPCompatibilityWP standards. The `acf-fields.php` file is excluded from linting (configured in `phpcs.xml.dist`). Use `composer lint` to check and `composer format` to auto-fix before committing.
