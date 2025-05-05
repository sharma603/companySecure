# Deploying to Hostinger

This guide provides step-by-step instructions for deploying the CompanySecure application to Hostinger.

## Pre-deployment Preparation

1. **Configure environment file**
   - Rename `.env.example` to `.env`
   - Update the following values:
     ```
     APP_URL=https://scriptqube.com/synergy
     
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database_name
     DB_USERNAME=your_database_username
     DB_PASSWORD=your_database_password
     ```

2. **Build assets (if using npm)**
   ```
   npm run build
   ```

3. **Optimize Composer autoloader**
   ```
   composer install --optimize-autoloader --no-dev
   ```

## Uploading Files

1. **Upload files to the server**
   - Use FTP or the Hostinger File Manager to upload all files to:
     `/home/u569470620/domains/scriptqube.com/public_html/synergy/`
   
   - Make sure to include the `vendor` directory, or be prepared to run `composer install` on the server

2. **Directory structure**
   ```
   public_html/
     synergy/
       app/
       bootstrap/
       config/
       database/
       public/
       resources/
       routes/
       storage/
       vendor/
       .env
       .htaccess
   ```

## Server Configuration

1. **Set proper permissions**
   ```
   chmod -R 755 /home/u569470620/domains/scriptqube.com/public_html/synergy
   chmod -R 777 /home/u569470620/domains/scriptqube.com/public_html/synergy/storage
   chmod -R 777 /home/u569470620/domains/scriptqube.com/public_html/synergy/bootstrap/cache
   ```

2. **Create symbolic link for storage**
   ```
   cd /home/u569470620/domains/scriptqube.com/public_html/synergy
   php artisan storage:link
   ```

3. **Clear caches**
   ```
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

## Troubleshooting

### Vendor Autoload Issue

If you see this error:
```
Warning: require(/home/u569470620/domains/scriptqube.com/public_html/synergy/public/../vendor/autoload.php): Failed to open stream
```

Try these solutions:

1. Make sure the vendor directory exists and has all dependencies:
   ```
   cd /home/u569470620/domains/scriptqube.com/public_html/synergy
   composer install
   ```

2. If you don't have SSH access, the modified `public/index.php` file should attempt to locate the vendor directory in multiple possible locations.

### Other Common Issues

1. **500 Internal Server Error**
   - Check the Laravel log at `storage/logs/laravel.log`
   - Ensure proper permissions on storage and bootstrap/cache directories
   - Verify .env file exists and has correct settings

2. **404 Not Found**
   - Check that .htaccess files exist in both the project root and public directory
   - Verify mod_rewrite is enabled in your hosting

3. **Redirect Loop Issues**
   - Clear browser cookies and cache
   - Try incognito/private browsing
   - Run `php artisan config:clear` and `php artisan cache:clear`

## Post-deployment Steps

1. **Run migrations and seeders (if needed)**
   ```
   php artisan migrate --force
   php artisan db:seed --force
   ```

2. **Generate application key (if not already done)**
   ```
   php artisan key:generate
   ```

3. **Test the application**
   Visit https://scriptqube.com/synergy to verify everything is working

## Support

If you encounter issues not covered in this guide, please contact the development team or refer to Laravel's official documentation. 