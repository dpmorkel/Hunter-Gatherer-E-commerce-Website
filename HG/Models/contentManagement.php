<?php

//CONTENT MANAGEMENT VIEW

function selectCollections($sql)   { 
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        $i = 0;
        while ($row = $result->fetch_assoc())   {
            $data[$i][0] = $row["collectionID"];
            $data[$i][1] = $row["name"];
            $data[$i][2] =$row["active"];
            $data[$i][3] =$row["numProducts"];
            $i++;
        }
    }
    $conn->close();
    return $data;
}


//UPDATE COLLECTION VIEW

function selectProducts($sql)   {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        $i = 0;
        while ($row = $result->fetch_assoc())   {
            $data[$i][0] = $row["productID"];
            $data[$i][1] = $row["name"];
            $data[$i][2] =$row["available"];
            $i++;
        }
    }
    $conn->close();
    return $data;
}

function selectCollectionDetails($sql)  {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        while ($row = $result->fetch_assoc())   {
            $data[0] = $row["name"];
            $data[1] = $row["flavourText"];
            $data[2] =$row["image"];
            $data[3] =$row["active"];
        }
    }
    $conn->close();
    return $data;
}


//UPDATE PRODUCT VIEW

function selectProductDetails($sql)  {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        while ($row = $result->fetch_assoc())   {
            $data[0] = $row["name"];
            $data[1] = $row["keyword"];
            $data[2] =$row["price"];
            $data[3] =$row["size"];
            $data[4] =$row["image"];
            $data[5] =$row["available"];
        }
    }
    $conn->close();
    return $data;
}


//VIEW REPORT VIEW

function selectView1($sql)  {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        while ($row = $result->fetch_assoc())   {
            $data[0] = $row["passiveCustomers"];
            $data[1] = $row["totalCustomers"];
        }
    }
    $conn->close();
    return $data;
}

function selectView2($sql)  {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        $i = 0;
        while ($row = $result->fetch_assoc())   {
            $data[$i][0] = $row["firstName"];
            $data[$i][1] = $row["lastName"];
            $data[$i][2] = $row["totalSpend"];
            $i++;
        }
    }
    $conn->close();
    return $data;
}

function selectView3($sql)  {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        $i = 0;
        while ($row = $result->fetch_assoc())   {
            $data[$i][0] = $row["keyword"];
            $data[$i][1] = $row["totalSales"];
            $i++;
        }
    }
    $conn->close();
    return $data;
}


//MISCELLANEOUS

function insertUpdate($sql) {
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $conn->query($sql);
    $conn->close();
}

?>