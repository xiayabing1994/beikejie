define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'usercash/index' + location.search,
                    add_url: 'usercash/add',
                    edit_url: 'usercash/edit',
                    del_url: 'usercash/del',
                    multi_url: 'usercash/multi',
                    table: 'usercash',
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
                        {field: 'user_id', title: __('User_id')},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'cash_account', title: __('Cash_account')},
                        {field: 'cash_realname', title: __('Cash_realname')},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'memo', title: __('Memo')},
                        {field: 'status', title: __('Status'), searchList: {"wait":__('Status wait'),"checked":__('Status checked'),"refuse":__('Status refuse'),"remited":__('Status remited')}, formatter: Table.api.formatter.status},
                        {field: 'check_memo', title: __('Check_memo')},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {
                            field: 'buttons',
                            width: "120px",
                            title: __('管理员操作'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    text: __('确认打款'),
                                    title: __('确认打款'),
                                    classname: 'btn btn-xs btn-primary btn-ajax',
                                    icon: 'fa fa-blind',
                                    confirm:'确认打款吗？',
                                    url: 'usercash/remit/ids/{ids}',
                                    success: function (data,ret) {
                                        console.log(data,ret);
                                        // Layer.alert(ret.msg + ",返回数据：" + JSON.stringify(data));
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        if(row.status=='checked')return true;
                                        return false;
                                    }
                                },

                            ],
                            formatter: Table.api.formatter.buttons
                        },
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});