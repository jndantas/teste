<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Crie suas assinaturas digitalmente e assine documentos em pdf..">
    <meta name="author" content="WTM">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Templates | WTM | Assine seus documentos online</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/select2/css/select2.min.css" rel="stylesheet">
    <link href="<?=url("");?>assets/css/simcify.min.css" rel="stylesheet">
    <!-- Signer CSS -->
    <link href="<?=url("");?>assets/css/style.css" rel="stylesheet">
    <script type="text/javascript">
          var developerKey = '<?=env("GOOGLE_API_KEY");?>';
          var clientId = '<?=env("GOOGLE_CLIENT_ID");?>';
    </script>
    <script src="<?=url("");?>assets/js/googledriveimporter.js"></script>
    <script type="text/javascript" src="https://apis.google.com/js/api.js?onload=onApiLoad"></script>
</head>

<body>

    <!-- header start -->
    {{ view("includes/header", $data); }}

    <!-- sidebar -->
    {{ view("includes/sidebar", $data); }}

    <div class="content">
        <div class="page-title templates-page" style="overflow:visible;">
            <div class="row">
                <div class="col-md-6 col-xs-6">
                    <h3>Templates</h3>
                    <p class="breadcrumbs text-muted">Gerencie seus modelos aqui.</p>
                </div>
                <div class="col-md-6 col-xs-6 text-right page-actions">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="ion-arrow-down-b"></i> Novo Template </button>
                        <ul class="dropdown-menu" role="menu">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)" data-toggle="modal" data-target="#uploadFile" data-backdrop="static" data-keyboard="false">
                                    <i class="ion-ios-cloud-upload-outline"></i> <span>Carregar Arquivo</span></a></li>
                            <?php if(!empty(env("DROPBOX_APP_KEY"))){ ?>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)" data-toggle="modal" data-target="#dropbox" data-backdrop="static" data-keyboard="false">
                                    <i class="ion-social-dropbox-outline"></i> <span>Dropbox</span></a></li>
                            <?php } ?>
                            <?php if(!empty(env("GOOGLE_CLIENT_ID")) && !empty(env("GOOGLE_API_KEY"))){ ?>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)" data-toggle="modal" data-target="#google-drive" data-backdrop="static" data-keyboard="false">
                                    <i class="ion-social-google-outline"></i> <span>Google Docs</span></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12 documents-group-holder">
                <div class="row documents-grid m-o">
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

    <!-- Upload file Modal -->
    <div class="modal fade" id="uploadFile" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Carregar Arquivo</h4>
                </div>
                <form class="simcy-form"action="<?=url("Template@uploadfile");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p>Apenas PDF<?php if(env("ALLOW_NON_PDF") == "Enabled"){ ?>, Word, Excel e Power Point <?php } ?> são permitidos.</p>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Nome do Arquivo</label>
                                    <input type="text" class="form-control" name="name" placeholder="Nome do Arquivo" data-parsley-required="true">
                                    <input type="hidden" name="folder" value="1">
                                    <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Escolha o Arquivo</label>
                                    <input type="file" name="file" class="dropify" data-parsley-required="true" data-allowed-file-extensions="pdf <?php if(env("ALLOW_NON_PDF") == "Enabled"){ ?>doc docx ppt pptx xls xlsx <?php } ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Carregar Arquivo</button>
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
                    <h4 class="modal-title">Renomear Arquivo</h4>
                </div>
                <form class="simcy-form"action="<?=url("Document@updatefile");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p class="text-muted">Troque o nome de seu arquivo.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Nome do Arquivo</label>
                                    <input type="text" class="form-control" name="filename" placeholder="Nome do Arquivo" data-parsley-required="true">
                                    <input type="hidden" name="folder" value="1">
                                    <input type="hidden" name="fileid">
                                    <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button class="btn btn-primary" type="submit">Renomear Arquivo</button>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <!-- Import from dropbox -->
    <div class="modal fade" id="dropbox" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Importar do Dropbox</h4>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Importe arquivos de sua Conta DropBox. Apenas PDF<?php if(env("ALLOW_NON_PDF") == "Enabled"){ ?>, Word, Excel e Power Point <?php } ?> são permitidos.</p>
                    <div class="dropbox-button-holder">
                        <div id="dropbox-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Google drive import-->
    <div class="modal fade" id="google-drive" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Importar do Google Drive</h4>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Importe arquivos de sua conta Google Drive. Apenas PDF<?php if(env("ALLOW_NON_PDF") == "Enabled"){ ?>, Word, Excel e Power Point <?php } ?> são permitidos.</p>. Verifique também se você possui <a href="https://support.google.com/drive/answer/2494822" target="_blank">ativado o compartilhamento do seu documento</a></p>
                    <div class="dropbox-button-holder">
                        <button class="btn btn-primary" type="button" id="auth" disabled><i class="ion-social-google-outline"></i> Importar agora</button>
                        <div id="result"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
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
                    <h4 class="modal-title">Atualizar Informação</h4>
                </div>
                <form class="shared-holder simcy-form"action="" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="loader-box"><div class="circle-loader"></div></div>
                </form>
            </div>

        </div>
    </div>



    <!--  file right click -->
    <div id="file-menu" class="dropdown clearfix file-actions">
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
            <li><a action="open" href="">Pre-visualizar</a>
            </li>
            <li><a action="rename" href="">Renomear</a>
            </li>
            <li><a action="duplicate" href="">Duplicar</a>
            </li>
            <li><a action="download" href="">Download</a>
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
              docType = "templates",
              appUrl = "<?=env("APP_URL");?>",
              openFileUrl = "<?=url("Document@open");?>",
              templatesUrl = "<?=url("Template@fetch");?>",
              documentsUrl = "<?=url("Document@fetch");?>",
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
              dropboxUrl = "<?=url("Template@dropboximport");?>",
              googledriveimportUrl = "<?=url("Template@googledriveimport");?>",
              allowNonPDF = '<?=env("ALLOW_NON_PDF")?>';
    </script>
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/libs/select2/js/select2.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>
    <script src="<?=url("");?>assets/libs/clipboard/clipboard.min.js"></script>
    <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="<?=env("DROPBOX_APP_KEY");?>"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/dropboximporter.js"></script>
    <script src="<?=url("");?>assets/js/app.js"></script>
    <script src="<?=url("");?>assets/js/files.js"></script>
</body>

</html>
