# Getting started with BodyaFrame

### Contents

- [Installation](#Installation)
- [Working environment](#Working environment)
- [Initial Configuration](#Initial Configuration)
- [Framework features list](#Framework features list)
## Installation
To install framework, pull this repository into your folder and run `composer install` to install necessary packages which are required for framework.

It will be possible to use `composer create-project` soon.
## Working environment
Now you have a lot of directories and what are they for? Let's take a look:
 ```
📦Your folder
 ┣ 📂app             -> Your working directory
 ┃ ┣ 📂attributes        -> App attributes (will be changed)
 ┃ ┣ 📂config            -> Web configuration
 ┃ ┣ 📂controllers       -> Web controllers
 ┃ ┣ 📂files             -> CSS, JS files, fonts, images, etc.
 ┃ ┣ 📂lang              -> Language files
 ┃ ┣ 📂models            -> Web models
 ┃ ┣ 📂user              -> User stuff (will be changed)
 ┃ ┣ 📂views             -> Web views
 ┃ ┗ 📂web               -> Routes
 ┣ 📂console         -> Console stuff
 ┃ ┣ 📂controllers       -> Console controllers
 ┃ ┣ 📂migrations        -> DB Migrations
 ┃ ┣ 📂models            -> Console models
 ┃ ┗ 📜config.php        -> Console configuration
 ┣ 📂docs            -> Documentation [YOU ARE HERE]
 ┣ 📂logs            -> Log files
 ┣ 📂public          -> Web application entry point
 ┣ 📂system          -> Framework files (You don't need to work with this folder)
 ┣ 📂tests           -> UNIT Tests (soon)
 ┣ 📜.htaccess
 ┣ 📜composer.json
 ┣ 📜config.php      -> Application config
 ┣ 📜robots.txt
 ┣ 📜run             -> Console application entry point
 ┗ 📜sitemap.xml
```
## Initial Configuration
When installed, you need to perform initial configuration in order to make framework operate correctly.

**App configuration**

Sections descriptions will be moved to another docs files soon.

Go to `config.php`.
1. Set up your database and don't forget to specify `trusted_tables` which are the only tables framework will work with.
2. Enable `multilang` if your website will use more than 1 language. This option will add language prefix to URL after domain (for example, `https://example.com/ru/about`), so `lang` is the language which will be used when user removes language prefix. `langs` array contains all the languages and their names, which is useful when developing language switching buttons. `useFile` will make the app taking texts from language folder if `multilang` is off. It's useful when your website had multiple languages, but then you decided to leave 1 and don't want to move all the texts to view files.
3. You can enable `debug` and `db_debug` in order to get framework-level messages (for example, "missing controller") and see DB queries.

Go to `app/config/config.php`.
1. If your application is located in subdirectory (for example, `https://example.com/subdirectory`), specify it in `website -> prefix` section with leading slash. 
2. If you want to change images folder, modify `website -> img` section.
3. You can also modify `cssFolder` and `jsFolder` sections if you want to make CSS and JS files accessible with another path.
4. `disableCache`, if you want to disable CSS and JS cache during development.
5. `safety -> beginSalt` and `safety -> endSalt` are salts for passwords. `safety -> csrfProtection` is used for CSRF protection. `safety -> xFrameOptions` defines `X-Frame-Options` header, use `DENY` or `SAMEORIGIN` values. `safety -> sessionCookieName` defines session cookie name.
## Framework features list

*(In progress)*

 - Configuration
 - Defining routes
 - Controllers and rules
 - Models
 - Views
 - Multilingualism
 - Form validation
 - Database methods
 - Console
 - Migrations
 - Logging
 - Security
 - UNIT Testing