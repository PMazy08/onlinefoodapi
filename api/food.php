<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//http://localhost/onlinefoodapi/food
$app->get('/food', function (Request $request, Response $response) {
    $conn = $GLOBALS['conn'];
    $sql = 'select food.f_id, food.name, food.price, food.image, food.f_type, food_type.tname   from food inner join food_type on food.f_type = food_type.t_id';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

$app->get('/food/name/{name}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $sql = 'select food.f_id, food.name, food.price, food.image, food.f_type,food_type.tname  from food inner join food_type on food.f_type = food_type.t_id where food_type.tname like ?';
    $stmt = $conn->prepare($sql);
    $name = '%'.$args['name'].'%';
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        array_push($data, $row);
    }
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

?>