<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//http://localhost/onlinefoodapi/user
$app->get('/user', function (Request $request, Response $response) {
    $conn = $GLOBALS['conn'];
    $sql = 'select * from user where role = "cus"';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});


//login
//http://localhost/onlinefoodapi/user/0999999999/c1234
$app->get('/user/{phone}/{pwd}', function (Request $request, Response $response, array $args) {
    $conn = $GLOBALS['conn'];
    $phone = $args['phone'];
    $pwd = $args['pwd'];
    
    $stmt = $conn->prepare("select * from user where phone=?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();

    $data = array();
    $result = $stmt->get_result();
    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
        if($pwd == $row['password']){
            $sql = "SELECT * FROM user WHERE phone='$phone'";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                array_push($data, $row);
            }
        }else{
            die("Login Fial");
        }
    }else{
        die("Invalid email");
    }
    $json = json_encode($data);
    $response->getBody()->write($json);
    // return $response;
    return $response->withHeader('Content-Type', 'application/json');
});




?>