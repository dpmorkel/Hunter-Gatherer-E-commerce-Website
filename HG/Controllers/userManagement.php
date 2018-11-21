<?php

header("Content-Type: application/json; charset=UTF-8");
session_start();

//HEADER VIEW

if ($_GET["x"] == "loadHeader") {
    //Check if session is active
    if ($_SESSION["login"] == 1)   {
        //Encode results with JSON
        $customerName = $_SESSION["firstName"];
        echo json_encode($customerName);
    } else  {
        //Encode results with JSON
        echo json_encode(0);
    }
}

if (isset($_POST["loginLogout"]))    {
    //Check if session is active
    if ($_SESSION["login"] == 1)   {
        //End the user's session
        $_SESSION["login"] = 0;
        
        //Return to Home Page View
        header("Location: ../index.html"); 
    } else  {
        //Return to Login View
        header("Location: ../Views/login.html");
    }
}


//LOGIN VIEW

if ($_GET["x"] == "loadLogin")  {
    //Send SQL statement to User Management Model
    $sql = "SELECT user.userID, user.email, user.password FROM user";
    
    //Save database results
    include "../Models/userManagement.php";
    $data = array();
    $data = selectUserDetails($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

if (isset($_POST["login"]))  {
    
    $_SESSION["login"] = 1;
    
    if ($_POST["userID"] == 7)  {
        //Save admin ID as session variable
        $_SESSION["adminID"] = $_POST["userID"];
        
        //Return to Content Management View
        header("Location: ../Views/contentManagement.html");
    } else  {
        //Save customer ID as session variable
        $_SESSION["customerID"] = $_POST["userID"];
        
        //Send SQL statement to User Management Model
        $sql = "SELECT customer.firstName, cart.cartID FROM customer, cart WHERE customer.customerID = " . $_POST["userID"] . " AND customer.customerID = cart.customerID AND cart.datePaid IS NULL";
        
        //Save database results
        include "../Models/userManagement.php";
        $data = selectCustomerDetails($sql);
        
        //Save firstName and cartID as session variables
        $_SESSION["firstName"] = $data[0];
        $_SESSION["cartID"] = $data[1];
        
        //Return to Shop View
        header("Location: ../Views/shop.html");
    }
}

if (isset($_POST["register"]))  {
    //Save customer details
    $email = $_POST["registerEmail"];
    $password = $_POST["registerPassword"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $newsletter = 0;
    if (isset($_POST["news"]))  {
        $newsletter = 1;
    }
    
    //Send SQL statement to User Management Model
    $sql = "CALL register ('" . $email . "', '" . $password . "', '" . $firstName . "', '" . $lastName . "', " . $newsletter . ")";
    include "../Models/userManagement.php";
    callProcedure($sql);
    
    //Return to Home Page View
    header("Location: ../index.html");
}

?>