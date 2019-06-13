<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:81:"/www/wwwroot/beike/public/../application/admin/view/litestoregoodsrights/add.html";i:1560394052;s:61:"/www/wwwroot/beike/application/admin/view/layout/default.html";i:1557482264;s:58:"/www/wwwroot/beike/application/admin/view/common/meta.html";i:1557482264;s:80:"/www/wwwroot/beike/application/admin/view/litestoregoods/spec_many_template.html";i:1559744802;s:60:"/www/wwwroot/beike/application/admin/view/common/script.html";i:1557482264;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !$config['fastadmin']['multiplenav']): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Goods_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-goods_name" data-rule="required" class="form-control form-control" name="row[goods_name]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Category_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-category_id" data-rule="required" data-source="litestorecategory/selectpage" class="form-control selectpage form-control" name="row[category_id]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Images'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-images" data-rule="required" class="form-control form-control" size="50" name="row[images]" type="text">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-images" class="btn btn-danger plupload" data-input-id="c-images" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-images"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-images" class="btn btn-primary fachoose" data-input-id="c-images" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-images"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-images"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Spec_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-spec_type" data-rule="required" class="form-control selectpicker" name="row[spec_type]">
                <?php if(is_array($specTypeList) || $specTypeList instanceof \think\Collection || $specTypeList instanceof \think\Paginator): if( count($specTypeList)==0 ) : echo "" ;else: foreach($specTypeList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"10"))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>

<!-- 商品规格属性 start -->
<div class="goods-spec-many form-group">
    <!-- <label class="control-label col-xs-12 col-sm-2"></label>-->
    <div class="goods-spec-box col-xs-12 col-sm-11" style="background-color: white;">
        <!-- 规格属性 -->
        <div class="spec-attr"></div>

        <!-- 添加规格：按钮 -->
        <div class="spec-group-button">
            <button type="button" class="btn-addSpecGroup btn btn-xs btn-success">添加规格</button>
        </div>

        <!-- 添加规格：表单 -->
        <div class="spec-group-add">
            <div class="spec-group-add-item form-group">
                <label class="control-label form-require">规格名 </label>
                <input type="text" class="input-specName tpl-form-input"
                       placeholder="请输入规格名称">
            </div>
            <div class="spec-group-add-item form-group">
                <label class="control-label form-require">规格值 </label>
                <input type="text" class="input-specValue tpl-form-input"
                       placeholder="请输入规格值">
            </div>
            <div class="spec-group-add-item margin-top">
                <button type="button" class="btn-addSpecName btn btn-xs btn-success"> 确定
                </button>
                <button type="button" class="btn-cancleAddSpecName btn btn-xs btn-default"> 取消
                </button>
            </div>
        </div>
        <!-- 商品多规格sku信息 -->
        <div class="goods-sku scrollable-horizontal">
            <!-- 分割线 -->
            <div class="goods-spec-line"></div>
            <!-- sku 批量设置 -->
            <div class="spec-batch form-inline">
                <div class="form-group">
                    <label class="control-label">批量设置</label>
                </div>
                <div class="form-group">
                    <input type="text" data-type="goods_no" placeholder="商家编码">
                </div>
                <div class="form-group">
                    <input type="number" data-type="goods_price" placeholder="销售价">
                </div>
                <div class="form-group">
                    <input type="number" data-type="line_price" placeholder="划线价">
                </div>
                <div class="form-group">
                    <input type="number" data-type="stock_num" placeholder="库存数量">
                </div>
                <div class="form-group">
                    <input type="number" data-type="goods_weight" placeholder="重量">
                </div>
                <div class="form-group">
                    <button type="button" class="btn-specBatchBtn btn btn-sm btn-secondary
                     radius">确定
                    </button>
                </div>
            </div>
            <!-- sku table -->
            <table class="spec-sku-tabel table table-bordered table-centered margin-bottom-xs text-nowrap"></table>
        </div>
    </div>
</div>

<div class="goods-spec-single">
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">商品编码:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-goods_no" class="form-control form-control" name="spec[goods_no]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">商品价格:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-goods_price" data-rule="required" class="form-control form-control" name="spec[goods_price]" type="number" value="0">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">所需配额:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-line_price" class="form-control form-control" name="spec[goods_quota]" type="number" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">当前库存数量:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-stock_num" class="form-control form-control" name="spec[stock_num]" type="number" value="100">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">商品重量(Kg):</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-goods_weight" class="form-control form-control" name="spec[goods_weight]" type="number" value="1">
        </div>
    </div>
</div>
<!-- 商品规格属性 end -->

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Deduct_stock_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-deduct_stock_type" data-rule="required" class="form-control selectpicker" name="row[deduct_stock_type]">
                <?php if(is_array($deductStockTypeList) || $deductStockTypeList instanceof \think\Collection || $deductStockTypeList instanceof \think\Paginator): if( count($deductStockTypeList)==0 ) : echo "" ;else: foreach($deductStockTypeList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"20"))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Content'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-content" data-rule="required" class="form-control editor form-control" rows="5" name="row[content]" cols="50"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Sales_initial'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-sales_initial" data-rule="required" class="form-control form-control" name="row[sales_initial]" type="number" value="0">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Sales_actual'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-sales_actual" data-rule="required" class="form-control form-control" name="row[sales_actual]" type="number" value="0">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Goods_sort'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-goods_sort" data-rule="required" class="form-control form-control" name="row[goods_sort]" type="number" value="100">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Delivery_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-delivery_id" data-rule="required" data-source="litestorefreight/index" class="form-control selectpage form-control" name="row[delivery_id]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Goods_status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($goodsStatusList) || $goodsStatusList instanceof \think\Collection || $goodsStatusList instanceof \think\Paginator): if( count($goodsStatusList)==0 ) : echo "" ;else: foreach($goodsStatusList as $key=>$vo): ?>
            <label for="row[goods_status]-<?php echo $key; ?>"><input id="row[goods_status]-<?php echo $key; ?>" name="row[goods_status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"10"))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Is_delete'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-is_delete" data-rule="required" class="form-control selectpicker" name="row[is_delete]">
                <?php if(is_array($isDeleteList) || $isDeleteList instanceof \think\Collection || $isDeleteList instanceof \think\Paginator): if( count($isDeleteList)==0 ) : echo "" ;else: foreach($isDeleteList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"0"))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>
<!-- 商品规格属性模板 -->
<script id="tpl_spec_attr" type="text/template">
    <% for(var j=0;j< spec_attr.length;j++){ %>
    <div class="spec-group-item" data-index="<%=j%>" data-group-id="<%=spec_attr[j].group_id%>">
        <div class="spec-group-name">
            <span><%=spec_attr[j].group_name%></span>
            <i class="spec-group-delete fa fa-trash icon-shanchu1" title="点击删除"></i>
        </div>
        <div class="spec-list am-cf">
            <% for(var k=0;k<spec_attr[j].spec_items.length;k++){ %>
            <div class="spec-item am-fl" data-item-index="<%=k%>">
                <span><%=spec_attr[j].spec_items[k].spec_value%></span>
                <i class="spec-item-delete fa fa-trash icon-shanchu1" title="点击删除"></i>
            </div>
            <% } %>
            <div class="spec-item-add am-cf am-fl">
                <input type="text" class="ipt-specItem am-fl am-field-valid">
                <button type="button" class="btn-addSpecItem btn btn-xs btn-success am-fl">添加</button>
            </div>
        </div>
    </div>
    <% } %>
</script>

<!-- 商品规格table模板 -->
<script id="tpl_spec_table" type="text/template">
    <tbody>
    <tr>
        <th>单sku隐藏</th>
        <% for(var i=0;i< spec_attr.length;i++){ %>
            <th><%=spec_attr[i].group_name%></th>
        <% } %>
        <th>商品编码</th>
        <th>销售价</th>
        <th>划线价</th>
        <th>库存(-1隐藏)</th>
        <th>重量(kg)</th>
        <th>规格封面</th>
        <th>封面编辑</th>
    </tr>
    <% for(var i=0;i<spec_list.length;i++){ %>
    <tr data-index="<%=i%>" data-sku-id="<%=spec_list[i].spec_sku_id %>">
        <td>
            <button type="button" class="am-text-middle btn delsku-bt"><% if(spec_list[i].form.stock_num===-1) {%> 显示  <%}else{%> 隐藏 <%} %></button>
        </td>
        <% for(var k=0;k<spec_list[i].rows.length;k++){ %>
        <td class="td-spec-value am-text-middle" rowspan="<%=spec_list[i].rows[k].rowspan%>">
            <%=spec_list[i].rows[k].spec_value%>
        </td>
        <% } %>
        <td>
            <input <% if(spec_list[i].form.stock_num===-1) {%> disabled <%}else{%>  <%} %> type="text" name="goods_no" data-rule="required" value="<%=spec_list[i].form.goods_no%>" class="ipt-goods-no am-field-valid">
        </td>
        <td>
            <input  <% if(spec_list[i].form.stock_num===-1) {%> disabled <%}else{%>  <%} %>  type="number" name="goods_price" data-rule="required" value="<%=spec_list[i].form.goods_price%>" class="am-field-valid ipt-w80"
                   required>
        </td>
        <td>
            <input <% if(spec_list[i].form.stock_num===-1) {%> disabled <%}else{%>  <%} %> type="number" name="line_price" data-rule="required" value="<%=spec_list[i].form.line_price%>" class="am-field-valid ipt-w80">
        </td>
        <td>
            <input <% if(spec_list[i].form.stock_num===-1) {%>disabled  <%}else{%>  <%} %> type="number" name="stock_num" data-rule="required" value="<%=spec_list[i].form.stock_num%>" class="am-field-valid ipt-w80"
                   required>
        </td>
        <td>
            <input <% if(spec_list[i].form.stock_num===-1) {%>disabled  <%}else{%>  <%} %> type="number" name="goods_weight" data-rule="required" value="<%=spec_list[i].form.goods_weight%>" class="am-field-valid ipt-w80"
                   required>
        </td>
        <td>
            <input <% if(spec_list[i].form.stock_num===-1) {%> disabled <%}else{%>  <%} %>  id="c-image<%=i%>" class="form-control form-control" style="display: none!important;" name="spec_image" type="text" value="<%=spec_list[i].form.spec_image%>">
            <div class="row list-inline plupload-preview spe_img_one" id="p-image<%=i%>"></div>
        </td>
        <td>
            <div class="input-group-addon no-border no-padding">
                <span><button type="button" id="plupload-image<%=i%>" style="display: none!important;" class="btn btn-danger plupload" data-input-id="c-image<%=i%>" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-image<%=i%>"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                <span><button type="button" <% if(spec_list[i].form.stock_num===-1) {%> disabled <%}else{%>  <%} %> id="fachoose-image<%=i%>" class="btn btn-primary fachoose" data-input-id="c-image<%=i%>" data-mimetype="image/*" data-preview-id="p-image<%=i%>" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
            </div>
        </td>
    </tr>
    <% } %>
    </tbody>
</script>


<link rel="stylesheet" href="/assets/addons/litestore/css/litestoregoods.css">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>