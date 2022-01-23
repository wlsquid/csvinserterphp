# Command Line PHP CSV to DB 
Set up in your connection.php using the example provided to run the program.

You can use the following commands 
``` You can use the following commands to run the script: \n
    --file "csv file name" - this is the name of the CSV to be parsed and will also insert the values into the DB \n
    --create_table - this will cause the MySQL users table to be built (and no further
    action will be taken)\n
    --dry_run - this will be used with the --file directive in case we want to run the script but not \n
    insert into the DB. All other functions will be executed, but the database won't be altered \n
    -u - Displays MySQL username \n
    -p - Displays MySQL password \n
    -h - Displays MySQL host \n
    --help - which will output the above list of directives with details. \n 
```