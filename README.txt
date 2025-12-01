Visitor Inquiry Logging System
Ready-to-run PHP project for XAMPP / LAMP.
Location: visitor_logging_project_FINAL/

How to run:
1. Copy the folder to your XAMPP htdocs (e.g., C:\xampp\htdocs\visitor_logging_project_FINAL)
2. Create a MySQL database (e.g., visitor_db) and import db_init.sql
   - You can use phpMyAdmin or MySQL CLI:
     mysql -u root -p
     CREATE DATABASE visitor_db;
     USE visitor_db;
     SOURCE /path/to/db_init.sql;
3. Update config.php with your DB credentials if needed. (Just Delete the password in DB_PASS)
4. Start Apache & MySQL, then open: http://localhost/visitor_logging_project_FINAL/
Default test user:
  username: admin@example.com
  password: Password123!

Files:
- db_init.sql : creates tables and inserts sample user + sample visitors
- config.php : DB connection
- functions.php : helper functions (auth, validation)
- index.php : login page
- register.php : simple registration (for admin)
- dashboard.php : main dashboard (list, filter, stats)
- add_visitor.php : form to add visitor
- delete_visitor.php : delete endpoint
- update_visitor.php : form to update visitor
- export.php : export CSV (bonus feature)
- logout.php : logout
- assets/: CSS file

................

Programmer: BonJohn Mayores (BSIT-3B)