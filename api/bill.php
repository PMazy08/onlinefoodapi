<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//SELECT item.bill_id, food.name, item.amount, food.price FROM item JOIN bill on bill.b_id = item.bill_id JOIN food on item.food_id = food.f_id WHERE bill.cus_id = 4 AND bill.name != '' AND bill.phone != '' ORDER BY bill.b_id ASC;
$app->get('/bill/order/item/{u_id}/{b_id}', function (Request $request, Response $response,$args) {
    $conn = $GLOBALS['conn'];
    $id = $args['u_id'];
    $bid = $args['b_id'];
    $sql = "SELECT item.bill_id, food.name, item.amount, food.price FROM item JOIN bill on bill.b_id = item.bill_id JOIN food on item.food_id = food.f_id WHERE bill.cus_id = ? AND bill.name != '' AND bill.phone != '' AND item.bill_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $id,$bid);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});




//order
$app->get('/bill/order/{u_id}', function (Request $request, Response $response,$args) {
    $conn = $GLOBALS['conn'];
    $id = $args['u_id'];
    $sql = "SELECT * FROM bill WHERE bill.cus_id = ? AND bill.name != '' AND bill.phone != ''";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

//basic
$app->get('/bill', function (Request $request, Response $response) {
    $conn = $GLOBALS['conn'];
    $sql = 'SELECT * FROM bill ORDER BY b_id DESC LIMIT 1';
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

//chk 
$app->get('/bill/chk/{u_id}', function (Request $request, Response $response,$args) {
    $conn = $GLOBALS['conn'];
    $id = $args['u_id'];
    $sql = 'SELECT * FROM bill WHERE cus_id = ? ORDER BY b_id DESC LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});


$app->post('/bill', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);

    $conn = $GLOBALS['conn'];
    $sql = 'insert into bill (name,address,phone,sum,totalprice,status,cus_id) values (?,?,?,?,?,?,?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssiisi',  $jsonData['name'], $jsonData['address'], $jsonData['phone'],$jsonData['sum'], $jsonData['totalprice'], $jsonData['status'], $jsonData['cus_id']) ;
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {

        $data = ["affected_rows" => $affected, "last_idx" => $conn->insert_id];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});

$app->put('/bill/{id}', function (Request $request, Response $response, $args) {

    $json = $request->getBody();
    $jsonData = json_decode($json, true);
    $id = $args['id'];
    $conn = $GLOBALS['conn'];
    $sql = 'update bill set name=?, address=?, phone=?,sum=?, totalprice=?,datetime =?, status=? where b_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssiissi',$jsonData['name'],$jsonData['address'],$jsonData['phone'],$jsonData['sum'],$jsonData['totalprice'],$jsonData['datetime'],$jsonData['status'], $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        $data = ["affected_rows" => $affected];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});


?>