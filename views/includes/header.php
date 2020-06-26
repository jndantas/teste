    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-84926915-3"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'UA-84926915-3');
    </script>

    <header>
        <!-- Hambager -->
        <div class="humbager">
            <i class="ion-navicon-round"></i>
        </div>
        <!-- logo -->
        <div class="logo">
            <a href="<?=url("");?>">
                <img src="<?=url("");?>uploads/app/{{ env('APP_LOGO'); }}" class="img-responsive">
            </a>
        </div>

        <!-- search -->
        <div class="header-search hidden-xs">
            <form action="<?=url("Document@get");?>">
                <span class="search-icon">
                    <i class="ion-android-search"></i>
                </span>
                <input type="text" name="search" value="<?php if(isset($_GET['search'])){ echo $_GET['search']; } ?>" class="search-field" placeholder="Buscar por filas ou pastas">
            </form>
        </div>

        <!-- top right -->
        <ul class="nav header-links pull-right">
            <li class="notify  hidden-xs">
                <a href="{{ url('Notification@get') }}" class="notification-holder">
                    <span class="notifications">
                        <i class="notifications-count ion-ios-bell"></i>
                    </span>
                </a>
            </li>
            <li class="profile">
                <div class="dropdown">
                    <span class="dropdown-toggle" data-toggle="dropdown">
                        <span class="profile-name"> <span class="hidden-xs"> {{ $user->fname }} </span> <i class="ion-ios-arrow-down"></i> </span>
                        <span class="avatar">
                            @if( !empty($user->avatar) )
                            <img src="<?=url("");?>uploads/avatar/{{ $user->avatar }}" class="user-avatar img-circle">
                            @else
                            <img src="<?=url("");?>assets/images/avatar.png" class="user-avatar">
                            @endif
                        </span>
                    </span>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                        <li role="presentation"><a role="menuitem" href="<?=url("Settings@get");?>">
                                <i class="ion-ios-person-outline"></i> Perfil</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" href="<?=url("Settings@get");?>">
                                <i class="ion-ios-gear-outline"></i> Configurações</a></li>
                        <li role="presentation" class="divider"></li>
                        @if ( env('CERTIFICATE_DOWNLOAD') == "Enabled" ) 
                        <li role="presentation"><a role="menuitem" href="<?=url("");?>uploads/downloads/credentials.zip" download>
                                <i class="ion-ios-locked-outline"></i> P12 Cert</a></li>
                        <li role="presentation" class="divider"></li>
                        @endif
                        <li role="presentation"><a role="menuitem" href="<?=url("Auth@signout");?>">
                                <i class="ion-ios-arrow-right"></i> Sair</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </header>