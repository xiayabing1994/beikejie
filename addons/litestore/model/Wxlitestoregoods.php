<?php

namespace addons\litestore\model;

use think\Model;
use app\admin\model\Litestoregoods;

class Wxlitestoregoods extends Litestoregoods
{
    protected $append = ['goods_sales'];
    
    public function getGoodsSalesAttr($value, $data)
    {
        return $data['sales_initial'] + $data['sales_actual'];
    }

	//这里是最新上架的8件商品
	public function getNewList()
    {
        return $this->with(['spec', 'category'])
            ->where('is_delete', '=', 0)
            ->where('goods_status', '=', 10)
            ->order(['goods_id' => 'desc', 'goods_sort' => 'asc'])
            ->limit(8)
            ->select();
    }

    //这里是随机的8件商品
    public function getRandom8()
    {
        return $this->with(['spec', 'category'])
            ->where('is_delete', '=', 0)
            ->where('goods_status', '=', 10)
            ->orderRaw('rand()')
            ->limit(8)
            ->select();
    }
    //这里是热销的8件商品
    public function getHot8()
    {
        return $this->with(['spec', 'category'])
            ->where('is_delete', '=', 0)
            ->where('goods_status', '=', 10)
            ->orderRaw('sales_actual')
            ->limit(8)
            ->select();
    }
    //
    public function getTypeList($type)
    {
        return $this->with(['spec', 'category'])
            ->where('is_delete', '=', 0)
            ->where('goods_status', '=', 10)
            ->where('goods_type','=',$type)
            ->orderRaw('sales_actual')
            ->limit(8)
            ->select();
    }

    public static function detail($goods_id)
    {
        $dataout = self::get($goods_id, ['category', 'spec', 'specRel', 'freight']);
        $dataout['image'] = cdnurl(explode(",",$dataout['images'])[0], true);
        return $dataout;
    }

    /**
     * 商品多规格信息
     */
    public function getGoodsSku($goods_sku_id)
    {
        $goodsSkuData = array_column($this['spec'], null, 'spec_sku_id');
        if (!isset($goodsSkuData[$goods_sku_id])) {
            return false;
        }
        $goods_sku = $goodsSkuData[$goods_sku_id];
        // 多规格文字内容
        $goods_sku['goods_attr'] = '';
        if ($this['spec_type'] === '20') {
            $attrs = explode('_', $goods_sku['spec_sku_id']);
            $spec_rel = array_column($this['specRel'], null, 'id');

            foreach ($attrs as $specValueId) {
                $goods_sku['goods_attr'] .= $spec_rel[$specValueId]['spec']['spec_name'] . ':'
                    . $spec_rel[$specValueId]['spec_value'] . '; ';
            }

            //这里格式化 展示图片
            $goods_sku['img_show'] = $goods_sku['spec_image']=='' ? '': cdnurl($goods_sku['spec_image'], true);
        }
        return $goods_sku;
    }

    public function getListByIds($goodsIds) {
        $dataout = $this->with(['category', 'spec', 'spec_rel.spec', 'freight.rule'])
            ->where('goods_id', 'in', $goodsIds)->select();
        $blistdatarList = [];
        foreach ($dataout as $index => $item) {
            $item['image'] = cdnurl(explode(",",$item['images'])[0], true);
            $blistdatarList[] = $item;
        }
        return $blistdatarList;
    }

    public function updateStockSales($goodsList)
    {
        // 整理批量更新商品销量
        $goodsSave = [];
        // 批量更新商品规格：sku销量、库存
        $goodsSpecSave = [];
        foreach ($goodsList as $goods) {
            $goodsSave[] = [
                'goods_id' => $goods['goods_id'],
                'sales_actual' => ['inc', $goods['total_num']]
            ];
            $specData = [
                'goods_spec_id' => $goods['goods_spec_id'],
                'goods_sales' => ['inc', $goods['total_num']]
            ];
            // 付款减库存
            if ($goods['deduct_stock_type'] === '20') {
                $specData['stock_num'] = ['dec', $goods['total_num']];
            }
            $goodsSpecSave[] = $specData;
        }
        // 更新商品总销量
        $this->allowField(true)->isUpdate()->saveAll($goodsSave);
        // 更新商品规格库存
        (new Litestoregoodsspec)->isUpdate()->saveAll($goodsSpecSave);
    }

}

