<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Test Elevator</title>
    </head>
    <body>
   
        <?php
        require_once 'elevator.php';
        $elevator = new my_elevator;

        if(isset($_POST['sbtFloor']) && isset($_POST['current_floor']) && isset($_POST['requested_floor'])){
       
            $requestedFloor = explode(',', $_POST['requested_floor']);
            
            $elevator->current_floor = $_POST['current_floor'];
            $elevator->request_floor = $requestedFloor;
            $elevator->maintenance(array(2, 4));
            $elevator->call_evelator();
        }
        ?>
        <form method="post" >
            Current Floor   :<input type="text" name="current_floor">  eg: 7 <br/>
            Requested Floors:<input type="text" name="requested_floor">eg: 5,3,6 <br/>
                                <input type="submit" name="sbtFloor">
        </form>
    </body>
</html>