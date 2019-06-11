define(['jquery', 'bootstrap', 'backend', 'table', 'form','template','litestoregoods'], function ($, undefined, Backend, Table, Form,Template,litestoregoods) {

    var Controller = {
        index: function () {
            $(".btn-add").data("area", ["1000px","800px"]);
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'litestoregoodsrights/index',
                    add_url: 'litestoregoodsrights/add',
                    edit_url: 'litestoregoodsrights/edit',
                    del_url: 'litestoregoodsrights/del',
                    multi_url: 'litestoregoodsrights/multi',
                    table: 'litestore_goods',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'goods_id',
                sortName: 'goods_sort',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'goods_id', title: __('Goods_id')},
                        {field: 'goods_name', title: __('Goods_name')},
                        {field: 'category.name', title: __('Category.name')},
                        {field: 'category_id', visible:false,title: __('Category_id')},
                        {field: 'images', title: __('Images'), formatter: Table.api.formatter.images},
                        {field: 'spec_type', title: __('Spec_type'), searchList: {"10":__('Spec_type 10'),"20":__('Spec_type 20')}, formatter: Table.api.formatter.normal},
                        {field: 'deduct_stock_type', title: __('Deduct_stock_type'), searchList: {"10":__('Deduct_stock_type 10'),"20":__('Deduct_stock_type 20')}, formatter: Table.api.formatter.normal},
                        {field: 'freight.name', title: __('Freight.name')},
                        {field: 'sales_initial', title: __('Sales_initial')},
                        {field: 'sales_actual', title: __('Sales_actual')},
                        {field: 'goods_sort', title: __('Goods_sort')},
                        {field: 'delivery_id', title: __('Delivery_id')},
                        {field: 'goods_status', title: __('Goods_status'), searchList: {"10":__('Goods_status 10'),"20":__('Goods_status 20')}, formatter: Table.api.formatter.status},
                        {field: 'is_delete', title: __('Is_delete'), searchList: {"0":__('Is_delete 0'),"1":__('Is_delete 1')}, formatter: Table.api.formatter.normal},
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
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                Fast.api.close(data);
                Toastr.success("商品提交成功");
            }, function(data, ret){
                Toastr.success("商品提交失败");
            }, function(success, error){
                //注意如果我们需要阻止表单，可以在此使用return false;即可
                //如果我们处理完成需要再次提交表单则可以使用submit提交,如下
                console.log(this);
                var form = this;
                if (form.size() === 0) {
                    Toastr.error("表单未初始化完成,无法提交");
                    return false;
                }
                var type = form.attr("method") ? form.attr("method").toUpperCase() : 'GET';
                type = type && (type === 'GET' || type === 'POST') ? type : 'GET';
                url = form.attr("action");
                url = url ? url : location.href;
                //修复当存在多选项元素时提交的BUG
                var params = {};
                var multipleList = $("[name$='[]']", form);
                if (multipleList.size() > 0) {
                    var postFields = form.serializeArray().map(function (obj) {
                        return $(obj).prop("name");
                    });
                    $.each(multipleList, function (i, j) {
                        if (postFields.indexOf($(this).prop("name")) < 0) {
                            params[$(this).prop("name")] = '';
                        }
                    });
                }
                var dataParam = {spec_many: specMany.getData()};
                console.log(dataParam);
                Fast.api.ajax({
                    type: type,
                    url: url,
                    data: form.serialize() + (Object.keys(params).length > 0 ? '&' + $.param(params) : '') + (Object.keys(dataParam).length > 0 ? '&' + $.param(dataParam) : ''),
                    dataType: 'json',
                    complete: function (xhr) {
                        var token = xhr.getResponseHeader('__token__');
                        if (token) {
                            $("input[name='__token__']", form).val(token);
                        }
                        //关闭弹窗
                        var index = parent.Layer.getFrameIndex(window.name);
                        var callback = parent.$("#layui-layer" + index).data("callback");
                        parent.Layer.close(index);
                        //刷新列表
                        parent.$("#table").bootstrapTable('refresh');
                    }
                }, function (data, ret) {
                    $('.form-group', form).removeClass('has-feedback has-success has-error');
                    if (data && typeof data === 'object') {
                        if (typeof data.token !== 'undefined') {
                            $("input[name='__token__']", form).val(data.token);
                        }
                        if (typeof data.callback !== 'undefined' && typeof data.callback === 'function') {
                            data.callback.call(form, data);
                        }
                    }
                }, function (data, ret) {
                    if (data && typeof data === 'object' && typeof data.token !== 'undefined') {
                        $("input[name='__token__']", form).val(data.token);
                    }
                });
                return false;
            });

            // 注册商品多规格组件
            var specMany = new GoodsSpec({
                container: '.goods-spec-many',
                OutForm:Form
            });

            // 切换单/多规格
            $('select[name="row[spec_type]"').change(function (e) {
                var $goodsSpecMany = $('.goods-spec-many')
                    , $goodsSpecSingle = $('.goods-spec-single');
                if (e.currentTarget.value === '10') {
                    $goodsSpecMany.hide() && $goodsSpecSingle.show();
                } else {
                    $goodsSpecMany.show() && $goodsSpecSingle.hide();
                }
            });


        },
        edit: function () {
            //Controller.api.bindevent();

            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //Fast.api.close(data);
                Toastr.success("商品提交成功");
            }, function(data, ret){
                Toastr.success("商品提交失败");
            }, function(success, error){
                //注意如果我们需要阻止表单，可以在此使用return false;即可
                //如果我们处理完成需要再次提交表单则可以使用submit提交,如下
                console.log(this);
                var form = this;
                if (form.size() === 0) {
                    Toastr.error("表单未初始化完成,无法提交");
                    return false;
                }
                var type = form.attr("method") ? form.attr("method").toUpperCase() : 'GET';
                type = type && (type === 'GET' || type === 'POST') ? type : 'GET';
                url = form.attr("action");
                url = url ? url : location.href;
                //修复当存在多选项元素时提交的BUG
                var params = {};
                var multipleList = $("[name$='[]']", form);
                if (multipleList.size() > 0) {
                    var postFields = form.serializeArray().map(function (obj) {
                        return $(obj).prop("name");
                    });
                    $.each(multipleList, function (i, j) {
                        if (postFields.indexOf($(this).prop("name")) < 0) {
                            params[$(this).prop("name")] = '';
                        }
                    });
                }
                var dataParam = {spec_many: specMany.getData()};
                console.log(dataParam);
                Fast.api.ajax({
                    type: type,
                    url: url,
                    data: form.serialize() + (Object.keys(params).length > 0 ? '&' + $.param(params) : '') + (Object.keys(dataParam).length > 0 ? '&' + $.param(dataParam) : ''),
                    dataType: 'json',
                    complete: function (xhr) {
                        var token = xhr.getResponseHeader('__token__');
                        if (token) {
                            $("input[name='__token__']", form).val(token);
                        }
                        //关闭弹窗
                        var index = parent.Layer.getFrameIndex(window.name);
                        var callback = parent.$("#layui-layer" + index).data("callback");
                        parent.Layer.close(index);
                        //刷新列表
                        parent.$("#table").bootstrapTable('refresh');
                    }
                }, function (data, ret) {
                    $('.form-group', form).removeClass('has-feedback has-success has-error');
                    if (data && typeof data === 'object') {
                        if (typeof data.token !== 'undefined') {
                            $("input[name='__token__']", form).val(data.token);
                        }
                        if (typeof data.callback !== 'undefined' && typeof data.callback === 'function') {
                            data.callback.call(form, data);
                        }
                    }
                }, function (data, ret) {
                    if (data && typeof data === 'object' && typeof data.token !== 'undefined') {
                        $("input[name='__token__']", form).val(data.token);
                    }
                });
                return false;
            });
            // 注册商品多规格组件
            var specMany = new GoodsSpec({
                container: '.goods-spec-many',
                OutForm:Form
            }, from_specData);

            // 切换单/多规格
            $('select[name="row[spec_type]"').change(function (e) {
                var $goodsSpecMany = $('.goods-spec-many')
                    , $goodsSpecSingle = $('.goods-spec-single');
                if (e.currentTarget.value === '10') {
                    $goodsSpecMany.hide() && $goodsSpecSingle.show();
                } else {
                    $goodsSpecMany.show() && $goodsSpecSingle.hide();
                }
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