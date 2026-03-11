# Header & Footer Settings Flow Analysis

## Overview
This document analyzes how the header settings system works so the same pattern can be applied to footer templates.

---

## Header Flow Architecture

### 1. **Plugin Admin Settings** (Backend)

**File**: `/wp-content/plugins/beyond-borders/admin/partials/beyond-borders-header-settings.php`

**Purpose**: Admin settings page where users configure header options

**Key Components**:

```php
// 1. Save settings on form submit
if ( isset( $_POST['beyond_borders_save_header'] ) ) {
    $header_settings = array(
        'header_layout'  => sanitize_text_field( $_POST['header_layout'] ?? 'default' ),
        'logo_type'      => sanitize_text_field( $_POST['logo_type'] ?? 'icon' ),
        'show_search'    => isset( $_POST['show_search'] ) ? 1 : 0,
        // ... more settings
    );
    
    // Save to WordPress options table
    update_option( 'beyond_borders_header_settings', $header_settings );
}

// 2. Load existing settings with defaults
$header_settings = get_option( 'beyond_borders_header_settings', array(
    'header_layout' => 'default',
    'logo_type'     => 'icon',
    // ... defaults
) );

// 3. Display form with current values
<select name="header_layout">
    <option value="default" <?php selected( $header_settings['header_layout'], 'default' ); ?>>Default</option>
    <option value="minimal" <?php selected( $header_settings['header_layout'], 'minimal' ); ?>>Minimal</option>
    <option value="magazine" <?php selected( $header_settings['header_layout'], 'magazine' ); ?>>Magazine</option>
    <option value="centered" <?php selected( $header_settings['header_layout'], 'centered' ); ?>>Centered</option>
</select>
```

**Available Header Layouts**:
- `default` - Standard layout
- `minimal` - Clean minimal style
- `magazine` - Editorial magazine style
- `centered` - Centered navigation

---

### 2. **Admin Menu Registration** (Plugin)

**File**: `/wp-content/plugins/beyond-borders/admin/class-beyond-borders-admin.php`

```php
public function add_plugin_admin_menu() {
    // Main menu
    add_menu_page(
        'Beyond Borders',
        'Beyond Borders',
        'manage_options',
        'beyond-borders',
        array( $this, 'display_plugin_admin_page' ),
        'dashicons-admin-site-alt3',
        26
    );

    // Header submenu
    add_submenu_page(
        'beyond-borders',
        __( 'Header Settings', 'beyond-borders' ),
        __( 'Header', 'beyond-borders' ),
        'manage_options',
        'beyond-borders-header',
        array( $this, 'display_header_settings_page' )
    );
}

public function display_header_settings_page() {
    include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-header-settings.php';
}
```

---

### 3. **Theme Integration** (Frontend)

**File**: `/wp-content/themes/beond-custom/header.php`

**Purpose**: Main theme header file that loads appropriate header template

```php
<?php
// 1. Get settings from WordPress options
$header_settings = get_option( 'beyond_borders_header_settings', array() );

// 2. Extract the layout choice
$header_layout = $header_settings['header_layout'] ?? 'default';

// 3. Load the corresponding template file
get_template_part( 'template-parts/header/header', $header_layout );
?>
```

**How `get_template_part()` works**:
- `get_template_part( 'template-parts/header/header', 'default' )` 
  - Looks for: `template-parts/header/header-default.php`
- `get_template_part( 'template-parts/header/header', 'minimal' )` 
  - Looks for: `template-parts/header/header-minimal.php`

---

### 4. **Header Template Files** (Theme)

**Location**: `/wp-content/themes/beond-custom/template-parts/header/`

**Available Templates**:
```
template-parts/header/
├── header-default.php    (when header_layout = 'default')
├── header-minimal.php    (when header_layout = 'minimal')
├── header-magazine.php   (when header_layout = 'magazine')
└── header-centered.php   (when header_layout = 'centered')
```

**Each template file structure**:

```php
<?php
/**
 * Header Default Template
 */

// Get settings inside template
$header_settings = get_option( 'beyond_borders_header_settings', array() );
?>

<header class="site-header header-default">
    <?php if ( ! empty( $header_settings['show_search'] ) ) : ?>
        <!-- Search icon -->
    <?php endif; ?>
    
    <?php if ( ! empty( $header_settings['show_theme_toggle'] ) ) : ?>
        <!-- Theme toggle -->
    <?php endif; ?>
    
    <!-- Header specific markup -->
</header>
```

---

## Complete Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│  ADMIN SETTINGS PAGE                                        │
│  (Plugin: admin/partials/beyond-borders-header-settings.php)│
├─────────────────────────────────────────────────────────────┤
│  User selects:                                              │
│  ✓ Header Layout: "magazine"                                │
│  ✓ Logo Type: "icon"                                        │
│  ✓ Show Search: Yes                                         │
│  ✓ Show Theme Toggle: Yes                                   │
│                                                              │
│  Clicks "Save Settings" ──────────────────┐                │
└───────────────────────────────────────────┼─────────────────┘
                                            │
                                            ▼
                        ┌──────────────────────────────────┐
                        │  WordPress Database              │
                        │  wp_options table                │
                        ├──────────────────────────────────┤
                        │  option_name:                    │
                        │  "beyond_borders_header_settings"│
                        │                                  │
                        │  option_value:                   │
                        │  {                               │
                        │    "header_layout": "magazine",  │
                        │    "logo_type": "icon",          │
                        │    "show_search": 1,             │
                        │    "show_theme_toggle": 1,       │
                        │    ...                           │
                        │  }                               │
                        └──────────────────────────────────┘
                                            │
                                            │ get_option()
                                            ▼
                        ┌──────────────────────────────────┐
                        │  THEME: header.php               │
                        ├──────────────────────────────────┤
                        │  $settings = get_option(...)     │
                        │  $layout = $settings['header_    │
                        │            layout'] ?? 'default' │
                        │                                  │
                        │  get_template_part(              │
                        │    'template-parts/header/header'│
                        │    $layout                       │
                        │  );                              │
                        └──────────────────────────────────┘
                                            │
                                            │ Loads template based on $layout
                                            ▼
        ┌───────────────────────────────────────────────────────────┐
        │  TEMPLATE FILES (one loaded based on header_layout)       │
        ├───────────────────────────────────────────────────────────┤
        │                                                           │
        │  header-default.php  ◄─── if header_layout = 'default'   │
        │  header-minimal.php  ◄─── if header_layout = 'minimal'   │
        │  header-magazine.php ◄─── if header_layout = 'magazine'  │
        │  header-centered.php ◄─── if header_layout = 'centered'  │
        │                                                           │
        │  Each template:                                           │
        │  - Gets settings again via get_option()                   │
        │  - Checks individual settings (show_search, etc.)         │
        │  - Renders HTML based on settings                         │
        │                                                           │
        └───────────────────────────────────────────────────────────┘
```

---

## Footer Implementation Pattern

### **Current Footer Status**:

✅ **Plugin settings page exists**: `beyond-borders-footer-settings.php`
✅ **Footer settings saved to**: `beyond_borders_footer_settings` option
✅ **Footer.php uses settings**: Gets `footer_layout` value
❌ **Missing**: Individual footer template files for different layouts

---

### **To Implement Footer Templates (Same as Header)**:

#### 1. **Create Footer Template Directory**:

```
/wp-content/themes/beond-custom/template-parts/footer/
├── footer-default.php
├── footer-minimal.php
├── footer-magazine.php
└── footer-dark.php
```

#### 2. **Update footer.php** (Main theme footer file):

```php
<?php
/**
 * The template for displaying the footer
 */

// Get footer settings from plugin
$footer_settings = get_option( 'beyond_borders_footer_settings', array() );

// Get footer layout choice (default to 'default')
$footer_layout = $footer_settings['footer_layout'] ?? 'default';

// Load the appropriate footer template
get_template_part( 'template-parts/footer/footer', $footer_layout );

wp_footer();
?>
</body>
</html>
```

#### 3. **Create Individual Footer Templates**:

**Example: `/template-parts/footer/footer-default.php`**:

```php
<?php
/**
 * Default Footer Template
 */

$footer_settings = get_option( 'beyond_borders_footer_settings', array() );
?>

<footer class="site-footer footer-default">
    
    <?php if ( ! empty( $footer_settings['show_newsletter_signup'] ) ) : ?>
    <!-- Newsletter Section -->
    <div class="newsletter-section">
        <h2><?php echo esc_html( $footer_settings['newsletter_title'] ?? 'Stay Informed' ); ?></h2>
        <p><?php echo esc_html( $footer_settings['newsletter_description'] ?? '' ); ?></p>
        <!-- Newsletter form -->
    </div>
    <?php endif; ?>
    
    <?php if ( ! empty( $footer_settings['enable_footer_widgets'] ) ) : ?>
    <!-- Footer Widgets -->
    <div class="footer-widgets">
        <div class="footer-columns columns-<?php echo esc_attr( $footer_settings['footer_columns'] ?? 4 ); ?>">
            <!-- Widget areas -->
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Copyright -->
    <div class="footer-bottom">
        <p><?php echo esc_html( $footer_settings['copyright_text'] ?? '' ); ?></p>
    </div>
    
</footer>
```

**Example: `/template-parts/footer/footer-minimal.php`**:

```php
<?php
/**
 * Minimal Footer Template
 */

$footer_settings = get_option( 'beyond_borders_footer_settings', array() );
?>

<footer class="site-footer footer-minimal">
    <div class="container">
        <div class="minimal-footer-content">
            <p><?php echo esc_html( $footer_settings['copyright_text'] ?? '' ); ?></p>
            
            <?php if ( ! empty( $footer_settings['show_social_links'] ) ) : ?>
            <div class="social-links">
                <!-- Social icons -->
            </div>
            <?php endif; ?>
        </div>
    </div>
</footer>
```

---

## Key Settings Available in Footer

From `beyond_borders_footer_settings`:

```php
array(
    'footer_layout'          => 'default|minimal|magazine|dark',
    'enable_footer_widgets'  => true|false,
    'footer_columns'         => 1|2|3|4,
    'show_newsletter_signup' => true|false,
    'newsletter_title'       => 'Stay Informed',
    'newsletter_description' => 'Subscribe...',
    'show_social_links'      => true|false,
    'copyright_text'         => '© 2026...',
    'show_back_to_top'       => true|false,
    'enable_footer_menu'     => true|false,
)
```

---

## Summary: Replicating Header Flow for Footer

| Step | Header Implementation | Footer Implementation |
|------|----------------------|----------------------|
| **1. Admin Settings** | `beyond-borders-header-settings.php` | ✅ `beyond-borders-footer-settings.php` (exists) |
| **2. Option Name** | `beyond_borders_header_settings` | ✅ `beyond_borders_footer_settings` (exists) |
| **3. Layout Setting** | `header_layout` → default/minimal/magazine/centered | ✅ `footer_layout` → default/minimal/magazine/dark |
| **4. Main Theme File** | `header.php` loads template via `get_template_part()` | ⚠️ `footer.php` needs update to use `get_template_part()` |
| **5. Template Directory** | `template-parts/header/` | ❌ Need to create: `template-parts/footer/` |
| **6. Template Files** | ✅ header-default.php, header-minimal.php, etc. | ❌ Need to create: footer-default.php, footer-minimal.php, etc. |

---

## Next Steps to Complete Footer System:

1. ✅ Create directory: `/template-parts/footer/`
2. ✅ Create template files:
   - `footer-default.php`
   - `footer-minimal.php`
   - `footer-magazine.php`
   - `footer-dark.php`
3. ✅ Update `footer.php` to use `get_template_part()`
4. ✅ Move footer HTML from current `footer.php` into individual templates
5. ✅ Add conditional logic in each template based on settings
6. ✅ Style each footer layout differently via CSS

---

## Benefits of This Pattern

✅ **Modularity**: Each layout is self-contained
✅ **Maintainability**: Easy to update one layout without affecting others
✅ **Flexibility**: Users can switch layouts without code changes
✅ **Consistency**: Same pattern for header, footer, and future components
✅ **Clean Code**: Separation of concerns

---

## Code Example: Complete Footer Flow

**Admin saves**: `footer_layout = 'magazine'`

**WordPress stores**: 
```php
// In wp_options table
'beyond_borders_footer_settings' => [
    'footer_layout' => 'magazine',
    'show_newsletter_signup' => true,
    // ...
]
```

**footer.php executes**:
```php
$footer_settings = get_option( 'beyond_borders_footer_settings' );
$footer_layout = $footer_settings['footer_layout']; // = 'magazine'
get_template_part( 'template-parts/footer/footer', $footer_layout );
// Loads: template-parts/footer/footer-magazine.php
```

**footer-magazine.php renders**:
```php
$footer_settings = get_option( 'beyond_borders_footer_settings' );
// Uses settings to conditionally render sections
```

---

**End of Analysis**
