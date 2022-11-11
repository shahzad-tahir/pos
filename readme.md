## POS

POS is a POS application developed by Shahzad Tahir. The POS system with everything you need to sell in person, backed by everything you need to sell online.

## Installation

Clone the repository-
```
git clone https://github.com/shahzad-tahir/pos.git
```

Then cd into the folder with this command-
```
cd pos
```

Then do a composer install
```
composer install
```

Then create a environment file using this command-
```
cp .env.example .env
```

Then edit `.env` file with appropriate credential for your database server. Just edit these two parameter(`DB_USERNAME`, `DB_PASSWORD`).

Then create a database named `pos_db` and then do a database migration using this command-
```
php artisan migrate
```

Generate application key, which will be used for password hashing, session and cookie encryption etc.
```
php artisan key:generate
```

## Run server

Run server using this command-
```
php artisan serve
```

Then go to `http://localhost:8000` from your browser and see the app.



