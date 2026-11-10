-- BookWise / BOOKSHELF digital library
-- First create/select a database in phpMyAdmin, then use the Import tab with this file.

DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS borrows;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS users;

-- People who use the library
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  university_id VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone         VARCHAR(20)  NOT NULL DEFAULT '',
  bio           VARCHAR(500) NOT NULL DEFAULT '',
  photo         VARCHAR(255) NOT NULL DEFAULT '',
  is_verified   TINYINT      NOT NULL DEFAULT 0,  -- 1 after the OTP is entered
  otp_code      VARCHAR(6)   DEFAULT NULL,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Books in the library (cover = file name inside Assets/Images)
CREATE TABLE books (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  title            VARCHAR(200) NOT NULL,
  author           VARCHAR(150) NOT NULL,
  edition          VARCHAR(50)  NOT NULL DEFAULT '',
  published_year   INT          NOT NULL,
  cover            VARCHAR(255) NOT NULL DEFAULT 'img.png',
  category         VARCHAR(80)  NOT NULL,
  sub_category     VARCHAR(80)  NOT NULL DEFAULT '',
  type             VARCHAR(20)  NOT NULL DEFAULT 'book',  -- 'book' or 'article'
  rating           DECIMAL(2,1) NOT NULL DEFAULT 0.0,
  reviews_count    INT          NOT NULL DEFAULT 0,
  has_hardcopy     TINYINT      NOT NULL DEFAULT 1,
  has_ebook        TINYINT      NOT NULL DEFAULT 0,
  has_audiobook    TINYINT      NOT NULL DEFAULT 0,
  location         VARCHAR(20)  NOT NULL DEFAULT '',
  total_copies     INT          NOT NULL DEFAULT 1,
  available_copies INT          NOT NULL DEFAULT 1,
  description      TEXT         NOT NULL,
  author_bio       TEXT         NOT NULL,
  read_url         VARCHAR(500) NOT NULL DEFAULT '',
  created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Each time a user borrows a book
CREATE TABLE borrows (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT         NOT NULL,
  book_id     INT         NOT NULL,
  borrowed_at DATETIME    NOT NULL,
  due_date    DATE        NOT NULL,
  returned_at DATETIME    DEFAULT NULL,
  status      VARCHAR(20) NOT NULL DEFAULT 'borrowed',  -- 'borrowed' or 'returned'
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Books a user marked with the heart
CREATE TABLE favorites (
  user_id    INT      NOT NULL,
  book_id    INT      NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, book_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

INSERT INTO books
  (title, author, edition, published_year, cover, category, sub_category, rating, reviews_count,
   has_hardcopy, has_ebook, has_audiobook, location, total_copies, available_copies,
   description, author_bio, read_url, created_at)
VALUES
('Don''t Make Me Think', 'Steve Krug', 'Second Edition', 2000, 'img.png', 'Computer Science', 'UX Design', 4.5, 200,
 1, 1, 1, 'CS A-15', 5, 5,
 '"Don''t Make Me Think" is a classic book on web usability that emphasizes the importance of creating user-friendly websites. The book provides practical advice and principles for designing intuitive interfaces that require minimal effort from users to navigate and understand. Steve Krug uses humor and real-world examples to illustrate key concepts, making it an engaging read for both beginners and experienced web designers. The second edition includes updated content to reflect changes in web design trends and technologies.',
 'Steve Krug is a usability consultant and the author of the best-selling book "Don''t Make Me Think," which has become a classic in the field of web usability. His practical, no-nonsense approach to usability has made him a sought-after speaker and educator in the industry.',
 'https://drive.google.com/file/d/0BzqRwa81UHwKcXVwWGI1RDRiak0/edit?resourcekey=0-v5RaUQx5WyVJu6KFAIO3pg',
 '2025-10-01 09:00:00'),
('Origin', 'Dan Brown', 'First Edition', 2017, 'img-1.png', 'Thriller', 'Mystery', 4.7, 180,
 1, 1, 1, 'CS K-12', 4, 4,
 'Symbologist Robert Langdon is drawn into a race across Spain after a futurist''s presentation about the origins and destiny of humanity is violently interrupted. A fast-paced thriller that mixes science, religion, art and code-breaking.',
 'Dan Brown is an American author of thriller novels, best known for the Robert Langdon series.',
 '', '2025-10-02 09:00:00'),
('The Maidens', 'Alex Michaelides', 'First Edition', 2021, 'img-2.png', 'Thriller', 'Mystery', 4.3, 120,
 1, 1, 0, 'CS E-11', 3, 3,
 'A psychological thriller set in Cambridge, where a group therapist becomes convinced that a charismatic professor is behind the murder of a student from a secretive society known as The Maidens.',
 'Alex Michaelides is a British-Cypriot author and screenwriter, known for psychological thrillers.',
 '', '2025-10-03 09:00:00'),
('Amazing Facts', 'Nayem Islam', '', 2015, 'img-3.png', 'Non-Fiction', 'Trivia', 3.9, 45,
 1, 0, 1, 'CS F-04', 2, 2,
 'A collection of surprising facts about science, nature, history and everyday life, written for curious readers of every age.',
 'Nayem Islam writes popular non-fiction and general-knowledge books.',
 '', '2025-10-04 09:00:00'),
('Math', 'Jannat', '', 2019, 'img-4.png', 'Education', 'Mathematics', 4.4, 60,
 1, 1, 1, 'CS G-09', 6, 6,
 'A clear, example-driven guide to core mathematics topics, with worked problems and practice exercises for university students.',
 'Jannat is an educator who writes study guides for mathematics students.',
 '', '2025-10-05 09:00:00'),
('Don''t Turn', 'Adnan', '', 2018, 'img-5.png', 'Horror', 'Suspense', 3.8, 30,
 1, 0, 0, 'CS H-06', 2, 2,
 'A tense horror story about a group of friends who discover that some warnings exist for a reason.',
 'Adnan writes horror and suspense fiction.',
 '', '2025-10-06 09:00:00'),
('History', 'Shinchan', '', 2012, 'img-6.png', 'History', 'World', 4.0, 52,
 1, 0, 1, 'CS I-01', 3, 3,
 'A readable overview of world history, from early civilizations to the modern era, focused on the events that shaped today''s world.',
 'Shinchan writes accessible history books for general readers.',
 '', '2025-10-07 09:00:00'),
('Stepen King', 'Mahidul', '', 2016, 'img-7.png', 'Fiction', 'Collection', 4.6, 95,
 1, 1, 0, 'CS J-08', 3, 3,
 'A collection of short stories exploring fear, memory and the strange corners of ordinary life.',
 'Mahidul is a fiction writer and short-story collector.',
 '', '2025-10-08 09:00:00'),
('Islamic', 'Nayem Islam', '', 2020, 'img-8.png', 'Religion', 'Islam', 4.0, 70,
 1, 1, 0, 'CS C-07', 4, 4,
 'An introduction to Islamic history, beliefs and practices, written for students and general readers.',
 'Nayem Islam writes popular non-fiction and general-knowledge books.',
 '', '2025-10-09 09:00:00'),
('Tetul Bone Josna', 'Humayun Ahmed', '', 1998, 'img-9.png', 'Fiction', 'Novel', 4.2, 140,
 1, 0, 1, 'CS B-03', 3, 3,
 'A Bangla novel by Humayun Ahmed, one of the most widely read Bangladeshi writers.',
 'Humayun Ahmed was a Bangladeshi novelist, dramatist and filmmaker, one of the most popular writers in Bangla literature.',
 '', '2025-10-10 09:00:00');
