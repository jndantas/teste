<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Crie suas assinaturas digitalmente e assine documentos em pdf..">
    <meta name="author" content="WTM">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>WTM | Assine seus documentos online</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/select2/css/select2.min.css" rel="stylesheet">
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
            <div class="pull-right page-actions">
                <a href="<?=url("Settings@get");?>" class="btn btn-primary-ish hidden-xs"><i class="ion-edit"></i> Configurações</a>
                <a href="<?=url("Document@get");?>" class="btn btn-primary"><i class="ion-document-text"></i> Documentos</a>
            </div>
            <h3 class="m-t-5">Dashboard</h3>
        </div>


        <div class="row">
            <!-- Widget knob -->
            <div class="col-md-6">
                <div class="light-card widget">
                    <div class="meter-widget">
                        <div id="meter" style="height: 430px"></div>
                        <p class="text-center text-muted">Estatísticas de arquivo • Solicitações pendentes<span class="text-success">{{ $pendingRequests }}</span></p>
                    </div>
                </div>
            </div>
            <!-- End widget knob -->
            <!-- disk usage -->
            <div class="col-md-3">
                <div class="light-card knob-widget widget">
                    <h5>Uso do Disco</h5>
                    <div class="text-center">
                        <div class="knob-holder">
                            <input type="text" value="{{ round((($diskUsage / 1000) / $diskLimit) * 100) }}" class="dial" data-thickness=".1" data-width="150" data-linecap="round" data-fgColor="#3DA4FF" readonly>
                        </div>
                        <div class="knob-widget-info">
                            <p class="pull-left text-xs">
                                <span class="text-primary"><i class="ion-ios-circle-filled"></i></span>
                                <span class="count"> {{ round($diskUsage / 1000) }}MBs </span>
                                <span class="text-xs">Usado</span>
                            </p>
                            <p class="pull-right text-xs">
                                <span class="text-danger"><i class="ion-ios-circle-filled"></i></span>
                                <span class="count"> {{ $diskLimit - round($diskUsage / 1000) }}MBs </span>
                                <span class="">Remanescente</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- file usage -->
            <div class="col-md-3">
                <div class="light-card knob-widget widget bg-success">
                    <h5 class="text-white">Uso de arquivo</h5>
                    <div class="text-center">
                        <div class="knob-holder">
                            <input type="text" value="{{ round(($fileUsage / $fileLimit) * 100) }}" class="dial" data-thickness=".1" data-width="150" data-linecap="round" data-fgColor="#008000" readonly>
                        </div>
                        <div class="knob-widget-info ">
                            <p class="pull-left text-xs text-white">
                                <span class="text-white"><i class="ion-ios-circle-filled"></i></span>
                                <span class="count"> {{ $fileUsage }} </span>
                                <span class="text-xs">Usado</span>
                            </p>
                            <p class="pull-right text-xs text-white">
                                <span class="text-warning"><i class="ion-ios-circle-filled"></i></span>
                                <span class="count"> {{ $fileLimit - $fileUsage }} </span>
                                <span class="">Remanescente</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- awaiting signing -->
            <div class="col-md-3">
                <div class="light-card folder-counter">
                    <div class="widget-icon widget-success">
                        <i class="ion-folder"></i>
                    </div>
                    <h4>{{ $folders }}</h4>
                    <p>Pastas</p>
                </div>
            </div>
            <!-- awaiting signing -->
            <div class="col-md-3">
                <div class="light-card widget-signature">
                    @if ( empty($user->signature) )
                    <img src="<?=url("");?>uploads/signatures/demo.png" class="img-responsive">
                    @else
                    <img src="<?=url("");?>uploads/signatures/{{ $user->signature }}" class="img-responsive">
                    @endif
                    <p>Sua Assinatura</p>
                </div>
            </div>
            <!-- awaiting signing -->
        </div>
        @if ( $user->role == "superadmin" )
        <!-- admin -->
        <div class="row">
            <div class="col-md-3">
                <div class="light-card widget">
                    <h5>Tipos de Contas</h5>
                    <div class="account-types">
                        <div class="account-type-single">
                            <strong class="pull-right">{{ $businessAccounts }}</strong>
                            <p class="text-muted">Contas comerciais</p>
                            <div class="progress progress-bar-success-alt">
                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ round(($businessAccounts / ($personalAccounts + $businessAccounts)) * 100) }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ round(($businessAccounts / ($personalAccounts + $businessAccounts)) * 100) }}%">
                                </div>
                            </div>
                        </div>
                        <div class="account-type-single">
                            <strong class="pull-right">{{ $personalAccounts }}</strong>
                            <p class="text-muted">Contas pessoais</p>
                            <div class="progress progress-bar-primary-alt">
                                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="{{ round(($personalAccounts / ($personalAccounts + $businessAccounts)) * 100) }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ round(($personalAccounts / ($personalAccounts + $businessAccounts)) * 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="light-card widget">
                    <h5>Uso de disco</h5>
                    <div class="disk-usage">
                        <ol>
                            <li><strong>Arquivos PDF </strong><span class="pull-right text-muted">{{ $totalPdf }}</span></li>
                            <li><strong>MS Word </strong> <span class="pull-right text-muted">{{ $totalWord }}</span></li>
                            <li><strong>MS Excel </strong> <span class="pull-right text-muted">{{ $totalExcel }}</span></li>
                            <li><strong>Power Point</strong> <span class="pull-right text-muted">{{ $totalPpt }}</span></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="light-card">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="system-counter">
                                <div class="widget-icon widget-success"> <i class="ion-document-text"></i> </div>
                                <h4>{{ $systemFiles }}</h4>
                                <p>Total de Arquivos</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="system-counter">
                                <div class="widget-icon widget-info"> <i class="ion-android-upload"></i> </div>
                                <h4>{{ round($systemDisk / 1000) }}</h4>
                                <p>MBs Total</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="system-counter">
                                <div class="widget-icon widget-danger"> <i class="ion-ios-person"></i> </div>
                                <h4>{{ $systemUsers }}</h4>
                                <p>Usuários Total</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End team member -->
        </div>
        @endif

        <!-- documents -->

        <div class="page-title documents-page" style="overflow:visible;">
            <div class="row">
                <div class="col-md-6 col-xs-6">
                    <h3>Documentos</h3>
                    <p class="breadcrumbs text-muted"><span class="home-folder">Pasta Pessoal</span></p>
                </div>
                <div class="col-md-6 col-xs-6 text-right page-actions">
                    <a href="<?=url("Document@get");?>" class="btn btn-primary"><i class="ion-document-text"></i> Documentos</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 documents-group-holder">
                <div class="documents-filter light-card hidden-xs">
                    <div class="light-card-title">
                        <h4>Filtros</h4>
                    </div>
                    <div class="documents-filter-form">
                        <form>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="status" value="" checked><span class="outer"><span class="inner"></span></span>Todos</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="status" value="Signed"><span class="outer"><span class="inner"></span></span>Assinados</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="status" value="Unsigned"><span class="outer"><span class="inner"></span></span>Não assinados</label>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="type" value="" checked><span class="outer"><span class="inner"></span></span>Todos</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="type" value="files"><span class="outer"><span class="inner"></span></span>Arquivos</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="type" value="folders"><span class="outer"><span class="inner"></span></span>Pastas</label>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="extension" value="" checked><span class="outer"><span class="inner"></span></span>Todos</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="extension" value="pdf"><span class="outer"><span class="inner"></span></span>PDF</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="extension" value="doc"><span class="outer"><span class="inner"></span></span>Word</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="extension" value="xls"><span class="outer"><span class="inner"></span></span>Excel</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="radio"><input type="radio" name="extension" value="ppt"><span class="outer"><span class="inner"></span></span>Power Point</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row documents-grid">
                    <div class="col-md-12 content-list"><div class="loader-box"><div class="circle-loader"></div></div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    {{ view("includes/footer"); }}

    <div class="select-option">
        <div class="btn-group btn-group-justified">
            <a href="" action="open" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="Abrir"><i class="ion-ios-eye"></i></a>
            @if ( in_array("delete",json_decode($user->permissions)) )
            <a href="" action="delete" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="Apagar"><i class="ion-ios-trash"></i></a>
            @endif
            <a href="" action="rename" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="Renomear"><i class="ion-edit"></i></a>
            <a href="" action="share" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="Compartilhar"><i class="ion-share"></i></a>
        </div>
    </div>


    <!-- Rename folder Modal -->
    <div class="modal fade" id="renamefolder" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Renomear Pasta</h4>
                </div>
                <form class="simcy-form"action="<?=url("Document@updatefolder");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p class="text-muted">Mude o nome da sua pasta.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Nome da pasta</label>
                                    <input type="text" class="form-control" name="foldername" placeholder="Nome da pasta" data-parsley-required="true">
                                    <input type="hidden" name="folder" value="1">
                                    <input type="hidden" name="folderid">
                                    <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button class="btn btn-primary" type="submit">Renomear Pasta</button>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <!-- Rename file Modal -->
    <div class="modal fade" id="renamefile" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Renomear arquivo</h4>
                </div>
                <form class="simcy-form"action="<?=url("Document@updatefile");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p class="text-muted">Mude o nome do seu arquivo.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Nome do arquivo</label>
                                    <input type="text" class="form-control" name="filename" placeholder="Nome do arquivo" data-parsley-required="true">
                                    <input type="hidden" name="folder" value="1">
                                    <input type="hidden" name="fileid">
                                    <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button class="btn btn-primary" type="submit">Renomear arquivo</button>
                    </div>
                </form>
            </div>

        </div>
    </div>



    <!-- Shared Modal -->
    <div class="modal fade" id="shared" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Atualizar informação </h4>
                </div>
                <form class="shared-holder simcy-form"action="" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="loader-box"><div class="circle-loader"></div></div>
                </form>
            </div>

        </div>
    </div>



    <!-- Share Modal -->
    <div class="modal fade" id="sharefile" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Partilha de Documentos</h4>
                </div>
                <div class="modal-body">
                    <p>Qualquer pessoa com o link abaixo pode visualizar e editar este documento.</p>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Link de compartilhamento</label>
                                <input type="text" id="foo" class="form-control sharing-link" placeholder="Link de compartilhamento" readonly="readonly">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary copy-link" data-clipboard-action="copy" data-clipboard-target="#foo">Copiar Link</button>
                </div>
            </div>

        </div>
    </div>


    <!-- folder right click -->
    <div id="folder-menu" class="dropdown clearfix folder-actions">
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
            <li><a tabindex="-1" action="open" href="">Abrir</a>
            </li>
            <li><a tabindex="-1" action="rename" href="">Renomear</a>
            </li>
            <li><a tabindex="-1" action="protect" href="">Proteger</a>
            </li>
            @if ( $user->role != "user" )
            <li><a tabindex="-1" action="access" href="">Acessibilidade</a>
            </li>
            @endif
            @if ( in_array("delete",json_decode($user->permissions)) )
            <li class="divider"></li>
            <li><a tabindex="-1" action="delete" href="">Apagar</a>
            </li>
            @endif
        </ul>
    </div>
    
    <!--  file right click -->
    <div id="file-menu" class="dropdown clearfix file-actions">
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
            <li><a action="open" href="">Abrir</a>
            </li>
            <li><a action="rename" href="">Renomear</a>
            </li>
            <li><a action="duplicate" href="">Duplicar</a>
            </li>
            <li><a action="share" href="">Compartilhar</a>
            </li>
            <li><a action="download" href="">Baixar</a>
            </li>
            @if ( $user->role != "user" )
            <li><a tabindex="-1" action="access" href="">Acessibilidade</a>
            </li>
            @endif
            @if ( in_array("delete",json_decode($user->permissions)) )
            <li class="divider"></li>
            <li><a action="delete" href="">Apagar</a>
            </li>
            @endif
        </ul>
    </div>



    <!-- scripts -->
    <script type="text/javascript">
        var dropboxExtesions = ['.pdf'<?php if(env("ALLOW_NON_PDF") == "Enabled"){ ?>, '.doc', '.docx', '.ppt', '.pptx', '.xls', '.xlsx'<?php } ?>],
              docType = "documents",
              appUrl = "<?=env("APP_URL");?>",
              openFileUrl = "<?=url("Document@open");?>",
              documentsUrl = "<?=url("Document@fetch");?>",
              templatesUrl = "<?=url("Template@fetch");?>",
              relocateDocumentsUrl = "<?=url("Document@relocate");?>",
              duplicateFileUrl = "<?=url("Document@duplicate");?>",
              deleteUrl = "<?=url("Document@delete");?>",
              deleteFileUrl = "<?=url("Document@deletefile");?>",
              deleteFolderUrl = "<?=url("Document@deletefolder");?>",
              folderProtectViewUrl = "<?=url("Document@updatefolderprotectview");?>",
              folderProtectUrl = "<?=url("Document@updatefolderprotect");?>",
              folderAccessViewUrl = "<?=url("Document@updatefolderaccessview");?>",
              folderAccessUrl = "<?=url("Document@updatefolderaccess");?>",
              fileAccessViewUrl = "<?=url("Document@updatefileaccessview");?>",
              fileAccessUrl = "<?=url("Document@updatefileaccess");?>",
              dropboxUrl = "<?=url("Document@dropboximport");?>",
              googledriveimportUrl = "<?=url("Document@googledriveimport");?>",
              allowNonPDF = '<?=env("ALLOW_NON_PDF")?>';
    </script>
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/libs/clipboard/clipboard.min.js"></script>
    <script src="<?=url("");?>assets/libs/knob/jquery.knob.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/js/echarts.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
    <script src="<?=url("");?>assets/js/auth.js"></script>
    <script src="<?=url("");?>assets/js/files.js"></script>
    <script>
        $(function() {
            $(".dial").knob();
        });


        var dom = document.getElementById("meter");
        var myChart = echarts.init(dom);
        var app = {};
        option = null;

        option = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            color: ["#f62d51", "#009efb", "#55ce63", "#ffbc34", "#2f3d4a"],
            series: [{
                    name: 'Porcentagem assinada.',
                    type: 'pie',
                    selectedMode: 'single',
                    radius: [0, '30%'],

                    label: {
                        normal: {
                            position: 'inner'
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false,
                            color: "#f62d51",
                            type: "dashed"
                        }
                    },
                    data: [{
                            value: {{ $signed }},
                            name: 'Assinada',
                            selected: true
                        },
                        {
                            value: {{ $unsigned }},
                            name: 'Não Assinada'
                        }
                    ]
                },
                {
                    name: 'Tipos de Arquivo',
                    type: 'pie',
                    radius: ['40%', '55%'],
                    data: [
                        {
                            value: {{ $myWord }},
                            name: 'Microsoft Word'
                        },
                        {
                            value: {{ $myPpt }},
                            name: 'Power Point'
                        },
                        {
                            value: {{ $myExcel }},
                            name: 'Excel'
                        },
                        {
                            value: {{ $myPdf }},
                            name: 'PDF',
                            selected: true
                        }
                    ]
                }
            ]
        };;
        if (option && typeof option === "object") {
            myChart.setOption(option, true);
        }

    </script>
</body>

</html>
