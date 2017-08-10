<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Test Elevator</title>
    </head>
    <body>
    Current Floor is : 7 <br/>
    Request Floors are : 5,3,6 <br/><br/><br/>
    Results: <br/><br/>
        <?php
        require_once 'elevator.php';
        $elevator = new my_elevator;
        $elevator->current_floor = 7;
        $elevator->request_floor = array(5, 3, 6);
        $elevator->maintenance(array(2, 4));
        $elevator->call_evelator();
        ?>

    </body>
</html>