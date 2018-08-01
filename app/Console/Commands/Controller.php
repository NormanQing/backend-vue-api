<?php

/**
 * 生成控制器
 */
 
 namespace App\console\Commands;
 
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
 
 class Controller extends Command
 {
	/**
	 * 控制台命令 signature 的名称
	 * @var string
	 */
	protected $signature = 'controller:build {name} {path=Http/Controllers/V1} {page=false}';
	
	/**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = '创建控制器';
	
	public function handle(){
		$args = $this->arguments();
		
		$className = ucfirst($args['name']);
		
		$page = $args['page'];
		
		$storeClassName = $className.'Store';
		$transformerClassName = $className.'Transformer';
		
		// 处理表名(className转换成表名)
		$formatStr = trim(preg_replace('/([A-Z])/','_\\1',$className), '_');
		$table = strtolower($formatStr).'s';
		$tablePrefix = config('database.connections.mysql.prefix');
		
		$tableColumns = DB::select("desc $tablePrefix{$table}");
		$requiredColumn = [];
		
		foreach ($tableColumns as $tableColumn) {
            if ($tableColumn->Null === 'NO' && is_null($tableColumn->Default) && $tableColumn->Extra != 'auto_increment') {
                $requiredColumn [] = $tableColumn->Field;
            }
        }
		
		// 表单验证参数处理
        $requiredColumn = var_export($requiredColumn, true);
        $pattern = range(0, 100);
        $pattern[] = "\r\n";
        $pattern[] = "\r";
        $pattern[] = "\n";
        $pattern[] = "=>";
        $pattern[] = " ";
        $pattern[] = "[";
        $pattern[] = ",]";
        $requiredColumn = '[' .str_replace($pattern, '', $requiredColumn) . ']';
		
		// 分页处理
		$listContent = "index ";
		if ('true' == $page) {
            $listContent .=  "(Request \$request) \n\t{\n";
            $listContent .= "\t\t\$pageSize = intval(\$request->input('pagesize'))?: \$this->getPageSize();\n";
            $listContent .= "\t\t\$obj = {$storeClassName}::getList(\$pageSize);\n";
            $listContent .= "\t\treturn \$this->response->paginator(\$obj, new $transformerClassName);\n";
            $listContent .= "\t}";
        } else {
            $listContent .=  "() {\n";
            $listContent .= "\t\t\$obj = $storeClassName::getList();\n";
            $listContent .= "\t\treturn \$this->response->collection(\$obj, new $transformerClassName);\n";
            $listContent .=  "\t}";
        }
		
		$namespace = $args['path']; // 命名空间
		$fileName =  $className. 'Controller.php'; //控制器文件名
		
		$path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $namespace; //控制文件路径
		$file = $path . DIRECTORY_SEPARATOR . $fileName;
		
		if (!is_file($file)) {
            $this->create($file, $namespace, $className, $requiredColumn, $listContent, $storeClassName, $transformerClassName);
            $this->comment("$fileName 创建成功");
        } else {
            unlink($file);
            $this->comment('文件已经存在,重新执行命令覆盖文件');
        }
	}
	
	 private function create($file, $namespace, $className, $requiredColumn, $listContent, $storeClassName, $transformerClassName)
    {

        $namespace = str_replace('/', '\\', $namespace);
        if (!$handle = fopen($file, 'w')) {
            $this->error('不能打开文件');
        }
        $content = <<<EOT
<?php \n
namespace App\\{$namespace};

use Dingo\Api\Http\Request;
use App\Common\Helper;
use App\Transformers\\$transformerClassName;
use App\Store\\$storeClassName;

class {$className}Controller extends BaseController
{

    public function $listContent

    public function store(Request \$request)
    {
        \$formData = {$requiredColumn};
        if (!\$request->filled(\$formData)) {
            return \$this->errorRequest('参数错误', \$request->all());
        }
        \$input = \$request->only(\$formData);
        \$obj = $storeClassName::insert(\$input);
        return \$this->created(\$obj, new {$className}Transformer);
    }

    public function update(Request \$request, \$id)
    {
        \$formData = {$requiredColumn};
        if (!\$request->filled(\$formData)) {
            return \$this->errorRequest('参数错误', \$request->all());
        }
        \$input = \$request->only(\$formData);
        $storeClassName::update(\$id, \$input);
        return \$this->updated();
    }
    
    public function destroy(\$id)
    {
        \$ids = Helper::strToArrayIds(\$id);
        $storeClassName::destroy(\$ids);
        return \$this->noContent();
    }
    

    public function show(\$id)
    {
        \$obj = $storeClassName::show(\$id);
        return \$this->item(\$obj, new $transformerClassName);
    }
    
}
EOT;

        if (!fwrite($handle, $content)) {
            $this->error('不能写入文件');
        }


        fclose($handle);
        return true;
    }
 }
 