<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Crie suas assinaturas digitalmente e assine documentos em pdf..">
    <meta name="author" content="WTM">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Clientes | WTM | Assine seus documentos online</title>


    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css" />

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
                <button class="btn btn-primary" data-toggle="modal" data-target="#create" data-backdrop="static" data-keyboard="false"><i class="ion-plus-round"></i> Novo Cliente</button>
            </div>
            <h3>Clientes </h3>
            <p>Esta é uma lista dos seus clientes.</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="light-card table-responsive p-b-3em">
                    <table class="table display companies-list" id="data-table">
                        <thead>
                            <tr>
                                <th class=""></th>
                                <th class="text-center w-70">Imagem</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Endereço</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Uso de disco</th>
                                <th class="text-center">Uso de arquivo</th>
                                <th class="text-center w-70">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ( count($customers) > 0 )
                            @foreach ( $customers as $index => $customer )
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    @if ( !empty($customer['user']->avatar) )
                                    <img src="<?=url("")?>uploads/avatar/{{ $customer['user']->avatar }}" class="img-responsive img-circle table-avatar">
                                    @else
                                    <img src="<?=url("")?>assets/images/avatar.png" class="img-responsive table-avatar">
                                    @endif
                                </td>
                                <td><strong>{{ $customer['user']->fname }} {{ $customer['user']->lname }}</strong></td>
                                <td><strong>{{ $customer['user']->email }}</strong></td>
                                <td>{{ $customer['user']->address }}</td>
                                @if ( $customer['user']->status == "Active" )
                                <td class="text-center"><span class="label label-success">Ativo</span></td>
                                @elseif ( $customer['user']->status == "Suspended" )
                                <td class="text-center"><span class="label label-warning">Suspenso</span></td>
                                @else
                                <td class="text-center"><span class="label label-danger">Inativo</span></td>
                                @endif
                                <td class="text-center">{{ round($customer['disk'] / 1000) }} / <?=env("PERSONAL_DISK_LIMIT");?> MBs</td>
                                <td class="text-center">{{ $customer['files'] }} / <?=env("PERSONAL_FILE_LIMIT");?></td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <span class="company-action dropdown-toggle" data-toggle="dropdown"><i class="ion-ios-more"></i></span>
                                        <ul class="dropdown-menu" role="menu">
                                            <li role="presentation">
                                                <a class="fetch-display-click" data="customerid:{{ $customer['user']->id }}|csrf-token:<?=csrf_token();?>" url="<?=url("Customer@updateview");?>" holder=".update-holder" modal="#update" href="">Editar</a>
                                                <a class="send-to-server-click"  data="customerid:{{ $customer['user']->id }}|csrf-token:<?=csrf_token();?>" url="<?=url("Customer@delete");?>" warning-title="Você tem certeza?" warning-message="O perfil e os dados deste cliente serão excluídos." warning-button="Continue" loader="true" href="">Apagar</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="9" class="text-center">Está vazio aqui</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!--Create User Account-->
    <div class="modal fade" id="create" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Criar conta de cliente</h4>
                </div>
                <form class="simcy-form"action="<?=url("Customer@create");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <p>Preencha os detalhes do cliente, um email com os detalhes de login será enviado ao usuário.</p>
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
                                    <label>Endereço</label>
                                    <input type="text" class="form-control" name="address" placeholder="Endereço" data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Foto do Perfil</label>
                                    <input type="file" name="avatar" class="croppie" default="<?=url("")?>assets/images/avatar.png" crop-width="200" crop-height="199"  accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Criar Conta</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Update User Modal -->
    <div class="modal fade" id="update" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Atualizar conta do Cliente </h4>
                </div>
                <form class="update-holder simcy-form"action="<?=url("Customer@update");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="loader-box"><div class="circle-loader"></div></div>
                </form>
            </div>

        </div>
    </div>

    <!-- footer -->
    {{ view("includes/footer"); }}
    
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.flash.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.print.min.js"></script>
    @if ( count($customers) > 0 )
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5'
                ]
            });
        });
    </script>
    @endif
    <!-- scripts -->
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>
    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
</body>

</html>
