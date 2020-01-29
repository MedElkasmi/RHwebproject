<nav>
    <ul>
        <li class="f-left"><a href="panel.php">DASHBOARD</a></li>
        <li class="f-right hidden-xs hidden-sm"> 
        <a href="logout.php">Log out <i class="fa fa-power-off"></i></a>
        </li>
    </ul>
</nav>

<div id="sidebar" class="sidebar">
    <div class="links">
        <a href="#" class="active"><i class="fa fa-cog"></i> Setting</a>
        <a href="employes.php?go=New_admin"><i class="fa fa-user-circle"></i> Create Admin</a>
        <a href="employes.php?go=list_of_admins"><i class="fa fa-users"></"></i> List of admins</a>
        <a href="#"><i class="fa fa-globe"></i> Langaugaes</a>
    </div>
    <i class="fa fa-gear"></i>
</div>

<div class="container">
<header class="panel-block row">
        <p>Welcome, <?php welcome(); ?></p>
        <div class="col-sm-6">
            <div class="panel" style="background-color:#33b5e5;">
                    <div style="float: left;">
                        <h3>Employees</h3>
                        <h3><?php total();?></h3>
                    </div>
                    <i class="fa fa-users fa-4x" style="float:right;"></i>
                </div>
        </div>
        <div class="col-sm-6">
            <div class="panel" style="background-color:#00C851;">
                <div style="float: left;">
                    <h3>Vacation</h3>
                    <h3><?php holidays_show("vaca1"); ?></h3>
                </div>
                <i class="fa fa-plane fa-4x" style="float:right;"></i>
            </div>
        </div>
</header>

<div class="options text-center">
    <div class="row">
        <div class="col-sm-2 op" onclick="workstation()">
            <a href="employes.php?go=add">
                <i class="fa fa-user-plus fa-5x"></i>
                <h3>New Employee</h3>
            </a>
        </div>
        <div class="col-sm-2 op">
            <a href="employes.php?go=info_print">
                <i class="fa fa-print fa-5x"></i>
                <h3>Payment Check</h3>      
            </a>    
        </div>
        <div class="col-sm-3 op">
            <a href="#">
                <i class="fa fa-clipboard fa-5x"></i>
                <h3>Payment Archive</h3>
            </a>
        </div>
        <div class="col-sm-2 op">
            <a href="employes.php?go=holiday">
                <i class="fa fa-file fa-5x"></i>
                <h3>Holidays</h3>
            </a>
        </div>
        <div class="col-sm-3 op">
            <a href="employes.php?go=Manage">
                <i class="fa fa-database fa-5x"></i>
                <h3>Human Resources</h3>
            </a>
        </div>
        <div class="col-sm-3 op">
            <a href="employes.php?go=document">
                <i class="fa fa-archive fa-5x"></i>
                <h3>Archive</h3>
            </a>
        </div>
        <div class="col-sm-2 op">
            <a href="employes.php?go=Salary_history">
                <i class="fa fa-address-card fa-5x"></i>
                <h3>Salary History</h3>
            </a>
        </div>
    </div>
</div>




<div class="scroll_top">
    <i class="fa fa-angle-double-up"></i>
</div>



</div><!--container -->
