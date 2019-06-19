<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:78:"/www/wwwroot/beike/public/../application/admin/view/litestorefreight/edit.html";i:1559744802;s:61:"/www/wwwroot/beike/application/admin/view/layout/default.html";i:1557482264;s:58:"/www/wwwroot/beike/application/admin/view/common/meta.html";i:1557482264;s:60:"/www/wwwroot/beike/application/admin/view/common/script.html";i:1557482264;}*/ ?>
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
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-name" data-rule="required" class="form-control form-control" name="row[name]" type="text" value="<?php echo $row['name']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Method'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-method" data-rule="required" class="form-control selectpicker" name="row[method]">
                <?php if(is_array($methodList) || $methodList instanceof \think\Collection || $methodList instanceof \think\Paginator): if( count($methodList)==0 ) : echo "" ;else: foreach($methodList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['method'])?$row['method']:explode(',',$row['method']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-3 col-sm-2 control-label">
            配送区域及运费
        </label>
        <div class="col-xs-12 col-sm-8 am-u-end">
            <div class="am-scrollable-horizontal">
                <table class="table table-bordered table-striped regional-table am-table-centered am-margin-bottom-xs" style="border:2px solid #d2d6de;">
                    <tbody>
                    <tr style="background-color: #ffffff;">
                        <th width="42%">可配送区域</th>
                        <th style="text-align: right;">
                            <span class="first"><?php echo $row['method']==10?'首件 (个)' : '首重 (Kg)'; ?></span>
                        </th>
                        <th style="text-align: right;">运费 (元)</th>
                        <th style="text-align: right;">
                            <span class="additional"><?php echo $row['method']==10?'续件 (个)' : '续重 (Kg)'; ?></span>
                        </th>
                        <th style="text-align: right;">续费 (元)</th>
                    </tr>
                    <?php if(is_array($row['rule']) || $row['rule'] instanceof \think\Collection || $row['rule'] instanceof \think\Paginator): if( count($row['rule'])==0 ) : echo "" ;else: foreach($row['rule'] as $key=>$item): ?>
                        <tr>
                            <td class="am-text-left">
                                <p class="selected-content am-margin-bottom-xs">
                                    <?php echo $item['region_content']; ?>
                                </p>
                                <p class="operation am-margin-bottom-xs">
                                    <a class="edit" href="javascript:;">编辑</a>
                                    <a class="delete" href="javascript:;">删除</a>
                                </p>
                                <input type="hidden" name="delivery[rule][region][]"
                                       value="<?= $item['region'] ?>">
                            </td>
                            <td>
                                <input type="number" name="delivery[rule][first][]"
                                       value="<?= $item['first'] ?>" required>
                            </td>
                            <td>
                                <input type="number" name="delivery[rule][first_fee][]"
                                       value="<?= $item['first_fee'] ?>" required>
                            </td>
                            <td>
                                <input type="number" name="delivery[rule][additional][]"
                                       value="<?= $item['additional'] ?>">
                            </td>
                            <td>
                                <input type="number" name="delivery[rule][additional_fee][]"
                                       value="<?= $item['additional_fee'] ?>">
                            </td>
                        </tr>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                    <tr>
                        <td colspan="5" class="am-text-left">
                            <a class="add-region btn btn-success" href="javascript:;">
                                <i class="fa fa-plus"></i>
                                点击添加可配送区域和运费
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Weigh'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-weigh" data-rule="required" class="form-control form-control" name="row[weigh]" type="number" value="<?php echo $row['weigh']; ?>">
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
<link rel="stylesheet" href="/assets/addons/litestore/css/litestorefeight.css">
<div class="regional-choice"></div>
<script>
    // 初始化区域选择界面
    var datas = JSON.parse('<?= $regionData ?>');
</script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>