
    <div class="left-bar">
        <div class="slimscroll-menu">
        <li><a href="<?=url("");?>">
                <label class="menu-icon"><i class="ion-ios-speedometer"></i> </label><span class="text">Dashboard</span>
            </a></li>
        <li><a href="<?=url("Notification@get");?>" class="notification-holder">
                <label class="menu-icon"><i class="ion-ios-bell"></i> </label><span class="text">Notificações</span>
            </a></li>
        <li><a href="<?=url("Document@get");?>">
                <label class="menu-icon"><i class="ion-document-text"></i> </label><span class="text">Documentos</span>
            </a></li>
        <li><a href="<?=url("Template@get");?>">
                <label class="menu-icon"><i class="ion-document"></i> </label><span class="text">Templates</span>
            </a></li>
        <li><a href="<?=url("Request@get");?>">
                <label class="menu-icon"><i class="ion-fireball"></i> </label><span class="text">Solicitações de assinatura</span>
            </a></li>
        @if ( $user->role == "superadmin" || $user->role == "admin" || $user->role == "staff" ) 
        <li><a href="<?=url("Customer@get");?>">
                <label class="menu-icon"><i class="ion-ribbon-b"></i> </label><span class="text">Clientes</span>
            </a></li>
        @endif
        @if ( $user->role == "superadmin" || $user->role == "admin" ) 
        <li><a href="<?=url("Department@get");?>">
                <label class="menu-icon"><i class="ion-coffee"></i> </label><span class="text">Departamentos</span>
            </a></li>
        <li><a href="<?=url("Team@get");?>">
                <label class="menu-icon"><i class="ion-ios-people"></i> </label><span class="text">Equipe</span>
            </a></li>
        @endif
        @if ( $user->role == "superadmin" ) 
        @if ( env('SHOW_SAAS') == "Enabled" ) 
        <li><a href="<?=url("Company@get");?>">
                <label class="menu-icon"><i class="ion-ios-flower"></i> </label><span class="text">Empresas</span>
            </a></li>
        <li><a href="<?=url("User@get");?>">
                <label class="menu-icon"><i class="ion-person"></i> </label><span class="text">Usários</span>
            </a></li>
        @endif
        @endif
        <li><a href="<?=url("Settings@get");?>">
                <label class="menu-icon"><i class="ion-gear-a"></i> </label><span class="text">Configurações</span>
            </a></li>
            </div>
    </div>