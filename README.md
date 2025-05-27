# rentalsv3
rental management system for department of housing
To set it up on your local machine using XAMPP or WAMP:

downloaad the code or folder https://github.com/lincolnmk/rentalsv3 
Place the rentalsv3 folder inside your XAMPP or WAMP htdocs directory.

Database Setup:
Locate the latest database dump file inside the "assets/database scripts/"directory
Import the database dump into your local MySQL server using phpMyAdmin or the MySQL command line.
configure "rentalsv3/db_connection.php" accordingly

Access the Application:
Open your browser and navigate to:
http://localhost/rentalsv3/index.php

admin user:  superadmin
admin pass:  amtheone
to create a user: 
1. login as admin
2. create user under users/adduser
3. set usernames, names, password and save the user 
4. got to the user details
5. authorizations tab
6. modify persmisions and save
7. test to login the user using the created user name.


Adding a module 
1. Modify the index file to create a page route /index.php
2. Modify the permissions array /index.php   eg  view_maientance =>['module' => 'Maintenance', 'action' => 'can_view']
3. add links to the sidebar at /assets/templates/sidebar.php
4. add the module in the module db table  : INSERT INTO module (module_name) VALUES ('modulename');
5. create yours pages + php logic and place them in /pages

before launching
1. populate the db with a list of modules 
2. populate the db with a list of property types
3. config db connection
4. config the rate
