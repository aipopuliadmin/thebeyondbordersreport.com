# Beyond Borders Plugin

Editorial platform customization plugin for Beyond Borders Report.

## Features

- **Custom Post Types**: Featured Articles with full editorial workflow
- **Custom Taxonomies**: Regions for geographic categorization
- **WordPress Customizer Integration**: Live color palette customization
- **Theme Customization**: CSS variables for navy/gold editorial design system
- **User Capabilities**: Enhanced role management for editorial workflow
- **Admin Interface**: Settings pages for plugin configuration

## Installation

1. Upload the `beyond-borders` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings at **Beyond Borders → Settings**
4. Customize colors at **Beyond Borders → Color Palette** or via **Appearance → Customize**

## Usage

### Custom Post Types

The plugin registers a "Featured Articles" custom post type for premium editorial content.

### Color Customization

Colors can be customized in two ways:
1. **Admin Panel**: Navigate to Beyond Borders → Color Palette
2. **Customizer**: Appearance → Customize → Beyond Borders Colors

All colors are output as CSS variables and can be used in themes:
```css
var(--navy-deep)
var(--navy-primary)
var(--gold-primary)
var(--gold-champagne)
var(--cream)
var(--charcoal-dark)
```

### Hooks & Filters

The plugin provides various hooks for extensibility:
- Actions: `beyond_borders_activated`, `beyond_borders_deactivated`
- Filters: Theme developers can hook into custom post type registration

## Uninstallation

When the plugin is deleted (not just deactivated), it will:
- Remove all plugin options
- Clear transients
- Remove custom capabilities
- Optionally delete custom post type content (commented out by default)

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher

## Version

1.0.0

## License

GPL v2 or later
