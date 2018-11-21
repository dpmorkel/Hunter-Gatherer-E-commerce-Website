<?php

//SHOP VIEW

function selectCollections($sql)    {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        $i = 0;
        while ($row = $result->fetch_assoc())   {
            $data[$i][0] = $row["collectionID"];
            $data[$i][1] = $row["name"];
            $data[$i][2] =$row["flavourText"];
            $data[$i][3] =$row["image"];
            $i++;
        }
    }
    $conn->close();
    return $data;
}

function selectCollectionDetails($sql)    {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        while ($row = $result->fetch_assoc())   {
            $data[0] = $row["name"];
            $data[1] = $row["flavourText"];
        }
    }
    $conn->close();
    return $data;
}


//COLLECTION VIEW

function selectProducts($sql)   {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        $i = 0;
        while ($row = $result->fetch_assoc())   {
            $data[$i][0] = $row["productID"];
            $data[$i][1] = $row["keyword"];
            $data[$i][2] =$row["price"];
            $data[$i][3] =$row["image"];
            $i++;
        }
    }
    $conn->close();
    return $data;
}


//PRODUCT VIEW

function selectProductDetails($sql) {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        while ($row = $result->fetch_assoc())   {
            $data[0] = $row["name"];
            $data[1] = $row["keyword"];
            $data[2] = $row["price"];
            $data[3] = $row["size"];
            $data[4] = $row["image"];
        }
    }
    $conn->close();
    return $data;
}

?>