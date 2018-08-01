<?php
/**
 * 生成模型
 */

namespace App\Console\Commands;


use Illuminate\Console\Command;

class Store extends Command
{
    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'store:build {name} {path=Store} {page=false}';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = '创建存储服务';

    public function handle()
    {

        $args = $this->arguments();
        $model = ucfirst($args['name']);
        $page = $args['page'];
        $className = ucfirst($args['name']) .'Store';

        $namespace = $args['path'];
        $fileName =  $className. '.php';
        $path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $namespace;
        $file = $path . DIRECTORY_SEPARATOR . $fileName;

        // 分页处理
        $listContent = "getList ";
        if ($page == 'true') {
            $listContent .=  "(\$pageSize) \n\t{\n";
            $listContent .= "\t\treturn $model::paginate(\$pageSize);\n";
            $listContent .= "\t}\n";
        } else {
            $listContent .=  "() \n\t{\n";
            $listContent .= "\t\treturn $model::all();\n";
            $listContent .=  "\t}";
        }

        if (!is_file($file)) {
            $this->create($file, $namespace, $model, $listContent);
            $this->comment("$fileName 创建成功");
        } else {
            unlink($file);
            $this->comment('文件已经存在');
        }
    }

    public function create($file, $namespace, $model, $listContent)
    {
        $namespace = str_replace('/', '\\', $namespace);
        if (!$handle = fopen($file, 'w')) {
            $this->error('不能打开文件');
        }

        $content = <<<EOT
<?php

namespace App\\{$namespace};

use App\Models\V1\\{$model};

class {$model}Store extends BaseStore
{
    public static function $listContent
    
    public static function insert(\$input)
    {
        return {$model}::create(\$input);
    }
    
    public static function update(\$id, \$input)
    {
        return {$model}::where('id', \$id)->update(\$input);
    }
    
    public static function destroy(\$id)
    {
        return {$model}::destroy(\$id);
    }
    
    public static function show(\$id)
    {
        return {$model}::findOrFail(\$id);
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