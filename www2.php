<?php
// 1.1
class WWW2 {
    function __construct($title) {
        $title = "<title>$title</title>";
        $this->head = "";
        $this->bhead = $title;
        $this->body = "";

        // settings
        $this->show_stack_trace = false;

        // db
        $this->conn = NULL;
    }
    function settings($setting, $value) {
        $value = str_ireplace("\"", "", $value);
        switch ($setting) {
            case "show_stack_trace":
                $this->show_stack_trace = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                break;
        }
    }
    function meta($meta, $value) {
        $meta = str_ireplace("\"", "", $meta);
        $value = str_ireplace("\"", "", $value);
        $tag = "<meta name='$meta' content='$value'>";
        $this->bhead .= $tag;
    }
    function css($link) {
        $link = str_ireplace("\"", "", $link);
        $tag = "<link rel='stylesheet' href='$link'>";
        $this->bhead .= $tag;
    }
    function js($link) {
        $link = str_ireplace("\"", "", $link);
        $tag = "<script src='$link'></script>";
        $this->bhead .= $tag;
    }
    function style($content) {
        $content = str_ireplace("\"", "'", $content);
        $this->bhead .= "<style>$content</style>";
    }
    function script($content) {
        $content = str_ireplace("\"", "'", $content);
        $this->bhead .= "<script>$content</script>";
    }
    function keywords($words) {
        $content = "";
        foreach ($words as $word) {
            $content .= $this->filter("$word,");
        }
        $content = mb_substr($content, 0, -1);
        $this->bhead .= "<meta name='keywords' content='$content'>\r\n";
    }
    function body($content) {
        $this->body = $content;
    }
    function add($content) {
        $this->body .= $content;
    }
    function filter($content) {
        $f = htmlspecialchars(strip_tags($content));
        return str_ireplace("\"", "'", $f);
    }
    function generateHtml($bodyContent) {
        return "<!DOCTYPE html>\r\n<html>\r\n<head>\r\n{$this->bhead}{$this->head}\r\n</head>\r\n<body>\r\n{$bodyContent}\r\n</body>\r\n</html>";
    }
    function gen($mode) {
        switch ($mode) {
            case "a":
            case "a+":
                $content = $this->generateHtml($this->body);
                if ($mode === "a+") {
                    echo $content;
                }
                return $content;
            case "h":
            case "h+":
                $content = "<head>\r\n{$this->bhead}{$this->head}\r\n</head>";
                if ($mode === "h+") {
                    echo $content;
                }
                return $content;
            case "b":
            case "b+":
                $content = "<body>\r\n{$this->body}\r\n</body>";
                if ($mode === "b+") {
                    echo $content;
                }
                return $content;
            default:
                if ($this->show_stack_trace) {
                    throw new Exception("Invalid mode '$mode'");
                }
                else {
                    die("<b>Fatal Error:</b> Invalid mode '$mode' in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
                }
                break;
        }
    }
    // formula generation
    function gen_form(array $form_fields, $answerfile, $submit_value = "Send", $method = "post") {
        $method = strtolower($method);

        $body = "<form action='$answerfile' method='$method'>";
        foreach ($form_fields as $field) {
            $type = "";
            $name = "";
            $id = "";
            $value = "";
            $placeholder = "";
            $rdv = "";

            if (isset($field["type"])) {
                $type = $field["type"];
            }
            else {
                if ($this->show_stack_trace) {
                    throw new Exception("Missing form field: type");
                }
                else {
                    die("<b>Fatal Error:</b> Missing form field: type in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
                }
            }
            if (isset($field["name"])) {
                $name = $field["name"];
            }
            else {
                if ($this->show_stack_trace) {
                    throw new Exception("Missing form field: name");
                }
                else {
                    die("<b>Fatal Error:</b> Missing form field: name in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
                }
            }
            if (isset($field["id"])) {
                $id = $field["id"];
            }
            if (isset($field["value"])) {
                $value = $field["value"];
            }
            if (isset($field["placeholder"])) {
                $placeholder = $field["placeholder"];
            }
            if (isset($field["rdv"])) {
                $rdv = $field["rdv"];
            }

            $input = "<input type='$type' name='$name'";
            if (!empty($id)) {
                $input .= " id='$id'";
            }
            if (!empty($value)) {
                $input .= " value='$value'";
            }
            if (!empty($placeholder)) {
                $input .= " placeholder='$placeholder'";
            }
            $input .= " class='www2_inputfield'>";
            if (!empty($rdv)) {
                $input .= $rdv;
            }

            $body .= $input;
        }
        $body .= "
        <input type='submit' value='$submit_value'>
        </form>";
        return $body;
    }
    // hashing
    function gen_hash($string, $algo = "sha512") {
        return hash($algo, $string);
    }
    // user management
    function login_page($answerfile, $ph_username = "Username", $ph_pw = "Password", $submit_value = "Login", $method = "post") {
        $form_fields = array(
            array(
                "type" => "text",
                "name" => "username",
                "placeholder" => $ph_username
            ),
            array(
                "type" => "password",
                "name" => "password",
                "placeholder" => $ph_pw
            ),
            array(
                "type" => "hidden",
                "name" => "sent",
                "value" => "login"
            )
        );
        $result = $this->gen_form($form_fields, $answerfile, $submit_value, $method);
        return $result;
    }
    function register_page(array $form_fields, $answerfile, $ph_username = "Username", $ph_pw = "Password", $submit_value = "Login", $method = "post") {
        $username = array(
            "type" => "text",
            "name" => "username",
            "placeholder" => $ph_username
        );
        $password = array(
            "type" => "password",
            "name" => "password",
            "placeholder" => $ph_pw
        );
        $hidden = array(
            "type" => "hidden",
            "name" => "sent",
            "value" => "register"
        );
        array_unshift($form_fields, $password);
        array_unshift($form_fields, $username);
        array_push($form_fields, $hidden);
        $result = $this->gen_form($form_fields, $answerfile, $submit_value, $method);
        return $result;
    }
    function check_registerform_input() {
        if (isset($_POST["sent"]) && $_POST["sent"] == "register") {
            return true;
        }
        else {
            return false;
        }
    }
    function process_register() {
        if (!isset($_POST["username"])) {
            if ($this->show_stack_trace) {
                throw new Exception("Missing form field: username");
            }
            else {
                die("<b>Fatal Error:</b> Missing form field: username in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
            }
        }
        if (!isset($_POST["password"])) {
            if ($this->show_stack_trace) {
                throw new Exception("Missing form field: password");
            }
            else {
                die("<b>Fatal Error:</b> Missing form field: password in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
            }
        }
        $username = htmlspecialchars(strip_tags($_POST["username"]));
        $password = htmlspecialchars(strip_tags($_POST["password"]));
        $hashed_pw = $this->gen_hash($password);

        $sql = "INSERT INTO users (username, password, ";
        foreach ($_POST as $key => $value) {
            if ($key == "username" || $key == "password" || $key == "sent") {
                continue;
            }
            $sql .= "$key, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ") VALUES ('$username', '$hashed_pw', ";
        foreach ($_POST as $key => $value) {
            if ($key == "username" || $key == "password" || $key == "sent") {
                continue;
            }
            $sql .= "'$value', ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ")";

        $result = $this->execute_sql($sql);

        return true;
    }
    function check_loginform_input() {
        if (isset($_POST["sent"]) && $_POST["sent"] == "login") {
            return true;
        }
        else {
            return false;
        }
    }
    function process_login() {
        if (!isset($_POST["username"])) {
            if ($this->show_stack_trace) {
                throw new Exception("Missing form field: username");
            }
            else {
                die("<b>Fatal Error:</b> Missing form field: username in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
            }
        }
        if (!isset($_POST["password"])) {
            if ($this->show_stack_trace) {
                throw new Exception("Missing form field: password");
            }
            else {
                die("<b>Fatal Error:</b> Missing form field: password in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
            }
        }
        $username = htmlspecialchars(strip_tags($_POST["username"]));
        $password = htmlspecialchars(strip_tags($_POST["password"]));
        $hashed_pw = $this->gen_hash($password);

        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $this->execute_sql($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $pw_from_db = $row["password"];
            }
        }
        else {
            return 2;
        }

        if ($hashed_pw === $pw_from_db) {
            return 0;
        }
        else {
            return 1;
        }
    }
    function init_db($servername, $username, $password, $db_name) {
        $this->db_servername = strip_tags($servername);
        $this->db_username = strip_tags($username);
        $this->db_password = strip_tags($password);
        $this->db_dbname = strip_tags($db_name);
    }
    function create_db() {
        $conn = new mysqli($this->db_servername, $this->db_username, $this->db_password);

        $sql = "CREATE DATABASE IF NOT EXISTS $this->db_dbname";
        $result = $conn->query($sql);
        if (!$result) {
            if ($this->show_stack_trace) {
                throw new Exception("Error creating database: ". $this->conn->error);
            }
            else {
                die("<b>Fatal Error:</b> Error creating database: " . $this->conn->error . " in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
            }
            return false;
        }

        $conn->close();
    }
    function db_login() {
        $this->conn = new mysqli($this->db_servername, $this->db_username, $this->db_password, $this->db_dbname);
        if ($this->conn->connect_error) {
            if ($this->show_stack_trace) {
                throw new Exception("Error connecting to db: ". $this->conn->error);
            }
            else {
                die("<b>Fatal Error:</b> Error connecting to db: " . $this->conn->error . " in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
            }
            return false;
        }
    }
    function execute_sql($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            if ($this->show_stack_trace) {
                throw new Exception("Error executing sql-query: ". $this->conn->error);
            }
            else {
                die("<b>Fatal Error:</b> Error executing sql-query: " . $this->conn->error . " in <b>" . __FILE__ . "</b> on line <b>" . __LINE__ . "</b>");
            }
            return false;
        }
        return $result;
    }
    function close_db() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>