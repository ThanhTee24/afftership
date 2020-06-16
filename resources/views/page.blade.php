@extends('master')
@section('content')

    <style>
        dataTables_wrapper .dataTables_processing {
            position: absolute;
            top: 30%;
            left: 50%;
            width: 30%;
            height: 40px;
            margin-left: -20%;
            margin-top: -25px;
            padding-top: 20px;
            text-align: center;
            font-size: 1.2em;
            background: none;
        }

        option {
            font-weight: normal;
            display: block;
            white-space: pre;
            min-height: 1.5em;
            padding: 5px;
        }
    </style>

    <div class="card-body">
        <form id="search-form" method="POST" enctype="multipart/form-data">@csrf
            <div class="row">
                <div class="col-lg-2" id="filter_col1" data-column="0">
                    <label>Order date:</label>
                    <input type="date" class="form-control col-lg-12 column_filter" id="col0_filter" name="order_date"
                           placeholder="2020/05/20"/>
                </div>
                <div class="col-lg-2" id="filter_col2" data-column="1">
                    <label>Order ID:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col1_filter" name="order_id"
                           placeholder="489454894797"/>
                </div>
                <div class="col-lg-2" id="filter_col3" data-column="2">
                    <label>Courier:</label>
                    {{--                    <input type="text" class="form-control col-lg-12 column_filter" id="col2_filter" name="courier">--}}
                    <select class="form-control col-lg-12 column_filter" id="col2_filter" name="courier">
                        <option selected></option>
                        <option>DHL</option>
                        <option>Yun Express</option>
                        <option>USPS</option>
                        <option>Epacket</option>
                        <option>UPS</option>
                        <option>Fedex</option>
                        <option>Yanwen</option>
                    </select>
                </div>
                <div class="col-lg-2" id="filter_col4" data-column="3">
                    <label>Tracking number:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col3_filter"
                           name="tracking_number"
                           placeholder="TO783237123"/>
                </div>
                <div class="col-lg-2" id="filter_col5" data-column="4">
                    <label>Tracking date:</label>
                    <input type="date" class="form-control col-lg-12 column_filter" id="col4_filter"
                           name="tracking_date"/>
                </div>
                <div class="col-lg-2" id="filter_col6" data-column="5">
                    <label>Count day:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col5_filter" name="count_day"/>
                </div>
            </div>
            <div class="row mb-8">
                <div class="col-lg-2" id="filter_col7" data-column="6">
                    <label>Supplier:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col6_filter" name="status"/>
                </div>
                <div class="col-lg-2" id="filter_col8" data-column="7">
                    <label>Status:</label>
                    {{--                    <input type="text" class="form-control col-lg-12 column_filter" id="col7_filter"--}}
                    {{--                           name="process_content"/>--}}
                    <select class="form-control col-lg-12 column_filter" id="col7_filter"
                            name="process_content">
                        <option selected></option>
                        <option>Info Received</option>
                        <option>In Transit</option>
                        <option>Out for Delivery</option>
                        <option>Delivered</option>
                        <option>Failed Attempt</option>
                        <option>Available for Pickup</option>
                        <option>Alert</option>
                        <option>Expired</option>
                        <option>Pending</option>
                    </select>
                </div>
                <div class="col-lg-2" id="filter_col9" data-column="8">
                    <label>Detail:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col8_filter"
                           name="process_date"/>
                </div>
                <div class="col-lg-2" id="filter_col10" data-column="9">
                    <label>Process date:</label>
                    <input type="date" class="form-control col-lg-12 column_filter" id="col9_filter" name="total_day"/>
                </div>
                <div class="col-lg-2" id="filter_col11" data-column="10">
                    <label>Total day:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col10_filter" name="note"/>
                </div>
                <div class="col-lg-2" id="filter_col11" data-column="11">
                    <label>Note:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col11_filter" name="note"/>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-checkable display" id="myTable">
            <thead>
            <tr>
                {{--                <th>No</th>--}}
                <th>Order Date</th>
                <th width="2%">Order ID</th>
                <th>Courier</th>
                <th width="5%">Tracking number</th>
                <th>Tracking date</th>
                <th>Count Day</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Detail</th>
                <th>Process Date</th>
                <th>Total day</th>
                <th>Note</th>
                <th></th>
                <th width="2%">Action</th>
            </tr>
            </thead>
        </table>
        <!--end: Datatable-->
        <div class="content-right">
        </div>
    </div>


    <div id="Edit_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                             role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0"
                             aria-valuemax="100"></div>
                    </div>
                    <form class="form-horizontal" method="post" enctype="multipart/form-data" role="modal">@csrf
                        <div class="form-group" hidden>
                            <label class="control-label col-sm-6" for="id">ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="fid" disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-6" for="name">Order Date</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="order_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6" for="name">Order ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="order_id">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6" for="name">Courier</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="courier">
                                {{--                                <select class="form-control" id="courier">--}}
                                {{--                                    <option value="DHL">DHL</option>--}}
                                {{--                                    <option value="Yun Express">Yun Express</option>--}}
                                {{--                                    <option value="USPS">USPS</option>--}}
                                {{--                                    <option value="Epacket">Epacket</option>--}}
                                {{--                                    <option value="UPS">UPS</option>--}}
                                {{--                                    <option value="Fedex">Fedex</option>--}}
                                {{--                                    <option value="YANDE">Yanwen</option>--}}
                                {{--                                </select>--}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6" for="name">Tracking Number</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="tracking_number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6" for="name">Tracking Date</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="tracking_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6" for="name">Note</label>
                            <div class="col-sm-10">
                                <textarea name="note" class="form-control" id="note"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn actionBtn" data-dismiss="modal">
                        <span id="footer_action_button" class="si"></span>
                    </button>

                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <span class="si si"></span>Close
                    </button>

                </div>

            </div>
        </div>
    </div>

    <div id="update" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="post" enctype="multipart/form-data" role="modal">@csrf
                        <div class="form-group" hidden>
                            <label class="control-label col-sm-6" for="id">ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="fid" disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-6" for="name">Tracking Number</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="tracking_number_update" disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-6" for="name">Courier</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="courier_update" disabled>
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn actionBtn" data-dismiss="modal">
                        <span id="footer_action_button_1" class="si"></span>
                    </button>

                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <span class="si si"></span>Close
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div id="detail" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group" hidden>
                        <label class="control-label col-sm-6" for="name">Courier</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="tracking_number_detail" disabled>
                        </div>
                    </div>
                    <table class="table table-bordered table-checkable">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Detail</th>
                        </tr>
                        </thead>
                        <tbody class="trackingdetail">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--    Export modal--}}
    <div id="show" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <form class="form-horizontal" role="form">@csrf
                    <div class="modal-body">
                        <div class="div_check">
                            <div class="form-group" style="width: 100%;display: flex">
                                <label>Order Date</label><br>
                                <div style="width: 50%;display: flex">
                                    <label>Từ ngày:</label>
                                    <input type="date" name="export_todate" class="form-control" required>
                                </div>

                                <div style="width: 50%;display: flex">
                                    <label>Đến ngày</label>
                                    <input type="date" name="export_fromdate" class="form-control" required>
                                </div>

                            </div>
                            <div class="form-group">
                                <label>Supplier</label>
                                <select name="export_supplier" class="form-control">
                                    <option></option>
                                    @foreach($list_supplier as $value)
                                        <option value="{{$value->name}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                                {{--                                <input type="text" name="export_supplier" class="form-control">--}}
                            </div>
                        </div>
                    </div>
                </form>

                <div class="modal-footer">
                    <button class="btn btn-success" type="submit" id="export-file">
                        <span class=""></span>Export file
                    </button>
                    <button class="btn btn-warning" type="button" data-dismiss="modal">
                        <span class="si si-remobe"></span>Close
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script type="text/javascript">

        // function Edit
        $(document).on('click', '.edit-modal', function () {
            $('#footer_action_button').text("Save");
            $('#footer_action_button').addClass('icon-checkmark-cricle');
            $('#footer_action_button').removeClass('si-trash');
            $('.actionBtn').addClass('btn-success');
            $('.actionBtn').removeClass('btn-danger');
            $('.actionBtn').addClass('edit');
            $('.modal-title').text('Edit');
            $('.deleteContent').hide();
            $('.form-horizontal').show();
            $('#fid').val($(this).data('id'));
            $('#order_date').val($(this).data('order_date'));
            $('#order_id').val($(this).data('order_id'));
            $('#courier').val($(this).data('courier'));
            $('#tracking_number').val($(this).data('tracking_number'));
            $('#tracking_date').val($(this).data('tracking_date'));
            $('#count_day').val($(this).data('count_day'));
            $('#status').val($(this).data('status'));
            $('#content').val($(this).data('content'));
            $('#process_date').val($(this).data('process_date'));
            $('#total').val($(this).data('total'));
            $('#note').val($(this).data('note'));
            $('#Edit_modal').modal('show');
        });

        $('.modal-footer').on('click', '.edit', function () {
            $.ajax({
                type: 'POST',
                url: 'Edit_tracking',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': $("#fid").val(),
                    'order_date': $('#order_date').val(),
                    'order_id': $('#order_id').val(),
                    'courier': $('#courier').val(),
                    'tracking_number': $('#tracking_number').val(),
                    'tracking_date': $('#tracking_date').val(),
                    'note': $('#note').val()
                },
                success: function (data) {
                    $('.progress-bar').text('Uploaded');
                    $('.progress-bar').css('width', '100%');
                    $('#myTable').DataTable().ajax.reload(null, false);
                }
            });
        });


        $(document).on('click', '.update-modal', function () {
            $('#footer_action_button_1').text("Update");
            $('#footer_action_button').removeClass('si-check');
            $('#footer_action_button').addClass('si-trash');
            $('.actionBtn').removeClass('btn-success');
            $('.actionBtn').addClass('btn-danger');
            $('.actionBtn').addClass('update');
            $('.modal-title').text('update');
            $('.form-horizontal').show();
            $('#fid').val($(this).data('id'));
            $('#courier_update').val($(this).data('courier'));
            $('#tracking_number_update').val($(this).data('tracking_number'));
            $('#update').modal('show');
        });

        $('.modal-footer').on('click', '.update', function () {
            $.ajax({
                type: 'POST',
                url: '{{ URL::to('api/update-tracking') }}',
                // http://localhost/aftership/public/api/update-tracking
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': $("#fid").val(),
                    'courier': $('#courier_update').val(),
                    'tracking': $('#tracking_number_update').val()
                },
                success: function (data) {

                    $('#myTable').DataTable().ajax.reload(null, false);
                }
            });
        });

        // Show function
        $(document).on('click', '.detail-modal', function () {
            $('#tracking_number_detail').val($(this).data('tracking_number'));
            // $('#process_date_detail').val($(this).data('process_date'));
            // $('#process_content_detail').val($(this).data('content'));
            // $('#status_detail').val($(this).data('status'));
            $('.modal-title').text('Detail');
            $.ajax({
                type: 'POST',
                url: 'Detail_tracking',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'tracking_number': $('#tracking_number_detail').val()
                },
                success: function (data) {
                    console.log(data);
                    $('.trackingdetail').replaceWith(data)
                }
            });
            $('#detail').modal('show');

        });


    </script>

    <script>
        $(document).ready(function () {
            $('#myTable').DataTable({
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'B>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-4'i><'col-sm-12 col-md-8'p>>",
                buttons: ["colvis",
                ],
                serverSide: true,
                ajax: {
                    url: '{{route('getdata')}}',
                    type: 'GET'
                },
                columns: [
                    {"data": "order_date"},
                    {"data": "order_id"},
                    {"data": "courier"},
                    {
                        "data": "tracking_number",
                        render: function (data) {
                            dataRender = '<label style=" width: 150px; height: 50px; max-width: 200px; max-height: 100px; min-width: 150px; min-height: 50px; overflow: auto">' + data + '</label>';

                            return dataRender;
                        }
                    },
                    {"data": "tracking_date"},
                    {"data": "count_day"},
                    {"data": "supplier"},
                    {"data": "status"},
                    {
                        "data": "process_content",
                        // render: function (data) {
                        //     dataRender = '<p style="with=30px;" ">'+data+'</p>';
                        //
                        //     return dataRender;
                        // }
                    },
                    {"data": "process_date"},
                    {"data": "total"},
                    {"data": "note"},
                    {
                        "data": "approved",
                        render: function (data) {
                            if (data == 1) {
                                dataRender = '<i class="ki ki-bold-check-1 text-success">';
                            } else {
                                dataRender = '';
                            }
                            return dataRender;
                        }
                    },
                    {"data": "action", orderable: false, searchable: false}
                ],
                lengthMenu: [10, 25, 50, 75, 100, 1000],
                deferRender: true,
                responsive: true,
                processing: true,
                language: {
                    "infoFiltered": "",
                    "processing": "'<i class=\"fa fa-spinner fa-spin fa-3x fa-fw text-success\"></i><span class=\"sr-only\" style='position: fixed; top: 0px;left: 500px; vertical-align: middle; text-align: center;'>Loading...</span> '"
                },
            });

        });


        function filterColumn(i) {
            $('#myTable').DataTable().column(i).search(
                $('#col' + i + '_filter').val()
                // $('#col'+i+'_regex').prop('checked'),
                // $('#col'+i+'_smart').prop('checked')
            ).draw();
        }

        $(document).ready(function () {
            $('#myTable').DataTable();

            $('input.global_filter').on('keyup click', function () {
                filterGlobal();
            });

            $('input.column_filter').on('keyup click', function () {
                filterColumn($(this).parents('div').attr('data-column'));
            });
        });

        $('select.column_filter').on('change', function () {
            filterColumn($(this).parents('div').attr('data-column'));
        });


    </script>
    <script>
        function docthem() {

            var dots = document.getElementById("dots" + id);
            var moreText = document.getElementById("more");
            var btnText = document.getElementById("myBtn");

            if (dots.style.display === "none") {
                dots.style.display = "inline";
                dots.style.cursor = "pointer";
                dots.style.color = "red";
                dots.style.marginleft = "15px";
                btnText.innerHTML = "Read more";
                moreText.style.display = "none";
            } else {
                dots.style.display = "none";
                btnText.innerHTML = "Read less";
                moreText.style.display = "inline";
            }
        }
    </script>

    <script type="text/javascript">
        $(document).on('click', '.export-modal', function () {
            $('#show').modal('show');
            $('.modal-title').text('Export');
        });

        $("#export-file").click(function () {
            $.ajax({
                type: 'POST',
                url: 'exportTracking',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'to_date': $('input[name=export_todate]').val(),
                    'from_date': $('input[name=export_fromdate]').val(),
                    'supplier': $('select[name=export_supplier]').val(),
                },
                success: function (data) {
                    if ((data.errors)) {
                        $('#message').html(data.message);
                        $('.error').text(data.errors.to_date);
                        $('.error').text(data.errors.from_date);
                        $('.error').text(data.errors.supplier);
                    } else {
                        const workbook = XLSX.utils.book_new();
                        const myHeader = [];
                        const worksheet = XLSX.utils.json_to_sheet(data, {header: myHeader});

                        const range = XLSX.utils.decode_range(worksheet['!ref']);
                        range.e['c'] = myHeader.length - 1;
                        worksheet['!ref'] = XLSX.utils.encode_range(range);

                        XLSX.utils.book_append_sheet(workbook, worksheet, 'tab1');
                        XLSX.writeFile(workbook, 'Tracking.xlsx');
                    }
                }
            });

        });
    </script>


@endsection
