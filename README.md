# BOOKSHELF – Digital Library Management

A library website made with HTML, CSS, JavaScript, PHP and MySQL.

## How to run it (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) (with PHP 8 or newer) and start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Copy this whole folder into `C:\xampp\htdocs\` so you have `C:\xampp\htdocs\Digital_Library_Management\`.
3. Open <http://localhost/phpmyadmin>, click **New**, create a database named `digital_library`,
   then (with that database selected) click **Import**, choose `database/library.sql` and press **Go**.
   This creates the tables and adds 10 books.
4. Open <http://localhost/Digital_Library_Management/registration.php> and create an account.
5. On the OTP page the code is shown in a green box (XAMPP cannot send email). Type it in, then log in.

## Demo accounts

`database/library.sql` also creates two ready-to-use accounts (already verified, no OTP needed):

| Role | Email | University ID | Password |
|---|---|---|---|
| Librarian | `librarian@bookshelf.local` | `LIB-0001` | `Librarian@123` |
| Student | `student@bookshelf.local` | `STU-0001` | `Student@123` |

These are for testing only. Change the passwords (or delete the accounts) before putting the site online.

Librarians get a **Librarian** link in the sidebar with:

- **Dashboard**: book, member and loan numbers, overdue books and the latest loans
- **Books**: add, edit (with cover upload) and delete books, and set how many copies there are
- **Members**: verify accounts, make someone a librarian (or a normal member again) and remove accounts
- **Loans**: see borrowed / overdue / returned books, mark a book returned, or give 7 more days

If your database was made with an older `library.sql` (before librarians existed), import
`database/librarian.sql` once instead. It adds the `role` column and the demo accounts and keeps all your data.

The database settings are in the `.env` file (`connection.php` reads them). If your MySQL user or
password is not `root` with an empty password, change them there. If MySQL runs on a port other than
3306 (the XAMPP Control Panel shows it next to MySQL), set `DB_PORT` to that number. If `.env` is missing, copy `.env.example` to `.env`.

## Files

| File | What it does |
| --- | --- |
| `.env` | Database host, user, password, name and port (not uploaded to GitHub) |
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
| `check_librarian.php` | Lets only librarians open the librarian pages |
| `librarian_nav.php` | Tabs and messages at the top of the librarian pages |
| `librarian.php` | Librarian dashboard |
| `librarian_books.php` / `librarian_bookform.php` | List, add, edit and delete books |
| `librarian_members.php` | Verify, promote and remove members |
| `librarian_loans.php` | Mark books returned and extend due dates |
| `database/library.sql` | Creates the tables and adds the books and the librarian account |
| `database/librarian.sql` | Adds librarians to a database made with an older `library.sql` |

## Database tables

- `users` – accounts (password is stored with `password_hash`; `role` is `member` or `librarian`)
- `books` – the library's books
- `borrows` – who borrowed which book, when it is due, and when it was returned
- `favorites` – books each user marked with the heart
