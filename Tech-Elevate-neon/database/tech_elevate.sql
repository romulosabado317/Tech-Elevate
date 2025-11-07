DROP DATABASE IF EXISTS tech_elevate;
CREATE DATABASE tech_elevate;
USE tech_elevate;

CREATE TABLE users (
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

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  project_title VARCHAR(255) NOT NULL,
  project_description TEXT,
  filename VARCHAR(255) DEFAULT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE skills (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  skill_name VARCHAR(100) NOT NULL,
  skill_level ENUM('Beginner','Intermediate','Advanced') DEFAULT 'Beginner',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE activity_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(255) NOT NULL,
  ip_address VARCHAR(100) DEFAULT NULL,
  user_agent TEXT DEFAULT NULL,
  date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE admin_reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NOT NULL,
  reviewed_user INT NOT NULL,
  action_taken VARCHAR(255) NOT NULL,
  remarks TEXT DEFAULT NULL,
  reviewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewed_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (name, email, password, role, photo, status) VALUES
('Admin', 'tech@gmail.com', MD5('admin123'), 'admin', 'admin_photo.txt', 'active'),
('Student Group 1', 'group1@gmail.com', MD5('demo123'), 'student', 'student_photo.txt', 'active');

INSERT INTO projects (user_id, project_title, project_description, filename) VALUES
(2, 'Sample Project', 'This is a demo student project.', 'sample_project.txt');

INSERT INTO skills (user_id, skill_name, skill_level) VALUES
(2, 'HTML', 'Advanced'),
(2, 'CSS', 'Intermediate'),
(2, 'JavaScript', 'Beginner');

INSERT INTO activity_log (user_id, action, ip_address, user_agent) VALUES
(2, 'Registered account', '127.0.0.1', 'Chrome on Windows'),
(2, 'Uploaded project', '127.0.0.1', 'Chrome on Windows'),
(1, 'Admin reviewed activity log', '127.0.0.1', 'Chrome on Windows');

INSERT INTO admin_reviews (admin_id, reviewed_user, action_taken, remarks) VALUES
(1, 2, 'Reviewed student project', 'Project reviewed and verified.'),
(1, 2, 'Reviewed activity log', 'Activity log verified for accuracy.');

CREATE INDEX idx_activity_user ON activity_log(user_id);
CREATE INDEX idx_activity_date ON activity_log(date_time);
CREATE INDEX idx_activity_action ON activity_log(action);
CREATE INDEX idx_review_admin ON admin_reviews(admin_id);
CREATE INDEX idx_review_user ON admin_reviews(reviewed_user);
CREATE INDEX idx_skill_user ON skills(user_id);


ALTER TABLE projects ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'active';
