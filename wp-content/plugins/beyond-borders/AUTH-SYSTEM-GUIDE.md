# Beyond Borders Authentication System Documentation

## Overview

The Beyond Borders Authentication System provides custom registration and login pages separate from WordPress's default login interface. Users can register with extended profile information, log in, and manage their profiles from the frontend.

## Features

### 1. **Custom Registration Page** (`/user/register`)
- Captures full profile information during registration
- Fields include:
  - **Basic Info**: First name, Last name, Email, Password
  - **Professional Info**: Job title, Company, Bio
  - **Profile Image**: Upload custom profile picture
- Form validation on both client and server side
- All users stored in WordPress `wp_users` table
- Profile metadata stored in `wp_usermeta` table
- Users assigned "Guest Author" role by default

### 2. **Custom Login Page** (`/user/login`)
- Email-based login (uses WordPress user database)
- Redirects to `/user/profile` on successful login
- Redirect to `/user/register` if user doesn't have account
- Password validation against WordPress user records

### 3. **User Profile Page** (`/user/profile`)
- Protected page (requires login)
- Display current profile information
- Edit profile details
- Upload/update profile image
- View account stats (member since, posts published)
- Logout button

### 4. **Logout** (`/user/logout`)
- Logout endpoint that clears session
- Redirects to homepage

## URL Structure

All authentication pages are accessible under the `/user/` prefix to maintain flexibility for future changes:

```
/user/register  - Registration page
/user/login     - Login page
/user/profile   - User profile page (requires login)
/user/logout    - Logout endpoint
```

## File Structure

```
beyond-borders/
├── includes/
│   └── class-beyond-borders-auth.php       (Core auth class)
├── templates/
│   ├── auth-login.php                      (Login page template)
│   ├── auth-register.php                   (Registration template)
│   └── auth-profile.php                    (Profile page template)
├── css/
│   └── auth-styles.css                     (Styling for auth pages)
└── js/
    └── auth.js                             (Client-side validation)
```

## Class: Beyond_Borders_Auth

### Core Methods

#### `init()`
- Initializes authentication hooks on plugin load
- Sets up URL rewrite rules
- Adds custom query variables
- Enqueues scripts and styles

#### `setup_rewrite_rules()`
- Creates URL rewrite rules for `/user/register`, `/user/login`, `/user/profile`
- Uses WordPress rewrite API for clean URLs

#### `add_query_vars($vars)`
- Registers `bb_auth` custom query variable

#### `handle_auth_pages()`
- Router function that directs requests to appropriate page handler
- Handles logout redirect
- Calls specific handlers based on `bb_auth` query var

#### `handle_login_page()`
- Displays login form
- Processes login form submission
- Redirects logged-in users to profile

#### `handle_register_page()`
- Displays registration form
- Processes registration form submission
- Redirects already-logged-in users to profile

#### `handle_profile_page()`
- Displays user profile page
- Processes profile updates
- Protects page (requires login)

#### `process_login_form()`
- Verifies nonce security
- Validates email and password
- Looks up user by email
- Verifies password with WordPress password hashing
- Sets authentication cookies

#### `process_register_form()`
- Verifies nonce security
- Validates all input fields:
  - Email uniqueness
  - Password length (min 6 chars)
  - Password confirmation match
- Creates WordPress user with `wp_insert_user()`
- Stores profile metadata:
  - `job_title_bb` - Job title
  - `company_bb` - Company name
  - `bio_bb` - User biography
  - `author_type` - Set to "guest_author"
  - `profile_image_id` - Attachment ID for profile image
- Automatically logs user in
- Redirects to profile page

#### `process_profile_update()`
- Verifies nonce security
- Updates user data with `wp_update_user()`
- Updates all profile metadata
- Handles profile image upload

#### `handle_image_upload($file, $user_id)`
- Handles file upload with WordPress media functions
- Validates file type and size
- Creates attachment post
- Returns attachment ID for metadata storage

#### `enqueue_auth_scripts()`
- Loads auth CSS and JavaScript on auth pages only
- Prevents unnecessary asset loading

#### `get_user_profile_image($user_id, $size)`
- Retrieves user's profile image URL
- Falls back to gravatar if no custom image
- Supports different image sizes (thumbnail, medium, large)

#### `get_default_avatar($user_id)`
- Returns gravatar URL for user

## Database Structure

### User Data
- Stored in WordPress `wp_users` table using `wp_insert_user()`
- Generated username format: `{email_without_@}_{random_3_digits}`
- Password hashed with WordPress password hashing algorithm

### Profile Metadata
Stored in `wp_usermeta` table:
- `job_title_bb` - Job title (string)
- `company_bb` - Company name (string)
- `bio_bb` - Biography text (string)
- `author_type` - Author classification (default: "guest_author")
- `profile_image_id` - Attachment post ID (integer)

## Security Features

### Nonce Verification
- Every form includes `wp_nonce_field()` for CSRF protection
- Nonce verified with `wp_verify_nonce()` before processing

### Input Sanitization
- All inputs sanitized with appropriate WordPress functions:
  - `sanitize_text_field()` - Regular text
  - `sanitize_email()` - Email addresses
  - `sanitize_textarea_field()` - Longer text content
- File uploads validated for type and size

### Password Security
- Minimum 6 characters enforced
- Uses WordPress password hashing via `wp_insert_user()`
- Password verification with `wp_check_password()`

### Authentication
- Uses WordPress authentication cookies
- Session-based login tracking
- Automatic logout available at `/user/logout`

## Form Validation

### Client-Side (JavaScript)
- Email format validation with regex
- Password length check (minimum 6 characters)
- Password match verification
- Image file validation:
  - Accepted types: JPEG, PNG, GIF
  - Max file size: 5MB
- Real-time feedback for password mismatch

### Server-Side (PHP)
- All client-side validation repeated server-side
- Email uniqueness check: `email_exists()`
- Comprehensive error collection and reporting
- Graceful error messages in session

## Styling

The authentication system uses a custom CSS file (`auth-styles.css`) that:
- Matches Beyond Borders brand colors (Navy, Gold, Cream)
- Uses consistent typography (Playfair Display, Lora, Inter)
- Provides responsive design for mobile/tablet
- Includes animations and transitions
- Accessible form design with proper labels

## Integration with Existing System

### User Management Tab Integration
The new auth system integrates with the existing user management system:
- Users created via registration are visible in User Management dashboard
- Author types can be managed via User Management page
- Additional capabilities can be assigned via User Management interface

### Author Types
- Default author type: `guest_author`
- Can be changed in User Management > User Management tab
- Works with existing author metadata system

## Usage Examples

### For Users
1. **Register**: Visit `/user/register` and fill in profile details
2. **Login**: Visit `/user/login` with email and password
3. **Profile**: Access `/user/profile` to view/edit details
4. **Logout**: Click logout button or visit `/user/logout`

### For Developers

#### Get user profile image:
```php
$image_url = Beyond_Borders_Auth::get_user_profile_image( $user_id, 'medium' );
```

#### Get user metadata:
```php
$job_title = get_user_meta( $user_id, 'job_title_bb', true );
$company = get_user_meta( $user_id, 'company_bb', true );
$bio = get_user_meta( $user_id, 'bio_bb', true );
$profile_image_id = get_user_meta( $user_id, 'profile_image_id', true );
```

#### Update user metadata:
```php
update_user_meta( $user_id, 'job_title_bb', 'Your Job Title' );
update_user_meta( $user_id, 'company_bb', 'Company Name' );
update_user_meta( $user_id, 'bio_bb', 'Your bio here' );
```

## Future Enhancements

Potential additions to the system:
1. Email verification on registration
2. Social login integration (Google, Facebook, LinkedIn)
3. Password reset functionality
4. Two-factor authentication
5. Email notifications on profile updates
6. User profile visibility management
7. Profile follow/subscribe system
8. User dashboard with content statistics

## Troubleshooting

### URLs not working (`404 errors`)
- Rewrite rules may not be flushed
- Solution: Go to Settings > Permalinks in WordPress admin and save (or run `flush_rewrite_rules()`)

### Images not uploading
- Check server file upload limits (php.ini `upload_max_filesize`)
- Ensure `/wp-content/uploads` directory is writable
- Verify `WP_CONTENT_DIR` is properly configured

### Users can't login
- Verify email in `wp_users.user_email` table
- Check password was set correctly (password should be hashed)
- Ensure session handling is enabled in PHP

### Styling issues
- Clear browser cache
- Verify `auth-styles.css` is loading:
  - Check Network tab in browser DevTools
  - Verify file exists at `wp-content/plugins/beyond-borders/css/auth-styles.css`

## Version Info

- **Current Version**: 1.0.0
- **Requires WordPress**: 6.0+
- **Requires PHP**: 7.4+
- **Tested with**: WordPress 6.4+

## Support

For issues or feature requests, contact: info@thebeyondbordersreport.com
