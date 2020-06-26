<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Crie suas assinaturas digitalmente e assine documentos em pdf..">
    <meta name="author" content="WTM">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Configurações | WTM | Assine seus documentos online</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Berkshire+Swash|Cookie|Courgette|Dr+Sugiyama|Grand+Hotel|Great+Vibes|League+Script|Meie+Script|Miss+Fajardose|Niconne|Pacifico|Petit+Formal+Script|Rochester|Sacramento|Tangerine" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/jcanvas/global.min.css" rel="stylesheet">
    <link href="<?=url("");?>assets/css/simcify.min.css" rel="stylesheet">
    <!-- Signer CSS -->
    <link href="<?=url("");?>assets/css/style.css" rel="stylesheet">
    <script src="<?=url("");?>assets/js/jscolor.js"></script>
</head>

<body>

    <!-- header start -->
    {{ view("includes/header", $data); }}

    <!-- sidebar -->
    {{ view("includes/sidebar", $data); }}
    
    <div class="content">
        <div class="page-title">
            <h3>Configurações</h3>
        </div>
        <div class="light-card settings-card">
            <div class="settings-menu">
                <ul>
                    <li class="active"><a data-toggle="tab" href="#profile">Perfil</a></li>
                    <li><a data-toggle="tab" href="#signature">Assinatura</a></li>
                    @if ( $user->role == "superadmin" || $user->role == "admin" ) 
                    <li><a data-toggle="tab" href="#company">Empresa</a></li>
                    <li><a data-toggle="tab" href="#reminders">Lembretes</a></li>
                    @endif
                    @if ( $user->role == "superadmin" ) 
                    <li><a data-toggle="tab" href="#system">Sistema</a></li>
                    @endif
                    <li><a data-toggle="tab" href="#password">Senha</a></li>
                </ul>
            </div>
            <div class="settings-forms">
                <div class="col-md-5 tab-content">
                    <!-- Profile start -->
                    <div id="profile" class="tab-pane fade in active">
                        <h4>Perfil</h4>
                        <form class="simcy-form"action="<?=url("Settings@updateprofile");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Foto do perfil</label>
                                        @if( !empty($user->avatar) )
                                        <input type="file" name="avatar" class="croppie" default="<?=url("");?>uploads/avatar/{{ $user->avatar }}" crop-width="200" crop-height="200" accept="image/*">
                                        @else
                                        <input type="file" name="avatar" class="croppie" crop-width="200" crop-height="200" accept="image/*">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Nome</label>
                                        <input type="text" class="form-control" name="fname" value="{{ $user->fname }}" placeholder="Nome" required>
                                        <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Sobrenome</label>
                                        <input type="text" class="form-control" name="lname" value="{{ $user->lname }}" placeholder="Sobrenome" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ $user->email }}" placeholder="Email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Telefone</label>
                                        <input type="text" class="form-control" name="phone" value="{{ $user->phone }}" placeholder="Telefone">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Endereço</label>
                                        <input type="text" class="form-control" name="address" value="{{ $user->address }}" placeholder="Endereço">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" type="submit">Salvar Mudanças</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- profile end -->
                    @if ( $user->role == "superadmin" || $user->role == "admin" ) 
                    <!-- Company start -->
                    <div id="reminders" class="tab-pane fade">
                        <h4>Lembretes</h4>
                        <p>Lembretes são emails enviados a alguém quando nenhuma ação foi executada por ele após o envio de uma solicitação de assinatura.
                            Os e-mails serão enviados após o número de dias selecionado.</p>
                        <form class="simcy-form"action="<?=url("Settings@updatereminders");?>" data-parsley-validate="" loader="true" method="POST">
                            <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if( $company->reminders == "On" )
                                        <input type="checkbox" id="enable-reminders" class="switch" name="reminders" value="On" checked />
                                        @else
                                        <input type="checkbox" id="enable-reminders" class="switch" name="reminders" value="Off" />
                                        @endif
                                        <label for="enable-reminders">Ativar lembretes</label>
                                    </div>
                                </div>
                            </div>
                            @if( $company->reminders == "On" )
                            <div class="panel-group reminders-holder" id="accordion">
                            @else
                            <div class="panel-group reminders-holder" id="accordion" style="display: none;">
                            @endif
                                @if( count($reminders) > 0 )
                                @foreach ($reminders as $index => $reminder)
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        @if( $index >  0 )
                                        <span class="delete-reminder" data-toggle="tooltip" title="Remove reminder"><i class="ion-ios-trash"></i></span>
                                        @endif
                                        <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse{{ $index + 1 }}">Lembrete #<span class="count">{{ $index + 1 }}</span></a></h4>
                                    </div>
                                    @if( $index ==  0 )
                                    <div class="panel-collapse collapse in" id="collapse{{ $index + 1 }}">
                                    @else
                                    <div class="panel-collapse collapse" id="collapse{{ $index + 1 }}">
                                    @endif
                                        <div class="panel-body">
                                            <div class="remider-item">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="count[]" value="1">
                                                            <label>Assunto do Email</label> <input class="form-control" name="subject[]" placeholder="Assunto do Email" required type="text" value="{{ $reminder->subject }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Dias após o pedido ser enviado</label> <input class="form-control" name="days[]" min="1" placeholder="Dias após o pedido ser enviado" required type="number" value="{{ $reminder->days }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Mensagem</label>
                                                            <textarea class="form-control" name="message[]" required rows="9">{{ $reminder->message }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse1">Lembrete #<span class="count">1</span></a></h4>
                                    </div>
                                    <div class="panel-collapse collapse in" id="collapse1">
                                        <div class="panel-body">
                                            <div class="remider-item">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="count[]" value="1">
                                                            <label>Assunto do Email</label> <input class="form-control" name="subject[]" placeholder="Assunto do Email" required type="text" value=" Lembrete de convite de assinatura">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Dias após o pedido ser enviado</label> <input class="form-control" name="days[]" min="1" placeholder="Dias após o pedido ser enviado" required type="number" value="3">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Mensagem</label>
                                                            <textarea class="form-control" name="message[]" required rows="9">
                                                                Olá,
                                                                eu espero que você esteja bem.
                                                                Estou escrevendo para lembrá-lo da solicitação de assinatura que enviei anteriormente.
                                                                Felicidades!
                                                                {{ $user->fname }} {{ $user->lname }}
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <span class="delete-reminder" data-toggle="tooltip" title="Remove reminder"><i class="ion-ios-trash"></i></span>
                                        <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse2">Lembrete #<span class="count">2</span></a></h4>
                                    </div>
                                    <div class="panel-collapse collapse " id="collapse2">
                                        <div class="panel-body">
                                            <div class="remider-item">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="count[]" value="1">
                                                            <label>Assunto do Email</label> <input class="form-control" name="subject[]" placeholder="Assunto do Email" required type="text" value="Lembrete de convite de assinatura ">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Dias após o pedido ser enviado</label> <input class="form-control" name="days[]" min="1" placeholder="Dias após o pedido ser enviado" required type="number" value="7">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Mensagem</label>
                                                            <textarea class="form-control" name="message[]" required rows="9">Olá,
                                                            eu espero que você esteja bem.
                                                            Estou escrevendo para lembrá-lo da solicitação de assinatura que enviei anteriormente.
                                                            Felicidades!
                                                            {{ $user->fname }} {{ $user->lname }}
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <span class="delete-reminder" data-toggle="tooltip" title="Remove reminder"><i class="ion-ios-trash"></i></span>
                                        <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse3">Lembrete #<span class="count">3</span></a></h4>
                                    </div>
                                    <div class="panel-collapse collapse " id="collapse3">
                                        <div class="panel-body">
                                            <div class="remider-item">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="count[]" value="1">
                                                            <label>Assunto do Email</label> <input class="form-control" name="subject[]" placeholder="Assunto do Email" required type="text" value="Lembrete de convite de assinatura">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Dias após o pedido ser enviado</label> <input class="form-control" name="days[]" min="1" placeholder="Dias após o pedido ser enviado" required type="number" value="7">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Mensagem</label>
                                                            <textarea class="form-control" name="message[]" required rows="9">
                                                                Olá,
                                                                Eu espero que você esteja bem.
                                                                Estou escrevendo para lembrá-lo da solicitação de assinatura que enviei anteriormente.
                                                                Felicidades!
                                                                {{ $user->fname }} {{ $user->lname }}
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-default add-reminder" type="button">Adicionar lembrete</button>
                                        <button class="btn btn-primary" type="submit">Salvar alterações</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- reminder end -->
                    <!-- Company start -->
                    <div id="company" class="tab-pane fade">
                        <h4>Empresa</h4>
                        <form class="simcy-form"action="<?=url("Settings@updatecompany");?>" data-parsley-validate="" loader="true" method="POST">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Nome da Empresa</label>
                                        <input type="text" class="form-control" name="name" placeholder="Nome da Empresa" value="{{ $company->name }}" required>
                                        <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email" placeholder="Email" value="{{ $company->email }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Telefone</label>
                                        <input type="text" class="form-control" name="phone" placeholder="Telefone" value="{{ $company->phone }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" type="submit">Salvar Mudanças</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- Company end -->
                    @endif
                    @if ( $user->role == "superadmin" ) 
                    <!-- System start -->
                    <div id="system" class="tab-pane fade">
                        <h4>Dados do Sistema</h4>
                        <form class="simcy-form"action="<?=url("Settings@updatesystem");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Nome do Sistema</label>
                                        <input type="text" class="form-control system-name" placeholder="Nome do Sistema" name="APP_NAME" value="{{ env('APP_NAME'); }}" required>
                                        <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Logo do Sistema</label>
                                        <input type="file" name="APP_LOGO" class="croppie" default="<?=url("");?>uploads/app/{{ env('APP_LOGO'); }}" crop-width="541" crop-height="152" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Sistema favicon/icon </label>
                                        <input type="file" name="APP_ICON" class="croppie" default="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}" crop-width="152" crop-height="152" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Usuário SMTP </label>
                                        <input type="text" class="form-control" name="MAIL_USERNAME" placeholder="SMTP Username" value="{{ env('MAIL_USERNAME'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Host</label>
                                        <input type="text" class="form-control" placeholder="SMTP Host" name="SMTP_HOST" value="{{ env('SMTP_HOST'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Port</label>
                                        <input type="text" class="form-control" placeholder="SMTP Port" name="SMTP_PORT" value="{{ env('SMTP_PORT'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Password</label>
                                        <input type="password" class="form-control" placeholder="SMTP Password" name="SMTP_PASSWORD" value="{{ env('SMTP_PASSWORD'); }}" autocomplete="false" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Encryption</label>
                                        <input type="text" class="form-control" placeholder="SMTP Encryption" name="MAIL_ENCRYPTION" value="{{ env('MAIL_ENCRYPTION'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('SMTP_AUTH') == "Enabled" ) 
                                        <input type="checkbox" class="switch" name="SMTP_AUTH" value="true" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="SMTP_AUTH" value="true" />
                                        @endif
                                        <label>SMTP Authenticado.</label>
                                    </div>
                                </div>
                            </div>

                            <div class="divider"></div>
                            <!--Google-->
                            <!--<h5>Google Settings</h5>-->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Google Client ID</label>
                                        <input type="text" class="form-control" placeholder="Google Client ID" name="GOOGLE_CLIENT_ID" value="{{ env('GOOGLE_CLIENT_ID'); }}" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Google API key</label>
                                        <input type="text" class="form-control" placeholder="Google API key" name="GOOGLE_API_KEY" value="{{ env('GOOGLE_API_KEY'); }}">
                                    </div>
                                </div>
                            </div>

                            <div class="divider"></div>
                            <!--Dropbox-->
                            <!--<h5>Dropbox Settings</h5>-->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Dropbox App key</label>
                                        <input type="text" class="form-control" placeholder="Dropbox App key" name="DROPBOX_APP_KEY" value="{{ env('DROPBOX_APP_KEY'); }}">
                                    </div>
                                </div>
                            </div>

                            <div class="divider"></div>
                            <!--Cloud Convert-->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('ALLOW_NON_PDF') == "Enabled" ) 
                                        <input type="checkbox" class="switch" name="ALLOW_NON_PDF" value="Enabled" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="ALLOW_NON_PDF" value="Enabled" />
                                        @endif
                                        <label>Permitir que os usuários carreguem Word, Excel e Power Point. </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('USE_CLOUD_CONVERT') == "Enabled" ) 
                                        <input type="checkbox" class="switch" name="USE_CLOUD_CONVERT" value="Enabled" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="USE_CLOUD_CONVERT" value="Enabled" />
                                        @endif
                                        <label>Use o Cloud Convert para converter arquivos em PDF </label><span class="text-muted text-xs"> Necessário para conversão de arquivos em PDF.</span>
                                    </div>
                                </div>
                            </div>
                            @if ( env('USE_CLOUD_CONVERT') == "Enabled" ) 
                            <div class="form-group cloud-convert-holder">
                            @else
                            <div class="form-group cloud-convert-holder" style="display: none;">
                            @endif
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Cloud Convert API key</label>
                                        <input type="text" class="form-control" placeholder="Cloud Convert App key" name="CLOUDCONVERT_APP_KEY" value="{{ env('CLOUDCONVERT_APP_KEY'); }}">
                                    </div>
                                </div>
                            </div>

                            <div class="divider"></div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('PKI_STATUS') == "Enabled" ) 
                                        <input type="checkbox" class="switch" name="PKI_STATUS" value="Enabled" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="PKI_STATUS" value="Enabled" />
                                        @endif
                                        <label>Ativar assinatura digital PKI.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('CERTIFICATE_DOWNLOAD') == "Enabled" ) 
                                        <input type="checkbox" class="switch" name="CERTIFICATE_DOWNLOAD" value="Enabled" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="CERTIFICATE_DOWNLOAD" value="Enabled" />
                                        @endif
                                        <label>Permitir que os usuários baixem o certificado p12</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('NEW_ACCOUNTS') == "Enabled" ) 
                                        <input type="checkbox" class="switch" name="NEW_ACCOUNTS" value="Enabled" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="NEW_ACCOUNTS" value="Enabled" />
                                        @endif
                                        <label>Permitir que novos usuários e empresas se inscrevam</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('SHOW_SAAS') == "Enabled" ) 
                                        <input type="checkbox" class="switch" name="SHOW_SAAS" value="Enabled" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="SHOW_SAAS" value="Enabled" />
                                        @endif
                                        <label>Mostrar menu Saas</label>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <h5>Contas comerciais</h5>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Limite de disco (MBs)</label>
                                        <input type="number" class="form-control" name="BUSINESS_DISK_LIMIT" placeholder="Limite de disco (MBs)" value="{{ env('BUSINESS_DISK_LIMIT'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Limite de Arquivo</label>
                                        <input type="number" class="form-control" name="BUSINESS_FILE_LIMIT" placeholder="Limite de Arquivo" value="{{ env('BUSINESS_FILE_LIMIT'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Limite de Equipe </label>
                                        <input type="number" class="form-control" name="TEAM_LIMIT" placeholder="Limite de equipe" value="{{ env('TEAM_LIMIT'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <h5>Personal Accounts</h5>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Limite de disco (MBs)</label>
                                        <input type="number" class="form-control" name="PERSONAL_DISK_LIMIT" placeholder="Limite de disco (MBs)" value="{{ env('PERSONAL_DISK_LIMIT'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Limite de Arquivos</label>
                                        <input type="number" class="form-control" name="PERSONAL_FILE_LIMIT" placeholder="Limite de Arquivos" value="{{ env('PERSONAL_FILE_LIMIT'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" type="submit">Salvar Mudanças</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- system end -->
                    @endif
                    <!-- Signature start -->
                    <div id="signature" class="tab-pane fade">
                        <h4>Assinatura</h4>
                        <p>Essa é sua assinatura, atualize em qualquer momento.</p>
                        <div class="row">
                            <div class="col-md-12">
                             <div class="signature-holder">
                                <div class="signature-body">
                                    @if ( empty( $user->signature ) )
                                    <img src="<?=url("");?>uploads/signatures/demo.png" class="img-responsive">
                                    @else
                                    <img src="<?=url("");?>uploads/signatures/{{ $user->signature }}" class="img-responsive">
                                    @endif
                                </div>
                            </div>
                            <div class="signature-btn-holder">
                                <button class="btn btn-primary btn-block"  data-toggle="modal" data-target="#updateSignature" data-target="#createFolder" data-backdrop="static" data-keyboard="false"> Atualizar Assinatura</button>
                            </div>
                            </div>
                        </div>
                    </div>
                    <!-- password end -->
                    <!-- password start -->
                    <div id="password" class="tab-pane fade">
                        <h4>Senha</h4>
                        <form class="simcy-form"action="<?=url("Settings@updatepassword");?>" data-parsley-validate="" loader="true" method="POST">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Senha Atual</label>
                                        <input type="password" class="form-control" name="current" required placeholder="Senha Atual">
                                        <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Nova Senha</label>
                                        <input type="password" class="form-control" name="password" data-parsley-required="true" data-parsley-minlength="6" data-parsley-error-message="Essa senha é muito curta!" id="newPassword" placeholder="Nova Senha">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Confirmar Senha</label>
                                        <input type="password" class="form-control" data-parsley-required="true" data-parsley-equalto="#newPassword" data-parsley-error-message="As senhas não correspondem!" placeholder="Confirmar Senha">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" type="submit">Salvar Mudanças</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- password end -->
                </div>
            </div>
        </div>
    </div>


      <!-- Upload file Modal -->
  <div class="modal fade" id="updateSignature" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
    <div class="modal-dialog">
      <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Atualizar Assinatura </h4>
                </div>
      <ul class="head-links">
        <li type="capture" class="active"><a data-toggle="tab" href="#text">Text</a></li>
        <li type="upload"><a data-toggle="tab" href="#upload">Upload</a></li>
        <li type="draw"><a data-toggle="tab" href="#draw">Desenhar</a></li>
      </ul>
        <div class="modal-body">
        <div class="tab-content">
            <div id="text" class="tab-pane fade in active">
                      <form>
                          <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                  <label>Digite sua assinatura</label>
                                  <input type="text" class="form-control signature-input" name="" placeholder="Digite sua assinatura" maxlength="18" value="Seu nome">
                                </div>
                                <div class="col-md-6">
                                  <label>Selecione a fonte</label>
                                  <select class="form-control signature-font" name="">
                                      <option value="Lato">Lato</option>
                                      <option value="Miss Fajardose">Miss Fajardose</option>
                                      <option value="Meie Script">Meie Script</option>
                                      <option value="Petit Formal Script">Petit Formal Script</option>
                                      <option value="Niconne">Niconne</option>
                                      <option value="Rochester">Rochester</option>
                                      <option value="Tangerine">Tangerine</option>
                                      <option value="Great Vibes">Great Vibes</option>
                                      <option value="Berkshire Swash">Berkshire Swash</option>
                                      <option value="Sacramento">Sacramento</option>
                                      <option value="Dr Sugiyama">Dr Sugiyama</option>
                                      <option value="League Script">League Script</option>
                                      <option value="Courgette">Courgette</option>
                                      <option value="Pacifico">Pacifico</option>
                                      <option value="Cookie">Cookie</option>
                                      <option value="Grand Hotel">Grand Hotel</option>
                                  </select>
                                </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                  <label>Densidade</label>
                                  <select class="form-control signature-weight" name="">
                                      <option value="normal">Normal</option>
                                      <option value="bold">Negrito</option>
                                      <option value="lighter">Claro</option>
                                  </select>
                                </div>
                                <div class="col-md-4">
                                  <label>Cor</label>
                                  <input  class="form-control signature-color jscolor { valueElement:null,borderRadius:'1px', borderColor:'#e6eaee',value:'000000',zIndex:'99999', onFineChange:'updateSignatureColor(this)'}" readonly="">
                                </div>
                                <div class="col-md-4">
                                  <label>Estilo</label>
                                  <select class="form-control signature-style" name="">
                                      <option value="normal">Regular</option>
                                      <option value="italic">Itálico</option>
                                  </select>
                                </div>
                            </div>
                          </div>
                      </form>
                      <div class="divider"></div>
                      <h4 class="text-center">Pré-visualizar</h4>
                      <div class="text-signature-preview">
                          <div class="text-signature" id="text-signature" style="color: #000000">Seu Nome</div>
                      </div>

            </div>
            <div id="upload" class="tab-pane fade">
                <p>Carregue sua assinatura, se você já a tiver.</p>
                  <div class="form-group">
                        <div class="row">
                          <div class="col-md-12">
                            <label>Carregue sua assinatura</label>
                                <input type="file" name="signatureupload" class="croppie" crop-width="400" crop-height="150">
                          </div>
                      </div>
                  </div>
            </div>
            <div id="draw" class="tab-pane fade text-center">
                    <p>Desenhe sua assinatura.</p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="draw-signature-holder"><canvas width="400" height="150" id="draw-signature"></canvas></div>
                            <div class="signature-tools text-center" id="controls">
                                <div class="signature-tool-item with-picker">
                                    <div><button class="jscolor { valueElement:null,borderRadius:'1px', borderColor:'#e6eaee',value:'000000',zIndex:'99999', onFineChange:'modules.color(this)'}"></button></div>
                                </div>
                                <div class="signature-tool-item" id="signature-stroke" stroke="5">
                                    <div class="tool-icon tool-stroke"></div>
                                </div>
                                <div class="signature-tool-item" id="undo">
                                    <div class="tool-icon tool-undo"></div>
                                </div>
                                <div class="signature-tool-item" id="clear">
                                    <div class="tool-icon tool-erase"></div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
          <button type="button" class="btn btn-primary save-signature">Salvar Assinatura</button>
        </div>
      </div>
      
    </div>
  </div>



    <!-- footer -->
    {{ view("includes/footer"); }}

    <!-- scripts -->
    <script>
        var fullName = "{{ $user->fname }} {{ $user->lname }}",
              saveSignatureUrl = "<?=url("Signature@save");?>",
              auth = true;
    </script>
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/libs/html2canvas/html2canvas.js"></script>
    <script src="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?=url("");?>assets/libs/jcanvas/jcanvas.min.js"></script>
    <script src="<?=url("");?>assets/libs/jcanvas/signature.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>


</body>

</html>
