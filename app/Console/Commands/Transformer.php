<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Transformer extends Command
{
    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'transformer:build {name} {path=Transformers}';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = '创建数据构建器';


    public function handle()
    {

        $args = $this->arguments();
        $className = ucfirst($args['name']);

        $namespace = $args['path'];
        $fileName =  $className. 'Transformer.php';
        $path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $namespace;
        $file = $path . DIRECTORY_SEPARATOR . $fileName;
        $variable = strtolower($className);

        // 处理表名,得到不为空字段
        $formatStr = ltrim(preg_replace("/([A-Z])/", "_\\1", $className), '_');
        $table = strtolower($formatStr) . 's';
        $tablePrefix = config('database.connections.mysql.prefix');
        $tableColumns = DB::select("desc $tablePrefix{$table}");
        $tableFields = [];
        foreach ($tableColumns as $tableColumn) {
            $tableFields[] = $tableColumn->Field;
        }
        $returnResult = [];
        foreach ($tableFields as $tableField) {
            if ($tableField == 'created_at' || $tableField == 'updated_at') {
                $returnResult["\t\t\t\t".'"'.$tableField.'"'] = "\$$variable->{$tableField}->format(\"Y-m-d H:i:s\")";
            } else {
                $returnResult["\t\t\t\t".'"'.$tableField.'"'] = "\$$variable->{$tableField}";
            }
        }
        $returnResult = var_export($returnResult, true);
        $returnResult = str_replace(["'", "\r"], '', $returnResult);
        $returnResult = "\t\t".substr($returnResult, 0, strrpos($returnResult, ',')) . "\n\t\t)";
        if (!is_file($file)) {
            $this->create($file, $namespace, $className, $returnResult, $variable);
            $this->comment("$fileName 创建成功");
        } else {
            unlink($file);
            $this->comment('文件已经存在');
        }
    }

    public function create($file, $namespace, $className, $returnResult, $variable)
    {

        $namespace = str_replace('/', '\\', $namespace);
        if (!$handle = fopen($file, 'w')) {
            $this->error('不能打开文件');
        }

        $content = <<<EOT
<?php \n
namespace App\\{$namespace};

use League\Fractal\TransformerAbstract;
\n
class {$className}Transformer extends TransformerAbstract
{
    public function transform(\App\Models\V1\\{$className} \$$variable)
    {
        return {$returnResult};
    }
}
EOT;
        ;
        if (!fwrite($handle, $content)) {
            $this->error('不能写入文件');
        }
        fclose($handle);
        return true;
    }
}