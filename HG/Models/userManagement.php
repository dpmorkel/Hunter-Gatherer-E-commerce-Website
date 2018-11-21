<?php

//LOGIN VIEW

function selectUserDetails($sql)    {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        $i = 0;
        while ($row = $result->fetch_assoc())   {
            $data[$i][0] = $row["userID"];
            $data[$i][1] = $row["email"];
            $data[$i][2] = $row["password"];
            $i++;
        }
    }
    $conn->close();
    return $data;
}

function selectCustomerDetails($sql)   {
    $data = array();
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)  {
        while ($row = $result->fetch_assoc())   {
            $data[0] = $row["firstName"];
            $data[1] = $row["cartID"];
        }
    }
    $conn->close();
    return $data;
}

function callProcedure($sql) {
    $conn = new mysqli("localhost", "root", "", "hunter_gatherer_db");
    $conn->query($sql);
    $conn->close();
}

?>