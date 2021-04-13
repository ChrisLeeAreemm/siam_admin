<?php

namespace app\plugs\curd\controller;

use think\facade\Db;
use think\helper\Str;

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
        $this->tableName      = request()->param('table_name');
        $this->table          = $this->prefix . request()->param('table_name');
        $this->modelPath      = app_path() . 'model\\';
        $this->controllerPath = app_path() . 'controller\admin\\';
    }
    /**
     *  创建模型和控制器
     * @return \think\response\Json
     */
    public function create_curd()
    {
        $putModelFile         = $this->parseField()->putModelFile();
        $putControllerFile    = $this->parseField()->putControllerFile();
        if ($putModelFile != true || $putControllerFile != true) {
            return json(['code' => '300', 'data' => '', 'msg' => '生成失败']);
        }
        return json(['code' => '200', 'data' => '', 'msg' => '生成成功']);
    }
    
    public function update_notes()
    {
        
        $file = $this->modelPath . Str::title($this->tableName) .'Model.php';
        if (!is_file($file)){
            return json(['code' => '300', 'data' => '', 'msg' => '文件不存在']);
        }
        $content = file_get_contents($file);
        $this->parseField();
        $content = preg_replace('/####.*####/',$this->tableNotes, $content);
        $content = file_put_contents($file,$content);
        if (!$content){
            return json(['code' => '300', 'data' => '', 'msg' => '更新失败']);
        }
        return json(['code' => '200', 'data' => '', 'msg' => '更新成功']);
    }
    /**
     * 解析字段，注释
     * @return $this
     */
    private function parseField()
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
        $this->tableNotes = $string;
        return $this;
    }
    
    private function putModelFile()
    {
        $modelName = Str::studly($this->tableName);
        
        $template = $this->modelFileTemplet();
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
    
    private function putControllerFile()
    {
        $modelName = Str::studly($this->tableName);
        
        $template = $this->controllerFileTemplate();
        $template = str_replace("-modelName-", $modelName.'Model', $template);
        $template = str_replace("-controllerName-", $modelName, $template);
        $template = str_replace("-pk-", $this->pk, $template);
        $template = str_replace("-conditionString-", '', $template);
        
        $fullPath = $this->controllerPath . "Admin$modelName" . "Controller.php";
        if (file_exists($fullPath)) {
            return false;
        }
        file_put_contents($fullPath, $template);
        return true;
    }
    
    private function modelFileTemplet()
    {
        $path = dirname(__FILE__) . "\model_file";
        return file_get_contents($path);
    }
    
    private function controllerFileTemplate()
    {
        $path = dirname(__FILE__) . "\controller_file";
        return file_get_contents($path);
    }
    
    public function create_html()
    {
        // TODO 创建页面
    }
}