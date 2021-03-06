<nav class="navbar-default navbar-static-side" role="navigation">
    <img src="<?php echo base_url('assets/logo.jpg') ;?>" class="text-left" width="250px" height="100px" alt="logo">
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">

            <li>
                <ul class="nav nav-second-level">
                    <?php if ($this->ion_auth->in_group(['admin','manager','support'])) :?>
                        <li>
                            <a href="<?php echo base_url('users')?>">
                                <i class="fa fa-user fa-fw"></i>Staff</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('customer')?>">
                                <i class="fa fa-user-md fa-fw"></i>Konsumen</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->ion_auth->in_group(['manager'])) :?>
                        <li>
                            <a href="<?php echo base_url('service_level')?>">
                                <i class="fa fa-adjust fa-fw"></i>Service Level</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->ion_auth->in_group(['admin','manager','support'])) :?>
                        <li>
                            <a href="<?php echo base_url('perangkat')?>">
                                <i class="fa fa-apple fa-fw"></i>Perangkat</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->ion_auth->in_group(['admin','manager'])) :?>
                        <li>
                            <a href="<?php echo base_url('boq')?>">
                                <i class="fa fa-magic fa-fw"></i>BoQ</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('boq/lists')?>">
                                <i class="fa fa-list fa-fw"></i>Daftar BoQ</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->ion_auth->in_group(['manager', 'support'])) :?>
                        <li>
                            <a href="<?php echo base_url('ticket/by_device')?>">
                                <i class="fa fa-android fa-fw"></i>Perangkat Terinstal</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->ion_auth->in_group(['manager', 'support'])) :?>
                        <li>
                            <a href="<?php echo base_url('ticket/by_customer')?>">
                                <i class="fa fa-book fa-fw"></i>Daftar Customer</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->ion_auth->in_group(['manager', 'support'])) :?>
                        <li>
                            <a href="<?php echo base_url('ticket_list')?>">
                                <i class="fa fa-ticket fa-fw"></i>Daftar Ticket</a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?php echo base_url('ticket_list/all')?>">
                            <i class="fa fa-ticket fa-fw"></i>Daftar Semua Ticket</a>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- /#side-menu -->
    </div>
    <!-- /.sidebar-collapse -->
</nav>
<!-- /.navbar-static-side -->
