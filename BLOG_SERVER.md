 # nadaj właściciela pod użytkownika PHP-FPM/Apache (najczęściej www-data)
 sudo chown -R www-data:www-data storage bootstrap/cache database
 
 # prawa zapisu dla właściciela i grupy
 sudo chmod -R ug+rwX storage bootstrap/cache database
 
 # upewnij się, że plik bazy istnieje i jest zapisywalny
 sudo touch database/database.sqlite
 sudo chown www-data:www-data database/database.sqlite
 sudo chmod ug+rw database/database.sqlite
 
 # wyczyść cache Laravel
 sudo -u www-data php artisan optimize:clear

sudo -u www-data php artisan make:filament-user
