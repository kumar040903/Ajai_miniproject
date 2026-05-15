CREATE DATABASE IF NOT EXISTS dkap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dkap;

DROP TABLE IF EXISTS reading_progress, reviews, bookmarks, resources, categories, users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  streak_days INT DEFAULT 0,
  last_active DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) UNIQUE NOT NULL,
  icon VARCHAR(20) DEFAULT '📚'
);

CREATE TABLE resources (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  author VARCHAR(120) DEFAULT '',
  description TEXT,
  type ENUM('book','article','video','course','podcast') DEFAULT 'article',
  category_id INT,
  cover_url VARCHAR(500) DEFAULT '',
  content_url VARCHAR(500) DEFAULT '',
  body LONGTEXT,
  tags VARCHAR(255) DEFAULT '',
  views INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  FULLTEXT KEY ft_search (title, author, description, body, tags)
);

CREATE TABLE bookmarks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  resource_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_bm (user_id, resource_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  resource_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
);

CREATE TABLE reading_progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  resource_id INT NOT NULL,
  percent INT DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_rp (user_id, resource_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
);

-- Categories
INSERT INTO categories (name, icon) VALUES
('Technology','💻'), ('Science','🔬'), ('Business','📈'),
('Design','🎨'), ('Literature','📖'), ('Health','🧘');

-- Admin (email: admin@dkap.local | password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin','admin@dkap.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin');

-- Sample Resources
INSERT INTO resources (title, author, description, type, category_id, body, tags) VALUES
('Intro to Algorithms','CLRS','Best book for learning algorithms and data structures.','book',1,'Detailed explanations of algorithms...','algorithms,cs,data-structures'),
('Atomic Habits','James Clear','Small changes, remarkable results.','book',6,'Habits shape your identity...','habits,productivity'),
('Designing Calm Interfaces','J. Park','Principles for peaceful UI/UX.','article',4,'Reduce cognitive load...','ui,ux,design');