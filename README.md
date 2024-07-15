Clone the repo and cd into it
In your terminal composer install
Rename or copy .env.example file to .env
php artisan key:generate
Set your database credentials in your .env file
Set your Braintree credentials in your .env file if you want to use PayPal
Import db file(database/e-shop.sql) into your database (mysql,sql)
npm install
npm run watch
run command[laravel file manager]:- php artisan storage:link
Edit .env file :- remove APP_URL
php artisan serve or use virtual host
Visit localhost:8000 in your browse
