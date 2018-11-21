<?php

header("Content-Type: application/json; charset=UTF-8");
session_start();

//HEADER VIEW

if (isset($_POST["search"]))    {
    //Save search term as session variable
    $_SESSION["searchTerm"] = $_POST["searchTerm"];
    unset($_SESSION["collectionID"]);
    
    //Return to Collection View
    header("Location: ../Views/collection.html");
}


//SHOP VIEW

if ($_GET["x"] == "loadShop")   {
    //Send SQL statement to Product Management Model
    $sql = "SELECT collection.collectionID, collection.name, collection.flavourText, collection.image FROM collection WHERE collection.active = 1";
    
    //Save database results
    include "../Models/productManagement.php";
    $data = array();
    $data = selectCollections($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

if (isset($_POST["selectCollection"])) {
    //Save collection ID as cookie variable
    $_SESSION["collectionID"] = $_POST["collectionID"];
    unset($_SESSION["searchTerm"]);
    
    //Return to Collection View
    header("Location: ../Views/collection.html");
}


//COLLECTION VIEW

if ($_GET["x"] == "loadCollection1")    {
    $data = array();
    //Via Shop View or Search Button
    if (isset($_SESSION["collectionID"]))    {
        //Send SQL statement to Product Management Model
        $sql = "SELECT collection.name, collection.flavourText FROM collection WHERE collection.collectionID = " . $_SESSION["collectionID"];
        
        //Save results
        include "../Models/productManagement.php";
        $data = selectCollectionDetails($sql);
    } else if (isset($_SESSION["searchTerm"]))  {
        //Save results
        $data = $_SESSION["searchTerm"];
    } else  {
        //Return to Shop View
        header("Location: ../Views/shop.html");
    }

    //Encode results with JSON
    echo json_encode($data);
}

if ($_GET["x"] == "loadCollection2")    {
    //Via Shop View or Search Button
    if (isset($_SESSION["collectionID"]))    {
        //Send SQL statement to Product Management Model
        $sql = "SELECT product.productID, product.keyword, product.price, product.image FROM product WHERE product.collectionID = " . $_SESSION["collectionID"] . " AND product.available = 1";
    } else if (isset($_SESSION["searchTerm"]))  {
        //Send SQL statement to Product Management Model
        $sql = "SELECT product.productID, product.keyword, product.price, product.image FROM product, collection WHERE product.collectionID = collection.collectionID AND collection.active = 1 AND product.available = 1 AND ((product.name REGEXP '" . $_SESSION["searchTerm"] . "') OR (product.keyword REGEXP '" . $_SESSION["searchTerm"] . "'))";
    } else  {
        //Return to Shop View
        header("Location: ../Views/shop.html");
    }
    
    //Save database results
    include "../Models/productManagement.php";
    $data = array();
    $data = selectProducts($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

if (isset($_POST["selectProduct"])) {
    //Save product ID as cookie variable
    $_SESSION["productID"] = $_POST["productID"];
    
    //Return to Product View
    header("Location: ../Views/product.html");
}


//PRODUCT VIEW

if ($_GET["x"] == "loadProduct")   {
    //Send SQL statement to Product Management Model
    $sql = "SELECT product.name, product.keyword, product.price, product.size, product.image FROM product WHERE product.productID = " . $_SESSION["productID"];
    
    //Save database results
    include "../Models/productManagement.php";
    $data = array();
    $data = selectProductDetails($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

?>