<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//SELECT item.bill_id, food.name, item.amount, food.price FROM item JOIN bill on bill.b_id = item.bill_id JOIN food on item.food_id = food.f_id WHERE bill.cus_id = 4 AND bill.name != '' AND bill.phone != '' ORDER BY bill.b_id ASC;


//order   
$app->get('/owner', function (Request $request, Response $response,$args) {
    $conn = $GLOBALS['conn'];
    // 
    $sql = "SELECT * FROM bill WHERE bill.name != '' AND bill.phone != ''ORDER BY bill.datetime";

    // $sql = "SELECT bill.b_id, bill.name, bill.address, bill.totalprice, bill.phone, bill.status FROM item JOIN bill on bill.b_id = item.bill_id JOIN food on item.food_id = food.f_id WHERE bill.name != '' AND bill.phone != '' ORDER BY item.i_id";
    // $sql = "SELECT bill.datetime, bill.b_id, bill.name, bill.address, bill.totalprice, bill.phone, bill.status FROM item JOIN bill on bill.b_id = item.bill_id JOIN food on item.food_id = food.f_id WHERE bill.name != '' AND bill.phone != ''AND bill.datetime is NOT null ORDER BY bill.datetime";
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

//owner item
$app->get('/owner/item/{b_id}', function (Request $request, Response $response,$args) {
    $conn = $GLOBALS['conn'];
    $bid = $args['b_id'];
    $sql = "SELECT item.bill_id, food.name, item.amount, food.price FROM item JOIN bill on bill.b_id = item.bill_id JOIN food on item.food_id = food.f_id WHERE bill.name != '' AND bill.phone != '' AND item.bill_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $bid);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});


//order bill
$app->get('/owner/bill/{b_id}', function (Request $request, Response $response,$args) {
    $conn = $GLOBALS['conn'];
    $id = $args['b_id'];
    $sql = "SELECT * FROM bill WHERE b_id = ? ";
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


$app->put('/owner/{id}', function (Request $request, Response $response, $args) {

    $json = $request->getBody();
    $jsonData = json_decode($json, true);
    $id = $args['id'];
    $conn = $GLOBALS['conn'];
    $sql = 'update bill set status=? where b_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si',$jsonData['status'], $id);
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