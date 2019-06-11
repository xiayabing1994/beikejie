define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'litestoreorder/index',
                    add_url: 'litestoreorder/add',
                   // edit_url: 'litestoreorder/edit',
                    del_url: 'litestoreorder/del',
                    multi_url: 'litestoreorder/multi',
                    table: 'litestore_order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'order_no', title: __('Order_no')},
                        {field: 'total_price', title: __('Total_price'), operate:'BETWEEN'},
                        {field: 'express_price', title: __('Express_price'), operate:'BETWEEN'},
                        {field: 'pay_price', title: __('Pay_price'), operate:'BETWEEN'},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'freight_time', title: __('Freight_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'receipt_time', title: __('Receipt_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'order_status', title: __('Order_status'), searchList: {"10":__('Order_status 10'),"20":__('Order_status 20'),"30":__('Order_status 30')}, 
                                                formatter: Controller.api.status_formatter},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'address.name', title: __('Address.name')},
                        {field: 'address.Area.city', title:"发货城市"},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
                                {name: 'send', text: __('view'), icon: 'fa fa-eye', classname: 'btn btn-xs btn-warning btn-dialog chakan', url: 'litestoreorder/detail'},
                            ],  events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 绑定TAB事件
            $('.panel-heading a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var that = $(this);
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    var filter = {};
                    filter['pay_status'] = that.data("pay_status");
                    filter['freight_status'] = that.data("freight_status");
                    filter['order_status'] = that.data("order_status");
                    filter['receipt_status'] = that.data("receipt_status");
                    params.filter = JSON.stringify(filter);
                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.on('load-success.bs.table',function(data){
               $('.chakan').data("area", ["1000px","800px"]);
            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        detail: function(){
            $("#send").on('click', function() {
                var sn = $("#c-virtual_sn").val();
                var name = $("#c-virtual_name").val();
                if(sn == '' || name == '')
                {
                    layer.msg("请填写正确的快递信息");
                    return false;
                }
                $("#send-form").attr("action","litestoreorder/detail").submit();
            });
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            status_formatter: function (value, row, index) {
                var colorArr = ["success", "gray", "blue", "primary",  "danger", "warning", "info",  "red", "yellow", "aqua",  "navy", "teal", "olive", "lime", "fuchsia", "purple", "maroon"];
                var custom = {};
                if (typeof this.custom !== 'undefined') {
                    custom = $.extend(custom, this.custom);
                }
                value = value === null ? '' : value.toString();
                var keys = typeof this.searchList === 'object' ? Object.keys(this.searchList) : [];
                var index = keys.indexOf(value);
                var color = value && typeof custom[value] !== 'undefined' ? custom[value] : null;
                var display = index > -1 ? this.searchList[value] : null;
                var icon = "fa fa-circle";
                if (!color) {
                    color = index > -1 && typeof colorArr[index] !== 'undefined' ? colorArr[index] : 'primary';
                }
                if (!display) {
                    display = __(value.charAt(0).toUpperCase() + value.slice(1));
                }
                var html = '<span class="text-' + color + '">' + (icon ? '<i class="' + icon + '"></i> ' : '') + display + '</span>';
                if (this.operate != false) {
                    html = '<a href="javascript:;" class="searchit" data-toggle="tooltip" title="' + __('Click to search %s', display) + '" data-field="' + this.field + '" data-value="' + value + '">' + html + '</a>';
                }
                return html;
            },
        }
    };
    return Controller;
});