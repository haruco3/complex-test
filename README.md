# complex-test

## (It's actually quite simple)

This is a basic LMS that allows for teacher and student management,
custom tests, and results logging.\
I made this project a while ago and it needs some work but it is functional.

## Installation
You will need a web server, PHP and MySQL/MariaDB.
1. Create a new database and assign a user all permissions for that database.
2. Open the login2.php file in a text editor.
3. On lines 13-16, enter your database server's hostname, username, password and database name in the appropriate places. For example:\
`$servername = 'localhost';`\
`$sqlUser = 'lmsUser';`\
`$sqlPass = 'password';`\
`$dbname = 'lms';`
4. Navigate to the index.php page in your web browser and enter the username `admin` and password `pass`. Change your password afterwards!

## Things I would like to work on
- Clean up code
- Different question types
- Easier setup
- Lesson creation interface