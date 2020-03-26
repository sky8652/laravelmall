<?php

namespace App\Business;

use App\Helper\CommonResult;
use App\Models\Mysql\Footprint;

class FootprintBusiness extends BaseBusiness
{
    protected static $select = ['id', 'goods_id', 'created_at'];

    public static function getList(array $attributes)
    {
        $condition = [
            'user_id'   =>  $attributes['userId']
        ];
        $with = [
            'goods' =>  function($query) {
                $query->select(['id', 'name','brief', 'pic_url', 'retail_price']);
            }
        ];

        $list = self::queryListByCondition($attributes['page'], $attributes['limit'], $condition,'created_at','desc',[], $with);
        $count = self::queryCountByCondition($condition);
        $page = CommonResult::formatPaged($attributes['page'], $attributes['limit'], $count);

        $footprintList = [];
        if ($list) {
            foreach ($list as $vo) {
                $footprintList[] = [
                    'id'    =>  $vo['id'],
                    'created_at'  =>  $vo['created_at'],
                    'goods_id'  =>  $vo['goods_id'],
                    'name'  =>  $vo['goods']['name'],
                    'brief'  =>  $vo['goods']['brief'],
                    'pic_url'  =>  $vo['goods']['pic_url'],
                    'retail_price'  =>  $vo['goods']['retail_price'],
                ];
            }
        }

        return CommonResult::formatBody(array_merge($page, ['list'=>$footprintList]));
    }

    protected static function queryListByCondition($page, $limit, $condition=[], $sort='created_at', $order='desc', $select='', $with=[])
    {
        $query = Footprint::query();
        $select = $select ? $select : self::$select;
        if ($with) {
            $query = $query->with($with);
        }

        return $query->where($condition)
                    ->orderBy($sort, $order)
                    ->forPage($page, $limit)
                    ->select($select)
                    ->get()
                    ->toArray();
    }

    protected static function queryCountByCondition($condition)
    {
        $query = Footprint::query();
        if ($condition) {
            $query = $query->where($condition);
        }

        return $query->count();
    }
}