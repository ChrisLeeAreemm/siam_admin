<?php

namespace app\plugs\curd\controller;

use think\facade\Db;
use think\helper\Str;
use think\response\Json;
use think\Route;

class PlugsCurdController
{
    private $modelPath;
    private $controllerPath;
    private $tableName;
    private $table;
    private $tableInfo;
    private $pk;
    private $tableNotes;
    private $prefix;
    
    public function __construct()
    {
        $this->prefix         = env('database.prefix', '');
        $this->tableName      = request()->param('table_name', '');
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
        // 写入模型文件
        $putModelFile = $this->parseField()->putModelFile();
        // 写入控制器文件
        $putControllerFile = $this->parseField()->putControllerFile();
        if ($putModelFile != true || $putControllerFile != true) {
            return json(['code' => '300', 'data' => '', 'msg' => '生成失败']);
        }
        return json(['code' => '200', 'data' => '', 'msg' => '生成成功']);
    }
    
    /**
     * 更新所有数据模型字段
     */
    public function update_notes()
    {
        //查询所有表
        $tables = Db::query('SHOW TABLES');
        foreach ($tables as $key => $value) {
            //设置表名
            $this->table = $value['Tables_in_siamadmin'];
            //去掉前缀
            $tableName = str_replace($this->prefix, '', $value['Tables_in_siamadmin']);
            //获取文件
            $file = $this->modelPath . Str::title($tableName) . 'Model.php';
            //不存在不执行
            if (!is_file($file)) {
                continue;
            }
            //获取文件
            $content = file_get_contents($file);
            //获取最新表详情
            $this->parseField();
            //更新文件
            $content = preg_replace('/#start.*#end/sm', $this->tableNotes, $content);
            file_put_contents($file, $content);
        }
        return true;
        
        
    }
    
    public function create_html()
    {
        $ListsHtml = $this->putListsFile();
        $ActionHtml = $this->putActionFile();
        return json(['code' => '200', 'data' => ['lists'=>$ListsHtml,'action'=>$ActionHtml], 'msg' => '生成成功']);
        
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
        
        $string           .= "\n */";
        $string           .= "\n #end";
        $this->tableNotes = $string;
        return $this;
    }
    
    private function putModelFile(): bool
    {
        $modelName = Str::studly($this->tableName);
        
        $template = $this->modelFileTemplate();
        $template = str_replace("-modelName-", $modelName, $template);
        $template = str_replace("-notesString-", $this->tableNotes ?? '', $template);
        $template = str_replace("-tableName-", $this->tableName ?? '', $template);
        $template = str_replace("-pk-", $this->pk ?? '', $template);
        
        $fullPath = $this->modelPath . "$modelName" . "Model.php";
        if (file_exists($fullPath)) {
            return false;
        }
        file_put_contents($fullPath, $template);
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
            return false;
        }
        file_put_contents($fullPath, $template);
        return true;
    }
    
    private function putListsFile()
    {
        $fieldString = [];
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
                    <input type="text" name="{$value['name']}" lay-verify="" autocomplete="off" placeholder="请输入标题" class="layui-input">
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
        $template = str_replace("--add_url--", "admin/$this->tableName/add", $template);
        $template = str_replace("--edit_url--", "admin/$this->tableName/edit", $template);
        $template = str_replace("--get_one_url--", "admin/$this->tableName/get_one", $template);
        $template = str_replace("--get_one_val--", $get_one_val, $template);
        return $template;
    }
    
    private function modelFileTemplate()
    {
        $path = dirname(__FILE__) . "\model_file";
        return file_get_contents($path);
    }
    
    private function controllerFileTemplate()
    {
        $path = dirname(__FILE__) . "\controller_file";
        return file_get_contents($path);
    }
    
    private function listsFileTemplet()
    {
        $path = dirname(__FILE__) . "\lists.html";
        return file_get_contents($path);
    }
    private function actionFileTemplet()
    {
        $path = dirname(__FILE__) . "\action.html";
        return file_get_contents($path);
    }
    
    
}