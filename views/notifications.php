<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Crie suas assinaturas digitalmente e assine documentos em pdf..">
    <meta name="author" content="WTM">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Notificações | WTM | Assine seus documentos online</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/css/simcify.min.css" rel="stylesheet">
    <!-- Signer CSS -->
    <link href="<?=url("");?>assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- header start -->
    {{ view("includes/header", $data); }}

    <!-- sidebar -->
    {{ view("includes/sidebar", $data); }}

    <div class="content">
        <div class="page-title">
            <h3>Notificações</h3>
        </div>
        <div class="row">
            <!-- Notification start -->
            <div class="col-md-12 notifications-holder">

                @if ( count($requests) > 0 )
                @foreach ( $requests as $request )
                <div class="light-card notification-item unread">
                    <div class="notification-item-image bg-warning btn-round">
                        <span><i class="ion-ios-bell-outline"></i></span>
                    </div>
                    <span class="label label-warning">Importante!</span>
                <p>Você foi convidado a assinar um <a href="{{ url('Document@open').$request->document.'?signingKey='.$request->signing_key }}"><span class="text-primary">documento</span></a>.</p>
                </div>
                @endforeach
                @endif
                @if ( count($notifications) > 0 )
                @foreach ( $notifications as $notification )
                <div class="light-card notification-item">
                    @if ( $notification->type == "accept" )
                    <div class="notification-item-image bg-success btn-round">
                        <span><i class="ion-ios-checkmark"></i></span>
                    </div>
                    @elseif ( $notification->type == "decline" )
                    <div class="notification-item-image bg-danger btn-round">
                        <span><i class="ion-ios-close"></i></span>
                    </div>
                    @else
                    <div class="notification-item-image bg-warning btn-round">
                        <span><i class="ion-ios-bell-outline"></i></span>
                    </div>
                    @endif
                    <div class="pull-right">
                        <span class="delete-notification" data-id="{{ $notification->id }}"><i class="ion-close-round"></i></span>
                    </div>
                    <p>{{ $notification->message }}</p>
                </div>
                @endforeach
                @else
                <div class="center-notify">
                    <i class="ion-ios-information-outline"></i>
                    <h3>Está vazio aqui!</h3>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- footer -->
    {{ view("includes/footer"); }}

    <!-- scripts -->
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
    <script>
        var deleteNotificationUrl = '<?=url("Notification@delete");?>';
        $(document).ready(function() {
            $(".bubble").hide();
            readNotifications("<?=url("Notification@read");?>");
        });
    </script>
</body>

</html>
