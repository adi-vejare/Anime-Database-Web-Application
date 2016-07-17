# Anime-Database-Web-Application


##Secure Web Programming Project: Internet Anime Database
###Scope Statement
-Aditi Vejare
The project is an Internet Database for Anime. The database stores information on various anime and
the web application displays them. Anyone can read this information and can sign up to be registered
users. Registered users can rate the anime, make their personalized lists and view anime based on their
overall ranking. The user can update the ratings and status of anime if desired. While sign up, salt is
created and both salt and password are stored using sha256 hashing.
Admin can add new anime from the webpage. Admin also have interface to add new users and update
passwords of existing users. The application keeps track of the failed login attempts and blocks an IP if
more than 5 failed login attempts are made within 10minutes from that IP.
The application prevents XSS and SQLi injections by using prepared statements to query. Authentication
is required to access user and admin functions. User inputs are sanitized. Session handling is done such
that session is destroyed if session lasts for more than 30 minutes of if browser or IP changes.
[Next](https://github.com/adi-vejare/Anime-Database-Web-Application/blob/master/Scope_statement.pdf)
