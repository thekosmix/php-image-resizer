<VirtualHost *:80>
    ServerAdmin admin@domain.com
    ServerName domain.com
    ServerAlias cdn.domain.com
    DocumentRoot /var/www/cdn
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

<Directory "/var/www/cdn/">

    Options FollowSymLinks
    AllowOverride None

RewriteEngine On
RewriteBase /

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d 

# url: hight/width/imageLocation => h=$1, w=$2, i=$3
RewriteRule ^image/([0-9]+)/([0-9]+)/(.*)$ /image.php?i=$3&w=$2&h=$1 [QSA]


</Directory>

<ifmodule mod_deflate.c>
	AddOutputFilterByType DEFLATE text/text text/html text/plain text/xml text/css application/x-javascript application/javascript text/javascript image/jpeg image/png image/gif font/ttf font/otf image/svg+xml
</ifmodule>

</VirtualHost>

