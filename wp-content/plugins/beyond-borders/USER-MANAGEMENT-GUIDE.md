# Beyond Borders Plugin - User Management & Role-Based Settings

## Overview

The Beyond Borders plugin now includes a comprehensive **User Management & Role-Based Settings** system that provides administrators with full control over user roles, capabilities, and author types.

## Features

### 1. **Role Management Dashboard**
- View all user roles and their capabilities
- Display count of custom capabilities per role
- See total capabilities assigned to each role
- Monitor role distribution across the site

### 2. **Capability Assignment**
- Assign custom capabilities to specific roles (Administrator, Editor, Author, Contributor)
- Visual matrix showing which capabilities are assigned to which roles
- Easy checkbox-based interface for capability management
- Persistent storage of capability assignments

**Available Custom Capabilities:**
```
- edit_featured_article
- read_featured_article
- delete_featured_article
- edit_featured_articles
- edit_others_featured_articles
- publish_featured_articles
- read_private_featured_articles
- manage_newsletter
- view_analytics
- manage_author_types
```

### 3. **Author Types Management**
- View distribution of authors by type
- Support for three author types:
  - **BB Desk (Staff Writer)** - Internal journalists/editors
  - **Guest Author** - External contributors  
  - **Podcast Guest** - Podcast interviewees
- Display author count for each type
- Badge system for visual identification

### 4. **User Management**
- Comprehensive user list showing all authors and editors
- Assign author types to individual users
- View and manage user details:
  - Username
  - Email
  - Role
  - Author Type
  - Job Title
  - Company/Organization

### 5. **Tabbed Interface**
- **Role Management** - Overview of all roles and capabilities
- **Capabilities** - Detailed capability assignment matrix
- **Author Types** - Author type distribution and metadata
- **User Management** - Individual user type assignments

## How to Use

### Access the User Management Page

1. In the WordPress admin dashboard, click **Beyond Borders** in the left sidebar
2. Click **User Management** in the submenu
3. You'll see four tabs for different management functions

### Assign Capabilities to Roles

1. Go to the **Capabilities** tab
2. Review the matrix showing role/capability combinations
3. Check/uncheck boxes to enable/disable capabilities for each role
4. Click **"Update Capabilities"** to save changes

### Manage Author Types

1. Go to the **User Management** tab
2. Find the user you want to manage
3. Select their author type from the dropdown
4. Click **"Update Author Types"** to save

### View Statistics

1. Go to the **Role Management** tab to see role summaries
2. Go to the **Author Types** tab to see author distribution
3. Use the counts to understand your team composition

## Technical Details

### Class Files

**`class-beyond-borders-user-management.php`**
- Main user management class
- Handles role operations
- Manages capabilities
- Author type functions
- Statistical methods

**Added to:**
- `beyond-borders.php` - Plugin loader
- `class-beyond-borders.php` - Admin hooks registration
- `class-beyond-borders-admin.php` - Menu and display methods

### Database Operations

The system uses standard WordPress functions:
- `get_role()` - Retrieve role objects
- `get_users()` - Query users by role
- `get_user_meta()` - Retrieve user metadata
- `update_user_meta()` - Update user metadata
- `add_cap()` / `remove_cap()` - Manage capabilities

User data is stored in:
- `wp_usermeta` - User metadata (author_type, job_title, company, etc.)
- `wp_options` - Plugin settings if additional config options are needed

### Available Methods

```php
// Get all roles with their capabilities
Beyond_Borders_User_Management::get_all_roles()

// Get custom capabilities
Beyond_Borders_User_Management::get_custom_capabilities()

// Get author types
Beyond_Borders_User_Management::get_author_types()

// Add capability to role
$manager->add_capability_to_role( $role_name, $capability )

// Remove capability from role
$manager->remove_capability_from_role( $role_name, $capability )

// Get user's author type
Beyond_Borders_User_Management::get_user_author_type( $user_id )

// Set user's author type
$manager->set_user_author_type( $user_id, $author_type )

// Get users by author type
Beyond_Borders_User_Management::get_users_by_author_type( $author_type )

// Get capability statistics
Beyond_Borders_User_Management::get_capability_stats()

// Get author type distribution
Beyond_Borders_User_Management::get_author_type_distribution()

// Export user data
Beyond_Borders_User_Management::export_user_data()
```

## File Structure

```
wp-content/plugins/beyond-borders/
├── includes/
│   ├── class-beyond-borders-user-management.php    (NEW)
│   └── class-beyond-borders.php                    (UPDATED)
├── admin/
│   ├── class-beyond-borders-admin.php              (UPDATED)
│   ├── css/
│   │   └── admin-settings.css                      (UPDATED)
│   ├── js/
│   │   └── user-management.js                      (NEW)
│   └── partials/
│       └── beyond-borders-user-management.php      (NEW)
└── beyond-borders.php                              (UPDATED)
```

## Security Features

- **Nonce Verification** - All forms include `wp_nonce_field()`
- **Capability Checks** - Functions check `current_user_can()`
- **Input Sanitization** - All user inputs are sanitized
- **Output Escaping** - All output is properly escaped with `esc_html()`, `esc_attr()`, etc.
- **Admin-Only Access** - Role management pages require `manage_options` capability

## Styling

The user management page includes:
- Professional tabbed interface
- Responsive mobile design
- Color-coded badges:
  - Green (✓) for BB Desk authors
  - Yellow (◆) for Guest authors
  - Pink (♪) for Podcast guests
- Hover effects and animations
- Clean, accessible form elements

## JavaScript Features

**File:** `admin/js/user-management.js`

Features:
- Tab switching with smooth animations
- Form submission states
- Visual feedback on changes
- Accessibility enhancements
- Search and filter functionality (ready for enhancement)
- Checkbox management utilities
- Unsaved changes warning

## Usage Examples

### Programmatic Role Assignment

```php
// Get Beyond Borders user manager
$manager = new Beyond_Borders_User_Management( 'beyond-borders', '1.0.0' );

// Add featured article capability to editor role
$manager->add_capability_to_role( 'editor', 'publish_featured_articles' );

// Remove capability from contributor
$manager->remove_capability_from_role( 'contributor', 'edit_featured_article' );

// Set user as guest author
$manager->set_user_author_type( 123, 'guest_author' );

// Get all BB Desk authors
$staff_writers = Beyond_Borders_User_Management::get_users_by_author_type( 'bb_desk' );
```

### Frontend Template Integration

```php
<?php
// In theme template files
if ( function_exists( 'Beyond_Borders_User_Management' ) ) {
    $author_type = Beyond_Borders_User_Management::get_user_author_type( get_the_author_meta( 'ID' ) );
    $author_types = Beyond_Borders_User_Management::get_author_types();
    
    if ( isset( $author_types[ $author_type ] ) ) {
        echo $author_types[ $author_type ]['label'];
    }
}
?>
```

## Future Enhancements

Potential additions:
- Bulk user operations
- CSV export/import of user data
- Custom role creation
- Capability templates
- User activity logs
- Advanced filtering and search
- Author type-specific metadata management
- REST API endpoints for user management

## Support & Troubleshooting

### Role Capabilities Not Updating

1. Verify `manage_options` capability
2. Check browser console for JavaScript errors
3. Ensure nonce is being submitted with form

### Users Not Showing in List

1. Ensure users have role: author, editor, or administrator
2. Check WordPress database for user existence
3. Verify admin can access Users section

### Styling Issues

1. Clear WordPress cache
2. Clear browser cache
3. Verify `admin-settings.css` is enqueued
4. Check browser DevTools for CSS conflicts

## Version Info

- **Plugin Version:** 1.0.0+
- **Requires WordPress:** 6.0+
- **Requires PHP:** 7.4+
- **Author:** Beyond Borders Report
- **License:** GPL v2 or later
