<?php

//Total Employees
function total()
{
    global $con;
    $stmt2 = pg_prepare($con,"count","SELECT COUNT(id_employee) FROM employee_info");
    $stmt2 = pg_execute($con,"count",array());
    $total = pg_fetch_row($stmt2);
    echo $total[0];
}

//Session hello
function welcome()
{
    include '../dashboard/config.php';
    echo $_SESSION['username'];
}

function is_there($value) 
{
    include '../dashboard/config.php';
    $stmt4 = pg_prepare($con,"isthere","SELECT * FROM employee_info WHERE nom_prenom = '$value' LIMIT 1");
    $stmt4 = pg_execute($con,"isthere",array());
    $res = pg_fetch_array($stmt4);
    $hasrow = pg_num_rows($stmt4);
    return $hasrow;
}

function bring_value($col,$funid)
{
     // bring old value before updating
     global $con;
     $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
     $stmt5=pg_prepare($con,$funid,"SELECT $col FROM employee_info WHERE id='$id'");
     $stmt5=pg_execute($con,$funid,array());
     $res = pg_fetch_array($stmt5,null,PGSQL_ASSOC);

      echo $res[$col];
     
}

function holidays($strname)
{
    global $con;
    $stmt9 = pg_prepare($con,$strname,"SELECT count(employee) FROM vacation;");
    $stmt9 = pg_execute($con,$strname,array());
    $res4 =  pg_fetch_result($stmt9,0);

    return $res4;
    
}

function holidays_show($strname)
{
    global $con;
    $stmt9 = pg_prepare($con,$strname,"SELECT count(id) FROM vacation WHERE vacation_status = 'Active'");
    $stmt9 = pg_execute($con,$strname,array());
    $res4 =  pg_fetch_row($stmt9);
    echo $res4[0];  
}

function vacation_status()
{
    global $con;
    $stmt = pg_prepare($con,"t","SELECT vacation_status FROM vacation WHERE vacation_status = 'Active'");
    $stmt = pg_execute($con,"t",array());
    $res_vacation = pg_fetch_result($stmt,0,0);
    return $res_vacation;
}


function bulletien_cal($employee)
{
    // Some vars require to be dynamic inputs
    global $con;
    $stmt = pg_prepare($con,"cal","SELECT MAX(salaire_brut) from salary_history where employee = '$employee'");
    $stmt = pg_execute($con,"cal",array());
    $res_cal = pg_fetch_result($stmt,0);

    $Sbase = $res_cal;
    $tauxAMO = (2.26/100);
    $tauxCNSS = (4.48/100);
    $tauxIGR = (10.00/100);
    $tauxProf = (20.00/100);

    $FRAIS = ($Sbase * $tauxProf);
    $RTAMO = $Sbase * $tauxAMO;
    $RTCNSS = $Sbase * $tauxCNSS;
    $RT = $RTAMO + $RTCNSS + $FRAIS;
    $imposable = $Sbase - $RT;
    $RTIGR = ($imposable * $tauxIGR) - 250;
    $cumlRetenue = $RTAMO+$RTCNSS+$RTIGR;
    $SNet = $Sbase - $cumlRetenue;

    return array(

        number_format($RTAMO,2,",",""),
        number_format($RTCNSS,2,",",""),
        number_format($RTIGR,2,",",""),
        $Sbase,
        number_format($cumlRetenue,2,",",""),
        number_format($imposable,2,","," "),
        number_format($SNet,2,","," ")
    );
}

function Net_Salary($Sbrut)
{
    $Sbase = $Sbrut;
    $tauxAMO = (2.26/100);
    $tauxCNSS = (4.48/100);
    $tauxIGR = (10.00/100);
    $tauxProf = (20.00/100);

    $FRAIS = ($Sbase * $tauxProf);
    $RTAMO = $Sbase * $tauxAMO;
    $RTCNSS = $Sbase * $tauxCNSS;
    $RT = $RTAMO + $RTCNSS + $FRAIS;
    $imposable = $Sbase - $RT;
    $RTIGR = ($imposable * $tauxIGR) - 250;
    $cumlRetenue = $RTAMO+$RTCNSS+$RTIGR;
    $SNet = $Sbase - $cumlRetenue;

    return $SNet;
}

function vacation_total_days($employee)
{
    global $con;
    $ran = rand(1,900000);
    $stmt = pg_prepare($con,$ran,"SELECT num_days,selected_day FROM vacation WHERE employee = '$employee' and vacation_status = 'Over'");
    $stmt = pg_execute($con,$ran,array());
    $res_vacation_days = pg_fetch_row($stmt);
    $new_numbre_day = $res_vacation_days[0] - $res_vacation_days[1];
    
    $ran2 = rand(1,900000);
    $stmt2 = pg_prepare($con,$ran2,"UPDATE vacation SET num_days = '$new_numbre_day',selected_day = 0 WHERE employee = '$employee' and vacation_status = 'Over'");
    $stmt2 = pg_execute($con,$ran2,array());
    
}

function total_mailers()
{
    global $con;
    $stmt_total_mailers= pg_prepare($con,"total_mailer","SELECT count(id_employee) FROM employee_info WHERE qualification = 'Mailer'");
    $stmt_total_mailers  = pg_execute($con,"total_mailer",array());
    $res_total_mailers = pg_fetch_row($stmt_total_mailers);
    return $res_total_mailers[0];
}

function total_IT()
{
    global $con;
    $stmt_total_IT= pg_prepare($con,"total_IT","SELECT count(id_employee) FROM employee_info WHERE qualification = 'IT'");
    $stmt_total_IT  = pg_execute($con,"total_IT",array());
    $res_total_IT = pg_fetch_row($stmt_total_IT);
    return $res_total_IT[0];
}

function total_teamleaders()
{
    global $con;
    $stmt_total_leaders= pg_prepare($con,"total_teamleader","SELECT count(id_employee) FROM employee_info WHERE qualification = 'TeamLeader'");
    $stmt_total_leaders  = pg_execute($con,"total_teamleader",array());
    $res_total_leaders = pg_fetch_row($stmt_total_leaders);
    return $res_total_leaders[0];
}

function total_offerManager()
{
    global $con;
    $stmt_total_offerManager= pg_prepare($con,"total_offerManager","SELECT count(id_employee) FROM employee_info WHERE qualification = 'Offer_Manager'");
    $stmt_total_offerManager  = pg_execute($con,"total_offerManager",array());
    $res_total_offerManager = pg_fetch_row($stmt_total_offerManager);
    return $res_total_offerManager[0];
}

function total_security()
{
    global $con;
    $stmt_total_security= pg_prepare($con,"total_security","SELECT count(id_employee) FROM employee_info WHERE qualification = 'Security'");
    $stmt_total_security  = pg_execute($con,"total_security",array());
    $res_total_security  = pg_fetch_row($stmt_total_security);
    return $res_total_security[0];
}

//check admin
function check_admin_existance($check_admin)
{
    global $con;
    $stmt_check_admin = pg_prepare($con,"check_admin","SELECT username FROM admins WHERE username = '$check_admin'");
    $stmt_check_admin = pg_execute($con,"check_admin",array());
    $res_check_admin = pg_fetch_row($stmt_check_admin);

    return $res_check_admin[0];

}

//insert admin to db
function insert_admin($user,$pass)
{
    global $con;
    $stmt_insert_admin = pg_prepare($con,"insert_admin","INSERT INTO admins VALUES('$user','$pass',0,'No',now())");
    $stmt_insert_admin = pg_execute($con,"insert_admin",array());
}

function redirecttable($msg='',$delay=1)
{
    header("refresh:$delay;url=employes.php?go=Manage");
}

function redirecthome($msg='',$delay=3)
{
    header("refresh:$delay;url=employes.php?go=add");
}

function redirect_vacation($msg='',$delay=3)
{
    header("refresh:$delay;url=employes.php?go=holiday");
}

function redirect_salary($msg='',$delay=3)
{
    header("refresh:$delay;url=employes.php?go=Salary_history");
}

?>