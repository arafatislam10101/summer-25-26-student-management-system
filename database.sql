CREATE DATABASE IF NOT EXISTS student_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_management; SET FOREIGN_KEY_CHECKS=0; DROP TABLE IF EXISTS messages,teacher_availability,ratings,package_requests,payments,online_requests,leave_requests,marks,attendance,notices,subjects,classes,students,teachers,parents,users; SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(150) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,role ENUM('admin','teacher','student','parent') NOT NULL,status ENUM('active','inactive') NOT NULL DEFAULT 'active',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE teachers(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL UNIQUE,teacher_id VARCHAR(30) NOT NULL UNIQUE,phone VARCHAR(30),subject VARCHAR(100),background TEXT,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
CREATE TABLE parents(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL UNIQUE,phone VARCHAR(30),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
CREATE TABLE classes(id INT AUTO_INCREMENT PRIMARY KEY,class_name VARCHAR(50) NOT NULL,section VARCHAR(20) NOT NULL,teacher_id INT NULL,UNIQUE(class_name,section),FOREIGN KEY(teacher_id) REFERENCES teachers(id) ON DELETE SET NULL);
CREATE TABLE teacher_classes(teacher_id INT NOT NULL,class_id INT NOT NULL,UNIQUE(class_id),PRIMARY KEY(teacher_id,class_id),FOREIGN KEY(teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,FOREIGN KEY(class_id) REFERENCES classes(id) ON DELETE CASCADE);
CREATE TABLE students(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL UNIQUE,student_id VARCHAR(30) NOT NULL UNIQUE,phone VARCHAR(30),address VARCHAR(255),class_id INT NULL,parent_id INT NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(class_id) REFERENCES classes(id) ON DELETE SET NULL,FOREIGN KEY(parent_id) REFERENCES parents(id) ON DELETE SET NULL);
CREATE TABLE subjects(id INT AUTO_INCREMENT PRIMARY KEY,subject_name VARCHAR(100) NOT NULL,class_id INT NULL,UNIQUE(subject_name,class_id),FOREIGN KEY(class_id) REFERENCES classes(id) ON DELETE CASCADE);
CREATE TABLE attendance(id INT AUTO_INCREMENT PRIMARY KEY,student_id INT NOT NULL,class_id INT NULL,date DATE NOT NULL,status ENUM('Present','Absent','Late') NOT NULL,UNIQUE(student_id,date),FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,FOREIGN KEY(class_id) REFERENCES classes(id) ON DELETE SET NULL);
CREATE TABLE marks(id INT AUTO_INCREMENT PRIMARY KEY,student_id INT NOT NULL,subject_id INT NOT NULL,exam VARCHAR(80) NOT NULL,marks DECIMAL(5,2) NOT NULL,UNIQUE(student_id,subject_id,exam),FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,FOREIGN KEY(subject_id) REFERENCES subjects(id) ON DELETE CASCADE);
CREATE TABLE notices(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(150) NOT NULL,description TEXT NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE leave_requests(id INT AUTO_INCREMENT PRIMARY KEY,student_id INT NOT NULL,reason TEXT NOT NULL,from_date DATE NOT NULL,to_date DATE NOT NULL,status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE);
CREATE TABLE online_requests(id INT AUTO_INCREMENT PRIMARY KEY,student_id INT NOT NULL,request_type ENUM('Online','Offline') NOT NULL,reason TEXT NOT NULL,status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE);
CREATE TABLE payments(id INT AUTO_INCREMENT PRIMARY KEY,student_id INT NOT NULL,amount DECIMAL(10,2) NOT NULL,purpose VARCHAR(100) NOT NULL,status ENUM('Paid','Pending') DEFAULT 'Pending',paid_at DATE NULL,payment_method VARCHAR(30) NULL,transaction_id VARCHAR(100) NULL,FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE);
CREATE TABLE package_requests(id INT AUTO_INCREMENT PRIMARY KEY,student_id INT NOT NULL,package_name VARCHAR(100) NOT NULL,reason TEXT,status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE);
CREATE TABLE ratings(id INT AUTO_INCREMENT PRIMARY KEY,student_id INT NOT NULL,rating TINYINT NOT NULL CHECK(rating BETWEEN 1 AND 5),comment TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE);
CREATE TABLE teacher_availability(id INT AUTO_INCREMENT PRIMARY KEY,teacher_id INT NOT NULL,day ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,from_time TIME NOT NULL,to_time TIME NOT NULL,UNIQUE(teacher_id,day,from_time),FOREIGN KEY(teacher_id) REFERENCES teachers(id) ON DELETE CASCADE);
CREATE TABLE messages(id INT AUTO_INCREMENT PRIMARY KEY,sender_id INT NOT NULL,receiver_id INT NOT NULL,message TEXT NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(sender_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(receiver_id) REFERENCES users(id) ON DELETE CASCADE);
INSERT INTO users(name,email,password,role) VALUES
('System Admin','admin@example.com','$2y$12$os9MWST7/apl6lzSNsTtzu2i0oZDLNAXit3SAn4XS9LSbMNLKWAjO','admin'),
('John Teacher','teacher@example.com','$2y$12$lVuJK9WlqhiGzYCz4jE2yuKhUY9Vq/J6OQJqcYNeV2hKgV5ommTQO','teacher'),
('Rahim Student','student@example.com','$2y$12$9MCUpFq3oIDjqu6QWe9QIusqV55RzZp/Bd.qyR9hvb1nqPeXqMAh.','student'),
('Mrs. Rahim Parent','parent@example.com','$2y$12$dPYpIotS8HFqWkEb03bxIe62zSYogLkjkUcMt7WQZSXQXrFx/uIca','parent');
INSERT INTO teachers(user_id,teacher_id,phone,subject,background) VALUES(2,'T-001','01700000001','Mathematics','BSc in Mathematics; 5 years teaching experience.');
INSERT INTO parents(user_id,phone) VALUES(4,'01700000004');
INSERT INTO classes(class_name,section,teacher_id) VALUES('Class 10','A',1);
INSERT INTO teacher_classes(teacher_id,class_id) VALUES(1,1);
INSERT INTO students(user_id,student_id,phone,address,class_id,parent_id) VALUES(3,'S-001','01700000003','Dhaka, Bangladesh',1,1);
INSERT INTO subjects(subject_name,class_id) VALUES('Mathematics',1),('English',1),('Science',1);
INSERT INTO attendance(student_id,class_id,date,status) VALUES(1,1,CURDATE(),'Present'),(1,1,DATE_SUB(CURDATE(),INTERVAL 1 DAY),'Present'),(1,1,DATE_SUB(CURDATE(),INTERVAL 2 DAY),'Absent'),(1,1,DATE_SUB(CURDATE(),INTERVAL 3 DAY),'Present'),(1,1,DATE_SUB(CURDATE(),INTERVAL 4 DAY),'Present');
INSERT INTO marks(student_id,subject_id,exam,marks) VALUES(1,1,'Mid Term',85),(1,2,'Mid Term',78),(1,3,'Mid Term',82);
INSERT INTO notices(title,description) VALUES('Welcome','Please check your dashboard regularly.'),('Parent Meeting','Parent-teacher meeting will be held next week.');
INSERT INTO payments(student_id,amount,purpose,status,paid_at) VALUES(1,5000,'Monthly Fee','Paid',CURDATE());
INSERT INTO ratings(student_id,rating,comment) VALUES(1,5,'Good academic support.');
