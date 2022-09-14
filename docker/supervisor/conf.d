[program:horizon]
process_name=%(program_name)s
command=php artisan /var/www/ horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/storage/logs/horizon.log
stopwaitsecs=3600
