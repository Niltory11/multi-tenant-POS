<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'dbcon.php';

// Input field validation
function validate($inputData){

    global $conn;
    $validatedData = mysqli_real_escape_string($conn, $inputData);
    return trim($validatedData);
}

// Redirect from 1 page to another page with the message (status)
function redirect($url, $status){

    $_SESSION['status'] = $status;
    header('Location: '.$url);
    exit(0);
}


// Display messges or status after any process.
function alertMessage(){

    if(isset($_SESSION['status'])){
         echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6>'.$_SESSION['status'].'</h6>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
        unset($_SESSION['status']);
    }
}

// Insert record using this function
function insert($tableName, $data)
{
    global $conn;

    $table = validate($tableName);

    $columns = array_keys($data);
    $values = array_values($data);

    $finalColumn = implode(',', $columns);
    $finalValues = "'".implode("', '", $values)."'";

    $query = "INSERT INTO $table ($finalColumn) VALUES ($finalValues)";
    $result = mysqli_query($conn,$query);
    return $result;
}

// Update data using this function
function update($tableName, $id, $data){

    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $updateDataString = "";

    foreach($data as $column => $value){
        $updateDataString .= $column.'='."'$value',";
    }

    $finalUpdateData = substr(trim($updateDataString),0,-1);

    $query = "UPDATE $table SET $finalUpdateData WHERE id='$id'";
    $result = mysqli_query($conn, $query);
    return $result;
}

function getAll($tableName, $status = NULL, $tenant_id = NULL){

    global $conn;

    $table = validate($tableName);
    $status = validate($status);
    $tenant_id = validate($tenant_id);

    // Get tenant_id from session if not provided
    if (!$tenant_id && isset($_SESSION['loggedInUser']['tenant_id'])) {
        $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
    }

    $whereClause = "";
    if ($tenant_id) {
        $whereClause = "WHERE tenant_id='$tenant_id'";
    }

    if($status == 'status')
    {
        $query = "SELECT * FROM $table WHERE status='0'" . ($whereClause ? " AND tenant_id='$tenant_id'" : "");
    }
    else
    {
        $query = "SELECT * FROM $table " . $whereClause;
    }
    return mysqli_query($conn, $query);
}

function getById($tableName, $id, $tenant_id = NULL){

    global $conn;

    $table = validate($tableName);
    $id = validate($id);
    $tenant_id = validate($tenant_id);

    // Get tenant_id from session if not provided
    if (!$tenant_id && isset($_SESSION['loggedInUser']['tenant_id'])) {
        $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
    }

    $whereClause = "id='$id'";
    if ($tenant_id) {
        $whereClause .= " AND tenant_id='$tenant_id'";
    }

    $query = "SELECT * FROM $table WHERE $whereClause LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){

        if(mysqli_num_rows($result) == 1){

            $row = mysqli_fetch_assoc($result);
            $response = [
                'status' => 200,
                'data' => $row,
                'message' => 'Record Found'
            ];
            return $response;

        }else{

            $response = [
                'status' => 404,
                'message' => 'No Data Found'
            ];
            return $response;
        }

    }else{
        $response = [
            'status' => 500,
            'message' => 'Something Went Wrong'
        ];
        return $response;
    }
}

// Delete data from database using id 
function delete($tableName, $id){

    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $query = "DELETE FROM $table WHERE id='$id' LIMIT 1";
    $result = mysqli_query($conn, $query);
    return $result;
}


function checkParamId($type){

    if(isset($_GET[$type])){
        if($_GET[$type] != ''){

            return $_GET[$type];
        }else{

            return '<h5>No Id Found</h5>';
        }

    }else{
        return '<h5>No Id Given</h5>';
    }
}

function logoutSession(){

    unset($_SESSION['loggedIn']);
    unset($_SESSION['loggedInUser']);
}

function jsonResponse($status, $status_type, $message){

    $response = [
        'status' => $status,
        'status_type' => $status_type,
        'message' => $message
    ];
    echo json_encode($response);
    return;
}

function getCount($tableName)
{
    global $conn;

    $table = validate($tableName);

    $query = "SELECT * FROM $table";
    $query_run = mysqli_query($conn, $query);
    if($query_run){

        $totalCount = mysqli_num_rows($query_run);
        return $totalCount;
        
    }else{
        return 'Something Went Wrong!';
    }
}

// Get tenant company information
function getTenantInfo($tenant_id = NULL)
{
    global $conn;

    if (!$tenant_id && isset($_SESSION['loggedInUser']['tenant_id'])) {
        $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
    }

    if (!$tenant_id) {
        return null;
    }

    $tenant_id = validate($tenant_id);
    $query = "SELECT * FROM tenants WHERE tenant_id='$tenant_id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Get company name for display
function getCompanyName($tenant_id = NULL)
{
    $tenant = getTenantInfo($tenant_id);
    return $tenant ? $tenant['company_name'] : 'Unknown Company';
}

// Get company display info (name + subscription)
function getCompanyDisplayInfo($tenant_id = NULL)
{
    $tenant = getTenantInfo($tenant_id);
    if ($tenant) {
        $subscription = ucfirst($tenant['subscription_plan']);
        return [
            'name' => $tenant['company_name'],
            'subscription' => $subscription,
            'status' => ucfirst($tenant['subscription_status'])
        ];
    }
    return [
        'name' => 'Unknown Company',
        'subscription' => 'Basic',
        'status' => 'Active'
    ];
}

// Add tenant filtering to WHERE clause
function addTenantFilter($whereClause = '', $tenant_id = NULL)
{
    if (!$tenant_id && isset($_SESSION['loggedInUser']['tenant_id'])) {
        $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
    }
    
    if ($tenant_id) {
        $tenant_id = validate($tenant_id);
        $filter = "tenant_id = '$tenant_id'";
        
        if ($whereClause) {
            return $whereClause . " AND " . $filter;
        } else {
            return "WHERE " . $filter;
        }
    }
    
    return $whereClause;
}

// Execute query with tenant filtering
function executeTenantQuery($query, $tenant_id = NULL)
{
    global $conn;
    
    // Add tenant filter to the query
    $filteredQuery = addTenantFilterToQuery($query, $tenant_id);
    return mysqli_query($conn, $filteredQuery);
}

// Add tenant filter to existing query
function addTenantFilterToQuery($query, $tenant_id = NULL)
{
    if (!$tenant_id && isset($_SESSION['loggedInUser']['tenant_id'])) {
        $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
    }
    
    if (!$tenant_id) {
        return $query;
    }
    
    $tenant_id = validate($tenant_id);
    
    // Check if query already has WHERE clause
    if (stripos($query, 'WHERE') !== false) {
        // Add tenant filter to existing WHERE clause
        $query = str_replace('WHERE ', "WHERE tenant_id = '$tenant_id' AND ", $query);
    } else {
        // Add WHERE clause with tenant filter
        $query = str_replace('FROM ', "FROM ", $query);
        $query = str_replace('ORDER BY', "WHERE tenant_id = '$tenant_id' ORDER BY", $query);
        $query = str_replace('GROUP BY', "WHERE tenant_id = '$tenant_id' GROUP BY", $query);
    }
    
    return $query;
}


?>
