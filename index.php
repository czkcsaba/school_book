<?php
include_once("config.php");
include_once("db.php");
include_once("html.php");
include_once("request.php");

head();
displayBodyStart();
displayNav();
// requestHandle();
displayBodyEnd();

if (isset($_POST["create"])){
    createDatabase();
    createTableSubjects();
    createTableClasses();
    createTableMarks();
    createTableStudents();
    fillTables();
    echo "<script>document.getElementById('create').style.visibility = 'hidden';</script>";
}