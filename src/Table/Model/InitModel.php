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

    /**
     * 重写模型insert方法 自动注入时间,弥补了Laravel Model::insert() 不自动加入时间的问题。
     * @param array $data
     * @return bool
     */
    public function insert(array $data)
    {
        if ($this->timestamps == true) {
            if (count($data, true) === count($data)) {
                foreach ($data as &$item) {
                    $item = self::mergeDataAt($item);
                }
                unset($item);
            } else {
                $data = self::mergeDataAt($data);
            }
        }

        return static::insert($data);
    }

    /**
     * @param array $data
     * 重写 Model::insertGetId() 方法 添加时间
     * @param null  $args
     * @return int|mixed
     */
    public function insertGetId(array $data, $args = null)
    {
        if ($this->timestamps == true) {
            $data = self::mergeDataAt($data);
        }
        return static::insertGetId($data, $args);
    }

    public static function mergeDataAt(array $data)
    {

        $time = now()->toDateString();
        $data = array_merge($data, [
            'created_at' => $time,
        ]);
        return $data;
    }
}
