<?php

header("Content-Type: application/json; charset=UTF-8");
session_start();

//CONTENT MANAGEMENT VIEW

if ($_GET["x"] == "loadContentManagement")  {
    //Send SQL statement to Content Management Model
    $sql = "SELECT collection.collectionID, collection.name, collection.active, COUNT(product.collectionID) AS numProducts FROM collection LEFT JOIN product ON collection.collectionID = product.collectionID GROUP BY collection.collectionID ORDER BY collection.collectionID DESC";
     
    //Save database results
    include "../Models/contentManagement.php";
    $data = array();
    $data = selectCollections($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

if (isset($_POST["postCollectionID"]))  {
    //Save collection ID as session variable
    $_SESSION["collectionID"] = $_POST["collectionID"];
    
    //Return to Update Collection View
    header("Location: ../Views/updateCollection.html");
}


//INSERT COLLECTION VIEW

if (isset($_POST["insertCollection"])) {
    //Save collection details
    $adminID = 7;
    $collectionName = $_POST["name"];
    $flavourText = $_POST["flavour"];
    $imgInfo = pathinfo($_FILES["image"]["name"]);
    $imgExt = $imgInfo["extension"];
    $imgName = $_POST["name"] . "." . $imgExt;
    $image = "../Images/" . $imgName;
    move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    $active = 0;

    //Send SQL statement to Content Management Model
    $sql = "INSERT INTO collection (adminID, name, flavourText, image, active) VALUES (" . $adminID . ", '" . $collectionName . "', '" . $flavourText . "', '" . $image . "', " . $active . ")";
    include "../Models/contentManagement.php";
    insertUpdate($sql);

    //Return to Content Management View
    header("Location: ../Views/contentManagement.html");
}


//UPDATE COLLECTION VIEW

if ($_GET["x"] == "loadUpdateCollection1")  {
    //Send SQL statement to Content Management Model
    $collection = $_SESSION["collectionID"];
    $sql = "SELECT product.productID, product.name, product.available FROM product WHERE product.collectionID = " . $collection . " ORDER BY product.productID DESC";
    
    //Save database results
    include "../Models/contentManagement.php";
    $data = array();
    $data = selectProducts($sql);

    //Encode results with JSON
    echo json_encode($data);
    }

if ($_GET["x"] == "loadUpdateCollection2")  {
    //Send SQL statement to Content Management Model
    $collectionID = $_SESSION["collectionID"];
    $sql = "SELECT collection.name, collection.flavourText, collection.image, collection.active FROM collection WHERE collection.collectionID = " . $collectionID;
    
    //Save database results
    include "../Models/contentManagement.php";
    $data = array();
    $data = selectCollectionDetails($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

if (isset($_POST["updateCollection"]))  {
    //Save collection details
    $collectionID = $_SESSION["collectionID"];
    $adminID = 7;
    $collectionName = $_POST["name"];
    $flavourText = $_POST["flavour"];
    $image;
    if ($_FILES["image"]["error"] > UPLOAD_ERR_OK) {
        $image = $_POST["currentImage"];
    } else  {
        $imgInfo = pathinfo($_FILES["image"]["name"]);
        $imgExt = $imgInfo["extension"];
        $imgName = $_POST["name"] . "." . $imgExt;
        $image = "../Images/" . $imgName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }
    $active = $_POST["active"];

    //Send SQL statement to Content Management Model
    $sql = "UPDATE collection SET collection.name = '" . $collectionName . "', collection.flavourText = '" . $flavourText . "', collection.image = '" . $image . "', collection.active = " . $active . " WHERE collection.collectionID = " . $collectionID;
    include "../Models/contentManagement.php";
    insertUpdate($sql);

    //Return to Content Management View
    header("Location: ../Views/contentManagement.html");
}

if (isset($_POST["postProductID"])) {
    //Save product ID as session variable
    $_SESSION["productID"] = $_POST["productID"];
    
    //Return to Update Product View
    header("Location: ../Views/updateProduct.html");
}


//INSERT PRODUCT VIEW

if (isset($_POST["insertProduct"])) {
    //Save product details
    $collectionID = $_SESSION["collectionID"];
    $productName = $_POST["name"];
    $keyword = $_POST["keyword"];
    $price = $_POST["price"];
    $size = $_POST["size"];
    $imgInfo = pathinfo($_FILES["image"]["name"]);
    $imgExt = $imgInfo["extension"];
    $imgName = $_POST["name"] . "." . $imgExt;
    $image = "../Images/" . $imgName;
    move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    $available = 1;
    
    //Send SQL statement to Content Management Model
    $sql = "INSERT INTO product (collectionID, name, keyword, price, size, image, available) VALUES (" . $collectionID . ", '" . $productName . "', '" . $keyword . "', '" . $price . "', '" . $size . "', '" . $image . "', " . $available . ")";
    include "../Models/contentManagement.php";
    insertUpdate($sql);
    
    //Return to Update Collection View
    header("Location: ../Views/updateCollection.html");
}


//UPDATE PRODUCT VIEW

if ($_GET["x"] == "loadUpdateProduct")  {
    //Send SQL statement to Content Management Model
    $productID = $_SESSION["productID"];
    $sql = "SELECT product.name, product.keyword, product.price, product.size, product.image, product.available FROM product WHERE product.productID = " . $productID;
    
    //Save database results
    include "../Models/contentManagement.php";
    $data = array();
    $data = selectProductDetails($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

if (isset($_POST["updateProduct"]))  {
    //Save product details
    $productID = $_SESSION["productID"];
    $productName = $_POST["name"];
    $keyword = $_POST["keyword"];
    $price = $_POST["price"];
    $size = $_POST["size"];
    $image;
    if ($_FILES["image"]["error"] > UPLOAD_ERR_OK) {
        $image = $_POST["currentImage"];
    } else  {
        $imgInfo = pathinfo($_FILES["image"]["name"]);
        $imgExt = $imgInfo["extension"];
        $imgName = $_POST["name"] . "." . $imgExt;
        $image = "../Images/" . $imgName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }
    $available = $_POST["available"];

    //Send SQL statement to Content Management Model
    $sql = "UPDATE product SET product.name = '" . $productName . "', product.keyword = '" . $keyword . "', product.price = '" . $price . "', product.size = '" . $size . "', product.image = '" . $image . "', product.available = " . $available . " WHERE product.productID = " . $productID;
    include "../Models/contentManagement.php";
    insertUpdate($sql);

    //Return to Content Management View
    header("Location: ../Views/updateCollection.html");
}


//VIEW REPORT VIEW

if ($_GET["x"] == "loadViewReport1")    {
    //Send SQL statement to Content Management Model
    $sql = "SELECT * FROM view1";
    
    //Save database results
    include "../Models/contentManagement.php";
    $data = array();
    $data = selectView1($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

if ($_GET["x"] == "loadViewReport2")    {
    //Send SQL statement to Content Management Model
    $sql = "SELECT * FROM view2";
    
    //Save database results
    include "../Models/contentManagement.php";
    $data = array();
    $data = selectView2($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

if ($_GET["x"] == "loadViewReport3")    {
    //Send SQL statement to Content Management Model
    $sql = "SELECT * FROM view3";
    
    //Save database results
    include "../Models/contentManagement.php";
    $data = array();
    $data = selectView3($sql);
    
    //Encode results with JSON
    echo json_encode($data);
}

?>