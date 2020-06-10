@extends('master')
@section('content')

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript"
            src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>


    <div class="card-body">
        <form id="search-form" method="POST" enctype="multipart/form-data">@csrf
            <div class="row">
                <div class="col-lg-2" id="filter_col1" data-column="0">
                    <label>Order date:</label>
                    <input type="date" class="form-control col-lg-12 column_filter" id="col0_filter" name="order_date"
                           placeholder="2020/05/20"/>
                </div>

                {{--                <script>--}}
                {{--                    $('input[name="order_date"]').daterangepicker({--}}
                {{--                        timePicker: true,--}}
                {{--                        startDate: moment().startOf('hour'),--}}
                {{--                        endDate: moment().startOf('hour').add(32, 'hour'),--}}
                {{--                        locale: {--}}
                {{--                            format: 'M/DD hh:mm A'--}}
                {{--                        }--}}
                {{--                    });--}}
                {{--                </script>--}}
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
            {{--            <tbody>--}}
            {{--            @foreach($Tracking as $value)--}}
            {{--                <tr>--}}

            {{--                    <td>{{$value->order_date}}</td>--}}
            {{--                    <td>{{$value->order_id}}</td>--}}
            {{--                    <td>{{$value->courier}}</td>--}}
            {{--                    <td>{{$value->tracking_number}}--}}
            {{--                        <a class="detail-modal btn btn-sm btn-clean btn-icon"--}}
            {{--                           data-tracking_number="{{$value->tracking_number}}"--}}
            {{--                           data-status="{{$value->status}}"--}}
            {{--                           data-content="{{ucwords(strtolower($value->process_content))}}"--}}
            {{--                           data-process_date="{{$value->process_date}}"><i--}}
            {{--                                class="fa fa-search text-warning mr-5 icon-md"></i></a>--}}
            {{--                    </td>--}}
            {{--                    <td>{{$value->tracking_date}}</td>--}}
            {{--                    <td>{{$value->count_day}}</td>--}}
            {{--                    <td>{{$value->status}}</td>--}}
            {{--                    <td>{{ucwords(strtolower($value->process_content))}}</td>--}}
            {{--                    <td>{{$value->process_date}}</td>--}}
            {{--                    <td>{{$value->total}}</td>--}}
            {{--                    <td>{{$value->note}}</td>--}}
            {{--                    @if($value->approved == 1)--}}
            {{--                        <td><i class="ki ki-bold-check-1 text-success"></i></td>--}}
            {{--                    @else--}}
            {{--                        <td></td>--}}
            {{--                    @endif--}}
            {{--                    <td><a class="edit-modal btn btn-sm btn-clean btn-icon" title="Edit" data-id="{{$value->id}}"--}}
            {{--                           data-order_date="{{$value->order_date}}" data-order_id="{{$value->order_id}}"--}}
            {{--                           data-courier="{{$value->courier}}" data-tracking_number="{{$value->tracking_number}}"--}}
            {{--                           data-tracking_date="{{$value->tracking_date}}" data-count_day="{{$value->count_day}}"--}}
            {{--                           data-status="{{$value->status}}"--}}
            {{--                           data-content="{{ucwords(strtolower($value->process_content))}}"--}}
            {{--                           data-process_date="{{$value->process_date}}" data-total="{{$value->total}}"--}}
            {{--                           data-note="{{$value->note}}">--}}
            {{--                            <i class="flaticon2-pen icon-lg text-danger"></i>--}}
            {{--                        </a>--}}

            {{--                        <form action="{{route('update_tracking')}}" method="post" enctype="multipart/form-data"--}}
            {{--                              style="display: inline-block">@csrf--}}
            {{--                            <input type="text" name="tracking" value="{{$value->tracking_number}}" hidden>--}}
            {{--                            <input type="text" name="courier" value="{{$value->courier}}" hidden>--}}
            {{--                            <button type="submit" name="submit" value="btnsubmit" title="Update"--}}
            {{--                                    class="btn btn-sm btn-clean btn-icon"><i--}}
            {{--                                    class="ki ki-round icon-lg text-success"></i></button>--}}
            {{--                        </form>--}}
            {{--                    </td>--}}
            {{--                </tr>--}}
            {{--            @endforeach--}}
            {{--            </tbody>--}}

        </table>
        <!--end: Datatable-->
        <div class="content-right">
            {{--            {{$Tracking->links()}}--}}
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
        let editor;

        $(document).ready(function () {

            {{--editor = new $.fn.dataTable.Editor( {--}}
            {{--    ajax: "{{route('getdata')}}",--}}
            {{--    table: "#myTable",--}}
            {{--    fields: [ {--}}
            {{--        label: "Courier:",--}}
            {{--        name: "courier"--}}
            {{--    }--}}
            {{--    ]--}}
            {{--} );--}}

            $('#myTable').DataTable({
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'B>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-4'i><'col-sm-12 col-md-8'p>>",
                buttons: ["colvis",
                    // {extend: "edit", editor: editor},
                ],
                serverSide: true,
                select: true,
                ajax: {
                    url: '{{route('getdata')}}',
                    type: 'GET'
                },
                columns: [
                    {"data": "order_date"},
                    {"data": "order_id"},
                    {"data": "courier"},
                    {"data": "tracking_number"},
                    {"data": "tracking_date"},
                    {"data": "count_day"},
                    {"data": "supplier"},
                    {"data": "status"},
                    {"data": "process_content"},
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
                    {"data": "action"}
                ],
                lengthMenu: [10, 25, 50, 75, 100, 1000],
                deferRender: true,
                responsive: true,
                processing: true,
                select: {
                    style: 'multi'
                },
                language: {
                    "infoFiltered": "",
                    "processing": "'<i class=\"fa fa-spinner fa-spin fa-3x fa-fw text-success\"></i><span class=\"sr-only\">Loading...</span> '"
                },
            });
        });


        function filterGlobal() {
            $('#myTable').DataTable().search(
                $('#global_filter').val()
                // $('#global_regex').prop('checked'),
                // $('#global_smart').prop('checked')
            ).draw();
        }

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

        //
        // $(document).ready(function () {
        //     var table = $('#myTable').DataTable();
        //
        //     $('#myTable tbody').on('click', 'tr', function () {
        //         $(this).toggleClass('selected');
        //     });
        //
        //     $('#button').click(function () {
        //         alert(table.rows('.selected').data().length + ' row(s) selected');
        //     });
        // });

    </script>



    <script>
        var wb = XLSX.utils.table_to_book(document.getElementById('myTable'), {sheet: "Sheet JS"});
        var wbout = XLSX.write(wb, {bookType: 'xlsx', bookSST: true, type: 'binary'});

        function s2ab(s) {
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        }

        $("#button-a").click(function () {
            saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), 'test.xlsx');
        });
    </script>


@endsection
