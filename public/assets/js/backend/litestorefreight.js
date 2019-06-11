define(['jquery', 'bootstrap', 'backend', 'table', 'form','litestorefreight_delivery','litestorefreight_regionalChoice'], function ($, undefined, Backend, Table, Form,delivery,regionalChoice) {

    var Controller = {
        index: function () {
            $(".btn-add").data("area", ["1000px","800px"]);
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'litestorefreight/index',
                    add_url: 'litestorefreight/add',
                    edit_url: 'litestorefreight/edit',
                    del_url: 'litestorefreight/del',
                    multi_url: 'litestorefreight/multi',
                    table: 'litestorefreight',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name')},
                        {field: 'method', title: __('Method'), searchList: {"10":__('Method 10'),"20":__('Method 20')}, formatter: Table.api.formatter.normal},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.on('load-success.bs.table',function(data){
               $(".btn-editone").data("area", ["1000px","800px"]);
            });
            
        },
        add: function () {
            Controller.api.bindevent();

            //这里增加地区的逻辑
            // 配送区域表格
            new Delivery({
                table: '.regional-table',
                regional: '.regional-choice',
                datas: datas
            });

        },
        edit: function () {
            Controller.api.bindevent();
             //这里增加地区的逻辑
            // 配送区域表格
            new Delivery({
                table: '.regional-table',
                regional: '.regional-choice',
                datas: datas
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});