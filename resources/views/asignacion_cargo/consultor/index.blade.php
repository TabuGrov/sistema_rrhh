@extends('adminlte::page')

@section('title', 'Consultores')

@section('content_header')
    <div class="row">
        <div class="col-6 mb-0">
            <h4>CONSULTORES</h4>
        </div>
        <div class="col-6">
            <select data-column="8" class="filter-select form-control-select float-right" id="getEstado">
                <option value="TODOS">TODOS</option>
                <option value="VIGENTE" selected>VIGENTE</option>
                <option value="INHABILITADO">INHABILITADO</option>
            </select>
        </div>
    </div>
@stop
@section('content')
    <div class="card card-secondary card-outline">
        <div class="card-body sinpadding">
            <div class="col-12">
                <button id="nuevoConsultor" class="btn btn-success btn-sm">Nuevo <i class="fa fa-plus-circle"></i></button>
                <button id="modificarConsultor" class="btn btn-warning btn-sm">Modificar <i
                        class="fa fa-edit"></i></button>
                <button id="cambiarConsultor" class="btn btn-primary btn-sm">Cambiar <i
                        class="fa fa-exchange-alt"></i></button>
                <button id="bajaConsultor" class="btn btn-danger btn-sm">Dar de Baja <i
                        class="fa fa-times-circle"></i></button>
                {{-- <button id="eliminarconsultor" class="btn btn-danger btn-sm">Eliminar <i
                        class="fa fa-times-circle"></i></button> --}}
                <button class="btn btn-default dropdown-toggle float-right" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Imprimir <i class="fa fa-file-pdf"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-consultor" href="#">Imprimir</a>
                    <a class="dropdown-consultor" href="#">Altas</a>
                    <a class="dropdown-consultor" href="#">Bajas</a>
                </div>
            </div>
            <div class="table table-bordered table-hover dataTable table-responsive">
                <table width="100%" class="table table-striped table-bordered datatable data_table "
                    id="table_consultores">
                    <thead>
                        <tr>
                            <th width='3px'>Nro</th>
                            <th >id</th>
                            <th >Inicio</th>
                            <th width="110px">Documento (C.I.)</th>
                            <th width="135px">Trabajador</th>
                            <th width="150px">Cargo</th>
                            <th >Salario</th>
                            <th >Conclusi??n</th>
                            <th >Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th><button id="btnClean" class="btn btn-primary-outline"><i class="fas fa-sync"></i></button></th>
                            <th></th>
                            <th></th>
                            <th><input placeholder="Buscar CI" data-column="3" class="form-control filter-input btnCi" type="text" id="getDocumento"></th>
                            <th><input placeholder="Buscar nombre" data-column="4" class="form-control filter-input" type="text" id="getName"></th>
                            <th><input placeholder="Buscar Cargo" data-column="5" class="form-control filter-input" type="text" id="getCArgo"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalConsultores" role="dialog" data-backdrop="static" data-keyboard="false">
    </div>
    <input type="hidden" id="fecha_actual" value="{{ahora()}}">
    {{-- @include('asignacion_cargo.consultor.modalConsultores') --}}
@stop
@section('footer')
    <strong>{{ date('Y') }} || ELAPAS - SISTEMA DE RECURSOS HUMANOS </strong>
@stop
@section('js')
    <script>
        var now = $('#fecha_actual').val();
        function lista_consultores(){
            // Tabla de los consultores
            table_consultores = $('#table_consultores').DataTable({
                ajax: {
                    url: "{{ route('consultores.lista') }}",
                },
                columns: [
                    {data: null,
                        render: function (data, type, full, meta)
                        {
                            return meta.row + 1;
                        }
                    },
                    {data: 'id',visible: false},
                    {data: 'fecha_ingreso'},
                    {data: 'trabajador_ci'},
                    {data: 'trabajador_nombre'},
                    {data: 'cargo'},
                    {data: 'salario'},
                    {data: 'fecha_conclusion'},
                    {
                        render: function(data, type, row) {
                            if (row['estado'] == 'HABILITADO') {
                                estado_t = "<span class='badge badge-success' >VIGENTE</span>"
                                return estado_t;
                            } else {
                                estado_t = "<span class='badge badge-danger' >INHABILITADO</span>"
                                return estado_t;
                            }
                        },
                    },
                ],
                columnDefs: [{
                    searchable: false,
                    orderable: false,
                    targets: [0, 6, 7],
                }, ],
                responsive: true,
                pagingType: "full_numbers",
                scrollY: "43vh",
                scrollX: true,
                deferRender: true,
                lengthMenu: [
                    [10, 10, 25, 50, -1],
                    [10, 10, 25, 50, "All"]
                ],
                autoWidth: true,
                dom: "<'row'<'col-sm-12 col-md-12'>>" +
                    "<'row'<'col-sm-12't>>" +
                    "<'row'i<'col-sm-12 col-md-5'p>>",
                select: true,
                language: {
                    url: "../../vendor/funciones/datatable_spanish.json"
                },
                initComplete: function(settings, json) {
                    if(json.recordsTotal == 0){
                        Swal.fire({
                            title: 'No existen Consultores',
                            type: 'warning',
                            confirmButtonText: 'Aceptar',
                        });
                    }else{console.log(true)}
                }
            });
        }
        function filterSelect(){
            var filter = $('.filter-select')
            table_consultores.column(filter.data('column'))
                    .search(filter.val())
                    .draw();
        }
        $(document).ready(function() {
            lista_consultores();
            // Filtro por estado
            filterSelect()
            // buscar por texto
            $('.filter-input').keyup(function(){
                table_consultores.column($(this).data('column'))
                .search($(this).val())
                .draw();
            });
            // buscar por estado
            $('.filter-select').change(function(){
                if($(this).val() == 'TODOS'){
                    var table = $('#table_consultores').DataTable();
                    table
                    .search('')
                    .columns().search('')
                    .draw();
                }
                else{
                    table_consultores.column($(this).data('column'))
                    .search($(this).val())
                    .draw();
                }
            });
            // limpiar inputs
            $('button#btnClean').click(function (e) {
                Pace.restart();
                e.preventDefault();
                var filter = $('.filter-select')
                var table = $('#table_consultores').DataTable();
                console.log("reset table");
                $('.filter-input').val("");
                $('.filter-select').val("VIGENTE");
                filterSelect()
            });

            // Nuevo consultor
            var urlAddConsultor = "{{ route('consultor.nuevo') }}";
            $('#nuevoConsultor').click(function() {
                $('#modalConsultores').load(urlAddConsultor, null,
                    function(response, status, xhr) {
                        switch (xhr.status) {
                            case 403:
                                Swal.fire({
                                    title: 'Acceso restringido o sesi??n caducada',
                                    icon: 'warning',
                                    confirmButtonText: 'Aceptar',
                                }).then(() => {
                                    location.reload();
                                });
                                break;
                            case 200:
                                $('#modalConsultores').modal('show');
                                $('.modal-custom').css("max-width", "600px");
                                break;
                            case 500:
                                Swal.fire({
                                    title: xhr.statusText,
                                    icon: 'warning',
                                    confirmButtonText: 'Aceptar',
                                }).then(() => {
                                    location.reload();
                                });
                                break;

                            default:
                                break;
                        }
                    }
                );
            });
            // MODIFICAR consultor
            $('#modificarConsultor').on('click', function() {
                var data = table_consultores.rows('tr.selected').data().toArray();
                if (data.length != 0) {
                    var rowId = data[0]['id'];
                    var rowConclusion = data[0]['fecha_conclusion'];
                    var finishDate = moment(rowConclusion, "DD-MM-YYYY");
                    var rowStatus = data[0]['estado'];
                    if(!moment(now, "DD-MM-YYYY").isSameOrBefore(finishDate) || rowStatus != 'HABILITADO'){
                        Swal.fire({
                            title: 'El contrato no se puede modificar porque este ya ha concluido',
                            icon: 'warning',
                            confirmButtonText: 'Aceptar',
                        });
                    }
                    else{
                        var urlEditconsultor = "/consultor/" + rowId + "/editar";
                        $('#modalConsultores').load(urlEditconsultor, null,
                            function(response, status, xhr) {
                                switch (xhr.status) {
                                    case 403:
                                        Swal.fire({
                                            title: 'Acceso restringido o sesi??n caducada',
                                            icon: 'warning',
                                            confirmButtonText: 'Aceptar',
                                        }).then(() => {
                                            location.reload();
                                        });
                                        break;
                                    case 200:
                                        $('#modalConsultores').modal('show');
                                        $('.modal-custom').css("max-width", "600px");
                                        break;
                                    case 500:
                                        Swal.fire({
                                            title: xhr.statusText,
                                            icon: 'warning',
                                            confirmButtonText: 'Aceptar',
                                        }).then(() => {
                                            location.reload();
                                        });
                                        break;

                                    default:
                                        break;
                                }
                            }
                        );
                    }

                } else {
                    Swal.fire({
                        title: 'Por favor, seleccione un registro',
                        icon: 'warning',
                        confirmButtonText: 'Aceptar',
                    });
                }

            });
            // CAMBIAR EL CARGO DE CONSULTOR A UN CONSULTOR EN LINEA
            $('#cambiarConsultor').on('click', function() {
                var data = table_consultores.rows('tr.selected').data().toArray();
                if (data.length != 0) {
                    var rowId = data[0]['id'];
                    var rowConclusion = data[0]['fecha_conclusion'];
                    var finishDate = moment(rowConclusion, "DD-MM-YYYY");
                    var rowStatus = data[0]['estado'];
                    if(!moment(now, "DD-MM-YYYY").isSameOrBefore(finishDate) || rowStatus != 'HABILITADO'){
                        Swal.fire({
                            title: 'El contrato no se puede modificar porque este ya ha concluido',
                            icon: 'warning',
                            confirmButtonText: 'Aceptar',
                        });
                    }
                    else{
                        var urlChangeconsultor = "/consultor/" + rowId + "/cambiar";
                        $('#modalConsultores').load(urlChangeconsultor, null,
                            function(response, status, xhr) {
                                switch (xhr.status) {
                                    case 403:
                                        Swal.fire({
                                            title: 'Acceso restringido o sesi??n caducada',
                                            icon: 'warning',
                                            confirmButtonText: 'Aceptar',
                                        }).then(() => {
                                            location.reload();
                                        });
                                        break;
                                    case 200:
                                        $('#modalConsultores').modal('show');
                                        $('.modal-custom').css("max-width", "800px");
                                        break;
                                    case 500:
                                        Swal.fire({
                                            title: xhr.statusText,
                                            icon: 'warning',
                                            confirmButtonText: 'Aceptar',
                                        }).then(() => {
                                            location.reload();
                                        });
                                        break;

                                    default:
                                        break;
                                }
                            }
                        );
                    }
                } else {
                    Swal.fire({
                        title: 'Por favor, seleccione un registro',
                        icon: 'warning',
                        confirmButtonText: 'Aceptar',
                    });
                }

            });
            // BAja de consultor
            $('#bajaConsultor').on('click', function() {
                var data = table_consultores.rows('tr.selected').data().toArray();
                if (data.length != 0) {
                    var rowId = data[0]['id'];
                    var rowConclusion = data[0]['fecha_conclusion'];
                    var finishDate = moment(rowConclusion, "DD-MM-YYYY");
                    var rowStatus = data[0]['estado'];
                    if(!moment(now, "DD-MM-YYYY").isSameOrBefore(finishDate) || rowStatus != 'HABILITADO'){
                        Swal.fire({
                            title: 'El contrato no se puede dar de baja porque este ya ha concluido',
                            icon: 'warning',
                            confirmButtonText: 'Aceptar',
                        });
                    }
                    else{
                        var urlCancelconsultor = "/consultor/" + rowId + "/dar-baja";
                        $('#modalConsultores').load(urlCancelconsultor, null,
                            function(response, status, xhr) {
                                switch (xhr.status) {
                                    case 403:
                                        Swal.fire({
                                            title: 'Acceso restringido o sesi??n caducada',
                                            icon: 'warning',
                                            confirmButtonText: 'Aceptar',
                                        }).then(() => {
                                            location.reload();
                                        });
                                        break;
                                    case 200:
                                        console.log(response)
                                        console.log(status)
                                        console.log(xhr)
                                        $('#modalConsultores').modal('show');
                                        $('.modal-custom').css("max-width", "800px");
                                        break;
                                    case 500:
                                        Swal.fire({
                                            title: xhr.statusText,
                                            icon: 'warning',
                                            confirmButtonText: 'Aceptar',
                                        }).then(() => {
                                            location.reload();
                                        });
                                        break;

                                    default:
                                        break;
                                }
                            }
                        );
                    }
                } else {
                    Swal.fire({
                        title: 'Por favor, seleccione un registro',
                        icon: 'warning',
                        confirmButtonText: 'Aceptar',
                    });
                }

            });
        });
    </script>
    {{-- <script src="{{asset('js/scripts/asignacion_cargo.js')}}"></script> --}}
@stop
