<?php
include_once("html.php");
include_once("config.php");

function execSql($sql){
    $mysqli = null;

    try{
        $mysqli = getConn();

        // Check if connection was established
        if (!$mysqli){
            return false;
        }

        $result = $mysqli->query($sql);

        // Check if the query execution was successful
        if (!$result){
            throw new Exception("Hiba lépett fel az SQL utasítás futtatása közben: " . $mysqli->error);
        }

        // Handle INSERT queries
        if (str_starts_with(strtoupper(trim($sql)), 'INSERT')){
            return $mysqli->insert_id > 0 ? $mysqli->insert_id : false;
        }

        // Handle SELECT queries
        if (str_starts_with(strtoupper(trim($sql)), 'SELECT')){
            $numRows = $result->num_rows;

            if ($numRows === 0){
                return false;
            }

            if ($numRows === 1){
                return $result->fetch_assoc();
            }

            return $result->fetch_all(MYSQLI_ASSOC);
        }

        // For the other types of queries (e.g., UPDATE, DELETE)
        return $mysqli->affected_rows > 0;
    }
    catch (Exception $e){
        // Handle exceptions by logging and displaying messages
        displayMessage($e->getMessage(), 'error');
        error_log($e->getMessage());
        return false;
    }
    finally{
        // Ensure the database connection is always closed
        $mysqli->close();
    }
}

function getConn($dbName = DB_NAME){
    try{
        // Kapcsolódás az adatbázishoz
        $mysqli = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, $dbName);

        // Ellenőrizzük a csatlakozás sikerességét
        if (!$mysqli){
            throw new Exception("Kapcsolódási hiba az adatbázishoz: " . mysqli_connect_error());
        }

        return $mysqli;
    }
    catch (Exception $e){
        // Hibaüzenet megjelenítése a felhasználónak
        displayMessage($e->getMessage(), 'error');

        // Hibanaplózás
        error_log($e->getMessage());

        // Hibás csatlakozás esetén `null`-t ad vissza
        return null;
    }
}

function createTable($tableBody, $tableName, $dbName = DB_NAME){
    $sql = "CREATE TABLE IF NOT EXISTS $dbName.$tableName
    ($tableBody)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8
    COLLATE = utf8_hungarian_ci;";

    $database = getConn("mysql");
    $database->query($sql);
    $database->close();

    return $sql;
}

function createTableSubjects($dbName = DB_NAME){
    $tableBody = "
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(15) NOT NULL
    ";

    return createTable($tableBody, 'subjects', $dbName);
}

function createTableClasses($dbName = DB_NAME){
    $tableBody = "
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(15) NOT NULL,
        year INT NOT NULL
    ";

    return createTable($tableBody, "classes", $dbName);
}

function createTableMarks($dbName = DB_NAME){
    $tableBody = "
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        subject_id INT NOT NULL,
        mark INT NOT NULL,
        date VARCHAR(15) NOT NULL
    ";

    return createTable($tableBody, "marks", $dbName);
}

function createTableStudents($dbName = DB_NAME){
    $tableBody = "
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(15) NOT NULL,
        class_id INT NOT NULL
    ";

    return createTable($tableBody, "students", $dbName);
}

function createDatabase($dbName = DB_NAME){
    $database = getConn("mysql");
    $database->query("CREATE DATABASE IF NOT EXISTS $dbName 
    CHARACTER SET utf8 COLLATE utf8_hungarian_ci;");
    $database->close();
}

function insertIntoTable($tableName, $header, $values, $dbName = DB_NAME){
    $database = getConn($dbName);
    $database->query("INSERT INTO $tableName($header) VALUES($values)");
    $database->close();
}

function fillTables(){
    // students table
    $class_id = 0;
    foreach (CLASSES as $class){
        $class_id++;
        $classCount = rand(MIN_CLASS_COUNT, MAX_CLASS_COUNT);
        $student_id = 0;
        for ($i = 0; $i < $classCount; $i++){
            $student_id++;
            $lastName = NAMES["lastnames"][rand(0, count(NAMES["lastnames"])-1)];
            $gender = rand(1,2) == 1 ? "men" : "women";
            $firstName = NAMES["firstnames"][$gender][rand(0, count(NAMES["firstnames"][$gender])-1)];
            insertIntoTable("students", "name, class_id", "'$lastName $firstName', $class_id");
            $gradeCount = rand(MIN_MARKS_COUNT,MAX_MARKS_COUNT);
            for ($j = 0; $j < $gradeCount; $j++){
                $subject_id = rand(0, count(SUBJECTS)-1);
                $mark = rand(1,5);
                $date = date("Y-m-d_His");
                insertIntoTable("marks", "student_id, subject_id, mark, date", "$student_id, $subject_id, $mark, '$date'");
            }
        }
    }

    // classes table
    foreach (CLASSES as $class){
        $values = str_split($class, 2);
        insertIntoTable("classes", "name, year", "'$values[1]', $values[0]");
    }

    // subjects table
    foreach (SUBJECTS as $subject){
        insertIntoTable("subjects", "name", "'$subject'");
    }
}