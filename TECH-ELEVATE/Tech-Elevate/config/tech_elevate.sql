CREATE DATABASE IF NOT EXISTS tech_elevate;
USE tech_elevate;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','student') DEFAULT 'student',
  photo VARCHAR(255) DEFAULT NULL,
  course VARCHAR(150) DEFAULT NULL,
  year_level VARCHAR(100) DEFAULT NULL,
  about TEXT DEFAULT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  reset_token VARCHAR(255) DEFAULT NULL,
  reset_expires DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  project_title VARCHAR(255) NOT NULL,
  project_description TEXT,
  filename VARCHAR(255) DEFAULT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activity_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(255),
  date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


INSERT INTO users (name,email,password,role,photo,status) VALUES
('Admin','tech@gmail.com',MD5('admin123'),'admin','admin_photo.txt','active'),
('Student Group 1','group1@gmail.com',MD5('demo123'),'student','student_photo.txt','active');

INSERT INTO projects (user_id,project_title,project_description,filename) VALUES
(2,'Sample Project','This is a demo student project.','sample_project.txt');

INSERT INTO activity_log (user_id,action) VALUES
(2,'Registered account'),
(2,'Uploaded project');
