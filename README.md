# Digital Library Management (BOOKSHELF / BookWise)

A library website made with HTML, CSS, JavaScript, PHP and MySQL.

## How to run it (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) (with PHP 8 or newer) and start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Copy this whole folder into `C:\xampp\htdocs\` so you have `C:\xampp\htdocs\Digital_Library_Management\`.
3. Open <http://localhost/phpmyadmin>, click **New**, create a database named `digital_library`,
   then (with that database selected) click **Import**, choose `database/library.sql` and press **Go**.
   This creates the tables and adds 10 books.
4. Open <http://localhost/Digital_Library_Management/registration.php> and create an account.
5. On the OTP page the code is shown in a green box (XAMPP cannot send email). Type it in, then log in.

The database settings are in the `.env` file (`connection.php` reads them). If your MySQL user or
password is not `root` with an empty password, change them there. If `.env` is missing, copy `.env.example` to `.env`.

## How to put it online (InfinityFree, free)

1. Sign up at <https://www.infinityfree.com>, click **Create Account** and pick a free subdomain
   (for example `mylibrary.infinityfreeapp.com`).
2. In the account's **Control Panel**, open **MySQL Databases** and create a database (for example `library`).
   Write down the **MySQL host name** (like `sql123.infinityfree.com`), the **database name** (like `if0_12345678_library`),
   the **user name** (like `if0_12345678`) and the **password** (shown on the account page in the client area).
3. Click **phpMyAdmin** next to the database, then **Import**, choose `database/library.sql` and press **Go**.
4. Put those four values in the `.env` file instead of `localhost` / `root`.
5. Open **File Manager**, go into the `htdocs` folder, delete the `index2.html` that is already there,
   and upload all the project files and folders (not the `.git` folder) into `htdocs`.
   Make sure the hidden files `.env` and `.htaccess` are uploaded too.
6. Visit `https://your-subdomain.infinityfreeapp.com/registration.php`. A new subdomain can take a few hours to start working.

## Files

| File | What it does |
| --- | --- |
| `.env` | Database host, user, password and name (not uploaded to GitHub) |
| `connection.php` | Reads `.env` and connects to the MySQL database (`$conn`) |
| `check_login.php` | Sends visitors to the login page and loads `$user` |
| `header.php` | Sidebar and top bar used by every page |
| `registration.php` | Create an account (with photo upload) |
| `otp.php` / `verify.php` | Enter the 6-digit code to verify the account |
| `login.php` / `logout.php` | Log in and log out |
| `index.php` | Home: new arrivals, popular and recommended books |
| `allbooks.php` | Every book in the library |
| `search.php` | Search by all / title / author / subject / text |
| `bookdetails.php` | One book's details and the Borrow button |
| `borrow.php` / `return.php` | Borrow a book (14 days, max 5) and return it |
| `favorite.php` | Add / remove a book from favorites (the heart) |
| `yourshelf.php` | Your borrowed books, favorites, e-books, audio books |
| `profile.php` | Edit your name, phone, bio and profile picture |
| `aihelper.php` | AI Buddy: answers from the database (recommend, genres, summarize, search) |
| `database/library.sql` | Creates the tables and adds the books |

## Database tables

- `users` – accounts (password is stored with `password_hash`)
- `books` – the library's books
- `borrows` – who borrowed which book, when it is due, and when it was returned
- `favorites` – books each user marked with the heart
