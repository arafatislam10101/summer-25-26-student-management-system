# Student Management System (PHP + MySQL, MVC)

A full-stack **Student Management System** built with **PHP, MySQL, HTML, CSS, and JavaScript** using the **MVC architecture**.

The system supports four user roles:

* **Admin**
* **Teacher**
* **Student**
* **Parent**

The project is designed to run on **XAMPP** without any framework or Composer dependency.

---

## 1. Install (XAMPP)

1. Copy the `student_management` folder into:

```text
C:\xampp\htdocs\
```

So the project becomes:

```text
C:\xampp\htdocs\student_management\
```

2. Open the **XAMPP Control Panel**.

3. Start:

* **Apache**
* **MySQL**

4. Open:

```text
http://localhost/phpmyadmin
```

5. Create/import the project database using:

```text
database.sql
```

6. Check the database configuration in:

```text
config/config.php
```

7. Open the project:

```text
http://localhost/student_management/
```

---

## 2. Folder Structure

```text
student_management/
│
├── index.php                    Front controller / main router
├── database.sql                 MySQL database
├── README.md
│
├── config/
│   └── config.php               Database and application configuration
│
├── helpers/
│   └── helpers.php              Common functions, security and session helpers
│
├── models/                      M — Database operations
│   ├── model.php                Base model
│   ├── user_model.php           User and role operations
│   ├── admin_model.php          Admin operations
│   ├── student_model.php        Student operations
│   ├── teacher_model.php        Teacher operations
│   └── message_model.php        Message operations
│
├── controllers/                 C — Request handling
│   ├── auth_controller.php      Authentication
│   ├── admin_controller.php     Admin operations
│   ├── student_controller.php   Student operations
│   ├── teacher_controller.php    Teacher operations
│   ├── parent_controller.php    Parent operations
│   ├── dashboard_controller.php Dashboard operations
│   ├── message_controller.php   Messaging
│   └── ajax_controller.php      AJAX / JSON operations
│
├── views/                       V — User interface
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   │
│   ├── admin/
│   ├── student/
│   ├── teacher/
│   ├── parent/
│   └── partials/
│
└── assets/
    ├── css/
    │   └── style.css            Main website styling
    │
    ├── js/
    │   └── app.js               JavaScript and AJAX
    │
    └── profile/
        ├── admin.svg
        ├── parent.svg
        ├── student.svg
        └── teacher.svg
```

---

## 3. MVC Architecture

The project follows the **Model-View-Controller (MVC)** architecture.

### Model

The model handles database operations.

```text
models/
```

Examples:

* Student database operations
* Teacher database operations
* User operations
* Admin operations
* Message operations

### View

The view contains the HTML interface displayed to the user.

```text
views/
```

Examples:

* Admin dashboard
* Student dashboard
* Teacher dashboard
* Parent dashboard
* Login page
* Registration page

### Controller

The controller receives requests, validates data, communicates with models, and loads views.

```text
controllers/
```

The basic flow is:

```text
User Request
     ↓
index.php
     ↓
Controller
     ↓
Model
     ↓
MySQL Database
     ↓
Controller
     ↓
View
     ↓
User
```

### MVC Rule

> Views should handle presentation, models should handle database operations, and controllers should handle application logic.

---

## 4. How the Router Works

The main entry point of the application is:

```text
index.php
```

Requests are routed through the front controller.

Examples:

```text
index.php?page=login
```

Opens the login page.

```text
index.php?page=register
```

Opens the registration page.

```text
index.php?page=admin
```

Opens the admin dashboard.

```text
index.php?page=student
```

Opens the student dashboard.

```text
index.php?page=teacher
```

Opens the teacher dashboard.

```text
index.php?page=parent
```

Opens the parent dashboard.

```text
index.php?page=logout
```

Logs the current user out.

AJAX requests are handled through:

```text
index.php?page=ajax
```

---

## 5. Four User Roles

The Student Management System contains four main roles.

| Role        | Main Responsibilities                                                      |
| ----------- | -------------------------------------------------------------------------- |
| **Admin**   | Manage students, teachers, classes, subjects and users                     |
| **Teacher** | Manage classes, attendance, marks and student-related academic information |
| **Student** | View profile, attendance, results, notices and requests                    |
| **Parent**  | Monitor child information, attendance, results and notices                 |

---

## 6. Admin Features

The **Admin** is responsible for managing the complete system.

### Dashboard

The admin dashboard provides an overview of the system.

It can display:

* Total students
* Total teachers
* Total classes
* Total subjects
* User statistics
* Requests
* Notices
* Feedback
* At-risk students

### Manage Students

Admin can:

* Add Student
* View Students
* Edit Student
* Delete Student
* Search Students
* Activate / deactivate users

### Manage Teachers

Admin can:

* Add Teacher
* View Teachers
* Edit Teacher
* Delete Teacher
* Search Teachers
* Manage teacher information

### Manage Classes

Admin can:

* Add Class
* View Classes
* Edit Class
* Delete Class

### Manage Subjects

Admin can:

* Add Subject
* View Subjects
* Edit Subject
* Delete Subject

### Manage Users

Admin can manage system users and their roles.

Supported roles:

```text
Admin
Teacher
Student
Parent
```

---

## 7. Teacher Features

Teachers have their own dashboard and academic functions.

Teachers can manage or view:

* Assigned classes
* Student information
* Attendance
* Marks
* Results
* Teacher profile
* Availability
* Student requests

The teacher dashboard is separated from the admin dashboard so each user only accesses the features appropriate to their role.

---

## 8. Student Features

Students can access their personal academic information.

Student features include:

* Student profile
* Attendance
* Results
* Notices
* Teacher availability
* Leave requests
* Online/offline requests
* Messages

Students should only be able to access their own information.

---

## 9. Parent Features

Parents can monitor information related to their child.

Parent features include:

* Child profile
* Attendance
* Results
* Notices
* Payment information
* Payment slips
* Package requests
* Feedback and ratings
* At-risk information
* Messages

---

## 10. Messaging System

The system includes a messaging feature for communication between users.

The messaging module is handled by:

```text
controllers/message_controller.php
models/message_model.php
```

Users can communicate according to the permissions defined by the system.

---

## 11. AJAX / JavaScript

The project uses JavaScript and AJAX for interactive operations.

Main JavaScript file:

```text
assets/js/app.js
```

AJAX requests are handled by:

```text
controllers/ajax_controller.php
```

AJAX can be used for operations such as:

* Live search
* Dynamic data loading
* Updating records
* Deleting records
* Dashboard statistics
* User actions
* Form operations

The advantage of AJAX is that some operations can be performed without completely reloading the page.

---

## 12. Authentication and Authorization

The system uses PHP authentication and sessions.

Authentication is handled through:

```text
controllers/auth_controller.php
```

User information is maintained using PHP sessions.

The system identifies the user's role:

```text
admin
teacher
student
parent
```

Role-based authorization prevents users from accessing dashboards and operations that do not belong to them.

For example:

```text
Admin → Admin Dashboard
Teacher → Teacher Dashboard
Student → Student Dashboard
Parent → Parent Dashboard
```

---

## 13. Database

The project uses **MySQL**.

Database setup file:

```text
database.sql
```

The database stores information related to:

* Users
* Students
* Teachers
* Parents
* Classes
* Subjects
* Attendance
* Results
* Notices
* Requests
* Messages
* Feedback
* Other system information

The PHP application connects to MySQL through:

```text
config/config.php
```

---

## 14. Requirement Checklist

| Requirement          | Where to Look                                           |
| -------------------- | ------------------------------------------------------- |
| **MVC Architecture** | `models/`, `controllers/`, `views/`                     |
| **MySQL Database**   | `database.sql`                                          |
| **PHP Backend**      | `controllers/`, `models/`                               |
| **HTML**             | `views/`                                                |
| **CSS**              | `assets/css/style.css`                                  |
| **JavaScript**       | `assets/js/app.js`                                      |
| **Authentication**   | `controllers/auth_controller.php`                       |
| **Sessions**         | Authentication/helpers                                  |
| **AJAX**             | `controllers/ajax_controller.php` + `assets/js/app.js`  |
| **Admin Module**     | `controllers/admin_controller.php` + `views/admin/`     |
| **Student Module**   | `controllers/student_controller.php` + `views/student/` |
| **Teacher Module**   | `controllers/teacher_controller.php` + `views/teacher/` |
| **Parent Module**    | `controllers/parent_controller.php` + `views/parent/`   |

---

## 15. Security

The system follows basic web-security practices.

| Security Concern    | Protection                                  |
| ------------------- | ------------------------------------------- |
| SQL Injection       | Prepared/database queries                   |
| Password Security   | Password hashing and verification           |
| XSS                 | Escaping output before displaying user data |
| CSRF                | CSRF protection where implemented           |
| Session Security    | PHP session management                      |
| Unauthorized Access | Role-based authorization                    |
| URL Tampering       | Server-side permission checks               |
| Invalid Input       | PHP and JavaScript validation               |

### Important

JavaScript validation should not be considered the main security layer.

Users can disable JavaScript, so important validation must also be performed on the server using PHP.

---

## 16. Project Technologies

### Frontend

```text
HTML5
CSS3
JavaScript
AJAX
```

### Backend

```text
PHP
MySQL
PHP Sessions
Cookies
```

### Architecture

```text
MVC
Front Controller
Role-Based Access
```

### Development Environment

```text
XAMPP
Apache
MySQL
phpMyAdmin
```

---

## 17. Running the Project

After installing XAMPP:

### Step 1

Start:

```text
Apache
MySQL
```

### Step 2

Put the project inside:

```text
C:\xampp\htdocs\
```

### Step 3

Import:

```text
database.sql
```

through phpMyAdmin.

### Step 4

Check:

```text
config/config.php
```

and make sure the database credentials are correct.

### Step 5

Open:

```text
http://localhost/student_management/
```

---

## 18. Troubleshooting

### Apache is not starting

Check whether another application is using port:

```text
80
443
```

### MySQL is not starting

Check whether another MySQL/MariaDB service is already running.

### Database connection error

Check:

```text
config/config.php
```

Make sure:

* Database name is correct
* Username is correct
* Password is correct
* MySQL is running

### Page not found

Make sure the folder is inside:

```text
C:\xampp\htdocs\
```

and open:

```text
http://localhost/student_management/
```

### CSS is not loading

Check:

```text
assets/css/style.css
```

and make sure the project paths are correct.

### JavaScript/AJAX is not working

Check:

```text
assets/js/app.js
controllers/ajax_controller.php
```

and inspect the browser's Developer Console for JavaScript errors.

---

## 19. Project Workflow

The overall system works as follows:

```text
User
 ↓
Login / Registration
 ↓
Authentication
 ↓
Role Detection
 ↓
Role-Based Dashboard
 ↓
Controller
 ↓
Model
 ↓
MySQL
 ↓
Model Response
 ↓
Controller
 ↓
View
 ↓
User
```

---

## 20. Main Project Modules

```text
Student Management System
│
├── Authentication
│
├── Admin
│   ├── Dashboard
│   ├── Students
│   ├── Teachers
│   ├── Classes
│   ├── Subjects
│   ├── Users
│   ├── Notices
│   ├── Requests
│   └── Reports / Statistics
│
├── Teacher
│   ├── Dashboard
│   ├── Classes
│   ├── Students
│   ├── Attendance
│   ├── Marks
│   └── Results
│
├── Student
│   ├── Dashboard
│   ├── Profile
│   ├── Attendance
│   ├── Results
│   ├── Notices
│   └── Requests
│
├── Parent
│   ├── Dashboard
│   ├── Child Information
│   ├── Attendance
│   ├── Results
│   ├── Notices
│   └── Feedback
│
└── Messaging
```

---

## 21. Project Objective

The main objective of this project is to develop a centralized **Student Management System** that makes student-related administrative and academic activities easier to manage.

The system provides different dashboards for administrators, teachers, students, and parents.

It reduces manual work and provides a structured way to manage:

* Student information
* Teacher information
* Classes
* Subjects
* Attendance
* Results
* Notices
* Requests
* Communication

---

## 22. Conclusion

The **Student Management System** is a full-stack web application developed using PHP and MySQL with an MVC architecture.

The system provides role-based access for:

```text
Admin
Teacher
Student
Parent
```

The separation of **Models, Controllers, and Views** makes the project easier to understand, maintain, debug, and extend.

The project can be further expanded with features such as:

* Online payments
* Advanced reports
* Email notifications
* SMS notifications
* Result publishing
* Attendance reports
* PDF report generation
* Improved dashboard analytics
* Additional role permissions
