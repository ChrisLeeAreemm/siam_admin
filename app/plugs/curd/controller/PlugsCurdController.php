<?php

namespace app\plugs\curd\controller;

use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;
use think\db\BaseQuery;
use think\Exception;
use think\facade\Db;
use think\helper\Str;
use think\response\Json;

class PlugsCurdController extends PlugsBaseController
{
    private $modelPath;
    private $controllerPath;
    private $tableName;
    private $table;
    private $tableInfo;
    private $pk;
    private $tableNotes;
    private $prefix;
    private $relevance;

    public function __construct()
    {
        $this->prefix         = config('database.connections.mysql.prefix');
        $this->tableName      = Str::lower(request()->param('table_name', ''));
        $this->table          = $this->prefix . $this->tableName;
        $this->modelPath      = app_path() . 'model\\';
        $this->controllerPath = app_path() . 'controller\admin\\';
    }

    /**
     *  创建模型和控制器
     * @return Json
     */
    public function create_curd(): Json
    {
        try {
            // 写入模型文件
            $this->parseField()->putModelFile();
            // 写入控制器文件
            $this->parseField()->putControllerFile();
        }catch (\Exception $exception){
            return $this->send($exception->getCode(), [], $exception->getMessage());
        }
        return $this->send(ErrorCode::SUCCESS, [], '生成成功');
    }

    public function get_model()
    {
        try {
            $model_content = $this->parseField()->putModelFile();
        } catch (Exception $e) {
            return $this->send($e->getCode(), [], $e->getMessage());
        }

        return $this->send(ErrorCode::SUCCESS, [
            'model_content' => $model_content
        ], '生成成功');
    }

    /**
     * 更新所有数据模型字段
     */
    public function update_notes()
    {
        //查询所有表
        try {
            $tables = Db::query('SHOW TABLES');
        }catch (\Exception $exception){
            return $this->send(ErrorCode::DB_EXCEPTION, [$exception->getLine()], $exception->getMessage());
        }
        foreach ($tables as $value) {
            $database = config('database.connections.mysql.database');

            //设置表名
            $this->table = $value["Tables_in_$database"];
            //去掉前缀
            $tableName = str_replace($this->prefix, '', $value["Tables_in_$database"]);
            //获取文件
            $file = $this->modelPath . Str::title($tableName) . 'Model.php';
            if (!is_file($file)){
                continue;
            }

            //解析类
            $class = 'app\model\\'.Str::title($tableName). 'Model';
            $Model = new $class();
            if (!$Model){
                return $this->send(ErrorCode::FILE_NOT_EXIST, [], '类不存在');
            }
            $ref = new \ReflectionClass($Model);
            $methods = $ref->getMethods();
            $relevance =[];
            $up_file = [];
            foreach ($methods as $val){
                if ($val->class !== $class){
                    continue;
                }
                //判断是否关联方法
                if (Str::contains($val->getDocComment(),'@relevance') != true){
                    continue;
                }
                //获取方法代码
                $method_info = \ReflectionMethod::export($Model, $val->getName(), true);
                $str = strstr($method_info,Str::title($tableName) . 'Model.php');
                $str = preg_replace("/[a-zA-Z\/.]/",'',$str);
                $str = preg_replace('# #', '', $str);
                $line_arr = explode("-",$str);
                //打开文件
                $modelFile = file_get_contents($file);
                //转换数组
                $code_arr = explode("\n",$modelFile);
                //关联方法名
                $methodName = $val->getName();
                //获取方法内代码中的关联类
                for ($i=$line_arr[0];$i<$line_arr[1];$i++){
                    $preg= '/\([\s\S]*?:/i';
                    preg_match($preg,$code_arr[$i],$res);
                    $res = str_replace('(','',$res);
                    $res = str_replace(':','',$res);
                    if (!empty($res)){
                        //关联类名
                        $relevance[$methodName] = $res[0];
                        $up_file[] = Str::title($tableName). 'Model';
                    }
                }
            }
            $this->relevance = $relevance;

            //获取文件
            $content = file_get_contents($file);
            //获取最新表详情
            $this->parseField();
            //更新文件
            $content = preg_replace('/#start.*#end/sm', $this->tableNotes, $content);

            $res = file_put_contents($file, $content);
        }
        if (!$up_file){
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '更新失败');

        }
        return $this->send(ErrorCode::SUCCESS, [], '更新成功');

    }

    public function create_html()
    {
        try {
            $ListsHtml = $this->putListsFile();
            $ActionHtml = $this->putActionFile();
        }catch (\Exception $exception){
            return json(['code' => '300', 'data' =>'', 'msg' => $exception->getMessage()]);
        }
        return $this->send(ErrorCode::SUCCESS, ['lists'=>$ListsHtml,'action'=>$ActionHtml], '生成成功');

    }

    public function update_thinkphp_note()
    {

        $ref = new \ReflectionClass(BaseQuery::class);
        $method_list = [];
        $namespace_map = [];
        foreach ($ref->getMethods() as $method)
        {
            // 过滤一些无用的
            $black = [
                '__construct',
                '__call',
                'newQuery',
            ];
            if (in_array($method->getName(), $black)) continue;

            if ($method->getModifiers() == \ReflectionMethod::IS_PUBLIC){
                $param_array = [];
                foreach ($method->getParameters() as $param){
                    try {
                        $default_value = $param->getDefaultValue();
                    } catch (\ReflectionException $e) {
                        $default_value = 'NONE---';
                    }
                    $param_array[] = [
                        'name'     => $param->getName(),
                        'position' => $param->getPosition(),
                        'default'  => $default_value,
                        'typeHint' => (string) $param->getType(),
                    ];
                }

                $method_list[] = [
                    'name'        => $method->getName(),
                    'param'       => $param_array,
                    'return_type' => $method->getReturnType(),
                ];
            }
        }
        // 解析成注释
        $str = '';
        foreach ($method_list as $method){
            $param_temp_array = [];
            foreach ($method['param'] as $param){
                $temp_one_param = [];
                if (!!$param['typeHint']) {
                    $temp_one_param[] = $param['typeHint'];
                }
                $temp_one_param[] = "$".$param['name'];
                if ($param['default'] !== "NONE---") {
                    if ($param['default'] == '') $param['default'] = "''";
                    if (is_string($param['default']) && !!$param['default'] && $param['default'] !== "''") {
                        $param['default'] = "'{$param['default']}'";
                    }
                    if ($param['default'] === []) $param['default'] = "[]";

                    $temp_one_param[] = "=";
                    $temp_one_param[] = $param['default'];
                }
                $param_temp_array[] = implode(' ', $temp_one_param);

            }
            $param_str = implode(',', $param_temp_array);

            $return_type_str = (string)$method['return_type'];
            if (!$return_type_str) {
                $return_type_str = BaseQuery::class;
            }
            // 如果return_type是命名空间，则需要先use 然后这里只显示类名
            if (class_exists($return_type_str)){
                if (!isset($namespace_map[$return_type_str])) {
                    $namespace_map[$return_type_str] = class_basename($return_type_str). "Mapper";
                }
                $return_type_str = $namespace_map[$return_type_str];
            }

            $str.= " * @method static {$return_type_str} {$method['name']}({$param_str}) \r\n";
        }
        echo $str;
        // TODO 还需要把命名空间map 生成一个use的
        dump($namespace_map);
        die;
    }

    /**
     * 解析字段，注释
     * @return $this
     */
    private function parseField(): PlugsCurdController
    {
        $fullTable = $this->table;
        // 获取数据表字段详细信息
        $this->tableInfo = Db::table($fullTable)->getFields($fullTable);

        // 找出主键
        foreach ($this->tableInfo as $key => $value) {
            if ($value['primary'] === true) {
                $this->pk = $key;
                break;
            }
        }

        $string = <<<t
#start
/**
 * {$fullTable}
t;

        foreach ($this->tableInfo as $key => $value) {
            $comment = $value['comment'] ?? '';
            $string  .= <<<s
\n * @property mixed {$value['name']}\t{$comment}
s;
        }
        if (!empty($this->relevance)){
            foreach ($this->relevance as $key =>$value){
                $string .= <<<EOF
\n * @property $value $key\t
EOF;

            }
        }


        $string           .= "\n */";
        $string           .= "\n #end";
        $this->tableNotes = $string;
        return $this;
    }

    private function putModelFile()
    {
        $modelName = Str::studly($this->tableName);

        $template = $this->modelFileTemplate();
        $template = str_replace("-modelName-", $modelName, $template);
        $template = str_replace("-notesString-", $this->tableNotes ?? '', $template);
        $template = str_replace("-tableName-", $this->tableName ?? '', $template);
        $template = str_replace("-pk-", $this->pk ?? '', $template);
        // 是否有create_time和update_time
        $auto_write_timestamp = '';
        foreach ($this->tableInfo as $value) {
            if ($value['name']=='create_time' || $value['name'] == 'update_time'){
                $type = $value['type'];
                $auto_write_timestamp = "\n    protected \$autoWriteTimestamp ='$type';";
                break;
            }
        }
        if (!$auto_write_timestamp){
            $all_field = array_column($this->tableInfo, 'name');
            if (!in_array( 'update_time', $all_field)){
                $auto_write_timestamp .= "\n    protected \$updateTime = false;";
            }
        }
        $template = str_replace("--autoWriteTimeStamp--", $auto_write_timestamp, $template);

        if (input('return_html') == '1'){
            return $template;
        }

        $fullPath = $this->modelPath . "$modelName" . "Model.php";
        if (file_exists($fullPath)) {
            throw new Exception('模型已存在',ErrorCode::FILE_EXIST);
        }
        $create = file_put_contents($fullPath, $template);
        if ($create == false){
            throw new Exception('模型写入失败,请检查文件写入问题',ErrorCode::FILE_WRITE_FAIL);
        }
        return true;
    }

    private function putControllerFile(): bool
    {
        $modelName = Str::studly($this->tableName);

        $template  = $this->controllerFileTemplate();
        $template  = str_replace("-modelName-", $modelName . 'Model', $template);
        $template  = str_replace("-controllerName-", $modelName, $template);
        $template  = str_replace("-pk-", $this->pk, $template);
        $template  = str_replace("-conditionString-", '', $template);
        $file_name = "Admin$modelName" . "Controller.php";
        $fullPath  = $this->controllerPath . $file_name;
        if (file_exists($fullPath)) {
            throw new Exception('控制器已存在',ErrorCode::FILE_EXIST);
        }
        $create = file_put_contents($fullPath, $template);
        if ($create == false){
            throw new Exception('控制器写入失败,请检查文件写入问题',ErrorCode::FILE_WRITE_FAIL);
        }
        return true;
    }

    private function putListsFile()
    {
        $fieldString = [];
        $fullTable   = $this->table;
        // 获取数据表字段详细信息】
        $this->tableInfo = Db::table($fullTable)->getFields($fullTable);

        // 找出主键
        foreach ($this->tableInfo as $key => $value) {
            if ($value['primary'] === true) {
                $this->pk = $key;
                break;
            }
        }
        // 表格赋值
        foreach ($this->tableInfo as $value) {
            $field         = $value['name'];
            $title         = !empty($value['comment']) ? $value['comment'] : $field;
            $fieldString[] = " {field: '{$field}', title: '{$title}'}";
        }

        $fieldString = implode($fieldString, ',');

        $template = $this->listsFileTemplet();
        $template = str_replace("-table-", $this->tableName, $template);
        $template = str_replace("-pk-", $this->pk, $template);
        $template = str_replace("-fieldString-", $fieldString, $template);
        return $template;
    }
    private function putActionFile()
    {

        $fullTable   = $this->table;
        // 获取数据表字段详细信息
        $this->tableInfo = Db::table($fullTable)->getFields($fullTable);
        // 找出主键
        foreach ($this->tableInfo as $key => $value) {
            if ($value['primary'] === true) {
                $this->pk = $key;
                break;
            }
        }
        $str = '';
        $get_one_val = '';
        // 赋值
        foreach ($this->tableInfo as $value) {
            $str .= <<<EOF
            <div class="layui-form-item">
                <label class="layui-form-label">{$value['name']}</label>
                <div class="layui-input-block">
                    <input type="text" name="{$value['name']}" lay-verify="" autocomplete="off" placeholder="请输入" class="layui-input">
                </div>
            </div>
\n
EOF;
            $get_one_val .= <<<EOF
                "{$value['name']}" :res.data.lists.{$value['name']},\n
EOF;

        }


        $template = $this->actionFileTemplet();
        $template = str_replace("--demo--", $str, $template);
        $template = str_replace("--pk--", $this->pk, $template);
        $template = str_replace("--add_url--", "/admin/$this->tableName/add", $template);
        $template = str_replace("--edit_url--", "/admin/$this->tableName/edit", $template);
        $template = str_replace("--get_one_url--", "/admin/$this->tableName/get_one", $template);
        $template = str_replace("--get_one_val--", $get_one_val, $template);
        return $template;
    }

    private function modelFileTemplate()
    {
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . "model_file";
        return file_get_contents($path);
    }

    private function controllerFileTemplate()
    {
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . "controller_file";
        return file_get_contents($path);
    }

    private function listsFileTemplet()
    {
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . "lists.html";
        return file_get_contents($path);
    }
    private function actionFileTemplet()
    {
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . "action.html";
        return file_get_contents($path);
    }


    function auth()
    {

    }
}