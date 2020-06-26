<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Crie suas assinaturas digitalmente e assine documentos em pdf..">
    <meta name="author" content="WTM">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>{{ $document->name }} | WTM | Assine seus documentos online</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=B612+Mono:400,400i,700|Charm:400,700|EB+Garamond:400,400i,700|Noto+Sans+TC:400,700|Open+Sans:400,400i,700|Pacifico|Reem+Kufi|Scheherazade:400,700|Tajawal:400,700&amp;subset=arabic" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/select2/css/select2.min.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
    <link href="<?=url("");?>assets/css/simcify.min.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.css" rel="stylesheet">
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
        <div class="page-title" style="overflow:visible;">
            <div class="pull-right page-actions">
                @if ( is_object($request) && $request->status == "Pending" )
                <button class="btn btn-success accept-request"><i class="ion-ios-checkmark-outline"></i> <span>Aceitar</span> </button>
                <button class="btn btn-danger send-to-server-click"  data="requestid:{{ $request->id }}|file:{{ $document->id }}|csrf-token:{{ csrf_token() }}" url="<?=url("Request@decline");?>" warning-title="Você tem certeza?" warning-message="Esta solicitação será recusada e o email será enviado ao remetente." warning-button="Yes, Decline" loader="true"><i class="ion-ios-close-outline"></i> Rejeitar </button>
                @else
                @if ( in_array("edit",json_decode($user->permissions)) )
                @if ( $document->extension != "pdf" )
                <button class="btn btn-success btn-responsive send-to-server-click"  data="source:viewer|document_key:{{ $document->document_key }}|csrf-token:{{ csrf_token() }}" url="<?=url("Document@convert");?>" warning-title="Converter?" warning-message="Este arquivo deve ser convertido em PDF antes de assinar ou editar. Clique em converter para continuar." warning-button="Converter" loader="true"><i class="ion-edit"></i>{{ $lauchLabel }}</button>
                @else
                <button class="btn btn-success btn-responsive launch-editor"><i class="ion-edit"></i>{{ $lauchLabel }}</button>
                @endif
                @endif
                @if ( $document->company ==  $user->company )
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle btn-responsive" data-toggle="dropdown"><i class="ion-arrow-down-b"></i> Mais </button>
                    <ul class="dropdown-menu document-menu">
                        @if ( $document->is_template == "No" )
                        <li><a href=""  data-toggle="modal" data-target="#sendFile" data-backdrop="static" data-keyboard="false"><i class="ion-ios-email-outline"></i> <span>Enviar</span></a></li>
                        <li class="divider"></li>
                        <li><a href="" data-toggle="modal" data-target="#sharefile" data-backdrop="static" data-keyboard="false">
                            <i class="ion-ios-world-outline"></i> <span>Importar link</span></a></li>
                        <li class="divider"></li>
                        @endif
                        <li><a href="" data-toggle="modal" data-target="#sendRequest" data-backdrop="static" data-keyboard="false"><i class="ion-ios-plus-outline"></i><span> Solicitar Assinatura</span></a></li>
                        <li class="divider"></li>
                        @if ( $document->is_template == "No" )
                        <li><a href="" data-toggle="modal" data-target="#protectFile" data-backdrop="static" data-keyboard="false">
                            <i class="ion-ios-locked-outline"></i> <span>Proteger</span></a></li>
                        <li class="divider"></li>
                        @endif
                        <li><a href="" data-toggle="modal" data-target="#replaceFile" data-backdrop="static" data-keyboard="false"> <i class="ion-ios-browsers-outline"></i><span> Substituir</span></a></li>
                        <li class="divider"></li>
                        @if ( $document->status == "Signed" || $document->editted == "Yes" )
                        <li data-toggle="tooltip" data-placement="left" data-original-title="Restaurar versão original"><a href="" class="send-to-server-click"  data="file:{{ $document->id }}|csrf-token:{{ csrf_token() }}" url="<?=url("Document@restore");?>" warning-title="Você tem certeza?" warning-message="O arquivo original será restaurado e esta ação é irreversível." warning-button="Continue" loader="true"><i class="ion-ios-loop"></i><span> Restaurar</span></a></li>
                        <li class="divider"></li>
                        <li><a href=""  data-toggle="modal" data-target="#downloadOptions" data-backdrop="static" data-keyboard="false"><i class="ion-ios-cloud-download-outline"></i><span> Download</span></a></li>
                        <li class="divider"></li>
                        @else
                        <li><a href="{{ url('') }}uploads/files/{{ $document->filename }}" download="{{ $document->name.'.'.$document->extension }}"><i class="ion-ios-cloud-download-outline"></i><span> Download</span></a></li>
                        <li class="divider"></li>
                        @endif
                        @if ( $document->extension != "pdf" )
                        <li data-toggle="tooltip" data-placement="left" data-original-title="Converter para PDF"><a href="" class="send-to-server-click"  data="document_key:{{ $document->document_key }}|csrf-token:<?=csrf_token();?>" url="<?=url("Document@convert");?>" warning-title="Você tem certeza?" warning-message="Este arquivo será convertido para PDF e esta ação é irreversível." warning-button="Continue" loader="true"><i class="ion-usb"></i><span> Converter</span></a></li>
                        <li class="divider"></li>
                        @endif
                        @if ( in_array("delete",json_decode($user->permissions)) )
                        <li><a href="" class="send-to-server-click"  data="source:viewer|file:{{ $document->id }}|csrf-token:{{ csrf_token() }}" url="<?=url("Document@deletefile");?>" warning-title="Você tem certeza?" warning-message="Este arquivo será excluído e não poderá ser recuperado." warning-button="Continue" loader="true"><i class="ion-ios-trash-outline"></i> <span>Apagar</span></a></li>
                        @endif
                    </ul>
                </div>
               @endif
               @endif
            </div>
            <h3 class="title-responsive">Arquivo</h3>
            <p class="text-muted">{{ $document->name }}</p>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="light-card document">
                    <div class="signer-document">
                        @if ( $document->extension == "pdf" )
                        <!-- open PDF docements -->
                        <div class="document-pagination">
                            <div class="pull-left">
                                <button id="prev" class="btn btn-default btn-round"><i class="ion-ios-arrow-left"></i></button>
                                <button id="next" class="btn btn-default btn-round"><i class="ion-ios-arrow-right"></i></button>
                                <span class="text-muted ml-15">Página <span id="page_num">0</span> de <span id="page_count">0</span></span>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-default btn-round btn-zoom" zoom="plus"><i class="ion-plus"></i></button>
                                <button class="btn btn-default btn-round btn-zoom" zoom="minus"><i class="ion-minus"></i></button>

                            </div>
                        </div>
                        <div class="document-load">
                            <div class="loader-box"><div class="circle-loader"></div></div>
                        </div>
                        <div class="document-error">
                            <i class="ion-android-warning text-danger"></i>
                            <p class="text-muted"><strong>Oops! </strong> <span class="error-message"> Aconteceu algum erro.</span></p>
                        </div>
                        <div class="text-center">
                            <div class="document-map"></div>
                            <canvas id="document-viewer"></canvas>
                        </div>
                        @else
                        <iframe src='https://view.officeapps.live.com/op/embed.aspx?src={{ env("APP_URL") }}/uploads/files/{{ $document->filename }}' width='100%' height='1000px' frameborder='0'></iframe>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 document-right-side">

                <div class="light-card document-right">
                    <div class="document-right-head">
                        <ul>
                            <li class="active"><a data-toggle="tab" href="#history">Histórico</a></li>
                            <li><a href="" class="right-bar-toggle" bar="chat-holder">Chat <span class="text-danger" style="display:none;"><i class="ion-ios-circle-filled"></i></span></a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div id="history" class="tab-pane fade in active">

                            <div class="timeline">
                                <div class="circle"></div>
                                <ul>
                                    @foreach ( $history as $history )
                                    <li class="{{ $history->type }}"><em class="text-xs">{{ date("F j, Y H:i", strtotime($history->time_)) }}</em> {{ $history->activity }}</li>
                                    @endforeach
                                </ul>
                                <div class="circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- footer -->
    {{ view("includes/footer"); }}


    <div class="signer-overlay">
        <div class="signer-overlay-header">
            <div class="signer-overlay-logo">
                <a href="<?=url("");?>"><img src="<?=url("");?>uploads/app/{{ env('APP_LOGO'); }}" class="img-responsive"></a>
            </div>
            <div class="signer-overlay-action">
                <button class="btn btn-responsive btn-default close-editor-overlay"><i class="ion-ios-close-outline"></i> Fechar </button>
                <button class="btn btn-responsive btn-primary signer-save"><i class="ion-ios-checkmark-outline"></i> <span>Salvar</span> </button>
            </div>
            <div class="signer-header-tools">
                <div class="signer-header-tool-holder">
                    <div class="signer-tool" tool="signature" action="true">
                        <div class="tool-icon tool-signature"></div>
                        <p>Assinatura</p>
                    </div>
                    <div class="signer-tool" tool="text" action="true">
                        <div class="tool-icon tool-text"></div>
                        <p>Texto</p>
                    </div>
                    <div class="signer-tool" tool="draw" action="true">
                        <div class="tool-icon tool-draw"></div>
                        <p>Desenhar</p>
                    </div>
                    <div class="signer-tool" tool="image" action="true">
                        <div class="tool-icon tool-image"></div>
                        <p>Imagem</p>
                    </div>
                    <div class="signer-tool" tool="symbol" action="true">
                        <div class="tool-icon tool-symbols"></div>
                        <p>Simbolos</p>
                    </div>
                    <div class="signer-tool" tool="shape" action="true">
                        <div class="tool-icon tool-shapes"></div>
                        <p>Formas</p>
                    </div>
                    <div class="signer-tool" tool="fields" action="true">
                        <div class="tool-icon tool-fields"></div>
                        <p>Campos</p>
                    </div>
                    @if ( empty($request) )
                    <div class="signer-tool" tool="input" action="true">
                        <div class="tool-icon tool-textinput"></div>
                        <p>Entrada</p>
                    </div>
                    @endif
                    <div class="signer-tool" tool="rotate" action="true">
                        <div class="tool-icon tool-rotate"></div>
                        <p>Girar</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="signer-more-tools">
            <div class="signer-tool" tool="alignleft" action="false" group="text">
                <div class="tool-icon tool-alignleft"></div>
                <p>Alinhar à esquerda</p>
            </div>
            <div class="signer-tool" tool="aligncenter" action="false" group="text">
                <div class="tool-icon tool-aligncenter"></div>
                <p>Alinhar ao Centro</p>
            </div>
            <div class="signer-tool" tool="alignright" action="false" group="text">
                <div class="tool-icon tool-alignright"></div>
                <p>AAlinhar à direita</p>
            </div>
            <div class="signer-tool" tool="bold" action="false" group="text">
                <div class="tool-icon tool-bold"></div>
                <p>Negrito</p>
            </div>
            <div class="signer-tool" tool="italic" action="false" group="text">
                <div class="tool-icon tool-italic"></div>
                <p>Itálico</p>
            </div>
            <div class="signer-tool" tool="underline" action="false" group="text">
                <div class="tool-icon tool-underline"></div>
                <p>Sublinhado</p>
            </div>
            <div class="signer-tool" tool="strikethrough" action="false" group="text">
                <div class="tool-icon tool-strikethrough"></div>
                <p>Tachado</p>
            </div>
            <div class="signer-tool" tool="font" action="false" group="text">
                <div class="tool-icon tool-font"></div>
                <p>Fonte</p>
            </div>
            <div class="signer-tool" tool="fontsize" action="false">
                <div class="tool-icon tool-fontsize">
                    <div class="numberInput">
                      <input type="text" class="font-size" value="14" min="0" />
                      <span class="arrow up"></span>
                      <span class="arrow down"></span>
                    </div>
                </div>
                <p class="font-size-label">Tamanho da Fonte</p>
            </div>
            <div class="signer-tool" tool="color" action="false" color="#000000">
                <div class="tool-icon tool-colorfill"><button id="color-picker" class="jscolor {valueElement:null,value:'000000', borderRadius:'1px', borderColor:'#e6eaee', onFineChange:'updateColor(this)'}"></button></div>
                <p>Cor</p>
            </div>
            <div class="signer-tool" tool="duplicate" action="false">
                <div class="tool-icon tool-duplicate"></div>
                <p>Duplicar</p>
            </div>
            <div class="signer-tool" tool="delete" action="false">
                <div class="tool-icon tool-delete"></div>
                <p>Apagar</p>
            </div>
        </div>
        <div class="signer-overlay-previewer col-md-8 light-card"></div>
        <div class="signer-overlay-footer">
            <p class="text-muted text-center"> Desenvolvido por <?=env("APP_NAME")?> | <?=date("Y")?> &copy; <?=env("APP_NAME")?> | Todos Direitos Reservados. </p>
        </div>
        <div class="signer-assembler"></div>
        <div class="signer-builder"></div>
    </div>


    <!-- Share Modal -->
    <div class="modal fade" id="sharefile" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Compartilhamento de Documentos</h4>
                </div>
                <div class="modal-body">
                    <p>Qualquer pessoa com esse link pode visualizar e editar esse documento</p>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Compartilhar link</label>
                                <input type="text" id="foo" class="form-control" value="{{ env('APP_URL') }}/view/{{ $document->document_key }}" placeholder="Compartilhar link" readonly="readonly">
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

    <!-- Replace file Modal -->
    <div class="modal fade" id="replaceFile" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Substituir arquivo</h4>
                </div>
                <form class="simcy-form"action="<?=url("Document@replace");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p>A substituição de um arquivo manterá todo o histórico e as configurações do documento. Apenas PDF <?php if(env("ALLOW_NON_PDF") == "Enabled"){ ?>, Word, Excel e Power Point <?php } ?> são permitidos.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Escolha Arquivo</label>
                                    <input type="file" name="file" class="dropify" data-parsley-required="true" data-allowed-file-extensions="pdf <?php if(env("ALLOW_NON_PDF") == "Enabled"){ ?>doc docx ppt pptx xls xlsx <?php } ?>">
                                    <input type="hidden" name="document_key" value="{{ $document->document_key }}" />
                                    <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Substituir Arquivo</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- request Fields Modal -->
    <div class="modal fade" id="requestFields" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Campos Obrigatórios</h4>
                </div>
                <form class="simcy-front-form" callback="updateRequestFields()" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p>O Remetente solicitou alguns campos de você.</p>
                        <div class="row requested-fields"></div>
                    </div>
                    <div class="modal-footer">
                        <a href="" class="btn btn-default">Fechar</a>
                        <button type="submit" class="btn btn-primary">Pré-Visualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Fields Modal -->
    <div class="modal fade" id="addField" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Adicionar Campo Personalizado</h4>
                </div>
                <form class="simcy-front-form" callback="addField()" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p>Adicione campos personalizados que você usa com frequência.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Nome do Campo</label>
                                    <input type="text" name="fieldlabel" class="form-control" placeholder="i.e Cidade" data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Valor do Campo</label>
                                    <input type="text" name="fieldvalue" class="form-control" placeholder="i.e Salvador" data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Adicionar Campo</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Add Input Fields Modal -->
    <div class="modal fade" id="addInputField" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Adiconar Campo de Entrada</h4>
                </div>
                <form class="simcy-front-form" callback="addInputField()" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p>Adicione um campo de entrada que será preenchido pelos usuários quando receberem uma solicitação de assinatura.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Nome do Campo de Entrada</label>
                                    <input type="text" name="inputfieldlabel" class="form-control" placeholder="i.e Nome Completo" data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label><input type="checkbox" class="switch" name="savefield" value="save" checked /> Salve esse campo de entrada.</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Adiconar Campo de Entrada</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Choose doc image Modal -->
    <div class="modal fade" id="selectImage" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Escolha uma imagem</h4>
                </div>
                <form class="simcy-front-form" callback="selectDocImage()" data-parsley-validate="">
                    <div class="modal-body">
                        <p>Selecione uma imagem para adicionar o documento.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Escolha arquivo</label>
                                    <input type="file" name="document-selected-image" class="dropify" data-parsley-required="true" data-allowed-file-extensions="png jpg jpeg svg gif" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Inserir Imagem</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Request Invite Modal -->
    <div class="modal fade" id="sendRequest" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pedido de Assinatura</h4>
                </div>
                <form class="simcy-front-form" callback="validateRequest()" data-parsley-validate="" loader="false" method="POST">
                    <div class="modal-body">
                        @if ( $document->is_template == "Yes" )
                        <p>Uma cópia deste modelo será criada na pasta de documentos para esta solicitação de assinatura. Se houver algum campo, o destinatário será solicitado a preencher.</p>
                        @endif
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Mensagem para os destinatários</label>
                                    <textarea class="form-control" name="requestmessage" rows="3" required="" placeholder="Mensagem para os destinatários"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <p class="text-muted">Digite os endereços de email dos destinatários separados por vírgula.</p>
                                    <input type="text" name="recipients" class="form-control" placeholder="Enter emails"  data-role="tagsinput" data-parsley-required="true">
                                    <p></p>
                                    <input type="hidden" name="document_key" value="{{ $document->document_key }}" />
                                    <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group duplicate-request" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>
                                        <input type="checkbox" class="switch" name="duplicate" value="1" />Envie cópias separadas para cada solicitação
                                    </label>
                                </div>
                            </div>
                        </div>
                        @if ( $document->is_template == "No" || $document->is_template == "Yes" && empty($document->template_fields) )
                        <div class="form-group restricted-request">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>
                                        <input type="checkbox" class="switch" name="restricted" value="1" /> Destinatário para assinar pontos específicos ou adicionar entradas.
                                    </label> 
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Select from</label>
                                    <div class="select-from-tabs">
                                        <ul class="tabs">
                                            <li class="active"><a data-toggle="tab" href="#send-team">Equipe</a></li>
                                            <li><a data-toggle="tab" href="#send-customers">Clientes </a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="send-team" class="tab-pane fade in active">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if ( !empty($team) )
                                                        @foreach ( $team as $member )
                                                            <div class="user-select-item">
                                                                <div class="user-select-action">
                                                                    <input type="checkbox" class="switch" name="request-select" email="{{ $member->email }}" value="{{ $member->email }}" />
                                                                </div>
                                                                <div class="user-select-image">
                                                                    @if ( !empty($member->avatar) )
                                                                    <img src="<?=url("")?>uploads/avatar/{{ $member->avatar }}" class="img-responsive img-circle table-avatar">
                                                                    @else
                                                                    <img src="<?=url("")?>assets/images/avatar.png" class="img-responsive table-avatar">
                                                                    @endif
                                                                </div>
                                                                <div class="user-select-name">
                                                                    <h5>{{ $member->fname }} {{ $member->lname }}</h5>
                                                                    <p>{{ $member->email }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        @else
                                                            <p class="text-center">Tudo Vazio aqui!!</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="send-customers" class="tab-pane fade">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if ( !empty($customers) )
                                                        @foreach ( $customers as $customer )
                                                            <div class="user-select-item">
                                                                <div class="user-select-action">
                                                                    <input type="checkbox" class="switch" name="request-select" email="{{ $customer->email }}" value="{{ $customer->email }}" />
                                                                </div>
                                                                <div class="user-select-image">
                                                                    @if ( !empty($customer->avatar) )
                                                                    <img src="<?=url("")?>uploads/avatar/{{ $customer->avatar }}" class="img-responsive img-circle table-avatar">
                                                                    @else
                                                                    <img src="<?=url("")?>assets/images/avatar.png" class="img-responsive table-avatar">
                                                                    @endif
                                                                </div>
                                                                <div class="user-select-name">
                                                                    <h5>{{ $customer->fname }} {{ $customer->lname }}</h5>
                                                                    <p>{{ $customer->email }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        @else
                                                            <p class="text-center">Tudo vazio aqui!</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Enviar Pedido</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Send file Modal -->
    <div class="modal fade" id="sendFile" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Enviar Arquivo</h4>
                </div>
                <form class="simcy-form"action="<?=url("Document@send");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Mensagem para os destinatários</label>
                                    <textarea class="form-control" name="message" rows="3" required="" placeholder="Mensagem para os destinatários"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <p class="text-muted">Digite os endereços de email dos destinatários separados por vírgula.</p>
                                    <input type="text" name="receivers" class="form-control" placeholder="Digite e-mails"  data-role="tagsinput" data-parsley-required="true">
                                    <p></p>
                                    <input type="hidden" name="document_key" value="{{ $document->document_key }}" />
                                    <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Selecione de</label>
                                    <div class="select-from-tabs">
                                        <ul class="tabs">
                                            <li class="active"><a data-toggle="tab" href="#send-team">Equipe</a></li>
                                            <li><a data-toggle="tab" href="#send-customers">Clientes </a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="send-team" class="tab-pane fade in active">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if ( !empty($team) )
                                                        @foreach ( $team as $member )
                                                            <div class="user-select-item">
                                                                <div class="user-select-action">
                                                                    <input type="checkbox" class="switch" name="send-select" email="{{ $member->email }}" value="{{ $member->email }}" />
                                                                </div>
                                                                <div class="user-select-image">
                                                                    @if ( !empty($member->avatar) )
                                                                    <img src="<?=url("")?>uploads/avatar/{{ $member->avatar }}" class="img-responsive img-circle table-avatar">
                                                                    @else
                                                                    <img src="<?=url("")?>assets/images/avatar.png" class="img-responsive table-avatar">
                                                                    @endif
                                                                </div>
                                                                <div class="user-select-name">
                                                                    <h5>{{ $member->fname }} {{ $member->lname }}</h5>
                                                                    <p>{{ $member->email }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        @else
                                                            <p class="text-center">Tudo vazio aqui!</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="send-customers" class="tab-pane fade">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if ( !empty($customers) )
                                                        @foreach ( $customers as $customer )
                                                            <div class="user-select-item">
                                                                <div class="user-select-action">
                                                                    <input type="checkbox" class="switch" name="send-select" email="{{ $customer->email }}" value="{{ $customer->email }}" />
                                                                </div>
                                                                <div class="user-select-image">
                                                                    @if ( !empty($customer->avatar) )
                                                                    <img src="<?=url("")?>uploads/avatar/{{ $customer->avatar }}" class="img-responsive img-circle table-avatar">
                                                                    @else
                                                                    <img src="<?=url("")?>assets/images/avatar.png" class="img-responsive table-avatar">
                                                                    @endif
                                                                </div>
                                                                <div class="user-select-name">
                                                                    <h5>{{ $customer->fname }} {{ $customer->lname }}</h5>
                                                                    <p>{{ $customer->email }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        @else
                                                            <p class="text-center">Tudo vazio aqui!</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Enviar Arquivo</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Unlock File Modal -->
    <div class="modal fade" id="unlockFile" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="text-danger text-center"><i class="ion-ios-locked"></i></div>
                    <h4 class="modal-title">Arquivo Protegido</h4>
                </div>
                <form class="unlock-file"action="" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                    <div class="alert alert-danger alert-dismissable text-center document-password-error" style="display: none;">
                        <i class="ion-ios-information-outline"></i>  <span class="password-error"></span>
                    </div>
                        <p class="text-muted">Este arquivo é protegido por senha. Digite a senha correta para desbloquear.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Senha do Documento</label>
                                    <input type="password" class="form-control" name="docpassword" placeholder="Senha do Documento" data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="<?=url("Document@get");?>" class="btn btn-default">Fechar</a>
                        <button class="btn btn-primary" type="submit">Desbloquear Arquivo</button>
                    </div>
                </form>
            </div>

        </div>
    </div>


      <!-- protect file -->
      <div class="modal fade" id="protectFile" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Proteção de Documentos</h4>
            </div>
              <form class="simcy-form"action="<?=url("Document@protect");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <div class="alert alert-warning">
                          <strong>Atenção:</strong> Esta ação é irreversível e este documento não será mais assinado ou modificado pelo assinante após a proteção ser definida
                        </div>
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-12">
                                <label><input type="checkbox" class="switch" name="permission[]" value="print"/>Impedir a impressão de documentos.</label>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-12">
                                <label><input type="checkbox" class="switch" name="permission[]" value="copy"/> Impedir a cópia de texto e imagens deste documento.</label>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-12">
                                <label><input type="checkbox" class="switch" name="permission[]" value="modify"/>Impedir modificação de documento.</label>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-12">
                                <label><input type="checkbox" class="switch" name="permission[]" value="extract"/>Impedir a extração de conteúdo do documento.</label>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-12">
                                <label><input type="checkbox" class="switch" name="permission[]" value="assemble"/> Impedir a montagem de documentos.</label> <span class="text-muted text-xs"> inserir, girar ou excluir páginas e criar marcadores ou imagens em miniatura</span>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-12">
                                <label><input type="checkbox" class="switch password-protect-toggle" name="setPassword" value="Yes" /> Defina uma senha para este documento.</label>
                              </div>
                            </div>
                          </div>
                          <div class="form-group protection-password" style="display: none;">
                            <div class="row">
                              <div class="col-md-12">
                                    <label>Enter user password</label><span class="text-muted text-xs">Para outros usuários / público visualizarem o arquivo</span>
                                    <input type="password" class="form-control" name="userpassword" placeholder="Enter user password">
                                    <input type="hidden" name="document_key" value="{{ $document->document_key }}" />
                                    <input type="hidden" name="csrf-token" value="<?=csrf_token();?>" />
                              </div>
                            </div>
                          </div>
                          <div class="form-group protection-password" style="display: none;">
                            <div class="row">
                              <div class="col-md-12">
                                    <label>Enter owner password</label><span class="text-muted text-xs"> For owner with full permissions</span>
                                    <input type="password" class="form-control" name="ownerpassword" placeholder="Enter owner password">
                              </div>
                            </div>
                          </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                      <button type="submit" class="btn btn-primary">Set Protection</button>
                    </div>
              </form>
          </div>
          
        </div>
      </div>

    <!-- File Download Options Modal -->
    <div class="modal fade" id="downloadOptions" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Document Download</h4>
                </div>
                <div class="modal-body">
                    <p class="text-center">This document has been modified, would you like to download the modified or original version.</p>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 text-center mb-30">
                                <label>Choose Version</label>
                            </div>
                            <div class="col-md-12 text-center">
                                <a class="btn btn-success ml-15" href="{{ url('') }}uploads/files/{{ $document->filename }}" download="{{ $document->name.'.'.$document->extension }}">Modified</a>
                                <a class="btn btn-primary" href="{{ url('') }}uploads/copies/{{ $document->filename }}" download="{{ $document->name.'.'.$document->extension }}">Original</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </div>

        </div>
    </div>

    <!-- document chat -->
    <div class="right-bar chat-holder light-card">
       <div class="right-bar-head">
          <div class="pull-right close-right-bar-head close-right-bar"><i class="ion-ios-close-outline"></i></div>
          <h4>Document Chat</h4>
       </div>
       <div class="right-bar-body">
          <div class='chat-wrapper'>
             <div class='chat-message chat-list'>
                @if ( !empty($chats) )
                @foreach ( $chats as $chat )
                    @if ( $user->id == $chat->sender )
                    <div class="chat-message chat-message-sender" id="{{ $chat->id }}">
                    @else
                    <div class="chat-message chat-message-recipient" id="{{ $chat->id }}">
                    @endif
                            @if ( !empty($chat->avatar) )
                                <img class='chat-image chat-image-default' src='<?=url("")?>uploads/avatar/{{ $chat->avatar }}'  data-toggle="tooltip" data-placement="top" title="{{ $chat->fname }} {{ $chat->lname }}" />
                            @else
                                <img class='chat-image chat-image-default' src='<?=url("")?>assets/images/avatar.png'  data-toggle="tooltip" data-placement="top" title="{{ $chat->fname }} {{ $chat->lname }}" />
                            @endif
                       <div class='chat-message-wrapper'>
                          <div class='chat-message-content'>
                             <p>{{ $chat->message }}</p>
                          </div>
                          <div class='chat-details'>
                             <span class='chat-message-localization font-size-small'>{{ date("F j, Y H:i", strtotime($chat->time_)) }}</span>
                          </div>
                       </div>
                    </div>
                @endforeach
                @else
                    <div class="empty-chat" id="0">
                       <i class="ion-chatbubbles"></i>
                       <p>Tudo vazio aqui!</p>
                    </div>
                @endif
             </div>
          </div>
       </div>
       <form class="chat-box">
          <textarea class="form-control new-message" rows="2" placeholder="Write your message"></textarea>
       </form>
    </div>

    <!-- Fonts list -->
    <div class="right-bar font-list light-card">
       <div class="right-bar-head">
          <div class="pull-right close-right-bar-head close-right-bar"><i class="ion-ios-close-outline"></i></div>
          <h4>Selecione a Fonte</h4>
       </div>
       <div class="right-bar-body">
        <div class="font-list">
            <div class="font-item selected" font="lato" family="'Lato', sans-serif" style="font-family:'Lato', sans-serif;">Lato</div>
            <div class="font-item" font="opensans" family="'Open Sans', sans-serif" style="font-family:'Open Sans', sans-serif;">Open Sans</div>
            <div class="font-item" font="charmi" family="'Charm', cursive" style="font-family:'Charm', cursive;">Charm</div>
            <div class="font-item" font="tajawal" family="'Tajawal', sans-serif" style="font-family:'Tajawal', sans-serif;">Tajawal (Arabic)</div>
            <div class="font-item" font="scheherazade" family="'Scheherazade', serif" style="font-family:'Scheherazade', serif;">Scheherazade (Arabic)</div>
            <div class="font-item" font="reemkufi" family="'Reem Kufi', sans-serif" style="font-family:'Reem Kufi', sans-serif;">Reem Kufi (Arabic)</div>
            <div class="font-item" font="pacificoi" family="'Pacifico', cursive" style="font-family:'Pacifico', cursive;">Pacifico</div>
            <div class="font-item" font="b612mono" family="'B612 Mono', monospace" style="font-family:'B612 Mono', monospace;">B612 Mono</div>
            <div class="font-item" font="ebgaramond" family="'EB Garamond', serif" style="font-family:'EB Garamond', serif;">EB Garamond</div>
        </div>
       </div>
    </div>

    <!-- Fields list -->
    <div class="right-bar fields-list light-card">
       <div class="right-bar-head">
          <div class="pull-right close-right-bar-head close-right-bar"><i class="ion-ios-close-outline"></i></div>
          <h4>Custom Fields</h4>
       </div>
       <div class="right-bar-body">
        <div class="field-list">
            <div class="field-item">
                <div>{{ $user->fname }} {{ $user->lname }}</div>
                <span class="text-muted text-xs">Full Name</span>
            </div>
            @if ( !empty( $user->company ) )
            <div class="field-item">
                <div>{{ $company->name }}</div>
                <span class="text-muted text-xs">Empresa</span>
            </div>
            @endif
            <div class="field-item">
                <div>{{ mb_substr($user->fname, 0, 1, 'utf-8') }} {{ mb_substr($user->lname, 0, 1, 'utf-8') }}</div>
                <span class="text-muted text-xs">Inicial</span>
            </div>
            <div class="field-item">
                <div>{{ $user->email }}</div>
                <span class="text-muted text-xs">Email</span>
            </div>
            @if ( !empty( $user->address ) )
            <div class="field-item">
                <div>{{ $user->address }}</div>
                <span class="text-muted text-xs">Endereço</span>
            </div>
            @endif
            <div class="field-item">
                <div>{{ date("F j, Y") }}</div>
                <span class="text-muted text-xs">Data de Hoje</span>
            </div>
            @if ( !empty($fields) )
            @foreach ( $fields as $field )
            <div class="field-item" id="{{ $field->id }}">
                <a class="delete-field" id="delete-field" href=""><i class="ion-ios-trash-outline" id="delete-field"></i></a>
                <div>{{ $field->value }}</div>
                <span class="text-muted text-xs">{{ $field->label }}</span>
            </div>
            @endforeach
            @endif
            <button class="btn btn-primary btn-round" data-toggle="modal" data-target="#addField" data-backdrop="static" data-keyboard="false"><i class="ion-plus"></i></button>
        </div>
       </div>
    </div>



    <!-- Input Fields list -->
    <div class="right-bar input-fields-list light-card">
       <div class="right-bar-head">
          <div class="pull-right close-right-bar-head close-right-bar"><i class="ion-ios-close-outline"></i></div>
          <h4>Input Fields</h4>
       </div>
       <div class="right-bar-body">
        <div class="input-field-list">
            <p class="text-muted">These are fields a user will fill when they receive a signing request</p>
            <div class="input-field-item">
                <div>Signature</div>
            </div>
            <div class="input-field-item">
                <div>Full Name</div>
            </div>
            <div class="input-field-item">
                <div>Email</div>
            </div>
            <div class="input-field-item">
                <div>Phone Number</div>
            </div>
            <div class="input-field-item">
                <div>Empresa</div>
            </div>
            <div class="input-field-item">
                <div>Endereço</div>
            </div>
            @if ( !empty($inputfields) )
            @foreach ( $inputfields as $field )
            <div class="input-field-item" id="{{ $field->id }}">
                <a class="delete-input-field" href=""><i class="ion-ios-trash-outline" id="delete-input-field"></i></a>
                <div>{{ $field->label }}</div>
            </div>
            @endforeach
            @endif
            <button class="btn btn-primary btn-round" data-toggle="modal" data-target="#addInputField" data-backdrop="static" data-keyboard="false"><i class="ion-plus"></i></button>
        </div>
       </div>
    </div>

    <!-- Symbols list -->
    <div class="right-bar symbol-list light-card">
       <div class="right-bar-head">
          <div class="pull-right close-right-bar-head close-right-bar"><i class="ion-ios-close-outline"></i></div>
          <h4>Selecione o símbolo</h4>
       </div>
       <div class="right-bar-body">
        <div class="symbol-list">
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 249.425 249.425" style="enable-background:new 0 0 249.425 249.425;" xml:space="preserve"> <g> <path d="M206.79,81.505c-3.313,0-6,2.687-6,6v149.919H12V48.635h146.792c3.314,0,6-2.687,6-6s-2.686-6-6-6H6c-3.313,0-6,2.687-6,6 v200.789c0,3.313,2.687,6,6,6h200.79c3.313,0,6-2.687,6-6V87.505C212.79,84.192,210.103,81.505,206.79,81.505z"/> <path d="M247.667,1.758c-2.343-2.343-6.142-2.345-8.485,0L80.416,160.523L41.023,121.13c-2.343-2.344-6.143-2.344-8.485,0 c-2.343,2.343-2.343,6.142,0,8.484l43.636,43.636c1.171,1.172,2.707,1.758,4.243,1.758s3.071-0.586,4.243-1.758L247.667,10.243 C250.011,7.9,250.011,4.101,247.667,1.758z"/> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     viewBox="0 0 52 52" style="enable-background:new 0 0 52 52;" xml:space="preserve"><g>  <path d="M26,0C11.664,0,0,11.663,0,26s11.664,26,26,26s26-11.663,26-26S40.336,0,26,0z M26,50C12.767,50,2,39.233,2,26     S12.767,2,26,2s24,10.767,24,24S39.233,50,26,50z"/>  <path d="M38.252,15.336l-15.369,17.29l-9.259-7.407c-0.43-0.345-1.061-0.274-1.405,0.156c-0.345,0.432-0.275,1.061,0.156,1.406     l10,8C22.559,34.928,22.78,35,23,35c0.276,0,0.551-0.114,0.748-0.336l16-18c0.367-0.412,0.33-1.045-0.083-1.411     C39.251,14.885,38.62,14.922,38.252,15.336z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 511.999 511.999" style="enable-background:new 0 0 511.999 511.999;" xml:space="preserve"><g>  <g>     <path d="M506.231,75.508c-7.689-7.69-20.158-7.69-27.849,0l-319.21,319.211L33.617,269.163c-7.689-7.691-20.158-7.691-27.849,0         c-7.69,7.69-7.69,20.158,0,27.849l139.481,139.481c7.687,7.687,20.16,7.689,27.849,0l333.133-333.136           C513.921,95.666,513.921,83.198,506.231,75.508z"/>   </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M505.943,6.058c-8.077-8.077-21.172-8.077-29.249,0L6.058,476.693c-8.077,8.077-8.077,21.172,0,29.249         C10.096,509.982,15.39,512,20.683,512c5.293,0,10.586-2.019,14.625-6.059L505.943,35.306           C514.019,27.23,514.019,14.135,505.943,6.058z"/> </g></g><g> <g>     <path d="M505.942,476.694L35.306,6.059c-8.076-8.077-21.172-8.077-29.248,0c-8.077,8.076-8.077,21.171,0,29.248l470.636,470.636            c4.038,4.039,9.332,6.058,14.625,6.058c5.293,0,10.587-2.019,14.624-6.057C514.018,497.866,514.018,484.771,505.942,476.694z"/> </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="438.533px" height="438.533px" viewBox="0 0 438.533 438.533" style="enable-background:new 0 0 438.533 438.533;" xml:space="preserve"> <g> <path d="M409.133,109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736,9.801,259.058,0,219.273,0 c-39.781,0-76.47,9.801-110.063,29.407c-33.595,19.604-60.192,46.201-79.8,79.796C9.801,142.8,0,179.489,0,219.267 c0,39.78,9.804,76.463,29.407,110.062c19.607,33.592,46.204,60.189,79.799,79.798c33.597,19.605,70.283,29.407,110.063,29.407 s76.47-9.802,110.065-29.407c33.593-19.602,60.189-46.206,79.795-79.798c19.603-33.596,29.403-70.284,29.403-110.062 C438.533,179.485,428.732,142.795,409.133,109.203z M353.742,297.208c-13.894,23.791-32.736,42.633-56.527,56.534 c-23.791,13.894-49.771,20.834-77.945,20.834c-28.167,0-54.149-6.94-77.943-20.834c-23.791-13.901-42.633-32.743-56.527-56.534 c-13.897-23.791-20.843-49.772-20.843-77.941c0-28.171,6.949-54.152,20.843-77.943c13.891-23.791,32.738-42.637,56.527-56.53 c23.791-13.895,49.772-20.84,77.943-20.84c28.173,0,54.154,6.945,77.945,20.84c23.791,13.894,42.634,32.739,56.527,56.53 c13.895,23.791,20.838,49.772,20.838,77.943C374.58,247.436,367.637,273.417,353.742,297.208z"/> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     width="438.533px" height="438.533px" viewBox="0 0 438.533 438.533" style="enable-background:new 0 0 438.533 438.533;"   xml:space="preserve"><g>   <path d="M409.133,109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736,9.801,259.058,0,219.273,0      c-39.781,0-76.47,9.801-110.063,29.407c-33.595,19.604-60.192,46.201-79.8,79.796C9.801,142.8,0,179.489,0,219.267      c0,39.78,9.804,76.463,29.407,110.062c19.607,33.592,46.204,60.189,79.799,79.798c33.597,19.605,70.283,29.407,110.063,29.407       s76.47-9.802,110.065-29.407c33.593-19.602,60.189-46.206,79.795-79.798c19.603-33.596,29.403-70.284,29.403-110.062        C438.533,179.485,428.732,142.795,409.133,109.203z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve"><path d="M30,0C13.458,0,0,13.458,0,30s13.458,30,30,30s30-13.458,30-30S46.542,0,30,0z M30,58C14.561,58,2,45.439,2,30  S14.561,2,30,2s28,12.561,28,28S45.439,58,30,58z"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     width="44.238px" height="44.238px" viewBox="0 0 44.238 44.238" style="enable-background:new 0 0 44.238 44.238;"     xml:space="preserve"><g>   <g>     <g>         <path d="M15.533,29.455c-0.192,0-0.384-0.073-0.53-0.22c-0.293-0.293-0.293-0.769,0-1.062l13.171-13.171               c0.293-0.293,0.768-0.293,1.061,0s0.293,0.768,0,1.061L16.063,29.235C15.917,29.382,15.725,29.455,15.533,29.455z"/>        </g>        <g>         <path d="M28.704,29.455c-0.192,0-0.384-0.073-0.53-0.22L15.002,16.064c-0.293-0.293-0.293-0.768,0-1.061s0.768-0.293,1.061,0               l13.171,13.171c0.293,0.293,0.293,0.769,0,1.062C29.088,29.382,28.896,29.455,28.704,29.455z"/>        </g>        <path d="M22.119,44.237C9.922,44.237,0,34.315,0,22.12C0,9.924,9.922,0.001,22.119,0.001S44.238,9.923,44.238,22.12            S34.314,44.237,22.119,44.237z M22.119,1.501C10.75,1.501,1.5,10.751,1.5,22.12s9.25,20.619,20.619,20.619          s20.619-9.25,20.619-20.619S33.488,1.501,22.119,1.501z"/>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     width="438.533px" height="438.533px" viewBox="0 0 438.533 438.533" style="enable-background:new 0 0 438.533 438.533;"   xml:space="preserve"><g>   <g>     <path d="M409.133,109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736,9.801,259.058,0,219.273,0          c-39.781,0-76.47,9.801-110.063,29.407c-33.595,19.604-60.192,46.201-79.8,79.796C9.801,142.8,0,179.489,0,219.267          c0,39.78,9.804,76.463,29.407,110.062c19.607,33.592,46.204,60.189,79.799,79.798c33.597,19.605,70.283,29.407,110.063,29.407           s76.47-9.802,110.065-29.407c33.593-19.602,60.189-46.206,79.795-79.798c19.603-33.596,29.403-70.284,29.403-110.062            C438.533,179.485,428.732,142.795,409.133,109.203z M353.742,297.208c-13.894,23.791-32.736,42.633-56.527,56.534           c-23.791,13.894-49.771,20.834-77.945,20.834c-28.167,0-54.149-6.94-77.943-20.834c-23.791-13.901-42.633-32.743-56.527-56.534          c-13.897-23.791-20.843-49.772-20.843-77.941c0-28.171,6.949-54.152,20.843-77.943c13.891-23.791,32.738-42.637,56.527-56.53            c23.791-13.895,49.772-20.84,77.943-20.84c28.173,0,54.154,6.945,77.945,20.84c23.791,13.894,42.634,32.739,56.527,56.53            c13.895,23.791,20.838,49.772,20.838,77.943C374.58,247.436,367.637,273.417,353.742,297.208z"/>       <path d="M219.27,146.178c-20.177,0-37.401,7.139-51.678,21.411c-14.272,14.277-21.411,31.501-21.411,51.678            c0,20.175,7.135,37.402,21.411,51.673c14.277,14.277,31.504,21.416,51.678,21.416c20.179,0,37.406-7.139,51.676-21.416          c14.274-14.271,21.413-31.498,21.413-51.673c0-20.177-7.139-37.401-21.413-51.678C256.676,153.316,239.449,146.178,219.27,146.178           z"/>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     viewBox="0 0 51.997 51.997" style="enable-background:new 0 0 51.997 51.997;" xml:space="preserve"><path d="M51.911,16.242C51.152,7.888,45.239,1.827,37.839,1.827c-4.93,0-9.444,2.653-11.984,6.905  c-2.517-4.307-6.846-6.906-11.697-6.906c-7.399,0-13.313,6.061-14.071,14.415c-0.06,0.369-0.306,2.311,0.442,5.478  c1.078,4.568,3.568,8.723,7.199,12.013l18.115,16.439l18.426-16.438c3.631-3.291,6.121-7.445,7.199-12.014  C52.216,18.553,51.97,16.611,51.911,16.242z"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     viewBox="0 0 33.875 33.875" style="enable-background:new 0 0 33.875 33.875;" xml:space="preserve"><g>  <path d="M21.173,32.393c-4.11,0-6.222-0.592-7.618-0.984c-0.655-0.184-1.129-0.317-1.558-0.317H6.339c-0.276,0-0.5-0.224-0.5-0.5       V18.816c0-0.276,0.224-0.5,0.5-0.5c1.96,0,3.658-1.084,5.046-3.222c1.951-3.008,3.1-4.695,5.162-7.354      c1.951-2.514,3.032-5.272,3.226-6.146l0.072-0.351C19.965,0.632,20.089,0,21.107,0c1.995,0,3.072,2.303,3.072,4.471     c0,1.338-0.504,3.138-1.54,5.503c-0.403,0.921-0.34,1.563-0.163,1.833c0.088,0.135,0.212,0.198,0.39,0.198h7.937        c0.966,0.111,2.68,0.864,2.68,2.938c0,1.374-0.498,2.14-0.968,2.557c0.464,0.421,0.968,1.155,0.968,2.355       c0,1.396-0.925,2.276-1.61,2.736c0.354,0.405,0.698,1.087,0.698,2.209c0,1.597-1.362,2.408-2.229,2.763     c0.344,0.622,0.411,1.356,0.239,2.364C30.285,31.656,27.471,32.393,21.173,32.393z M6.839,30.092h5.158     c0.566,0,1.123,0.156,1.828,0.354c1.344,0.377,3.375,0.947,7.348,0.947c7.084,0,8.316-1.023,8.421-1.633        c0.2-1.171-0.014-1.675-0.425-2.109c-0.123-0.13-0.166-0.316-0.115-0.487c0.052-0.171,0.19-0.302,0.365-0.343       c0.021-0.005,2.15-0.524,2.15-2.019c0-1.491-0.74-1.794-0.771-1.806c-0.191-0.073-0.324-0.259-0.326-0.464      c-0.002-0.205,0.114-0.393,0.305-0.471c0.017-0.007,1.705-0.715,1.705-2.205c0-1.488-1.063-1.903-1.073-1.907       c-0.205-0.077-0.336-0.277-0.324-0.495c0.012-0.218,0.163-0.403,0.375-0.458c0.034-0.01,1.022-0.321,1.022-2.053        c0-1.714-1.668-1.933-1.739-1.941l-7.877,0.003c-0.517,0-0.952-0.231-1.227-0.651c-0.432-0.661-0.401-1.674,0.084-2.782     c0.979-2.236,1.456-3.905,1.456-5.102C23.179,3.03,22.537,1,21.107,1c-0.152,0-0.198,0.019-0.199,0.02      c-0.005,0.027-0.047,0.243-0.081,0.416L20.75,1.812c-0.209,0.941-1.357,3.895-3.412,6.542c-2.04,2.631-3.178,4.302-5.113,7.286      c-1.47,2.263-3.278,3.491-5.385,3.658V30.092z"/> <path d="M6.339,33.875H0.893c-0.276,0-0.5-0.224-0.5-0.5V16.196c0-0.276,0.224-0.5,0.5-0.5h5.446c0.276,0,0.5,0.224,0.5,0.5v17.179     C6.839,33.651,6.616,33.875,6.339,33.875z M1.393,32.875h4.446V16.696H1.393V32.875z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     viewBox="0 0 51.997 51.997" style="enable-background:new 0 0 51.997 51.997;" xml:space="preserve"><g>  <path d="M51.911,16.242C51.152,7.888,45.239,1.827,37.839,1.827c-4.93,0-9.444,2.653-11.984,6.905     c-2.517-4.307-6.846-6.906-11.697-6.906c-7.399,0-13.313,6.061-14.071,14.415c-0.06,0.369-0.306,2.311,0.442,5.478      c1.078,4.568,3.568,8.723,7.199,12.013l18.115,16.439l18.426-16.438c3.631-3.291,6.121-7.445,7.199-12.014      C52.216,18.553,51.97,16.611,51.911,16.242z M49.521,21.261c-0.984,4.172-3.265,7.973-6.59,10.985L25.855,47.481L9.072,32.25        c-3.331-3.018-5.611-6.818-6.596-10.99c-0.708-2.997-0.417-4.69-0.416-4.701l0.015-0.101C2.725,9.139,7.806,3.826,14.158,3.826      c4.687,0,8.813,2.88,10.771,7.515l0.921,2.183l0.921-2.183c1.927-4.564,6.271-7.514,11.069-7.514       c6.351,0,11.433,5.313,12.096,12.727C49.938,16.57,50.229,18.264,49.521,21.261z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     width="438.536px" height="438.536px" viewBox="0 0 438.536 438.536" style="enable-background:new 0 0 438.536 438.536;"   xml:space="preserve"><g>   <path d="M433.113,5.424C429.496,1.807,425.215,0,420.267,0H18.276C13.324,0,9.041,1.807,5.425,5.424       C1.808,9.04,0.001,13.322,0.001,18.271v401.991c0,4.948,1.807,9.233,5.424,12.847c3.619,3.614,7.902,5.428,12.851,5.428h401.991     c4.948,0,9.229-1.813,12.847-5.428c3.614-3.613,5.421-7.898,5.421-12.847V18.271C438.534,13.319,436.73,9.04,433.113,5.424z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="401.998px" height="401.998px" viewBox="0 0 401.998 401.998" style="enable-background:new 0 0 401.998 401.998;" xml:space="preserve"> <g> <path d="M377.87,24.126C361.786,8.042,342.417,0,319.769,0H82.227C59.579,0,40.211,8.042,24.125,24.126 C8.044,40.212,0.002,59.576,0.002,82.228v237.543c0,22.647,8.042,42.014,24.123,58.101c16.086,16.085,35.454,24.127,58.102,24.127 h237.542c22.648,0,42.011-8.042,58.102-24.127c16.085-16.087,24.126-35.453,24.126-58.101V82.228 C401.993,59.58,393.951,40.212,377.87,24.126z M365.448,319.771c0,12.559-4.47,23.314-13.415,32.264 c-8.945,8.945-19.698,13.411-32.265,13.411H82.227c-12.563,0-23.317-4.466-32.264-13.411c-8.945-8.949-13.418-19.705-13.418-32.264 V82.228c0-12.562,4.473-23.316,13.418-32.264c8.947-8.946,19.701-13.418,32.264-13.418h237.542 c12.566,0,23.319,4.473,32.265,13.418c8.945,8.947,13.415,19.701,13.415,32.264V319.771L365.448,319.771z"/> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg></div>
            <div class="symbol-item"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.481 19.481" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 19.481 19.481">  <g>    <path d="m10.201,.758l2.478,5.865 6.344,.545c0.44,0.038 0.619,0.587 0.285,0.876l-4.812,4.169 1.442,6.202c0.1,0.431-0.367,0.77-0.745,0.541l-5.452-3.288-5.452,3.288c-0.379,0.228-0.845-0.111-0.745-0.541l1.442-6.202-4.813-4.17c-0.334-0.289-0.156-0.838 0.285-0.876l6.344-.545 2.478-5.864c0.172-0.408 0.749-0.408 0.921,0z"/>  </g></svg></div>
            <div class="symbol-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 55.867 55.867" style="enable-background:new 0 0 55.867 55.867;" xml:space="preserve"><path d="M11.287,54.548c-0.207,0-0.414-0.064-0.588-0.191c-0.308-0.224-0.462-0.603-0.397-0.978l3.091-18.018L0.302,22.602  c-0.272-0.266-0.37-0.663-0.253-1.024c0.118-0.362,0.431-0.626,0.808-0.681l18.09-2.629l8.091-16.393   c0.168-0.342,0.516-0.558,0.896-0.558l0,0c0.381,0,0.729,0.216,0.896,0.558l8.09,16.393l18.091,2.629   c0.377,0.055,0.689,0.318,0.808,0.681c0.117,0.361,0.02,0.759-0.253,1.024L42.475,35.363l3.09,18.017   c0.064,0.375-0.09,0.754-0.397,0.978c-0.308,0.226-0.717,0.255-1.054,0.076l-16.18-8.506l-16.182,8.506 C11.606,54.51,11.446,54.548,11.287,54.548z M3.149,22.584l12.016,11.713c0.235,0.229,0.343,0.561,0.287,0.885L12.615,51.72 l14.854-7.808c0.291-0.154,0.638-0.154,0.931,0l14.852,7.808l-2.836-16.538c-0.056-0.324,0.052-0.655,0.287-0.885l12.016-11.713 l-16.605-2.413c-0.326-0.047-0.607-0.252-0.753-0.547L27.934,4.578l-7.427,15.047c-0.146,0.295-0.427,0.5-0.753,0.547L3.149,22.584z "/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
        </div>
       </div>
    </div>

    <!-- shapes list -->
    <div class="right-bar shape-list light-card">
       <div class="right-bar-head">
          <div class="pull-right close-right-bar-head close-right-bar"><i class="ion-ios-close-outline"></i></div>
          <h4>Selecionar forma</h4>
       </div>
       <div class="right-bar-body">
        <div class="shape-list">
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M256,0C114.837,0,0,114.837,0,256s114.837,256,256,256s256-114.837,256-256S397.163,0,256,0z"/>   </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M256,0C114.837,0,0,114.837,0,256s114.837,256,256,256s256-114.837,256-256S397.163,0,256,0z M256,490.667         c-129.387,0-234.667-105.28-234.667-234.667S126.613,21.333,256,21.333S490.667,126.613,490.667,256S385.387,490.667,256,490.667z           "/> </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 511.285 511.285" style="enable-background:new 0 0 511.285 511.285;" xml:space="preserve"><g>  <g>     <path d="M425.712,465.099l-160-458.667c-3.008-8.576-17.152-8.576-20.139,0l-160,458.667c-0.405,1.131-0.597,2.304-0.597,3.52          c0,41.472,153.195,42.667,170.667,42.667s170.667-1.195,170.667-42.667C426.309,467.424,426.117,466.229,425.712,465.099z"/>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 511.893 511.893" style="enable-background:new 0 0 511.893 511.893;" xml:space="preserve"><g>  <g>     <path d="M426.272,467.413c-0.107-0.576-0.043-1.152-0.235-1.707l-0.192-0.555c-0.043-0.128-0.085-0.235-0.128-0.363L266.037,7.061          c-0.085-0.235-0.256-0.384-0.341-0.597c-0.277-0.64-0.661-1.195-1.045-1.771c-0.384-0.576-0.747-1.152-1.216-1.643          c-0.469-0.469-1.024-0.789-1.579-1.173c-0.597-0.405-1.173-0.811-1.835-1.088c-0.213-0.085-0.341-0.256-0.555-0.32          c-0.491-0.171-0.981-0.107-1.472-0.192C257.333,0.149,256.715,0,256.032,0c-0.832,0-1.621,0.149-2.432,0.341            c-0.384,0.085-0.768,0.021-1.131,0.149c-0.171,0.064-0.277,0.192-0.448,0.256c-0.811,0.32-1.493,0.789-2.197,1.301          c-0.448,0.32-0.896,0.576-1.28,0.96c-0.533,0.533-0.939,1.173-1.365,1.813c-0.341,0.533-0.704,1.024-0.96,1.621         c-0.085,0.213-0.277,0.363-0.341,0.597l-160,458.667c-0.235,0.661-0.171,1.323-0.277,2.005c-0.085,0.512-0.32,0.96-0.32,1.515           c0,0.107,0.064,0.171,0.064,0.277c0.043,1.557,0.363,3.072,1.045,4.437c11.392,36.757,152.789,37.952,169.557,37.952            s158.101-1.195,169.557-37.909c0.683-1.408,1.003-2.923,1.045-4.501c0-0.085,0.064-0.171,0.064-0.256           C426.613,468.608,426.379,468.032,426.272,467.413z M255.947,490.56c-92.48,0-142.741-14.699-148.821-20.992L255.947,42.923         l148.48,425.643C396.171,476.544,346.336,490.56,255.947,490.56z"/>   </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <g>         <path d="M0,501.333C0,507.221,4.779,512,10.667,512h330.667V170.667H0V501.333z"/>            <path d="M160,0c-2.837,0-5.547,1.131-7.552,3.115L6.229,149.333h341.355L496.917,0H160z"/>            <path d="M362.667,164.416v341.333l146.219-146.219c1.984-1.984,3.115-4.693,3.115-7.531V15.083L362.667,164.416z"/>        </g>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M511.531,8.384c-0.128-0.576-0.128-1.195-0.363-1.749c-1.088-2.645-3.179-4.736-5.824-5.824           c-0.555-0.235-1.152-0.213-1.749-0.363C502.869,0.299,502.144,0,501.333,0H160c-0.619,0-1.173,0.256-1.771,0.363            c-0.768,0.128-1.557,0.149-2.283,0.448c-1.323,0.555-2.496,1.323-3.499,2.325L3.115,152.469c-0.085,0.085-0.107,0.213-0.213,0.299           c-0.853,0.917-1.6,1.963-2.091,3.136C0.277,157.248,0,158.635,0,160.043v341.291C0,507.221,4.779,512,10.667,512H352            c1.387,0,2.773-0.277,4.075-0.832c1.195-0.491,2.219-1.237,3.157-2.091c0.085-0.085,0.213-0.107,0.299-0.192l149.333-149.333            c0.981-0.981,1.771-2.176,2.304-3.477c0.299-0.747,0.341-1.515,0.469-2.304c0.107-0.597,0.363-1.152,0.363-1.771V10.667         C512,9.856,511.701,9.131,511.531,8.384z M341.333,490.667h-320v-320h320V490.667z M347.584,149.333H36.416l128-128h311.168         L347.584,149.333z M490.667,347.584l-128,128V164.416l128-128V347.584z"/> </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <g>         <path d="M85.333,77.888v391.445C85.333,510.805,238.549,512,256,512s170.667-1.195,170.667-42.667V77.888              c-28.48,19.093-85.44,28.779-170.667,28.779S113.835,96.981,85.333,77.888z"/>         <path d="M256,85.333c105.28,0,170.667-16.363,170.667-42.667c0-1.621-0.405-3.115-1.067-4.48              C415.872,5.141,301.653,0.661,265.493,0.085C262.336,0.064,259.243,0,256,0s-6.336,0.064-9.515,0.085               C210.325,0.661,96.107,5.12,86.379,38.187c-0.64,1.365-1.045,2.859-1.045,4.48C85.333,68.971,150.72,85.333,256,85.333z"/>      </g>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M425.621,38.187C414.763,1.216,272.789,0,256,0S97.237,1.216,86.379,38.187c-0.64,1.387-1.045,2.859-1.045,4.48v426.667            c0,1.621,0.405,3.093,1.045,4.48C97.237,510.784,239.211,512,256,512s158.763-1.216,169.621-38.187         c0.64-1.387,1.045-2.859,1.045-4.48V42.667C426.667,41.045,426.261,39.573,425.621,38.187z M256,21.333         c87.723,0,137.685,13.248,148.075,21.333C393.685,50.752,343.723,64,256,64S118.315,50.752,107.925,42.667          C118.315,34.581,168.277,21.333,256,21.333z M405.333,467.989c-6.101,7.851-56.448,22.677-149.333,22.677           c-93.995,0-144.619-15.211-149.333-21.333V65.429C149.312,84.544,242.603,85.333,256,85.333s106.688-0.789,149.333-19.904V467.989           z"/>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M485.291,129.408l-224-128c-3.285-1.877-7.296-1.877-10.581,0l-224,128c-3.328,1.899-5.376,5.44-5.376,9.259v234.667           c0,3.819,2.048,7.36,5.376,9.259l224,128c1.643,0.939,3.456,1.408,5.291,1.408s3.648-0.469,5.291-1.408l224-128         c3.328-1.899,5.376-5.44,5.376-9.259V138.667C490.667,134.848,488.619,131.307,485.291,129.408z"/> </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M485.291,129.408l-224-128c-3.285-1.877-7.296-1.877-10.581,0l-224,128c-3.328,1.899-5.376,5.44-5.376,9.259v234.667           c0,3.819,2.048,7.36,5.376,9.259l224,128c1.643,0.939,3.456,1.408,5.291,1.408c1.835,0,3.648-0.469,5.291-1.408l224-128         c3.328-1.899,5.376-5.44,5.376-9.259V138.667C490.667,134.848,488.619,131.307,485.291,129.408z M469.333,367.147L256,489.045           L42.667,367.147V144.853L256,22.955l213.333,121.899V367.147z"/>  </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <g>         <path d="M160,96c-1.856,0-3.669,0.491-5.291,1.408L7.829,181.333h341.376L500.907,96H160z"/>          <path d="M0,405.333C0,411.221,4.779,416,10.667,416h330.667V202.667H0V405.333z"/>            <path d="M362.667,198.229V411.52l143.957-82.261c3.328-1.899,5.376-5.44,5.376-9.259V114.24L362.667,198.229z"/>       </g>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M511.851,105.984c-0.043-0.619-0.213-1.195-0.363-1.813c-0.192-0.789-0.384-1.557-0.747-2.261         c-0.085-0.171-0.085-0.363-0.171-0.512c-0.213-0.363-0.555-0.576-0.789-0.896c-0.469-0.64-0.96-1.259-1.557-1.792           c-0.512-0.448-1.045-0.768-1.621-1.088c-0.597-0.341-1.152-0.661-1.813-0.896c-0.747-0.256-1.493-0.363-2.261-0.448         C502.123,96.192,501.76,96,501.333,96H160c-0.192,0-0.363,0.107-0.555,0.107c-0.768,0.043-1.493,0.235-2.261,0.448          c-0.64,0.192-1.28,0.341-1.877,0.619c-0.213,0.107-0.427,0.085-0.64,0.213L5.333,182.72c-0.427,0.256-0.683,0.64-1.067,0.939            c-0.533,0.448-1.067,0.832-1.515,1.365c-0.512,0.555-0.875,1.173-1.237,1.835c-0.299,0.533-0.597,1.024-0.811,1.6           c-0.256,0.789-0.384,1.557-0.469,2.389C0.192,191.253,0,191.595,0,192v213.333C0,411.221,4.779,416,10.667,416H352          c1.771,0,3.349-0.533,4.821-1.28c0.149-0.085,0.32-0.043,0.448-0.128l149.333-85.333c0.384-0.213,0.597-0.555,0.939-0.811           c0.64-0.469,1.259-0.939,1.771-1.557c0.405-0.469,0.704-0.981,1.024-1.515c0.384-0.64,0.725-1.259,0.981-1.984          c0.213-0.619,0.299-1.259,0.384-1.92c0.064-0.491,0.299-0.939,0.299-1.472V106.667C512,106.432,511.872,106.219,511.851,105.984z             M341.333,394.667h-320v-192h320V394.667z M349.163,181.333H50.816l112-64h298.347L349.163,181.333z M490.667,313.792l-128,73.152           V198.165l128-73.152V313.792z"/> </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     viewBox="0 0 31.494 31.494" style="enable-background:new 0 0 31.494 31.494;" xml:space="preserve"><path style="fill:#1E201D;" d="M10.273,5.009c0.444-0.444,1.143-0.444,1.587,0c0.429,0.429,0.429,1.143,0,1.571l-8.047,8.047h26.554 c0.619,0,1.127,0.492,1.127,1.111c0,0.619-0.508,1.127-1.127,1.127H3.813l8.047,8.032c0.429,0.444,0.429,1.159,0,1.587  c-0.444,0.444-1.143,0.444-1.587,0l-9.952-9.952c-0.429-0.429-0.429-1.143,0-1.571L10.273,5.009z"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512.01 512.01" style="enable-background:new 0 0 512.01 512.01;" xml:space="preserve"><g>  <g>     <path d="M507.804,200.28L262.471,12.866c-3.84-2.923-9.131-2.923-12.949,0L4.188,200.28c-3.605,2.773-5.077,7.531-3.648,11.84          l93.717,281.92c1.451,4.373,5.525,7.296,10.133,7.296h303.253c4.587,0,8.683-2.944,10.133-7.296l93.717-281.92          C512.882,207.789,511.41,203.053,507.804,200.28z"/>  </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 511.995 511.995" style="enable-background:new 0 0 511.995 511.995;" xml:space="preserve"><g>  <g>     <path d="M507.807,200.272L262.474,12.859c-3.819-2.923-9.109-2.923-12.949,0L4.191,200.272c-3.627,2.773-5.077,7.509-3.648,11.84           l93.717,281.92c1.451,4.352,5.547,7.296,10.133,7.296h303.253c4.608,0,8.683-2.944,10.091-7.296l93.717-281.92          C512.885,207.803,511.413,203.046,507.807,200.272z M399.946,479.995H112.095L23.221,212.582L256.031,34.747l232.811,177.856            L399.946,479.995z"/>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M501.333,96H10.667C4.779,96,0,100.779,0,106.667v298.667C0,411.221,4.779,416,10.667,416h490.667         c5.888,0,10.667-4.779,10.667-10.667V106.667C512,100.779,507.221,96,501.333,96z"/>   </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M501.333,96H10.667C4.779,96,0,100.779,0,106.667v298.667C0,411.221,4.779,416,10.667,416h490.667         c5.888,0,10.667-4.779,10.667-10.667V106.667C512,100.779,507.221,96,501.333,96z M490.667,394.667H21.333V117.333h469.333          V394.667z"/>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     viewBox="0 0 31.49 31.49" style="enable-background:new 0 0 31.49 31.49;" xml:space="preserve"><path style="fill:#1E201D;" d="M21.205,5.007c-0.429-0.444-1.143-0.444-1.587,0c-0.429,0.429-0.429,1.143,0,1.571l8.047,8.047H1.111 C0.492,14.626,0,15.118,0,15.737c0,0.619,0.492,1.127,1.111,1.127h26.554l-8.047,8.032c-0.429,0.444-0.429,1.159,0,1.587    c0.444,0.444,1.159,0.444,1.587,0l9.952-9.952c0.444-0.429,0.444-1.143,0-1.571L21.205,5.007z"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M501.333,0H10.667C4.779,0,0,4.779,0,10.667v490.667C0,507.221,4.779,512,10.667,512h490.667          c5.888,0,10.667-4.779,10.667-10.667V10.667C512,4.779,507.221,0,501.333,0z"/>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>  <g>     <path d="M501.333,0H10.667C4.779,0,0,4.779,0,10.667v490.667C0,507.221,4.779,512,10.667,512h490.667          c5.888,0,10.667-4.779,10.667-10.667V10.667C512,4.779,507.221,0,501.333,0z M490.667,490.667H21.333V21.333h469.333V490.667z"/>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"     viewBox="0 0 42 42" style="enable-background:new 0 0 42 42;" xml:space="preserve"><rect y="20" width="42" height="2"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 511.509 511.509" style="enable-background:new 0 0 511.509 511.509;" xml:space="preserve"><g>  <g>     <path d="M498.675,493.845L265.16,5.568c-3.541-7.424-15.701-7.424-19.243,0L11.251,496.235c-1.579,3.307-1.344,7.189,0.597,10.283          s5.355,4.992,9.024,4.992h469.76c5.888,0,10.667-4.779,10.667-10.667C501.299,498.176,500.317,495.723,498.675,493.845z"/>  </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
            <div class="shape-item"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"    viewBox="0 0 511.488 511.488" style="enable-background:new 0 0 511.488 511.488;" xml:space="preserve"><g>  <g>     <path d="M500.04,496.235L265.373,5.568c-3.541-7.424-15.701-7.424-19.243,0L11.464,496.235c-1.6,3.285-1.365,7.189,0.597,10.283            s5.355,4.971,9.024,4.971h469.333c3.669,0,7.083-1.877,9.024-4.971C501.384,503.424,501.619,499.541,500.04,496.235z             M38.003,490.155L255.752,34.88l217.749,455.275C473.501,490.155,38.003,490.155,38.003,490.155z"/>    </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></div>
        </div>
       </div>
    </div>

    <!-- scripts -->
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/dropify/js/dropify.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>
    <script src="<?=url("");?>assets/libs/clipboard/clipboard.min.js"></script>
    <script src="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?=url("");?>assets/libs/select2/js/select2.min.js"></script>
    <script src="<?=url("");?>assets/libs/tagsinput/bootstrap-tagsinput.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/libs/jcanvas/jcanvas.min.js"></script>
    <script src="<?=url("");?>assets/libs/jcanvas/editor.min.js"></script>
    <script src="<?=url("");?>assets/js/pdf.js"></script>
    <script type="text/javascript">
        var url = '<?=url("");?>uploads/files/{{ $document->filename }}',
              isTemplate = '{{ $document->is_template }}',
              postChatUrl = '<?=url("Chat@post");?>',
              settingsPage = '<?=url("Settings@get");?>',
              saveFieldsUrl = '<?=url("Field@save");?>',
              deleteFieldsUrl = '<?=url("Field@delete");?>',
              getChatUrl = '<?=url("Chat@fetch");?>',
              signDocumentUrl = '<?=url("Document@sign");?>',
              sendRequestUrl = '<?=url("Request@send");?>',
              createTemplateUrl = '<?=url("Template@create");?>',
              baseUrl = '<?=url("");?>',
              auth = true;
              document_key = '{{ $document->document_key }}';
        PDFJS.workerSrc = '<?=url("");?>assets/js/pdf.worker.min.js';

        @if ( empty( $user->signature ) )
        var signature = '';
        @else
        var signature = '<?=url("");?>uploads/signatures/{{ $user->signature }}';
        @endif

        @if ( is_object($request) && $request->status == "Pending" )
        var signingKey = '{{ $request->signing_key }}';
        var requestPositions = {{ $requestPositions }};
        var requestWidth = {{ $requestWidth }};
        @else
        var signingKey = '';
        @endif

        @if ( $document->is_template == "Yes" )
        var savedWidth = {{ $savedWidth }};
        var templateFields = {{ $templateFields }};
        @endif

    </script>
    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
    <script src="<?=url("");?>assets/js/signer.js"></script>
    <script src="<?=url("");?>assets/js/render.js"></script>
</body>

</html>
