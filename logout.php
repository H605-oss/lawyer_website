<?php
session_start();
session_unset();   // saare session variables clear
session_destroy(); // session destroy
header("Location: signup.php"); // logout ke baad signup page pe bhej do
exit();
