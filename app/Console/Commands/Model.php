<?php
/**
 * 生成模型
 */

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Model extends Command
{
    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'model:build {name} {path=Models/V1}';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = '创建模型';

    public function handle()
    {

        $args = $this->arguments();
        $className = ucfirst($args['name']);

        $namespace = $args['path'];
        $fileName =  $className. '.php';
        $path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $namespace;
        $file = $path . DIRECTORY_SEPARATOR . $fileName;


        // 处理表名,得到不为空字段
        $isTimestamps = 0;
        $formatStr = ltrim(preg_replace("/([A-Z])/", "_\\1", $className), '_');
        $table = strtolower($formatStr) . 's';
        $tablePrefix = config('database.connections.mysql.prefix');
        $tableColumns = DB::select("desc $tablePrefix{$table}");
        foreach ($tableColumns as $tableColumn) {
            if ($tableColumn->Field == 'created_at') {
                $isTimestamps = 1;
            }
        }

        if (!is_file($file)) {
            $this->create($file, $namespace, $className, $isTimestamps);
            $this->comment("$fileName 创建成功");
        } else {
            unlink($file);
            $this->comment('文件已经存在');
        }
    }

    public function create($file, $namespace, $className, $isTimestamps)
    {
        $namespace = str_replace('/', '\\', $namespace);
        $content = <<<EOT
<?php \n
namespace App\\{$namespace};

use App\Models\BaseModel;

class $className extends BaseModel
{
    /**
     * 不可被批量赋值的属性.
     *
     * @var array
     */
    protected \$guarded = [];
    
    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public \$timestamps = $isTimestamps;

}
EOT;
;
        file_put_contents($file, $content);
        return true;
    }
}