# Deployment Guide for CompanySecure on Hostinger

## Prerequisites
- A Hostinger account with PHP 8.1+ hosting
- Access to Hostinger's cPanel or equivalent
- MySQL database access

## Step 1: Prepare Your Environment File
Create a `.env` file in your project root with the following settings, customized for your Hostinger environment:

```
APP_NAME=CompanySecure
APP_ENV=production
APP_KEY=your_app_key_here
APP_DEBUG=false
APP_URL=https://your-hostinger-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your-mail@your-hostinger-domain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-mail@your-hostinger-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Step 2: Upload Your Files

### Method 1: cPanel File Manager
1. Log in to your Hostinger cPanel
2. Open File Manager
3. Navigate to your public_html directory (or subdirectory if you want to install in one)
4. Upload all project files maintaining the directory structure

### Method 2: FTP Upload
1. Use an FTP client like FileZilla
2. Connect to your hosting using the FTP credentials provided by Hostinger
3. Upload all project files to your public_html directory

## Step 3: Configure the Root Directory
Hostinger might point to the public_html directory as the document root. You have two options:

### Option 1: Use the Existing .htaccess (Recommended)
Ensure the root .htaccess file contains:
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Option 2: Move Public Files
If .htaccess redirection doesn't work:
1. Move all files from the `public` directory to the web root directory (like public_html)
2. Edit `index.php` to update paths by changing:
   - `__DIR__.'/../vendor/autoload.php'` to `__DIR__.'/vendor/autoload.php'`
   - `__DIR__.'/../bootstrap/app.php'` to `__DIR__.'/bootstrap/app.php'`

## Step 4: Set Up the Database
1. Create a new MySQL database through Hostinger's cPanel
2. Create a database user and assign it to the database
3. Update your `.env` file with the database credentials
4. Run migrations via SSH or PHPMyAdmin:
   ```
   php artisan migrate
   ```
   Or import the SQL dump file using PHPMyAdmin

## Step 5: Set Folder Permissions
Set proper permissions for Laravel to work:
```
chmod -R 755 your_project_directory
chmod -R 777 your_project_directory/storage
chmod -R 777 your_project_directory/bootstrap/cache
```

## Step 6: Generate Application Key
If not already set in your .env file:
```
php artisan key:generate --force
```

## Step 7: Cache Configuration for Performance
Run these commands via SSH or through a PHP script:
```
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 8: Set Up Symbolic Link for Storage
Link your storage directory to the public directory:
```
php artisan storage:link
```

## Troubleshooting

### Problem: Redirect Loops (ERR_TOO_MANY_REDIRECTS)
This is a common issue when deploying Laravel applications, especially with authentication.

**Common causes and solutions:**

1. **Authentication Middleware Conflict**
   - Check `app/Http/Middleware/Authenticate.php` and ensure it's using `route('login')` instead of hardcoded paths
   - Check `app/Http/Middleware/RedirectIfAuthenticated.php` to ensure it has proper checks to prevent loops
   - Clear cookies and sessions in your browser

2. **Route Redirect Chains**
   - Ensure your routes don't create circular redirects (A → B → A)
   - Use named routes (`route('name')`) instead of direct URL paths
   - Avoid using trailing slashes in redirects

3. **HTTPS Redirect Issues**
   - If you're enforcing HTTPS but using HTTP locally, this can cause loops
   - Modify your `.htaccess` file to only enforce HTTPS in production
   
4. **Session/Cookie Issues**
   - Try clearing your browser cookies
   - Check if `APP_URL` matches the actual domain you're using
   - Ensure session driver is working properly

If all else fails, add debugging output to your middleware files and routes to identify where the redirect loop is occurring.

### Problem: 500 Server Error
- Check `.env` file configuration
- Ensure proper folder permissions
- Look at the Laravel log file in `storage/logs/laravel.log`

### Problem: Page Not Found (404)
- Check if mod_rewrite is enabled
- Ensure .htaccess files are properly uploaded and not being ignored
- Verify your routes are working (try to access `/index.php/route_name` directly)

### Problem: Database Connection Error
- Verify database credentials in .env file
- Check if your database server is running
- Ensure your IP is allowed to connect to the database

### Problem: Images/Assets Not Loading
- Make sure you're using the `asset()` function for all assets
- Check if your APP_URL is set correctly in the .env file
- Try running `php artisan storage:link` if using the storage for public files

## Additional Security Tips
1. Keep `APP_DEBUG=false` in production
2. Remove or secure any development or database backup files
3. Set up HTTPS/SSL for your domain through Hostinger
4. Consider using Hostinger's cron job system for scheduled tasks
5. Set up a firewall and bot protection through Hostinger's control panel

For further assistance, contact Hostinger support or refer to Laravel's official deployment documentation. 