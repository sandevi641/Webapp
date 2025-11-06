# Blog App 

1. Create the MySQL DB and user then run `sql/schema.sql`

2. Copy `.env` to project root and fill DB credentials

3. Serve `public/` with your webserver (Apache, Nginx, or PHP built-in) -
   php -S localhost:8080 -t public

4. Open http://localhost:8080

 Uses PHP sessions and optional "remember me" cookie token
 No external libraries — a minimal Markdown converter included
 Keep `.env` outside webroot in production.
