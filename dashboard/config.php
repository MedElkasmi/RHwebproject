<?php

        // Connect to databe
        try 
        {
         $con = pg_connect("dbname=elkasmi host=localhost user=postgres password=123456");   
          
        }

        catch(Exception $e) 
        {
                echo $e->getMessage();
        }   
?>