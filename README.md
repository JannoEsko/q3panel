# q3panel
An application for hosting game servers based on Quake 3 engine on Linux.

Walkthrough how to set up a Q3 server with the panel can be seen under issue 1: 
https://github.com/JannoEsko/q3panel/issues/1

Update 08/03/2018 - XenForo authentication.
To enable XenForo authentication, you need the new User.php class and the folder extensions from this repo. Also, you have to manually add a line to the end of your config.php file.
//If you wish to include XenForo extension (so authentication works over XenForo forum software, this has to be in your config file)
define("IS_XENFORO", true);

Some of the functionality:

External authentication - you can connect this to any kind of a MySQL database, which has passwords hashed with PHP's password_hash function. Requirements are that the external table has an unique ID field (PK), username field, password field and email field (can be changed).

Web FTP interface - It has a small built-in web FTP interface. Normal text files can be easily modified with it, new file creation/uploads, new folders etc. Bases on javascript + PHP.

many:many relationship between users and servers - I've used a panel before called Swiftpanel. One of the largest issues it had, was that for each server, you had to have exactly one client. This panel can have multiple servers mapped to multiple users.

Group levels - Yet again, swiftpanel had 2 group levels, one was the administrative access and one was a simple server user/owner. This has 3, one which is a simple server user (can access the parts he's mapped to), server admin (if mapped to the server, can do everything with the server apart from disabling/deleting it, can map new users/remove mapped users) and panel admin, which can do everything. 

Forgotten password functionality - yet again, a missing feature from Swiftpanel.

RWD-based design - the page looks good on phone as well! ;)

Tickets system - users can send tickets regarding various problems.

SendGrid integration - The panel admins have a choice, whether they want to use the PHPMailer (which can be quite hard to set up on the server-side, mails can end up in spam and whatnot) or SendGrid, which will handle the actual mail sending on its own.



Biggest upside is that it's completely open-source. You can edit it as you see fit, you can do whatever you want with it. It's free, it's open-source.
