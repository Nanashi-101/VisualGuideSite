-- Fitness center database. Import once: mysql -u root < install.sql
CREATE DATABASE IF NOT EXISTS fitness_center CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE fitness_center;

CREATE TABLE IF NOT EXISTS users (          -- logins (the professor's users_auth)
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('member','admin') NOT NULL DEFAULT 'member',
  failed_count TINYINT NOT NULL DEFAULT 0,   -- wrong password/code attempts in a row
  locked_until DATETIME DEFAULT NULL,        -- login blocked until this time
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_2fa (       -- Google Authenticator secret per user
  user_id INT PRIMARY KEY,
  secret_base32 VARCHAR(64) NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS plans (          -- the professor's faculties
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  price_eur DECIMAL(6,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS members (        -- the professor's students
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  birthdate DATE DEFAULT NULL,
  address VARCHAR(150) NOT NULL DEFAULT '',
  notes VARCHAR(100) NOT NULL DEFAULT '',
  plan_id INT NOT NULL,
  FOREIGN KEY (plan_id) REFERENCES plans(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contacts (       -- public contact form
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject VARCHAR(150) NOT NULL,
  message VARCHAR(500) NOT NULL,
  email VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO plans (name, price_eur)
SELECT * FROM (SELECT 'Basic' n, 24.90 p UNION SELECT 'Premium', 39.90 UNION SELECT 'Student', 19.90) t
WHERE NOT EXISTS (SELECT 1 FROM plans);
