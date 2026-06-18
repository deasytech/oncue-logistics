# Newsletter Feature Documentation

## Overview

The newsletter feature allows Super Admins to send periodic email newsletters to customers with special offers, updates, and other promotional content.

## Features

-   **Bulk Email Sending**: Send newsletters to multiple selected customers at once
-   **Rich Text Editor**: Create engaging content with formatting options
-   **Test Email Functionality**: Send test emails to yourself before sending to customers
-   **Call-to-Action Buttons**: Add optional CTA buttons with custom text and URLs
-   **Error Handling**: Comprehensive error handling with user notifications
-   **Email Queue Support**: Uses Laravel's queue system for better performance

## How to Use

### Step 1: Navigate to Customers

1. Log in to the Filament admin panel as a Super Admin
2. Go to **Entries** → **Customers** in the navigation menu

### Step 2: Select Customers

1. Select the customers you want to send the newsletter to by checking the checkboxes next to their names
2. You can select all customers using the "Select All" option

### Step 3: Send Newsletter

1. Click on the **"Send Newsletter"** bulk action button in the toolbar
2. A modal will appear with the newsletter creation form

### Step 4: Create Newsletter Content

Fill in the following fields:

-   **Newsletter Subject**: Enter an engaging subject line for your email
-   **Newsletter Content**: Use the rich text editor to create your newsletter content
    -   Available formatting options: bold, italic, underline, links, lists, headings, quotes
-   **Call to Action Text** (Optional): Add text for a CTA button (e.g., "Learn More", "Shop Now")
-   **Call to Action URL** (Optional): Add the URL the CTA button should link to
-   **Send Test Email First**: Toggle this ON to send a test email to yourself first

### Step 5: Send Test Email (Recommended)

1. Keep "Send Test Email First" enabled
2. Click "Send Newsletter"
3. Check your email to preview how the newsletter will look
4. If satisfied, repeat the process but disable the test email option

### Step 6: Send to Customers

1. Disable "Send Test Email First" toggle
2. Click "Send Newsletter"
3. The system will send the newsletter to all selected customers
4. You'll receive a notification with the results (success count, failure count)

## Email Template

The newsletter uses a professional HTML email template with:

-   Responsive design that works on mobile and desktop
-   Clean, modern styling with your brand colors
-   Proper email formatting and structure
-   Unsubscribe footer (you may want to add this functionality)

## Technical Details

### Files Created/Modified

-   `app/Mail/NewsletterMail.php` - Mailable class for newsletter emails
-   `resources/views/emails/newsletter.blade.php` - Email template
-   `app/Filament/Resources/CustomerResource/Actions/SendNewsletterAction.php` - Bulk action implementation
-   `app/Filament/Resources/CustomerResource.php` - Updated to include the bulk action

### Security Features

-   Only authenticated Super Admins can send newsletters
-   Email validation for customer email addresses
-   Proper error handling and logging
-   Rate limiting through Laravel's queue system

### Performance Considerations

-   Uses Laravel's queue system (ShouldQueue interface)
-   Processes emails in batches
-   Includes proper error handling and retry logic
-   Logs failed attempts for debugging

## Best Practices

1. **Always send test emails** before sending to customers
2. **Keep content concise** and engaging
3. **Use clear CTAs** to drive action
4. **Monitor email performance** through logs
5. **Respect customer preferences** - consider adding unsubscribe functionality
6. **Test on different devices** to ensure responsive design

## Troubleshooting

### Common Issues

1. **Emails not sending**: Check Laravel queue workers and mail configuration
2. **Test email not received**: Verify your email address in user profile
3. **Formatting issues**: Test with different email clients
4. **High failure rate**: Check customer email addresses for validity

### Logs

Failed email attempts are logged to Laravel's log files with details about the error.

## Future Enhancements

Consider adding:

-   Newsletter templates/predefined layouts
-   Scheduling newsletters for future delivery
-   Email analytics and tracking
-   Customer preference management
-   Unsubscribe functionality
-   A/B testing capabilities
