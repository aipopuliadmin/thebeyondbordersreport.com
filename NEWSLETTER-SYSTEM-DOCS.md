# Newsletter System Documentation

## Overview
The Beyond Borders plugin now includes a complete newsletter subscription system integrated with the footer signup form.

## Features

### Frontend Features
- **Newsletter Signup Form**: Located in the footer (when enabled in settings)
- **AJAX Submission**: No page reload required
- **Real-time Feedback**: Success/error messages displayed immediately
- **Email Validation**: Built-in email format validation
- **Duplicate Prevention**: Prevents same email from subscribing twice

### Admin Features
- **Subscriber Management**: View all subscribers in WordPress admin
- **Statistics Dashboard**: See total subscribers at a glance
- **Export to CSV**: Download subscriber list for email marketing platforms
- **Delete Subscribers**: Remove individual subscribers
- **Pagination**: Easy navigation through large subscriber lists
- **Welcome Emails**: Automatic welcome email sent to new subscribers

## Database Structure

Table: `wp_newsletter_subscribers`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint(20) unsigned | Auto-increment primary key |
| email | varchar(100) | Subscriber email (unique) |
| name | varchar(100) | Subscriber name (optional) |
| status | varchar(20) | Subscription status (active/inactive) |
| subscribed_date | datetime | Timestamp of subscription |
| ip_address | varchar(45) | IP address of subscriber |
| user_agent | text | Browser user agent string |

## How It Works

### Frontend Subscription Flow
1. User enters email in footer newsletter form
2. JavaScript validates email format
3. AJAX request sent to WordPress backend
4. Server validates and stores email in database
5. Welcome email sent to subscriber
6. Success message displayed to user

### Admin Management
1. Navigate to **Beyond Borders > Newsletter** in WordPress admin
2. View subscriber statistics (total count, recent subscriptions)
3. Browse subscriber list with pagination
4. Export subscribers to CSV for use in email marketing tools
5. Delete individual subscribers as needed

## Files Modified/Created

### Plugin Files
- `includes/class-beyond-borders-newsletter.php` - Newsletter handler class
- `admin/partials/beyond-borders-newsletter.php` - Admin page interface
- `admin/class-beyond-borders-admin.php` - Added newsletter submenu
- `includes/class-beyond-borders-activator.php` - Database table creation
- `includes/class-beyond-borders.php` - Newsletter AJAX hooks registration

### Theme Files
- `assets/js/newsletter.js` - AJAX form submission handler
- `template-parts/footer/footer-default.php` - Updated form with AJAX support
- `functions.php` - Enqueued newsletter JavaScript

## Usage Instructions

### For Site Visitors
1. Scroll to footer
2. Enter email address in newsletter signup form
3. Click "Subscribe" button
4. Wait for confirmation message
5. Check email for welcome message

### For Administrators

#### View Subscribers
1. Log into WordPress admin
2. Go to **Beyond Borders > Newsletter**
3. View total subscriber count
4. Browse subscriber list

#### Export Subscribers
1. Go to **Beyond Borders > Newsletter**
2. Click **Export to CSV** button
3. Save the CSV file
4. Import into your email marketing platform (Mailchimp, SendGrid, etc.)

#### Delete a Subscriber
1. Go to **Beyond Borders > Newsletter**
2. Find the subscriber in the list
3. Click **Delete** link
4. Confirm deletion

## Email Configuration

The system uses WordPress default email settings. For reliable email delivery:

1. Use the Beyond Borders SMTP settings (if configured)
2. Or install an SMTP plugin like WP Mail SMTP
3. Configure with your email provider credentials

## CSV Export Format

The exported CSV includes:
- Email address
- Subscription status
- Subscribe date

This format is compatible with most email marketing platforms.

## Security Features

- **Nonce Verification**: All AJAX requests verified
- **Email Sanitization**: Email addresses sanitized before storage
- **SQL Injection Prevention**: Uses WordPress wpdb prepared statements
- **XSS Protection**: All output escaped
- **IP Tracking**: Records IP for spam prevention
- **Unique Constraint**: Prevents duplicate emails in database

## Future Enhancements (Potential)

- Unsubscribe link in emails
- Subscriber name collection
- Email campaign sending
- Subscription preferences
- Double opt-in confirmation
- Subscriber tagging/segmentation
- Integration with popular email marketing services

## Troubleshooting

### Subscribers Not Receiving Emails
- Check WordPress email settings
- Configure SMTP settings in Beyond Borders > SMTP
- Test email delivery with a plugin like Check Email

### Form Not Submitting
- Check browser console for JavaScript errors
- Verify jQuery is loaded
- Clear browser cache
- Check if newsletter.js is enqueued

### Database Table Missing
- Deactivate and reactivate the Beyond Borders plugin
- Check database for `wp_newsletter_subscribers` table
- Contact hosting provider if table creation fails

## Support

For issues or questions:
1. Check this documentation
2. Review WordPress debug logs
3. Contact Beyond Borders Report support team
