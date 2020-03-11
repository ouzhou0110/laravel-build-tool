<?php
namespace ZhouOu\LaravelTool\Command;

use Illuminate\Console\Command;
use ZhouOu\LaravelTool\Generator\ModelGenerator;
use ZhouOu\LaravelTool\Tool\PathTool;

class TableInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zhouou:table {tableName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '根据数据库表名自动生成Model、Action、Query、Service类（数据库表的名称，驼峰式命名）';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tableName = $this->argument('tableName');

        // 解析路径信息
        $parser = new PathTool();
        $config = $parser->tablePathParse($tableName);

        $this->_make_model($config);

    }

    private function _make_model($config)
    {
        // 读取默认配置--框架中config目录下没有找到，就使用包中config
        if (!$tableBaseConfig = config('tableConfig')) {
            $tableBaseConfig = require_once __DIR__ . '/../Config/tableConfig.php';
        }

        // 开启了model组件初始化
        if (true === $tableBaseConfig['tableInit']['model']) {
            ModelGenerator::init($tableBaseConfig, $config);
        }
    }
}
