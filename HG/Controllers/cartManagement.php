<?php

header("Content-Type: application/json; charset=UTF-8");
session_start();

//HEADER VIEW

if (isset($_POST["viewCart"]))  {
    //Check if session is active
    if ($_SESSION["login"] == 1) {
        //Return to Cart View
        header("Location: ../Views/cart.html");
    } else  {
        //Return to Login View
        header("Location: ../Views/login.html");
    }
}


//PRODUCT VIEW

if (isset($_POST["addProduct"]))    {
    //Check if session is active
    if ($_SESSION["login"] == 1)    {
        //Send SQL statement to Cart Management Model
        $sql = "INSERT INTO cart_details (cart_details.productID, cart_details.cartID) SELECT " . $_SESSION["productID"] . ", " . $_SESSION["cartID"] . " FROM dual WHERE NOT EXISTS (SELECT 1 FROM cart_details WHERE cart_details.productID = " . $_SESSION["productID"] . " AND cart_details.cartID = " . $_SESSION["cartID"] . ")";
        include "../Models/cartManagement.php";
        updateCart($sql);

        //Return to Cart View
        header("Location: ../Views/collection.html");
    } else  {
        //Return to Login View
        header("Location: ../Views/login.html");
    }
    
}


//CART VIEW

if ($_GET["x"] == "loadCart1")  {
    //Send SQL statement to Cart Management Model
    $sql = "SELECT cart.cartID, cart.totalPrice FROM cart WHERE cart.customerID = " . $_SESSION["customerID"] . " AND cart.datePaid IS NULL";
    
    //Save database results
    include "../Models/cartManagement.php";
    $data = array();
    $data = selectCart($sql);
    
    //Save cart ID as a session variable
    $_SESSION["cartID"] = $data[0];
    
    //Encode results with JSON
    echo json_encode($data[1]);
}

if ($_GET["x"] == "loadCart2")  {
    //Send SQL statement to Cart Management Model
    $sql = "SELECT product.productID, product.name, product.price, product.image FROM product, cart_details WHERE cart_details.cartID = " . $_SESSION["cartID"] . " AND cart_details.productID = product.productID";
    
    //Save database results
    include "../Models/cartManagement.php";
    $data = array();
    $data = selectCartDetails($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

if (isset($_POST["removeProduct"])) {
    //Send SQL statement to Cart Management Model
    $sql = "DELETE FROM cart_details WHERE cart_details.cartID = " . $_SESSION["cartID"] . " AND cart_details.productID = " . $_POST["productID"];
    include "../Models/cartManagement.php";
    updateCart($sql);
    
    //Return to Cart View
    header("Location: ../Views/cart.html");
}

if (isset($_POST["checkout"]))  {
    //Send SQL statement to Cart Management Model
    $sql = "CALL checkout (" . $_SESSION["cartID"] . ", " . $_SESSION["customerID"] . ")";
    $data = array();
    include "../Models/cartManagement.php";
    $data = callProcedure($sql);
    
    //Save database result as session variable
    $_SESSION["cartID"] = $data;
    
    //Return to Home Page View
    header("Location: ../index.html");
}

?>