CREATE DATABASE IF NOT EXISTS evoting_system;
USE evoting_system;
CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    voter_id VARCHAR(20) NOT NULL UNIQUE,
    has_voted BOOLEAN DEFAULT FALSE
);
CREATE TABLE IF NOT EXISTS votes(
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate VARCHAR(100) NOT NULL,
    vote_count INT DEFAULT 0
);

INSERT INTO votes (candidate) VALUES
('Kalai Selvi'),
('Uma maheshwari'),
('Sumithra Devi'),
('H.Devi Sri'),
('A.A.R.Tejas sri'),
('Radhi Devi'),
('V.Sowmiya'),
('Kowsalya'),
('Devi Sri Aadhithya');

CREATE TABLE candidates (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    position VARCHAR(255) NOT NULL,
    course VARCHAR(255) NOT NULL
);

INSERT INTO candidates (name, position, course) VALUES
('Kalai Selvi', 'Student Chairman', 'B.Com(C.A)'),
('Uma maheshwari', 'Student Chairman', 'B.A.(English)'),
('Sumithra Devi', 'Student Chairman', 'B.Com'),
('H.Devi Sri', 'Student Secretary', 'B.Com(C.A)'),
('A.A.R.Tejas sri', 'Student Joint Secretary', 'B.Sc (Computer Science)'),
('Radhi Devi', 'Student Joint Secretary', 'B.A.English'),
('V.Sowmiya', 'Student Cultural Secretary', 'M.Com'),
('Kowsalya', 'Student Cultural Secretary', 'B.Com'),
('Devi Sri Aadhithya', 'Student Sports Secretary', 'M.Sc(Maths)');