# Beyond Borders Authentication - Quick Start Guide

## 🚀 Setup Instructions

### 1. Plugin Already Activated
The authentication system is automatically integrated into the Beyond Borders plugin. No additional setup needed!

### 2. Flush Rewrite Rules (Important!)
After activation, you must flush WordPress rewrite rules:

**Option A: Via WordPress Admin**
1. Log in to WordPress dashboard
2. Go to Settings > Permalinks
3. Click "Save Changes" (no changes needed, just save)

**Option B: Via WP-CLI**
```bash
wp rewrite flush
```

**Option C: Via PHP**
```php
flush_rewrite_rules();
```

## 📍 Access Points

### For Your Users
- **Register**: `https://yoursite.com/user/register`
- **Login**: `https://yoursite.com/user/login`
- **Profile**: `https://yoursite.com/user/profile` (after login)
- **Logout**: `https://yoursite.com/user/logout`

### For Administrators
- **Manage Users**: WordPress Dashboard > Users > All Users
- **User Management Roles/Capabilities**: Dashboard > Beyond Borders > User Management

## 📋 Registration Form Fields

**Required Fields (marked with \*)**
- First Name\*
- Last Name\*
- Email\*
- Password\* (minimum 6 characters)
- Confirm Password\*

**Optional Fields**
- Job Title
- Company
- Bio (tell us about yourself)
- Profile Image (JPEG, PNG, or GIF, max 5MB)

## 🔐 Login Process

1. User visits `/user/login`
2. Enters email and password
3. System verifies credentials against WordPress database
4. On success: Redirects to `/user/profile`
5. On failure: Shows error message, user can retry

## 👤 Profile Management

On `/user/profile` (login required), users can:
- View current profile information
- Update first and last name
- Update job title and company
- Write or edit bio
- Upload/change profile image
- View account stats (member since, posts)
- See profile picture

## 📁 File Locations

All auth files are located in `/wp-content/plugins/beyond-borders/`:

```
├── includes/class-beyond-borders-auth.php      ← Main auth logic
├── templates/
│   ├── auth-login.php                          ← Login page
│   ├── auth-register.php                       ← Register page
│   └── auth-profile.php                        ← Profile page
├── css/auth-styles.css                         ← Styling
├── js/auth.js                                  ← Client validation
└── AUTH-SYSTEM-GUIDE.md                        ← Full documentation
```

## 🎨 Design Features

- **Modern Design**: Matches Beyond Borders brand colors and typography
- **Responsive**: Works on desktop, tablet, and mobile
- **Accessible**: Proper form labels, ARIA attributes, keyboard navigation
- **Animated**: Smooth transitions and hover effects
- **User Feedback**: Real-time validation, success/error messages

## 🔗 Data Integration

All user data is stored in WordPress database:
- Users stored in `wp_users` table
- Profile metadata in `wp_usermeta` table
- Profile images stored as WordPress attachments
- Full integration with WordPress user system

### User Metadata Keys
```
job_title_bb        → User's job title
company_bb          → User's company
bio_bb              → User's biography
author_type         → User classification (default: guest_author)
profile_image_id    → Attachment ID of profile image
```

## 🐛 Common Issues

### "Page not found" errors
→ Go to Settings > Permalinks and save (flushes rewrite rules)

### Profile image won't upload
→ Check server file upload limits (usually 25MB in php.ini)

### Can't log in
→ Verify email address matches exactly what was registered
→ Check caps lock on password

## ✨ Next Steps

1. **Test Registration**: Visit `/user/register` and create a test account
2. **Test Login**: Log out and test `/user/login` with test account
3. **Manage Users**: Go to User Management to assign roles/capabilities
4. **Customize**: Edit templates or CSS in the plugin folder as needed

## 📞 Support

For questions or issues:
- Email: info@thebeyondbordersreport.com
- Check AUTH-SYSTEM-GUIDE.md for detailed documentation
- Check WordPress debug logs if issues persist

## 💡 Pro Tips

1. **User Registration Emails**: Currently no auto-send when user registers. You can add email notifications using WordPress email hooks.

2. **Profile Image Sizes**: System supports multiple image sizes. Use `Beyond_Borders_Auth::get_user_profile_image($user_id, 'thumbnail')` with sizes: thumbnail, medium, large

3. **User Query**: Get users by author type:
   ```php
   $users = get_users( array(
       'meta_key' => 'author_type',
       'meta_value' => 'guest_author'
   ) );
   ```

4. **Customize Templates**: Edit `/templates/auth-*` files to modify forms and styling

5. **Add Fields**: To add new fields, update:
   - HTML in template file
   - Validation in `auth.js`
   - Processing in `process_*_form()` method in auth class
   - Metadata storage/retrieval

---

**System Version**: 1.0.0  
**Last Updated**: March 2026  
**Requires**: WordPress 6.0+, PHP 7.4+
