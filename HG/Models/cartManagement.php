<?php

//CART VIEW

function selectCart($sql)   {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        while ($row = $result->fetch_assoc())   {
            $data[0] = $row["cartID"];
            $data[1] = $row["totalPrice"];
        }
    }
    $conn->close();
    return $data;
}

function selectCartDetails($sql)    {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        $i = 0;
        while ($row = $result->fetch_assoc())   {
            $data[$i][0] = $row["productID"];
            $data[$i][1] = $row["name"];
            $data[$i][2] =$row["price"];
            $data[$i][3] =$row["image"];
            $i++;
        }
    }
    $conn->close();
    return $data;
}

function callProcedure($sql)    {
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        while ($row = $result->fetch_assoc())   {
            $data = $row["newCartID"];
        }
    }
    $conn->close();
    return $data;
}


//MISCELLANEOUS

function updateCart($sql)    {
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $conn->query($sql);
    $conn->close();
}

?>