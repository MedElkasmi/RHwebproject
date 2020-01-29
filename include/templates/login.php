<?php  

session_start();
if(isset($_SESSION['username']))
{
    header('location :panel.php');
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
        $user = $_POST['user'];
        $pass = $_POST['pass'];


        $stmt = pg_prepare($con,"myquery","SELECT * FROM admins WHERE username = '$user' AND password = '$pass'");
        $stmt = pg_execute($con,"myquery",array());
        $result = pg_fetch_array($stmt,null,PGSQL_ASSOC);
        $rowcount =pg_num_rows($stmt);

        if($rowcount == 1 )
        {

            $_SESSION['username'] = $user;
            echo $_SESSION['username'];
            header('location:panel.php');
            exit();
        }
         else $errorlogin = "<br><div class='container alert alert-danger' style='width: 50%;font-size:20px;'>
                            <i class='fa fa-exclamation-triangle'></i> Information incorrect</div>";
}?>

<body id="login">
    <div class="leftHalf">
        <div class="content">
            <h1><i class="fa fa-undo"></i>SUPFILES OFFER MANAGER</h1>
        </div>
    </div>
    <div class="righthalf">
        <h1 style="text-align:center;margin-top:32%;"><i class="fa fa-dashboard"></i> Login into Your Dashboard</h1>
        <?php if(isset($errorlogin)) echo $errorlogin ?>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="form-group">
                <input type="text" class="form-control" id="email" placeholder="Username" name="user">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" id="pwd" placeholder="Password" name="pass">
            </div>
            <button type="submit" class="btn btn-info">Connection <i class="fa fa-plug"></i></button>
        </form>
    </div>

    <footer>
        <span>Honest Media. All right reserved2222 &copy;</span>
    </footer>

</body>