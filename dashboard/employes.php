<?php 

session_start();
if(isset($_SESSION['username']))
{
    include '../dashboard/init.php';
    $go = isset($_GET['go']) ? $_GET['go'] : 'Manage';
// Manage page ===================================================================================================================
    if($go=='Manage')
    {
        include "$tpl/content.php";
        $stmt = pg_prepare($con,"show","SELECT * FROM employee_info ORDER BY id_employee ASC");
        $stmt = pg_execute($con,"show",array());
        $result = pg_fetch_all($stmt);
        $hasrow = pg_num_rows($stmt);
?>
        
        <div class="manage container main-table table-responsive">
            <!-- Resorce Charts -->
            <?php
            $total_mailer = total_mailers();
            $total_IT = total_IT();
            $total_leaders = total_teamleaders();
            $total_offerManager = total_offerManager();
            $total_security = total_security();
            $dataPoints = array(
                array("label"=> "TeamLeaders", "y"=> $total_leaders),
                array("label"=> "Maillers", "y"=> $total_mailer),
                array("label"=> "IT Team", "y"=> $total_IT),
                array("label"=> "Offer Manager", "y"=> $total_offerManager),
                array("label"=> "Security", "y"=> $total_security)
            );?>
            <script>
            window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                    animationEnabled: true,
                    title:{
                            text: "Human Resources",
                            fontFamily: "tahoma",
                            padding: 10,
                    },
                    data: [{
                            type: "pie",
                            legendText: "{label}",
                            indexLabelFontSize: 20,
                            indexLabel: "{label} - #percent%",
                            yValueFormatString: "#,##0",
                            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                    }]
            });
            chart.render();
            
            } // end of function
            </script>
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
            <!-- End Resources Charts -->                            
 
            
            <table class="table table-bordered text-center">
            <h1>List of Employes</h1>
            <tr>
                <td>Full Name</td>
                <td>Start date</td>
                <td>CNSS</td>
                <td>Naissance</td>
                <td>Email</td>
                <td>Phone</td>
                <td>Skills</td>
                <td>Gender</td>
                <td>Entity</td>
                <td>Control</td>
            </tr>
            <tr>
            <?php 
                if ($hasrow > 0)
                { foreach($result as $row)
                    {
                        echo '<tr>';
                        echo '<td>'.$row['nom_prenom'].'</td>';
                        echo '<td>'.$row['date_embauche'].'</td>';
                        echo '<td>'.$row['ncnss'].'</td>';
                        echo '<td>'.$row['date_naissance'].'</td>';
                        echo '<td>'.$row['email'].'</td>';
                        echo '<td>'.$row['phone'].'</td>';
                        echo '<td>'.$row['qualification'].'</td>';
                        echo '<td>'.$row['genre'].'</td>';
                        echo '<td>'.$row['entite'].'</td>';
                        echo "<td><a href='employes.php?go=delete&id=" .$row['id_employee']. "'class='btn btn-danger confirm'><i class='fa fa-ban'></i> Inactive</a>
                                    <br><br><a href='employes.php?go=update&id=" .$row['id_employee']. "' class='btn btn-info'><i class='fa fa-edit'></i> Edit</a></td>";
                        echo '</tr>';
                    }
                } else echo "<div class='alert alert-danger'>No Data Available</div>";             
            ?>
              </tr>
            </table>
        </div>
  <?php  }// end of manage pgae ===========================================================================================================
// Add page ===================================================================================================================
   elseif($go == 'add')
    {       
        include "$tpl/content.php";
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {   
            $fname = $_POST['fname'];
            $start = $_POST['start'];
            $CNSS = $_POST['CNSS'];
            $naissance = $_POST['naissance'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            //Valication input errors
            if(isset($_POST['skills']))
            {
                $skills = $_POST['skills'];
            }
            if(isset($_POST['gender']))
            {
                $gender = $_POST['gender'];
            }
            if(isset($_POST['entite']))
            {
                $entite = $_POST['entite'];
            }
            $errorlist = array();
            if(strlen($fname) < 4 || empty($fname))
            {
                $errorlist[] = "name must be more then 4 caracteres";
            }
            if(!is_numeric($CNSS))
            {
                $errorlist[] = "Cnss must be nemuric";
            }
            if(empty($start))
            {
                $errorlist[] = "date start is not set";
            }
            if(empty($naissance))
            {
                $errorlist[] = "date birthday is not set";
            }
            if(!is_numeric($phone))
            {
                $errorlist[] = "phone must be nemuric";
            }
            if(empty($skills))
            {
                $errorlist[] = "Skills field is empty";
            }
            if(empty($gender))
            {
                $errorlist[] = "Gender field is empty";
            }
            if(empty($entite))
            {
                $errorlist[] = "Entite field is empty";
            }

            foreach($errorlist as $error)
            {
                echo "<div class='container alert alert-danger'>".$error."</div>";
            }

            if(empty($errorlist))
            {
                $value = $fname;
                if(is_there($value) == 1)
                {
                    echo "<div class='container alert alert-danger'>This employee is already exist</div>";
                }
                else{
                    $stmt = pg_prepare($con,"newadd","INSERT INTO 
                    employee_info(nom_prenom,date_embauche,ncnss,date_naissance,email,phone,qualification,genre,entite)
                    VALUES('$fname','$start','$CNSS','$naissance','$email','$phone','$skills','$gender','$entite')");
        
                    $stmt=pg_execute($con,"newadd",array());
                    $status = pg_result_status($stmt);
                        if($status == PGSQL_COMMAND_OK)
                        {
                            $msg='An employee has been added'; 
                            $delay=3;              
                            redirecthome($msg);
                            echo "<div class='container alert alert-info'>".$msg."</div>";
                            echo "<div class='container alert alert-info'>You will be redirect in ".$delay."</div>";
                        }
                    }
                }// end if array check

            } // end request method ?>

            <div id="respond" class="container">
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="form-group">
                        <h1><i class="fa fa-id-card"></i> New Employee Information :</h1>
                        <h3>Nom Complet</h3> 
                        <input type="text" class="form-control" placeholder="Nom complet" name="fname">
                        <i class="fa fa-user"></i>
                        <br>

                        <h3>Date d'embauche</h3> 
                        <input type="date" class="form-control" placeholder="Date d'embauche" name="start">
                        <i class="fa fa-calendar"></i>
                        <br>

                        <h3>Numero CNSS</h3> 
                        <input type="text" class="form-control" placeholder="N C.N.S.S" name="CNSS">
                        <i class="fa fa-file"></i>
                        <br>

                        <h3>Date de Naissance</h3> 
                        <input type="date" class="form-control" placeholder="Date de naissance" name="naissance">
                        <i class="fa fa-calendar"></i>
                        <br>

                        <h3>Email</h3> 
                        <input type="email" class="form-control" placeholder="Entre email" name="email">
                        <i class="fa fa-at"></i>
                        <br>

                        <h3>Numero Telephone</h3> 
                        <input type="text" class="form-control" placeholder="Entre telephone" name="phone">
                        <i class="fa fa-phone"></i>
                        <br>

                        <div class="radiobox">
                            <h3>Qualification</h3> 
                            <input type="radio" name="skills" value="TeamLeader"> TeamLeader<br>
                            <input type="radio" name="skills" value="Mailer"> Mailer<br>
                            <input type="radio" name="skills" value="Offer_Manager"> Offer Manager<br>
                            <input type="radio" name="skills" value="Security"> Security<br>
                            <input type="radio" name="skills" value="IT"> IT<br>
                        </div>
                        <hr>
                        <div class="radiobox">
                            <h3>Gender</h3> 
                            <input type="radio" name="gender" value="Female"> Female<br>
                            <input type="radio" name="gender" value="Male"> Male<br>
                        </div>
                        <hr>
                        <div class="radiobox">
                            <h3>Entity</h3>
                            <input type="radio" name="entite" value="IT TEAM"> IT TEAM<br>
                            <input type="radio" name="entite" value="Support"> Support<br>
                            <input type="radio" name="entite" value="HM1"> HM1<br>
                            <input type="radio" name="entite" value="HM2"> HM2<br>
                            <input type="radio" name="entite" value="HM3"> HM3<br>
                            <input type="radio" name="entite" value="HM4"> HM4<br>
                            <input type="radio" name="entite" value="HM5"> HM5<br>
                            <input type="radio" name="entite" value="HM6"> HM6<br>
                            <input type="radio" name="entite" value="HM7"> HM7<br>
                            <input type="radio" name="entite" value="HM8"> HM8<br>
                        </div>
                        <hr>
                        <br>

                    </div><!-- end of form-group div -->
                    <button type="submit" class="btn btn-info">Valid</button>
                </form>
            </div>
    
 <?php }// end add page ======================================================================================================================

    elseif($go == 'info_print')
    {       
// info  page ======================================================================================================================
        include "$tpl/content.php";
        if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                if(isset($_POST['find']))
                {
                    $find = $_POST['find'];
                }
                                    
                $stmt = pg_prepare($con,"find","SELECT * FROM employee_info WHERE nom_prenom = '$find'");
                $stmt = pg_execute($con,"find",array());
                $result = pg_fetch_array($stmt,null,PGSQL_ASSOC);
                // Check if an employee reapetion 
                if($find == $result['nom_prenom'])
                {
                    echo '<div class="container alert alert-info">Data has been found</div>';
                } else echo '<div class="container alert alert-danger">Employee not found</div>';
            } // end request method
            else $find=''; ?>

            <div id="block-print" class="container">
                <form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="form-group find-form">
                        <input type="text" name="find">
                        <button type="submit">Preview</button>
                    </div>
                 </form>
                <h2>bulletin de paie</h2>
            </div>

            <div id="block-print" class="container">
            <?php 
            $cal = bulletien_cal($find); 
            ?>
                <table class="table table-bordered text-center" >
                    <thead>
                        <tr style="background-color:#e91e63;font-size:18px;color:white;">
                            <th colspan="2">Honest Media SARL, App 2 eme<br>
                            Etage Rue Mohamed Zerektouni<br>
                            Impasse Mohamed Azzouzi - <br>
                            Alhoceima//CNSS : 9817810<br>
                            Tel : 0539 84 12 33</th>
                            <th colspan="2">BULLETIEN DE PAIE</th>
                            <th colspan="3">HONEST MEDIA</th>
                        </tr>
                    </thead>
                    <tr style="background-color:#999;color:white">
                        <td colspan="2">Non & Prenom</td>
                        <td colspan="2">Qualification</td>
                        <td>M/H</td>
                        <td>Matricule</td>
                    </tr>
                    <tr>
                        <td colspan="2"><?php if(isset($result['nom_prenom'])) echo $result['nom_prenom'];?></td>
                        <td colspan="2"><?php if(isset($result['qualifation'])) echo $result['qualifation'];?></td>
                        <td><?php if(isset($result['genre'])) echo $result['genre'];?></td>
                        <td></td>
                    </tr>
                    <tr style="background-color:#999;color:white">
                        <td colspan="2">Date d'embauche</td>
                        <td>N C.N.S.S</td>
                        <td>Date de Naissance</td>
                        <td>Salaire de base</td>
                        <td>Periode de Paie</td>
                    </tr>
                    <tr>
                        <td colspan="2" ><?php if(isset($result['date_embauche'])) echo $result['date_embauche'];?></td>
                        <td><?php if(isset($result['ncnss'])) echo $result['ncnss'];?></td>
                        <td><?php if(isset($result['date_naissance'])) echo $result['date_naissance'];?></td>
                        <td><?php if(isset($result['salaire_de_base'])) echo $result['salaire_de_base'];?></td>
                        <td><?php $date = getdate(); echo $date['month']; echo $date['year']; ?></td>
                    </tr>
                    <tr style="background-color:#999;color:white">
                        <td colspan="2">Libelle</td>
                        <td>Base/Nombre</td>
                        <td>Taux</td>
                        <td>A Payer</td>
                        <td>A Retenir</td>
                    </tr>
                    <tr>

                        <td colspan="2">Salaire<br><br>Salaire brut<br><br>Retenue AMO <br><br>Retenue C.N.S.S<br><br>Retenue I.G.R<br><br>Primes</td>
                        <td><?php echo $cal[3];?><br><br><?php echo $cal[3];?><br><br><?php echo $cal[3];?><br><br><?php echo $cal[3];?><br><br><?php echo $cal[5];?></td>
                        <td>26j<br><br> <br><br>2.26%<br><br>4.48%<br><br>10.00%</td>
                        <td><?php echo $cal[3];?></td>
                        <td><br><br> <br><br><?php echo $cal[0];?><br><br><?php echo $cal[1];?><br><br><?php echo $cal[2];?></td>
                    </tr>
                    <tr>
                        <td colspan="2"> </td>
                    </tr>
                    <tr style="background-color:#999;color:white">
                        <td>Cuml J</td>
                        <td>Cuml base conges</td>
                        <td colspan="2" style="background-color:white;border:none;"></td>
                        <td>Cuml Gains</td>
                        <td>Cuml Retenue</td>
                    <tr >
                        <td>26.00</td>
                        <td>0.00</td>
                        <td colspan="2" style="background-color:white;border:none;"></td>
                        <td><?php echo $cal[3];?></td>
                        <td><?php echo $cal[4];?></td>
                    </tr>
                    <tr>
                        <td rowspan="2"  colspan="4">Paye Par Virement</td>
                        <td>Signature</td>
                        <td>Net a paye</td>
                        
                    </tr> 
                    <tr>
                        <td> </td>
                        <td><?php $NetSalaire = $cal[6]; echo $cal[6];?></td>  
                    </tr>                
                </table>
            </div><!--end div -->

   <?php }// end info page ======================================================================================================================

// This page base on Manage somewhere outthere, just look up for finding it **********************************************
        elseif($go =='delete')
        {
            $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
            $stmt = pg_prepare($con,"findid","SELECT * FROM employee_info WHERE id_employee = '$id' LIMIT 1");
            $stmt = pg_execute($con,"findid",array());
            $res = pg_num_rows($stmt);

            if($res == 1)
            {
                $stmt2 = pg_prepare($con,"archive","INSERT INTO emp_archive SELECT * FROM employee_info WHERE id_employee = '$id'");
                $stmt2 = pg_execute($con,"archive",array());
                $stmt = pg_prepare($con,"delete","DELETE FROM employee_info WHERE id_employee = '$id'");
                $stmt = pg_execute($con,"delete",array());
                $msg='You will be redirect to Manage Page in ';           
                redirecttable($msg,$delay = 2);
                echo '<div class="container alert alert-info">Employee has been removed</div>';
                echo "<div class='container alert alert-info'>".$msg. $delay.' second'."</div>";
               
            }

            else echo "No such id found";
        }//end of delete page ******************************************************************************************************

// This page based on Insert page and Manage page Role is The updating of an record ****************************************
        elseif($go == 'update')
        {
            include "$tpl/content.php";
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {   
                $fname = $_POST['fname'];
                $start = $_POST['start'];
                $CNSS = $_POST['CNSS'];
                $naissance = $_POST['naissance'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                if(isset($_POST['skills']))
                {
                    $skills = $_POST['skills'];
                }
                if(isset($_POST['gender']))
                {
                    $gender = $_POST['gender'];
                }
                if(isset($_POST['entite']))
                {
                    $entite = $_POST['entite'];
                }

                // Errors Validation 
                $errorlist = array();
                if(strlen($fname) < 4 || empty($fname))
                {
                    $errorlist[] = "name must be more then 4 caracteres";
                }
                if(!is_numeric($CNSS))
                {
                    $errorlist[] = "Cnss must be nemuric";
                }
                if(empty($start))
                {
                    $errorlist[] = "date start is not set";
                }
                if(empty($naissance))
                {
                    $errorlist[] = "date birthday is not set";
                }
                if(!is_numeric($phone))
                {
                    $errorlist[] = "phone must be nemuric";
                }
                if(empty($skills))
                {
                    $errorlist[] = "Skills field is empty";
                }
                if(empty($gender))
                {
                    $errorlist[] = "Gender field is empty";
                }
                if(empty($entite))
                {
                    $errorlist[] = "Entite field is empty";
                }

                //List of errors when they Occur 
                foreach($errorlist as $error)
                {
                    echo "<div class='container alert alert-danger'>".$error."</div>";
                    
                }

                if(empty($errorlist))
                {
                    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
                    $stmt = pg_prepare($con,"upgrade","UPDATE employee_info SET nom_prenom = '$fname',date_embauche = '$start',ncnss = '$CNSS',
                    date_naissance = '$naissance',email = '$email',phone = '$phone',qualifation = '$skills',genre = '$gender',entite = '$entite' WHERE id = '$id'");

                    $stmt=pg_execute($con,"upgrade",array());
                

                    echo "<div class='container alert alert-info'>Employee has been updated.</div>";
                }// end of array list

            } ?>

            <div id="respond" class="container">
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="form-group">
                        <h1><i class="fa fa-id-card"></i> Update Employee Information :</h1>
                        <h3>Nom Complet</h3> 
                        <input type="text" class="form-control" name="fname" value="<?php bring_value('nom_prenom',"na"); ?>">
                        <i class="fa fa-user"></i>
                        <br>
                        <h3>Date d'embauche</h3> 
                        <input type="date" class="form-control" name="start" value="<?php bring_value('date_embauche',"emba"); ?>">
                        <i class="fa fa-calendar"></i>
                        <br>
                        <h3>Numero CNSS</h3> 
                        <input type="text" class="form-control" name="CNSS" value="<?php bring_value('ncnss',"cnss"); ?>">
                        <i class="fa fa-file"></i>
                        <br>
                        <h3>Date de Naissance</h3> 
                        <input type="date" class="form-control" name="naissance" value="<?php bring_value('date_naissance',"naissance"); ?>">
                        <i class="fa fa-calendar"></i>
                        <br>
                        <h3>Email</h3> 
                        <input type="email" class="form-control" name="email" value="<?php bring_value('email',"ema"); ?>">
                        <i class="fa fa-at"></i>
                        <br>
                        <h3>Numero Telephone</h3> 
                        <input type="text" class="form-control" name="phone" value="<?php bring_value('phone',"ph"); ?>">
                        <i class="fa fa-phone"></i>
                        <br>
                        <div class="radiobox">
                            <h3>Qualification</h3> 
                            <input type="radio" name="skills" value="TeamLeader"> TeamLeader<br>
                            <input type="radio" name="skills" value="Mailer"> Mailer<br>
                            <input type="radio" name="skills" value="IT"> IT<br>
                        </div>
                        <hr>
                        <div class="radiobox">
                            <h3>Gender</h3> 
                            <input type="radio" name="gender" value="Female"> Female<br>
                            <input type="radio" name="gender" value="Male"> Male<br>
                        </div>
                        <hr>
                        <div class="radiobox">
                            <h3>Entity</h3> 
                            <input type="radio" name="entite" value="HM2"> HM2<br>
                            <input type="radio" name="entite" value="HM3"> HM3<br>
                            <input type="radio" name="entite" value="HM4"> HM4<br>
                            <input type="radio" name="entite" value="HM5"> HM5<br>
                            <input type="radio" name="entite" value="HM6"> HM6<br>
                            <input type="radio" name="entite" value="HM7"> HM7<br>
                        </div>
                        <hr>
                        <br>

                    </div>
                    <button type="submit" class="btn btn-info">Valid</button>
                </form>
            </div> 
     <?php   } // End of Update page ********************************************************************************************************
// Archive page or backup *******************************************************************************************************************
        elseif($go == 'document')
        { 
            include "$tpl/content.php";
            $stmt = pg_prepare($con,"show","SELECT * FROM emp_archive ORDER BY id ASC");
            $stmt = pg_execute($con,"show",array());
            $result = pg_fetch_all($stmt);
            $hasrow = pg_num_rows($stmt);
        ?>

            <div class="document container">
            <h1>Archive</h1>
                    <table class="table table-bordered text-center">
                    <tr>
                        <td>Full Name</td>
                        <td>Start date</td>
                        <td>CNSS</td>
                        <td>Naissance</td>
                        <td>Email</td>
                        <td>Phone</td>
                        <td>Skills</td>
                        <td>Gender</td>
                        <td>Entity</td>
                    </tr>
                    <tr>
                    <?php 
                        if ($hasrow > 0)
                        { foreach($result as $row)
                            {
                                echo '<tr>';
                                echo '<td>'.$row['nom_prenom'].'</td>';
                                echo '<td>'.$row['date_embauche'].'</td>';
                                echo '<td>'.$row['ncnss'].'</td>';
                                echo '<td>'.$row['date_naissance'].'</td>';
                                echo '<td>'.$row['email'].'</td>';
                                echo '<td>'.$row['phone'].'</td>';
                                echo '<td>'.$row['qualification'].'</td>';
                                echo '<td>'.$row['genre'].'</td>';
                                echo '<td>'.$row['entite'].'</td>';
                            }
                        } else echo "<div class='alert alert-danger'>No Data Available</div>";             
                    ?>
                    </tr>
                    </table>
            </div>
    <?php } // End of Archive page
// Vacation page, Average script can calculate and estimate some numbers for ya, just don't break it, he is so emotional.
    elseif($go == 'holiday')
    { 
        include "$tpl/content.php";
            $stmt8 = pg_prepare($con,"vacationdate","SELECT * FROM vacation order by id ASC");
            $stmt8 = pg_execute($con,"vacationdate",array());
            $stmt_employee = pg_prepare($con,"employee_search","SELECT employee FROM vacation WHERE vacation_status = 'Over'");
            $stmt_employee = pg_execute($con,"employee_search",array());
            $res3 = pg_fetch_all($stmt8);
            $res_employee = pg_fetch_row($stmt_employee);
            $hasrow = pg_num_rows($stmt8);

            if(empty($res_employee))
            {
                $res_employee= 'no_employee';
                
            }
            else{
                $new_res_employee= implode(",",$res_employee);
                vacation_total_days($new_res_employee);
            }
            // testing new method
            $nbr = holidays("test");

        
              

            for($i=0;$i<$nbr;$i++)
            {

                $ran = rand(1,9000000);
                $stmt18 = pg_prepare($con,$ran,"SELECT employee FROM vacation");
                $stmt18 = pg_execute($con,$ran,array());
                $res13 = pg_fetch_row($stmt18,$i);
                $newres13 = implode(",",$res13);

                

                $ran7 = rand(1,400000);
                $stmt10 = pg_prepare($con,$ran7,"SELECT vacation_end FROM vacation");
                $stmt10 = pg_execute($con,$ran7,array());
                $res = pg_fetch_row($stmt10,$i);
                $newres = implode(",",$res);
            
                $ran2 = rand(1,100000);
                $stmt9 = pg_prepare($con,$ran2,"UPDATE vacation 
                SET vacation_estimated = age(cast('$newres' as date),now()), today = now() 
                where employee = '$newres13' and vacation_status = 'Active'");
                $stmt9 = pg_execute($con,$ran2,array());

                if($res_vacation= "Active")
                {
                    $vacation_status = "Over";
                    $ran3 = rand(1,900000);
                    $stmt = pg_prepare($con,$ran3,"UPDATE vacation SET vacation_status = '$vacation_status', vacation_estimated = age(now(),now())  WHERE  vacation_end <= now()");
                    $stmt = pg_execute($con,$ran3,array());
                }
            }

          
            

            
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            //Duration validation
            if(isset($_POST['1days']))
            {
                $day1 = $_POST['1days'];
            }

            if(isset($_POST['7days']))
            {
                $day7 = $_POST['7days'];
            }
            if(isset($_POST['14days']))
            {
                $day14 = $_POST['14days'];
            }
            
            if(isset($_POST['find']))
            {
                $mailer = $_POST['find'];  
            }

            if(isset($_POST['id']))
            {
                $idcheck = $_POST['id'];  
            }

            if(isset($_POST['nbrday']))
            {
                $num_days = $_POST['nbrday'];  
            }

            if(isset($_POST['apply']))
            {
                $apply_day = $_POST['apply'];  
            }

            if(empty($apply_day))
            {
                $apply_day =0;
            }

            if(empty($num_days))
            {
                $stmt6 = pg_prepare($con,"bring_num_days","SELECT num_days FROM vacation WHERE employee = '$mailer'");
                $stmt6 = pg_execute($con,"bring_num_days",array());
                $num_days = pg_fetch_result($stmt6,0);
            } 
            

                $stmt6 = pg_prepare($con,"vacation","SELECT nom_prenom,qualification FROM employee_info WHERE nom_prenom = '$mailer'");
                $stmt6 = pg_execute($con,"vacation",array());
                $res = pg_fetch_array($stmt6,null,PGSQL_ASSOC);
                $hasrow = pg_num_rows($stmt6);
                $employee = $res['nom_prenom'];
                $skills = $res['qualification'];

            if(isset($_POST['1days'])=='1')
            {
                $start_day=date('Y-m-d',time()+($apply_day*86400));
                $day1 = '+ 1 days';
                $end_day= date('Y-m-d',strtotime($start_day . $day1));
                $vacation_status = "Active";
                $selected_day = 1;
                
            }

            if(isset($_POST['2days'])=='2')
            {
                $start_day=date('Y-m-d',time()+($apply_day*86400));
                $day2 = '+ 2 days';
                $end_day= date('Y-m-d',strtotime($start_day . $day2));
                $vacation_status = "Active";
                $selected_day = 2;
                
            }

            if(isset($_POST['7days'])=='7')
            {
                $start_day=date('Y-m-d',time()+($apply_day*86400));
                $day7 = '+ 7 days';
                $end_day= date('Y-m-d',strtotime($start_day . $day7));
                $vacation_status = "Active";
                $selected_day = 7;
                
            }
            if(isset($_POST['14days'])=='14')
            {
                $start_day=date('Y-m-d',time()+($apply_day*86400));
                $day14 = '+ 14 days';
                $end_day= date('Y-m-d',strtotime($start_day . $day14));
                $vacation_status = "Active";
                $selected_day = 14;
            }

                if(empty($num_days))
                {
                    $hasrow = 0; 
                }
                
                if($hasrow > 0)
                {
                    
                    $stmt7 = pg_prepare($con,"vacation2","INSERT INTO vacation (employee,skills,vacation_start,vacation_end,vacation_estimated,num_days,vacation_status,today,selected_day)
                    VALUES('$employee','$skills','$start_day','$end_day',age('$end_day',now()),'$num_days','$vacation_status',now(),'$selected_day')");
                    $stmt7 = pg_execute($con,"vacation2",array());
                    $res2 = pg_fetch_array($stmt7,null,PGSQL_ASSOC);
                }

                else {
                    echo "<div class='container alert alert-warning'><i class='fa fa-exclamation-circle'></i> Perhas you forget to add numbers days,
                    Or The employee you try to add is not existe.</div>";
                    
                }
                       
            }  
    ?>

    <div class="container holidays">
    <?php
        $stmt6 = pg_prepare($con,"fetch_num_days","SELECT employee,num_days FROM vacation WHERE vacation_status = 'Active'");
        $stmt6 = pg_execute($con,"fetch_num_days",array());
        $num_days = pg_fetch_all($stmt6);
        $dataPoints = array();
        
         foreach($num_days as $rows)
         {
            array_push($dataPoints, array("label"=> $rows['employee'], "y"=> $rows['num_days']));
         }
           
        ?>
        <script>
        window.onload = function () {
        
        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            theme: "light2", // "light1", "light2", "dark1", "dark2"
            title: {
                text: "Numbers of days remaing"
            },
            data: [{
                type: "column",
                dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart.render();
        
        }
        </script>
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
        </div>
        
        <div class="container holidays">
  
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="form-group">
        <h2>Parameters :</h2>
            <h4>Duration :</h4>
            <div class="form-group">
                <input type="radio" value="1" name="1days"> 1 day<br>
                <input type="radio" value="2" name="2days"> 2 days<br>
                <input type="radio" value="7" name="7days"> 7 days<br>
                <input type="radio" value="14" name="14days"> 14 days<br>
            </div>
            <h4>Choose an employee to affect number of days :</h4>
            <input type="text" name="find">
            <br>
            <h4>Add Number of vacation day foreach employee :</h4>
            <h6>(Leave blank if an employee already existe in list)</h6>
            <input type="text" name="nbrday">
            <h4>Add number to apply the vacation  :</h4>
            <h6>(Ex : Employee wants to take one day off, after tomorrow.)</h6>
            <h6>(Leave blank if an employee want to take holiday in same day )</h6>
            <input type="text" name="apply">
            <input type="submit" value="Add To List">
        </form>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <td>Employee</td>
                    <td>Skills</td>
                    <td>Vacation Start</td>
                    <td>Vacation end</td>
                    <td>Vacation Estimated left</td>
                    <td>Total of days available</td>
                    <td>Vacation status</td>
                    <td>Control</td>
                </tr>
            </thead>
                <tr>
                <?php if(isset($hasrow) && $hasrow > 0)
                {
                    foreach($res3 as $raw)
                    {
                        echo '<tr>';
                        echo "<td>".$raw['employee']."</td>";
                        echo '<td>'.$raw['skills'].'</td>';
                        echo '<td>'.$raw['vacation_start'].'</td>';
                        echo '<td>'.$raw['vacation_end'].'</td>';
                        echo '<td>'.$raw['vacation_estimated'].'</td>';
                        echo '<td>'.$raw['num_days'].'</td>';
                        echo '<td>'.$raw['vacation_status'].'</td>';
                        echo "<td><a href='employes.php?go=delete_vacation&name=" .$raw['employee']. "
                                'class='btn btn-danger confirm'><i class='fa fa-ban'></i> Cancel</a>";
                        echo '</tr>';
                    }// end of loop
                
                }// end of Condition HasRows ?>
                    
                </tr>
        </table>
    </div>

    <?php } // end holiday page ******************************************************************************************
//Salary history, everyhting you need to keep tracking salary
elseif($go =="Salary_history")
{
    include "$tpl/content.php";
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if(isset($_POST['history']))
        {
            $mailer_history = $_POST['history'];
        }

        if(isset($_POST['sbrut']))
        {
            $mailer_brut = $_POST['sbrut'];
        }

        if(empty($_POST['sbrut']))
        {
            $mailer_brut = $_POST['sbrut'];
            $mailer_brut = 2500;
        }

    

        $stmt_info = pg_prepare($con,"info","SELECT nom_prenom,qualification,entite FROM employee_info WHERE nom_prenom = '$mailer_history'");
        $stmt_info  = pg_execute($con,"info",array());
        $res_info = pg_fetch_array($stmt_info,null,PGSQL_ASSOC);
        $hasrow = pg_num_rows($stmt_info);
        $employee_history = $res_info['nom_prenom'];
        $skills_history = $res_info['qualification'];
        $entity_history = $res_info['entite'];
        $NetSalary = Net_Salary($mailer_brut);

        if($hasrow > 0)
        {
            $stmt_history = pg_prepare($con,"history","INSERT INTO salary_history (employee,skills,entity,date_upgrade,salaire_brut,salaire_net)
            VALUES('$employee_history','$skills_history','$entity_history',now(),'$mailer_brut','$NetSalary')");
            $stmt_history = pg_execute($con,"history",array());

        } 
        else
        {
            echo "<div class='container alert alert-danger'>Employee not found</div>";  
        }

        
    }

    else {
        $stmt_table = pg_prepare($con,"info_table","SELECT * FROM salary_history");
        $stmt_table  = pg_execute($con,"info_table",array());
        $res_table = pg_fetch_all($stmt_table);
        $hasrow = pg_num_rows($stmt_table);
    }

?>

    <div class="container history">
    <?php
 
            $stmt6 = pg_prepare($con,"fetch_salaire","SELECT employee,salaire_net,date_upgrade FROM salary_history");
            $stmt6 = pg_execute($con,"fetch_salaire",array());
            $num_days = pg_fetch_all($stmt6);
            $dataPoints = array();
            
            
            foreach($num_days as $rows)
            {
                array_push($dataPoints, array("label"=> $rows['employee']." In ".$rows['date_upgrade'], "y"=> $rows['salaire_net']));
            }
                        
            ?>
            <script>
            window.onload = function () {
            
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                title:{
                    text: "Tracking salary for each employee by time",
                    fontFamily: "tahoma"
                },
                axisY: {
                    title: "Salaire Net",
                    suffix: " DH"

                },
                data: [{
                    type: "spline",
                    markerSize: 5,
                    yValueFormatString: "#,##0 DH",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            
            chart.render();
            
            }
            </script>
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    </div>

        <div class="container history">
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <h2>Parameters :</h2>
                <h4>Choose an employee to add following informations :</h4>
                <input type="text" name="history">
                <br><br>
                <h4>Add salaire brut $:</h4>
                <h6>(Leave blank if not require)</h6>
                <input type="text" name="sbrut">
                <input type="submit" value="Add To List"><br><br>
            </form>
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <td>Employee</td>
                    <td>Skills</td>
                    <td>Entity</td>
                    <td>Date_upgrade</td>
                    <td>Salaire_Brut</td>
                    <td>Salaire_Net</td>
                    <td>Control</td>
                </tr>
                </thead>    
                <tr>
                    <?php 
                    
                        if(isset($hasrow) && $hasrow > 0)
                        {
                            foreach($res_table as $row)
                                {
                                    echo '<tr>';
                                    echo '<td>'.$row['employee'].'</td>';
                                    echo '<td>'.$row['skills'].'</td>';
                                    echo '<td>'.$row['entity'].'</td>';
                                    echo '<td>'.$row['date_upgrade'].'</td>';
                                    echo '<td>'.$row['salaire_brut'].'</td>';
                                    echo '<td>'.$row['salaire_net'].'</td>';
                                    echo "<td><a href='employes.php?go=delete_salary&id=" .$row['id_salary']. "
                                        'class='btn btn-danger confirm'><i class='fa fa-trash'></i> Remove</a></td>";
                                    echo '</tr>'; 
                                }
                        }?>
                </tr>
            </table>
        </div>
<?php } // end salary_history*************************************************************************
//  New-admin*************************************************************************
        elseif($go =="New_admin")
        {
            include "$tpl/content.php";
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $username = $_POST['user'];
                $password = $_POST['pass1'];
                $re_password = $_POST['pass2'];

                $check_admins = check_admin_existance($username);

                if(isset($check_admins))
                {
                    echo '<div class="container alert alert-danger">ADMIN is alredy there</div>';
                }
                elseif($password == $re_password)
                {
                    $hash_password = md5($password);
                    insert_admin($username,$hash_password);
                    echo '<div class="container alert alert-info">ADMIN has been created</div>';
                } 
                else echo '<div class="container alert alert-warning">Password is incorrect</div>';

                
            }
            ?>

            <div class="admin container">
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="form-group">
                    <h1 style="text-align:center;"><i class="fa fa-user-circle"></i> Create new admin</h1>
                        <input type="text" class="form-control" id="email" placeholder="Username" name="user">
                        <br>
                        <input type="password" class="form-control" id="pwd" placeholder="Password" name="pass1">
                        <br>
                        <input type="password" class="form-control" id="pwd" placeholder="Password" name="pass2">
                        <br>
                        <button type="submit" class="btn btn-info">Create</button>
                    </div>
                </form>
            </div>

        <?php   }//end new_admin

        elseif($go=='list_of_admins')
        { include "$tpl/content.php";?>
        <div class="admin container">
        <h1>Management Accounts</h1>
            <table class=" table table-bordered text-center">
                <thead>
                <tr>
                    <td>Admin</td>
                    <td>Password</td>
                    <td>Privileges</td>
                    <td>Date Registration</td>
                    <td>Control</td>
                </tr>
                </thead>    
                <tr>
                    <?php     
                        $stmt_table_admin_list = pg_prepare($con,"admins_list","SELECT * FROM admins");
                        $stmt_table_admin_list  = pg_execute($con,"admins_list",array());
                        $res_table = pg_fetch_all($stmt_table_admin_list);
                        $hasrow = pg_num_rows($stmt_table_admin_list);
                    
                        if(isset($hasrow) && $hasrow > 0)
                        {
                            foreach($res_table as $row)
                                {
                                    echo '<tr>';
                                    echo '<td>'.$row['username'].'</td>';
                                    echo '<td>*********</td>';
                                    echo '<td>'.$row['privilege'].'</td>';
                                    echo '<td>'.$row['date_registration'].'</td>';
                                    echo "<td><a href='employes.php?go=delete_admin&name=" .$row['username']. "
                                        'class='btn btn-danger confirm'><i class='fa fa-trash'></i> Remove</a>";
                                    echo '</tr>'; 
                                }
                        }?>
                </tr>
            </table>
                    </div>
       <?php }

            elseif($go =='delete_vacation')
            {
                
                if(isset($_GET['name']))
                {
                    $name = $_GET['name'];
                    $stmt = pg_prepare($con,"delete_vacation","SELECT * FROM vacation WHERE employee = '$name' LIMIT 1");
                    $stmt = pg_execute($con,"delete_vacation",array());
                    $res = pg_num_rows($stmt);

                    if($res == 1)
                    {
                    $stmt = pg_prepare($con,"delete","DELETE FROM vacation WHERE employee = '$name'");
                    $stmt = pg_execute($con,"delete",array());
                    $msg='You will be redirect to Vacation Page in ';           
                    redirect_vacation($msg,$delay = 2);
                    echo '<div class="container alert alert-info">Vacation has been Cancelled</div>';
                    echo "<div class='container alert alert-info'>".$msg. $delay.' second'."</div>";
                
                    }

                    
                }

                

                else echo $_GET['name'];
            }//end if delete

            elseif($go =='delete_salary')
            {
                $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
                $stmt = pg_prepare($con,"delete_salary","SELECT * FROM salary_history WHERE id = '$id' LIMIT 1");
                $stmt = pg_execute($con,"delete_salary",array());
                $res = pg_num_rows($stmt);

                if($res == 1)
                {
                    $stmt = pg_prepare($con,"delete","DELETE FROM salary_history WHERE id = '$id'");
                    $stmt = pg_execute($con,"delete",array());
                    $msg='You will be redirect to Vacation Page in ';           
                    redirect_salary($msg,$delay = 2);
                    echo '<div class="container alert alert-info">Following Informations has been removed</div>';
                    echo "<div class='container alert alert-info'>".$msg. $delay.' second'."</div>";
                
                }

                else echo "No such id found";
            }//end if delete

            elseif($go =='delete_admin')
            {
                if(isset($_GET['name']))
                {
                    $name = $_GET['name'];
                    $stmt_remove_admin = pg_prepare($con,"delete_admin","SELECT * FROM admins WHERE username = '$name' and privilege = 'No' LIMIT 1");
                    $stmt_remove_admin = pg_execute($con,"delete_admin",array());
                    $res_remove_admin = pg_num_rows($stmt_remove_admin);

                if($res_remove_admin == 1)
                {
                    $stmt = pg_prepare($con,"delete_admins","DELETE FROM admins WHERE username = '$name'");
                    $stmt = pg_execute($con,"delete_admins",array());
                    $msg='You will be redirect to Vacation Page in ';           
                    redirect_salary($msg,$delay = 2);
                    echo '<div class="container alert alert-info">Following Informations has been removed</div>';
                    echo "<div class='container alert alert-info'>".$msg. $delay.' second'."</div>";
                
                }

                else echo "No such admins found";
                }
            }//end if delete
   } // end session?>