
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS news_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_date (event_date)
);

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admission_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    child_name VARCHAR(120) NOT NULL,
    child_age INT NOT NULL,
    preferred_grade VARCHAR(80) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO news_posts (title, content) VALUES
('Inter-House Sports', 'The school successfully held its annual sports day with great participation from all houses.'),
('Science Fair', 'Students showcased exciting projects that demonstrated creativity and problem-solving skills.'),
('Parent Meeting', 'Teachers and parents discussed school performance and strategies to improve student outcomes.');

INSERT IGNORE INTO events (title, description, location, event_date) VALUES
('Inter-House Sports Day', 'A day of friendly competition between school houses on the main field.', 'School Grounds', DATE_ADD(CURDATE(), INTERVAL 8 DAY)),
('Science Club Showcase', 'Learners present their science projects to parents and local guests.', 'Hall A', DATE_ADD(CURDATE(), INTERVAL 15 DAY)),
('Parent-Teacher Meeting', 'Discuss student progress and upcoming school programs.', 'Library Conference Room', DATE_ADD(CURDATE(), INTERVAL 21 DAY));

INSERT IGNORE INTO admin_users (username, password, full_name) VALUES
('admin', '$2y$10$EB35QOsGExbqeo9TlutMo.v9hH5MP2H4wKKTwHA1XhIYNVzReKXBm', 'School Administrator');
