@extends('master')
@section('content')

    <div class="card-body">
        <form>
            <div class="row">
                <div class="col-lg-2" id="filter_col1" data-column="0">
                    <label>Order date:</label>
                    <input type="date" class="form-control col-lg-12 column_filter" id="col0_filter"
                           placeholder="2020/05/20"/>
                </div>
                <div class="col-lg-2" id="filter_col2" data-column="1">
                    <label>Order ID:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col1_filter"
                           placeholder="489454894797"/>
                </div>
                <div class="col-lg-2" id="filter_col3" data-column="2">
                    <label>Paypal account:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col2_filter">
                </div>
                <div class="col-lg-2" id="filter_col4" data-column="3">
                    <label>Transaction Id:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col3_filter"
                           placeholder="TO783237123"/>
                </div>
                <div class="col-lg-2" id="filter_col5" data-column="4">
                    <label>Courier:</label>
                    {{--                    <input type="text" class="form-control col-lg-12 column_filter" id="col4_filter"/>--}}
                    <select class="form-control col-lg-12 column_filter" id="col4_filter">
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
                <div class="col-lg-2" id="filter_col6" data-column="5">
                    <label>Tracking number:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col5_filter"/>
                </div>
            </div>
            <div class="row mb-8">

                <div class="col-lg-2" id="filter_col7" data-column="6">
                    <label>Tracking date:</label>
                    <input type="date" class="form-control col-lg-12 column_filter" id="col6_filter"/>
                </div>
                <div class="col-lg-2" id="filter_col8" data-column="7">
                    <label>Tracking status:</label>
{{--                    <input type="text" class="form-control col-lg-12 column_filter" id="col7_filter"/>--}}
                    <select class="form-control col-lg-12 column_filter" id="col7_filter">
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
                    <label>Update:</label>
                    <input type="text" class="form-control col-lg-12 column_filter" id="col8_filter"/>
                </div>
            </div>
        </form>
        <!--begin: Datatable-->
        <table class="table table-bordered table-checkable" id="paypal_table">
            <thead>
            <tr>
                <th>Order Date</th>
                <th>Order ID</th>
                <th>Paypal account</th>
                <th>Transaction ID</th>
                <th>Courier</th>
                <th>Tracking number</th>
                <th>Tracking date</th>
                <th>Tracking Status</th>
                <th>Update</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
        <!--end: Datatable-->
    </div>

    <div id="myModal" class="modal fade" role="dialog">
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
                            <label class="control-label col-sm-6" for="name">Paypal account</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="paypal_account">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-6" for="name">Transaction ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="transaction_id">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn actionBtn" data-dismiss="modal">
                        <span id="footer_action_button" class="si"></span>
                    </button>

                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <span class="si si"></span>Thoát
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

    <div id="update" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group" hidden>
                        <label class="control-label col-sm-6" for="name">Tracking number</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="tracking_number_update" disabled>
                        </div>
                    </div>
                    <div class="form-group" hidden>
                        <label class="control-label col-sm-6" for="name">Courier</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="courier_update" disabled>
                        </div>
                    </div>
                    <div class="form-group" hidden>
                        <label class="control-label col-sm-6" for="name">Paypal</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="paypal_account_update" disabled>
                        </div>
                    </div>
                    <div class="form-group" hidden>
                        <label class="control-label col-sm-6" for="name">Transaction</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="transaction_id_update" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-6" for="name">Update!s</label>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn actionBtn" data-dismiss="modal">
                            <span id="footer_action_button_1" class="si">Update</span>
                        </button>

                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class="si si"></span>Close
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="add" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group" hidden>
                        <label class="control-label col-sm-6" for="name">Tracking number</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="tracking_number_add" disabled>
                        </div>
                    </div>
                    <div class="form-group" hidden>
                        <label class="control-label col-sm-6" for="name">Courier</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="courier_add" disabled>
                        </div>
                    </div>
                    <div class="form-group" hidden>
                        <label class="control-label col-sm-6" for="name">Paypal</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="paypal_account_add" disabled>
                        </div>
                    </div>
                    <div class="form-group" hidden>
                        <label class="control-label col-sm-6" for="name">Transaction</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="transaction_id_add" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-6" for="name">Do you want to add?</label>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn actionBtn" data-dismiss="modal">
                            <span id="footer_action_button_1" class="si">Add</span>
                        </button>

                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class="si si"></span>Close
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        // function Edit POST
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
            $('#paypal_account').val($(this).data('paypal_account'));
            $('#transaction_id').val($(this).data('transaction_id'));
            $('#myModal').modal('show');
        });

        $('.modal-footer').on('click', '.edit', function () {
            $.ajax({
                type: 'POST',
                url: 'Edit_paypal',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': $("#fid").val(),
                    'paypal_account': $('#paypal_account').val(),
                    'transaction_id': $('#transaction_id').val()
                },
                success: function (data) {
                    $('#paypal_table').DataTable().ajax.reload(null, false);
                }
            });
        });

        $(document).on('click', '.add-modal', function () {
            $('#footer_action_button').text("Save");
            $('#footer_action_button').addClass('icon-checkmark-cricle');
            $('#footer_action_button').removeClass('si-trash');
            $('.actionBtn').addClass('btn-success');
            $('.actionBtn').removeClass('btn-danger');
            $('.actionBtn').addClass('add');
            $('.modal-title').text('Add');
            $('.deleteContent').hide();
            $('.form-horizontal').show();
            $('#fid').val($(this).data('id'));
            $('#tracking_number_add').val($(this).data('tracking_number'));
            $('#courier_add').val($(this).data('courier'));
            $('#paypal_account_add').val($(this).data('paypal_account'));
            $('#transaction_id_add').val($(this).data('transaction_id'));
            $('#add').modal('show');
        });

        $('.modal-footer').on('click', '.add', function () {
            $.ajax({
                type: 'POST',
                url: '{{ URL::to('api/add-tracking') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': $("#fid").val(),
                    'tracking': $('#tracking_number_add').val(),
                    'carrier': $('#courier_add').val(),
                    'paypal_account': $('#paypal_account_add').val(),
                    'transaction_id': $('#transaction_id_add').val()
                },
                success: function (data) {
                    $('#paypal_table').DataTable().ajax.reload(null, false);
                }
            });
        });

        $(document).on('click', '.update-modal', function () {
            $('#footer_action_button').text("Save");
            $('#footer_action_button').addClass('icon-checkmark-cricle');
            $('#footer_action_button').removeClass('si-trash');
            $('.actionBtn').addClass('btn-success');
            $('.actionBtn').removeClass('btn-danger');
            $('.actionBtn').addClass('update');
            $('.modal-title').text('Update');
            $('.deleteContent').hide();
            $('.form-horizontal').show();
            $('#fid').val($(this).data('id'));
            $('#tracking_number_update').val($(this).data('tracking_number'));
            $('#courier_update').val($(this).data('courier'));
            $('#paypal_account_update').val($(this).data('paypal_account'));
            $('#transaction_id_update').val($(this).data('transaction_id'));
            $('#update').modal('show');
        });

        $('.modal-footer').on('click', '.update', function () {
            $.ajax({
                type: 'POST',
                url: '{{ URL::to('api/paypal-tracking') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': $("#fid").val(),
                    'tracking': $('#tracking_number_update').val(),
                    'carrier': $('#courier_update').val(),
                    'paypal_account': $('#paypal_account_update').val(),
                    'transaction_id': $('#transaction_id_update').val()
                },
                success: function (data) {
                    $('#paypal_table').DataTable().ajax.reload(null, false);
                }
            });
        });


        // Show function
        $(document).on('click', '.detail-modal', function () {
            $('#tracking_number_detail').val($(this).data('tracking_number'));

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
            $('#paypal_table').DataTable({
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'B>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-4'i><'col-sm-12 col-md-8'p>>",
                buttons: [
                    "colvis"
                ],
                ajax: "{{ route('getpaypal') }}",
                serverSide: true,
                columns: [
                    {"data": "order_date"},
                    {"data": "order_id"},
                    {"data": "paypal_account"},
                    {"data": "transaction_id"},
                    {"data": "courier"},
                    {"data": "tracking_number"},
                    {"data": "tracking_date"},
                    {"data": "status"},
                    {
                        "data": "update_status",
                        render: function (data) {
                            switch (data) {
                                case "Updated":
                                    dataRender = '<a class="label label-lg font-weight-bold label-light-primary label-inline" style="text-align: center">Updated</a>';
                                    break;
                                case "Added":
                                    dataRender = '<a class="label label-lg font-weight-bold label-light-primary label-inline" style="text-align: center">Added</a>';
                                    break;
                                case null:
                                    dataRender = ''
                                    break
                                default:
                                    dataRender = '<td> <a class="label label-lg font-weight-bold label-light-danger label-inline" style="text-align: center">Error</a></td>'
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
                language: {
                    "infoFiltered":"",
                    "processing": "'<i class=\"fa fa-spinner fa-spin fa-3x fa-fw text-success\"></i><span class=\"sr-only\">Loading...</span> '"
                },

            });
        });

        function filterGlobal() {
            $('#paypal_table').DataTable().search(
                $('#global_filter').val()
                // $('#global_regex').prop('checked'),
                // $('#global_smart').prop('checked')
            ).draw();
        }

        function filterColumn(i) {
            $('#paypal_table').DataTable().column(i).search(
                $('#col' + i + '_filter').val()
                // $('#col'+i+'_regex').prop('checked'),
                // $('#col'+i+'_smart').prop('checked')
            ).draw();
        }

        $(document).ready(function () {
            $('#paypal_table').DataTable();

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
@endsection
