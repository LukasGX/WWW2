# WWW2
## Installation
+ Download www2.php
+ include into your php-file (e.g. index.php)
```php
include("www2.php");
```
## Documentation
### First steps
+ Create instance from class
```php
include("www2.php");
$site = new WWW2("Test");
```
### Developing HTML \<head>
+ Creating new Meta-Tag and adding to head:
```php
$site->meta($meta, $value);
// e.g.
$site->meta("author", "Lukas Grambs");
```
+ adding \<style\>-Tags
  + You must not use the \<style>-Tags!
  + It is recommended to use *.css files!
```php
$site->style("
.abc {
    color: red;
}
");
```
+ adding external css file
```php
$site->css("styles.css");
```
+ adding \<script\>-Tags
  + You must not use the \<script>-Tags!
  + It is recommended to use *.js files!
```php
$site->style("
document.addEventListener('DOMContentLoaded', function () {
    console.log('Hello World!');
});
");
```
+ adding external js file
```php
$site->js("script.js");
```
+ keywords
```php
$site->keywords(array("1", "2"));
// OR
$site->keywords(["1", "2"]);
```
### Developing HTML \<body>
+ using the body()-method
  + WARNING: this method replaces the full body!
```php
$body = "
My <br />
Body <br />
is saved here!";
$site->body($body);
```
+ using the add()-method
```php
$toAdd = "Hello World!";
$site->add($toAdd);
```
### Settings
Use the settings()-method
```php
$site->settings("show_stack_trace", true); // Should the full stack trace be showed if a error occures?
```
+ More settings in the future
### Generating and printing out the html
Use the gen()-method
```php
$site->gen($mode);
```
All modes explained:
+ **a**: Returns the full html-content
+ **a+**: Prints out the full html-content
+ **h**: Returns the head
+ **h+**: Prints out the head
+ **b**: Returns the body
+ **b+**: Prints out the body
### Generating forms
```php
$fields = array(
    array (
        "type" => "text", // required
        "name" => "username", // required
        "id" => "username", // optional
        "value" => "Username", // optional
        "placeholder" => "Username", // optional
        "rdv" => "abc" // for radio-input elements: the test after the button
    ),
    array (
        "type" => "submit" // must not!
    )
);
$actionfile = "register.php"; // where does the input go?
$submit_value = "Send"; // value for submit-button; optional
$method = "post"; // optional; standard is post
$site->gen_form($fields, $actionfile, $submit_value, $method);
```
### Database
```php
$site->init_db("servername", "username", "password", "database name"); // init database
$site->create_db(); // creates db if it doesn't exists already
$site->db_login(); // login to db (AFTER create_db()!!!)

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(200) NOT NULL,
    gender VARCHAR(30) NOT NULL
)";
$site->execute_sql($sql);
$site->close_db(); //closes db connection
```
### Login Site
For creating non-designed Standard-Loginsites there's a own method named login_page()
```php
$site->$site->login_page($answering_site, $username_placeholder, $password_placeholder, $submit_value, $method);
```
All parameters explained:
+ **answering site**: The site where is the data going (required)
+ **username placeholder**: The placeholder for the username field (optional; standard is Username)
+ **password placeholder**: The placeholder for the password field (optional; standard is Password)
+ **submit value**: The value of the submit-button (optional; standard is Login)
+ **method**: The sending-method (get or post; standard is post; optional)  

**Processing the input**:
```php
if ($site->check_loginform_input()) { // login form submitted?
    $process = $site->process_login();
    if ($process === 0) {
        $site->add("OK!");
    }
    else if ($process === 1) {
        $site->add("Wrong Password!");
    }
    else {
        $site->add("This user does not exist!");
    }
}
```
### Register site
For creating non-designed Standard-Registersites there's a own method named register_page()
```php
$additional_form_fields = array(
    array(
        "type" => "radio",
        "name" => "gender",
        "value" => "m",
        "rdv" => "Man"
    ),
    array(
        "type" => "radio",
        "name" => "gender",
        "value" => "w",
        "rdv" => "Woman"
    ),
    array(
        "type" => "radio",
        "name" => "gender",
        "value" => "d",
        "rdv" => "Diverse"
    )
);
$site->register_page($additional_form_fields, $answering_site, $username_placeholder, $password_placeholder, $submit_value, $method);
```
All parameters explained:
+ **additional form fields**: Form fields between Username, Password and submit
+ **answering site**: The site where is the data going (required)
+ **username placeholder**: The placeholder for the username field (optional; standard is Username)
+ **password placeholder**: The placeholder for the password field (optional; standard is Password)
+ **submit value**: The value of the submit-button (optional; standard is Login)
+ **method**: The sending-method (get or post; standard is post; optional)  

**Processing the input**:
```php
if ($site->check_registerform_input()) { // register form submitted?
    $process = $site->process_register();
    if ($process) {
        $site->add("OK!");
    }
}
```
