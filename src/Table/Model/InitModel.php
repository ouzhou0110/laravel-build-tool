<?php
namespace ZhouOu\LaravelTool\Table\Model;

use Illuminate\Database\Eloquent\Model;

class InitModel extends Model
{
    public    $timestamps = true; // 开启时间维护
    protected $primaryKey = 'id'; // 默认主键
    protected $guarded    = ['id']; // 禁止填充
    protected $dateFormat = 'Y-m-d H:i:s';// 时间格式
    protected $dates      = ['deleted_at', 'created_at', 'updated_at']; // 时间转换
    protected $hidden = ['password'];
}
