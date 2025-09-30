# GitHub Webhook Setup for Hostinger

## Step 1: Upload Webhook File to Your Hostinger Server

1. Upload either `webhook.php` or `webhook-simple.php` to your public_html directory on Hostinger
2. Make sure the file is accessible at: `https://yourdomain.com/webhook.php`

## Step 2: Configure the Webhook File

Edit the webhook file and update these settings:

```php
$secret = 'your_strong_secret_here'; // Create a strong secret
$branch = 'main'; // Change to 'master' if needed
```

## Step 3: Set Up GitHub Webhook

1. Go to your GitHub repository
2. Click **Settings** → **Webhooks** → **Add webhook**
3. Configure:
   - **Payload URL**: `https://yourdomain.com/webhook.php`
   - **Content type**: `application/json`
   - **Secret**: Use the same secret from your webhook file
   - **Events**: Select "Just the push event"
   - **Active**: ✅ Checked

## Step 4: Test the Webhook

1. Make a small change to your repository and push it
2. Check the webhook deliveries in GitHub (Settings → Webhooks → Recent Deliveries)
3. Check the log file at `https://yourdomain.com/webhook.log` for debugging

## Common Issues and Solutions

### Issue 1: Webhook Returns 500 Error
- Check if shell commands are allowed on your hosting
- Try the `webhook-simple.php` version
- Check the webhook.log file for error details

### Issue 2: Git Pull Fails
- Ensure your Hostinger account has Git access
- Check if the repository path is correct
- Verify SSH keys are set up properly

### Issue 3: Permission Denied
- Check file permissions (should be 644 for PHP files)
- Ensure the web server can write to the log file

### Issue 4: Laravel Cache Issues
- The webhook clears cache automatically
- If issues persist, manually clear cache via cPanel or SSH

## Hostinger-Specific Notes

1. **Shared Hosting Limitations**:
   - Some shell commands might be restricted
   - Use the simple webhook version if the full version fails

2. **Git Setup**:
   - Make sure Git is properly configured in your Hostinger account
   - You might need to set up SSH keys for private repositories

3. **File Permissions**:
   - Webhook file: 644
   - Log file: 666 (will be created automatically)

## Testing Commands

Test your webhook manually:
```bash
curl -X POST https://yourdomain.com/webhook.php \
  -H "Content-Type: application/json" \
  -d '{"ref":"refs/heads/main"}'
```

## Security Notes

1. Always use a strong secret
2. Keep your webhook URL private
3. Monitor the log file for suspicious activity
4. Consider IP whitelisting if possible

## Troubleshooting

If the webhook still doesn't work:

1. Check webhook.log for errors
2. Verify GitHub webhook deliveries show success (200 response)
3. Test git pull manually via SSH/cPanel terminal
4. Contact Hostinger support about Git/shell command restrictions