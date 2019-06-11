<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:100:"C:\Users\Administrator\Desktop\work\fast\public/../application/admin\view\litestoreorder\detail.html";i:1559744802;s:83:"C:\Users\Administrator\Desktop\work\fast\application\admin\view\layout\default.html";i:1557482264;s:80:"C:\Users\Administrator\Desktop\work\fast\application\admin\view\common\meta.html";i:1557482264;s:82:"C:\Users\Administrator\Desktop\work\fast\application\admin\view\common\script.html";i:1557482264;}*/ ?>
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
                                <form id="send-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

	<fieldset>
		<legend>
			<?php echo __('order detail info'); ?> 
		</legend>

		<!-- 订单信息 -->
		<div class="col-md-10 col-md-offset-1 col-sm-12">
			<div class="box radius-none">
				<div class="box-header with-border">
					<?php echo __('Order_no'); ?>：<?php echo $vo['order_no']; ?> ( 下单时间 <?php echo date("Y-m-d H:i:s",$vo['createtime']); ?>)
				</div>
				<div class="box-body">
					<div class="panel panel-default">
					  <div class="panel-heading">商品详情</div>
					  <table class="table">
					  	<?php if(is_array($vo['goods']) || $vo['goods'] instanceof \think\Collection || $vo['goods'] instanceof \think\Paginator): if( count($vo['goods'])==0 ) : echo "" ;else: foreach($vo['goods'] as $key=>$item): ?>
						  	<tr>
							  <td><?php echo $item['goods_name']; ?></td>
							  <td><?php echo $item['goods_attr']; ?></td>
							  <td><?php echo $item['goods_price']; ?>元</td>
							  <td>数量:<?php echo $item['total_num']; ?></td>
							</tr>
				        <?php endforeach; endif; else: echo "" ;endif; ?>
					  </table>
					</div>

					<div class="row" style="margin:1em 0;">
						<div class="col-md-4 padding">
							<span class="label label-danger"><?php echo __('Pay_status'); ?></span> : 
							<?php echo $vo['pay_status_text']; ?>
						</div>
						<div class="col-md-4 padding">
							<span class="label label-danger"><?php echo __('Pay_time'); ?></span> : 
							<?php if(($vo['pay_time']==0)): ?>
								无
							<?php else: ?>
								<?php echo date("Y-m-d H:i:s",$vo['pay_time']); endif; ?>
						</div>
						<div class="col-md-4 padding">
							<span class="label label-danger"><?php echo __('Pay_price'); ?></span> : 
							<?php echo $vo['pay_price']; ?>
						</div>
					</div>

					<div class="row" style="margin:1em 0;">
						<div class="col-md-4 padding">
							<span class="label label-success"><?php echo __('Freight_status'); ?></span> :
							<?php echo $vo['freight_status_text']; ?>
						</div>
						<div class="col-md-4 padding">
							<span class="label label-success"><?php echo __('Freight_time'); ?></span> :
							<?php if(($vo['freight_time']==0)): ?>
								无
							<?php else: ?>
								<?php echo date("Y-m-d H:i:s",$vo['freight_time']); endif; ?>
						</div>
						<div class="col-md-4 padding">
							<span class="label label-success"><?php echo __('Express_price'); ?></span> :
							<?php echo $vo['express_price']; ?>
						</div>
						<div class="col-md-4 padding">
							<span class="label label-success"><?php echo __('Express_company'); ?></span> :
							<?php echo $vo['express_company']; ?>
						</div>
						<div class="col-md-4 padding">
							<span class="label label-success"><?php echo __('Express_no'); ?></span> : 
							<?php echo $vo['express_no']; ?>
						</div>
					</div>

					<div class="row" style="margin:1em 0;">
						<div class="col-md-4 padding">
							<span class="label label-info"><?php echo __('Receipt_status'); ?></span> : 
							<?php echo $vo['receipt_status_text']; ?>
						</div>
						<div class="col-md-4 padding">
							<span class="label label-info"><?php echo __('Receipt_time'); ?></span> : 
							<?php if(($vo['receipt_time']==0)): ?>
								无
							<?php else: ?>
								<?php echo date("Y-m-d H:i:s",$vo['receipt_time']); endif; ?>
						</div>
						<div class="col-md-4 padding">
							<span class="label label-info"><?php echo __('Order_status'); ?></span> : 
							<?php echo $vo['order_status_text']; ?>
						</div>
					</div>

					<div class="col-md-12 padding">
						<fieldset>
							<legend><h4><?php echo __('consignee user info'); ?></h4></legend>
							<div class="col-md-6 padding">
								<?php echo __('province / Region'); ?>: <?php echo $vo['address']['Area']['province']; ?> / <?php echo $vo['address']['Area']['city']; ?> / <?php echo $vo['address']['Area']['region']; ?>
							</div>
							<div class="col-md-6 padding">
								<?php echo __('AddressDetail'); ?>: <?php echo $vo['address']['detail']; ?>
							</div>
							<div class="col-md-6 padding">
								<?php echo __('Address.name'); ?>:<?php echo $vo['address']['name']; ?>
							</div>
							<div class="col-md-6 padding">
								<?php echo __('mobile'); ?>: <?php echo $vo['address']['phone']; ?>
							</div>
						</fieldset>
					</div>

					<div class="col-md-12 padding">
						<fieldset>
							<legend><h4> </h4></legend>
							<div class="col-md-6 col-md-offset-6 padding">
								<div class="col-md-8 padding" style="text-align: right;">
									<span class="label label-success">下单微信昵称:</span><br><br><?php echo $vo['user']['nickname']; ?>
								</div>
								<div class="col-md-4 padding" style="text-align: left;">
									<img style="width: 78px;" class="img-circle" src="<?php echo $vo['user']['avatar']; ?>"/>
								</div>
								
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
		<!-- 订单信息 -->
	</fieldset>


	<?php if(in_array(($vo['freight_status']), explode(',',"10"))): ?>
	<fieldset>
		<legend><h4>发货信息</h4></legend>
			<div class="form-group">
				<input type="hidden" name="ids" id="ids" value="<?php echo $vo['id']; ?>">
				<label for="c-virtual_name" class="control-label col-xs-12 col-sm-2">快递公司:</label>
				<div class="col-xs-12 col-sm-8">
					<input placeholder="" id="c-virtual_name" data-rule="required" class="form-control" name="virtual_name" type="text" value="">
				</div>
			</div>
			<div class="form-group">
				<label for="c-virtual_sn" class="control-label col-xs-12 col-sm-2">快递单号:</label>
				<div class="col-xs-12 col-sm-8">
					<input placeholder="" id="c-virtual_sn" data-rule="required" class="form-control" name="virtual_sn" type="text" value="">
				</div>
			</div>
	</fieldset>

		<div class="form-group layer-footer">
			<label class="control-label col-xs-12 col-sm-2"></label>
			<div class="col-xs-12 col-sm-8">
				<button type="button" id="send" data-type="send" class="btn btn-success btn-embossed">确认发货</button>
			</div>
		</div>
	<?php endif; ?>

</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>