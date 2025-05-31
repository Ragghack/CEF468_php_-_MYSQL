# CEF468_php_-_MYSQL
CEF 468 - Introduction to PHP and Database Management
 Lab Sessions and Project Documentation
Welcome to the CEF 468 - Introduction to PHP and Database Management repository! This repository contains structured lab activities designed to enhance your understanding of PHP, MySQL integration, Object-Oriented Programming (OOP) concepts, and web security. Additionally, it includes documentation for Project 1: Online Bookstore Management System, a real-world web application using PHP and MySQL.
Repository Structure
CEF-468-PHP-Database-Management/
│── Lab2_Developing_Dynamic_Web_Applications_Part1/
│── Lab3_Developing_Dynamic_Web_Applications_Part2/
│── Lab4_Object_Oriented_Programming_PHP/
│── Lab5_Real_World_Web_Applications_PHP_MySQL/
│── Lab6_Web_Application_Security/
│── Project1_Online_Bookstore_Management_System/
│── README.md
│── docs/
│── assets/
│── src/



Lab Sessions
Lab 2: Developing Dynamic Web Applications Using PHP - Part 1
Objective:
- Understand the fundamentals of PHP and its integration with MySQL.
- Learn how to create forms, handle user input, and establish database connectivity.
   Key Activities:
✔ Setting up the development environment (XAMPP/MAMP/LAMP).
✔ Writing PHP scripts for basic functionality (variables, loops, conditionals).
✔ Connecting PHP to a MySQL database using mysqli or PDO.
✔ Executing queries for inserting and retrieving data.
✔ Structuring output using HTML and PHP combined.

Lab 3: Developing Dynamic Web Applications Using PHP - Part 2
Objective:
- Enhance database interaction and manage form submissions dynamically.
Key Activities:
✔ Implementing user authentication systems (session management).
✔ Validating and sanitizing user input to prevent security vulnerabilities.
✔ Using prepared statements for secure database transactions.
✔ Implementing basic CRUD operations for a web application.

Lab 4: Understanding Object-Oriented Programming (OOP) in PHP
Objective:
- Learn OOP principles and apply them in PHP for structured coding.
Key Activities:
✔ Understanding classes, objects, and properties in PHP.
✔ Implementing constructors and methods for efficient code reuse.
✔ Applying inheritance and polymorphism in web application development.
✔ Structuring data access using OOP principles for scalable applications.

Lab 5: Building Real-World Web Applications with PHP and MySQL
 Objective:
- Apply all learned concepts to build a functional web application.
 Key Activities:
✔ Designing a user-friendly interface using HTML and CSS.
✔ Implementing a dynamic content management system.
✔ Utilizing AJAX for smooth data updates without page reloads.
✔ Refining web application usability and optimization techniques.

Lab 6: Ensure Web Application Security
Objective:
- Protect web applications against common security threats.
Key Activities:
✔ Understanding SQL injection and implementing prepared statements.
✔ Securing user authentication using hashed passwords (bcrypt).
✔ Preventing Cross-Site Scripting (XSS) with proper escaping techniques.
✔ Implementing Cross-Site Request Forgery (CSRF) protections using security tokens.

Project 1: Online Bookstore Management System
Summary:
This project involves developing an Online Bookstore where users can browse and purchase books, while admins manage inventory. It incorporates user authentication, shopping cart functionality, and an admin dashboard.
Key Features:
 User Authentication: Users register, log in, and manage profiles securely.
  Admin Panel: CRUD operations for managing the bookstore’s inventory.
 Shopping Cart: Users add books to their cart, checkout, and track order history.
Security Measures: Mitigation of SQL injection, CSRF, and XSS vulnerabilities.
Expected Outcomes:
By completing this project, students will:
✔ Implement secure user authentication and data management.
✔ Design and develop a structured, dynamic web application.
✔ Understand security vulnerabilities and apply protective measures.
✔ Gain experience with database normalization and efficiency techniques.

🔧 Setup and Deployment
Requirements:
- XAMPP/MAMP/LAMP for local development
- PHP 8+
- MySQL database
- Web server (Apache or Nginx)
Installation Steps:
1️ Clone this repository:
git clone https://github.com/your-username/CEF468-PHP-Database-Management.git


2️ Configure the .env file with database credentials.
3️ Run the SQL script in docs/db-schema.sql to create tables.
4️ Start the local server (php -S localhost:8000).


