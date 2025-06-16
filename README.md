Project Setup Guide
This guide will help you set up and run the Stock Management System locally.

Requirements
PHP >= 7.4
MySQL or MariaDB
Apache or Nginx
Composer (optional, if used)

1️⃣ Clone the Project
git clone https://github.com/WaiPhyo22/stock-management.git
cd stock-management

2️⃣ Configure the Database Connection
Open the file:
core/Database.php

Edit the following values to match your local database configuration:
private $host = 'localhost';
private $dbname = 'your_database_name';
private $username = 'your_username';
private $password = 'your_password';

3️⃣ Create Database & Run SQL
Create a new database in MySQL (e.g., stock_db)

-- Run Sql
1. product.sql
2. transaction.sql
3. user.sql

4️⃣ Start Local Server and run

Default Admin
Email: admin@example.com
Password: admin123