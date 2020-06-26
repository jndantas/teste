<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Crie suas assinaturas digitalmente e assine documentos em pdf..">
    <meta name="author" content="WTM">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Equipe | WTM | Assine seus documentos online</title>
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
            <div class="pull-right page-actions lower">
                <button class="btn btn-primary" data-toggle="modal" data-target="#create" data-backdrop="static" data-keyboard="false"><i class="ion-plus-round"></i> Nova Equipe</button>
            </div>
            <div>
                <h3>Equipe</h3>
            </div>
        </div>
        <div class="row">
            @if ( count($team) > 0 )
            @foreach ( $team as $team )
            <!-- Team member -->
            <div class="col-md-4">
                <div class="light-card team-card-info text-center">
                    @if ( !empty($team->avatar) )
                    <img src="<?=url("")?>uploads/avatar/{{ $team->avatar }}" class="img-circle">
                    @else
                    <img src="<?=url("")?>assets/images/avatar.png" class="">
                    @endif
                    <h4>{{ $team->fname }} {{ $team->lname }}</h4>
                    <p>{{ $team->email }}</p>
                    <div class="team-card-extra">
                        <p class="pull-left">
                            @if ( in_array("delete",json_decode($team->permissions)) )
                            <span class="text-danger" data-toggle="tooltip" data-placement="top" title="Pode Apagar"><i class="ion-ios-circle-filled"></i></span>
                            @endif
                            @if ( in_array("upload",json_decode($team->permissions)) )
                            <span class="text-success" data-toggle="tooltip" data-placement="top" title="Pode fazer upload"><i class="ion-ios-circle-filled"></i></span>
                            @endif
                            @if ( in_array("edit",json_decode($team->permissions)) )
                            <span class="text-primary" data-toggle="tooltip" data-placement="top" title="Pode Assinar"><i class="ion-ios-circle-filled"></i></span>
                            @endif
                        </p>
                        <div class="dropup">
                            <span class="team-action dropdown-toggle" data-toggle="dropdown"><i class="ion-ios-more-outline"></i></span>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                                <li role="presentation"><a class="fetch-display-click" data="memberid:{{ $team->id }}|csrf-token:<?=csrf_token();?>" url="<?=url("Team@updateview");?>" holder=".update-holder" modal="#update" href="">Editar</a></li>
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><a class="send-to-server-click"  data="memberid:{{ $team->id }}|csrf-token:<?=csrf_token();?>" url="<?=url("Team@delete");?>" warning-title="Você tem certeza?" warning-message="O perfil e os dados deste membro serão excluídos" warning-button="Continue" loader="true" href="">Apagar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End team member -->
            @endforeach
            @else
                <div class="center-notify">
                    <i class="ion-ios-information-outline"></i>
                    <h3>Não há Equipes!</h3>
                </div>
            @endif
        </div>
    </div>


    <!--Add Team Member-->
    <div class="modal fade" id="create" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Criar Equipe</h4>
                </div>
                <form class="simcy-form"action="<?=url("Team@create");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <p>Preencha os detalhes.</p>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 ">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" name="fname" placeholder="Nome" data-parsley-required="true">
                                    <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                </div>
                                <div class="col-md-6">
                                    <label>Sobrenome</label>
                                    <input type="text" class="form-control" name="lname" placeholder="Sobrenome" data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Email" data-parsley-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label>Telefone</label>
                                    <input type="text" class="form-control" name="phone" placeholder="Telefone">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Permissões</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="checkbox" class="switch" name="permissions[]" value="upload" checked readonly/>  Upload Arquivos
                                        </div>
                                        <div class="col-md-3">
                                            <input type="checkbox" class="switch" name="permissions[]" value="edit" checked  /> Editar & Assinar
                                        </div>
                                        <div class="col-md-5">
                                            <input type="checkbox" class="switch" name="permissions[]" value="delete" checked  /> Apagar arquivos e pastas
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Foto</label>
                                    <input type="file" name="avatar" class="croppie" default="<?=url("")?>assets/images/avatar.png" crop-width="200" crop-height="199"  accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Criar Equipe</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Update Team Member Modal -->
    <div class="modal fade" id="update" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Atualizar Equipe </h4>
                </div>
                <form class="update-holder simcy-form"action="<?=url("Team@update");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="loader-box"><div class="circle-loader"></div></div>
                </form>
            </div>

        </div>
    </div>

    <!-- footer -->
    {{ view("includes/footer"); }}


    <!-- Modals -->

    <!-- add team modal -->
    <!-- Modal -->
    <div class="modal fade" id="addTeam" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Adicionar Membro a Equipe</h4>
                </div>
                <form class="add-team-form" action="files/ajaxProcesses.php" method="post" enctype="multipart/form-data" data-parsley-validate="">
                    <div class="modal-body">
                        <div class="alert alert-info alert-dismissable text-center saving" style="display: none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="ion-loading-c"></i> Salvando...
                        </div>
                        <p>Preencha os detalhes do membro da equipe.</p>
                        <div class="form-group">
                            <div class="col-md-6 p-l-o">
                                <label>Nome</label>
                                <input type="text" class="form-control" name="fname" placeholder="Nome" data-parsley-required="true">
                                <input type="hidden" name="action" value="addTeam">
                            </div>
                            <div class="col-md-6 p-r-o">
                                <label>Sobrenome</label>
                                <input type="text" class="form-control" name="lname" placeholder="Sobrenome" data-parsley-required="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 p-l-o">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Email" data-parsley-required="true">
                            </div>
                            <div class="col-md-6 p-r-o">
                                <label>Telefone</label>
                                <input type="text" class="form-control" name="phone" placeholder="Telefone">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 p-l-o">
                                <label>Senha</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Senha" data-parsley-required="true" data-parsley-minlength="6" data-parsley-error-message="Senha é muito curta!">
                            </div>
                            <div class="col-md-6 p-r-o">
                                <label>Confirmar Senha</label>
                                <input type="password" class="form-control" placeholder="Confirmar Senha" data-parsley-required="true" data-parsley-equalto="#password" data-parsley-error-message="As senhas não correspondem!">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 p-lr-o">
                                <label>Foto do Perfil <span class="text-muted text-xs">Pelo menos 200x200</span></label>
                                <input type="file" name="avatar" class="dropify" data-default-file="uploads/avatar/avatar.png" data-min-width="199" data-min-height="199" data-allowed-file-extensions="png jpg">
                            </div>
                        </div>
                        <div class="form-group permissions">
                            <div class="col-md-12 p-l-o">
                                <label>Permissões</label>
                            </div>
                            <div class="col-md-4 p-l-o">
                                <input type="checkbox" class="js-switch" name="permissions[]" value="upload" checked readonly /> Pode fazer upload
                            </div>
                            <div class="col-md-4">
                                <input type="checkbox" class="js-switch" name="permissions[]" value="sign" checked /> Pode assinar
                            </div>
                            <div class="col-md-4 p-r-o">
                                <input type="checkbox" class="js-switch" name="permissions[]" value="delete" checked /> Pode excluir
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Adicionar membro da equipe</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- add team modal -->
    <!-- Modal -->
    <div class="modal fade" id="editTeam" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Editar Informações da conta </h4>
                </div>
                <form class="edit-team-form" action="files/ajaxProcesses.php" method="post" enctype="multipart/form-data" data-parsley-validate="">
                    <div class="modal-body">

                        <p>Atualização nos detalhes do membro da equipe.</p>

                        <div class="alert alert-info alert-dismissable text-center saving" style="display: none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="ion-loading-c"></i> Salvando...
                        </div>
                        <div class="center-notify">
                            <i class="ion-loading-c"></i>
                        </div>
                        <div class="edit-fields"></div>

                    </div>
                    <div class="modal-footer" style="display: none;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar Mudanças</button>
                    </div>
                </form>
            </div>

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
