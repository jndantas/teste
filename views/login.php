<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Crie suas assinaturas digitalmente e assine documentos em pdf..">
    <meta name="author" content="WTM">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Login | WTM | Assine seus documentos online</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/css/simcify.min.css" rel="stylesheet">
    <!-- Signer CSS -->
    <link href="<?=url("");?>assets/css/style.css" rel="stylesheet">

</head>

<body>

    <div class="login-card mb-30">
        <img src="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}" class="img-responsive">
        @if ( $guest )
        <a class="btn btn-block btn-success m-t-50" href="{{ $signingLink }}">Inscreva-se como Convidado</a>
        @endif
        <div class="sign-in">
            <h5 class="mb-30">Faça login em sua conta </h5>
            <form class="text-left simcy-form" action="<?=url("Auth@signin");?>" data-parsley-validate="" loader="true" method="POST">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Endereço de Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                            <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Senha</label>
                            <input type="Password" class="form-control" name="password" placeholder="Senha" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="pull-left m-t-5"><a style="cursor: pointer" target="forgot-password">Esqueceu a senha?</a></p>
                            <button class="btn btn-primary pull-right" type="submit" name="login">Entrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @if ( env('NEW_ACCOUNTS') == "Enabled" ) 
        <div class="sign-up" style="display: none;">
            <h5 class="mb-30">Crie uma Conta Gratuitamente</h5>

            <form class="text-left simcy-form" action="<?=url("Auth@signup");?>" data-parsley-validate="" loader="true" method="POST">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Nome</label>
                            <input type="text" class="form-control" name="fname" placeholder="Nome" required>
                            <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                        </div>
                        <div class="col-md-6">
                            <label>Sobrenome</label>
                            <input type="text" class="form-control" name="lname" placeholder="Sobrenome" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label><input type="checkbox" class="switch business-account" name="business" value="1" />Esta é uma conta comercial </label>
                        </div>
                    </div>
                </div>
                <div class="form-group business-name" style="display:none">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Nome da Empresa</label>
                            <input type="text" class="form-control" name="company" placeholder="Nome da Empresa">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Senha</label>
                            <input type="Password" class="form-control" name="password" data-parsley-required="true" data-parsley-minlength="6" data-parsley-error-message="Password is too short!" id="password" placeholder="Senha">
                        </div>
                        <div class="col-md-6">
                            <label>Confirme a Senha</label>
                            <input type="Password" class="form-control" data-parsley-required="true" data-parsley-equalto="#password" data-parsley-error-message="Passwords don't Match!" placeholder="Confirme a Senha">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="pull-left m-t-5"><a style="cursor: pointer" target="sign-in">Entrar?</a></p>
                            <button class="btn btn-primary pull-right" type="submit">Criar Conta</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif
        <div class="forgot-password" style="display: none;">
            <h5 class="mb-30">Esqueceu a senha? não se preocupe, nós vamos <br>envie um link para redefinir.</h5>
            <form class="text-left simcy-form" action="<?=url("Auth@forgot");?>" method="POST" data-parsley-validate="" loader="true">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Email</label>
                            <input type="text" class="form-control" name="email" placeholder="Email" required>
                            <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="pull-left m-t-5"><a style="cursor: pointer" target="sign-in">Entrar?</a></p>
                            <button class="btn btn-primary pull-right" type="submit">Enviar Email</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @if ( env('NEW_ACCOUNTS') == "Enabled" ) 
        <div class="m-t-5">
            <a class="btn btn-block btn-primary-ish m-t-50 sign-up-btn"  target="sign-up">Criar uma Conta</a>
        </div>
        @endif
        <div class="copyright">
            <p class="text-center"><?=date("Y")?> &copy; <?=env("APP_NAME")?> | Todos os direitos Reservados.</p>
        </div>
    </div>

    <!-- scripts -->
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
</body>

</html>
