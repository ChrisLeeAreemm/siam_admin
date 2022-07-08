<?php
namespace app\model;
use think\Collection;
use think\Model;
use think\db\BaseQuery as BaseQueryMapper;
use think\Paginator as PaginatorMapper;
use think\Collection as CollectionMapper;

/**
 * Class BaseModel
 * @package app\model
 * @orm-ide-start

 * @method static BaseQueryMapper getConnection()
 * @method static BaseQueryMapper name(string $name)
 * @method static string getName()
 * @method static BaseQueryMapper getConfig(string $name = '')
 * @method static BaseQueryMapper getTable(string $name = '')
 * @method static BaseQueryMapper setFieldType(array $type)
 * @method static string getLastSql()
 * @method static int getNumRows()
 * @method static BaseQueryMapper getLastInsID(string $sequence = '')
 * @method static BaseQueryMapper value(string $field,$default = '')
 * @method static array column($field,string $key = '')
 * @method static BaseQueryMapper union($union,bool $all = '')
 * @method static BaseQueryMapper unionAll($union)
 * @method static BaseQueryMapper field($field)
 * @method static BaseQueryMapper withoutField($field)
 * @method static BaseQueryMapper tableField($field,string $tableName,string $prefix = '',string $alias = '')
 * @method static BaseQueryMapper data(array $data)
 * @method static BaseQueryMapper removeOption(string $option = '')
 * @method static BaseQueryMapper limit(int $offset,int $length = '')
 * @method static BaseQueryMapper page(int $page,int $listRows = '')
 * @method static BaseQueryMapper table($table)
 * @method static BaseQueryMapper order($field,string $order = '')
 * @method static PaginatorMapper paginate($listRows = '',$simple = '')
 * @method static PaginatorMapper paginateX($listRows = '',string $key = '',string $sort = '')
 * @method static array more(int $limit,$lastId = '',string $key = '',string $sort = '')
 * @method static BaseQueryMapper cache($key = 1,$expire = '',$tag = '')
 * @method static BaseQueryMapper lock($lock = '')
 * @method static BaseQueryMapper alias($alias)
 * @method static BaseQueryMapper master(bool $readMaster = 1)
 * @method static BaseQueryMapper strict(bool $strict = 1)
 * @method static BaseQueryMapper sequence(string $sequence = '')
 * @method static BaseQueryMapper json(array $json = [],bool $assoc = '')
 * @method static BaseQueryMapper pk($pk)
 * @method static BaseQueryMapper getOptions(string $name = '')
 * @method static BaseQueryMapper setOption(string $option,$value)
 * @method static BaseQueryMapper via(string $via = '')
 * @method static BaseQueryMapper save(array $data = [],bool $forceInsert = '')
 * @method static BaseQueryMapper insert(array $data = [],bool $getLastInsID = '')
 * @method static BaseQueryMapper insertGetId(array $data)
 * @method static int insertAll(array $dataSet = [],int $limit = '')
 * @method static int selectInsert(array $fields,string $table)
 * @method static int update(array $data = [],$where = [], array $allowField = [], string $suffix = '')
 * @method static int delete($data = '')
 * @method static CollectionMapper select($data = '')
 * @method static static find($data = '')
 * @method static array parseOptions()
 * @method static bool parseUpdateData($data)
 * @method static void parsePkWhere($data)
 * @method static BaseQueryMapper timeRule(array $rule)
 * @method static BaseQueryMapper whereTime(string $field,string $op,$range = '',string $logic = 'AND')
 * @method static BaseQueryMapper whereTimeInterval(string $field,string $start,string $interval = 'day',int $step = 1,string $logic = 'AND')
 * @method static BaseQueryMapper whereMonth(string $field,string $month = 'this month',int $step = 1,string $logic = 'AND')
 * @method static BaseQueryMapper whereWeek(string $field,string $week = 'this week',int $step = 1,string $logic = 'AND')
 * @method static BaseQueryMapper whereYear(string $field,string $year = 'this year',int $step = 1,string $logic = 'AND')
 * @method static BaseQueryMapper whereDay(string $field,string $day = 'today',int $step = 1,string $logic = 'AND')
 * @method static BaseQueryMapper whereBetweenTime(string $field,$startTime,$endTime,string $logic = 'AND')
 * @method static BaseQueryMapper whereNotBetweenTime(string $field,$startTime,$endTime)
 * @method static BaseQueryMapper whereBetweenTimeField(string $startField,string $endField)
 * @method static BaseQueryMapper whereNotBetweenTimeField(string $startField,string $endField)
 * @method static int count(string $field = '*')
 * @method static float sum($field)
 * @method static BaseQueryMapper min($field,bool $force = 1)
 * @method static BaseQueryMapper max($field,bool $force = 1)
 * @method static float avg($field)
 * @method static BaseQueryMapper model(think\Model $model)
 * @method static BaseQueryMapper getModel()
 * @method static BaseQueryMapper hidden(array $hidden)
 * @method static BaseQueryMapper visible(array $visible)
 * @method static BaseQueryMapper append(array $append)
 * @method static BaseQueryMapper scope($scope,$args)
 * @method static BaseQueryMapper relation(array $relation)
 * @method static BaseQueryMapper withSearch($fields,$data = [],string $prefix = '')
 * @method static BaseQueryMapper withAttr($name,callable $callback = '')
 * @method static BaseQueryMapper with($with)
 * @method static BaseQueryMapper withJoin($with,string $joinType = '')
 * @method static BaseQueryMapper withCache($relation = 1,$key = 1,$expire = '',string $tag = '')
 * @method static BaseQueryMapper withCount($relation,bool $subQuery = 1)
 * @method static BaseQueryMapper withSum($relation,string $field,bool $subQuery = 1)
 * @method static BaseQueryMapper withMax($relation,string $field,bool $subQuery = 1)
 * @method static BaseQueryMapper withMin($relation,string $field,bool $subQuery = 1)
 * @method static BaseQueryMapper withAvg($relation,string $field,bool $subQuery = 1)
 * @method static BaseQueryMapper has(string $relation,string $operator = '>=',int $count = 1,string $id = '*',string $joinType = '')
 * @method static BaseQueryMapper hasWhere(string $relation,$where = [],string $fields = '*',string $joinType = '')
 * @method static BaseQueryMapper allowEmpty(bool $allowEmpty = 1)
 * @method static BaseQueryMapper failException(bool $fail = 1)
 * @method static BaseQueryMapper findOrEmpty($data = '')
 * @method static BaseQueryMapper selectOrFail($data = '')
 * @method static BaseQueryMapper findOrFail($data = '')
 * @method static BaseQueryMapper transactionXa($callback,array $dbs = [])
 * @method static BaseQueryMapper transaction(callable $callback)
 * @method static void startTrans()
 * @method static void commit()
 * @method static void rollback()
 * @method static BaseQueryMapper where($field,$op = '',$condition = '')
 * @method static BaseQueryMapper whereOr($field,$op = '',$condition = '')
 * @method static BaseQueryMapper whereXor($field,$op = '',$condition = '')
 * @method static BaseQueryMapper whereNull(string $field,string $logic = 'AND')
 * @method static BaseQueryMapper whereNotNull(string $field,string $logic = 'AND')
 * @method static BaseQueryMapper whereExists($condition,string $logic = 'AND')
 * @method static BaseQueryMapper whereNotExists($condition,string $logic = 'AND')
 * @method static BaseQueryMapper whereIn(string $field,$condition,string $logic = 'AND')
 * @method static BaseQueryMapper whereNotIn(string $field,$condition,string $logic = 'AND')
 * @method static BaseQueryMapper whereLike(string $field,$condition,string $logic = 'AND')
 * @method static BaseQueryMapper whereNotLike(string $field,$condition,string $logic = 'AND')
 * @method static BaseQueryMapper whereBetween(string $field,$condition,string $logic = 'AND')
 * @method static BaseQueryMapper whereNotBetween(string $field,$condition,string $logic = 'AND')
 * @method static BaseQueryMapper whereFindInSet(string $field,$condition,string $logic = 'AND')
 * @method static BaseQueryMapper whereColumn(string $field1,string $operator,string $field2 = '',string $logic = 'AND')
 * @method static BaseQueryMapper useSoftDelete(string $field,$condition = '')
 * @method static BaseQueryMapper whereExp(string $field,string $where,array $bind = [],string $logic = 'AND')
 * @method static BaseQueryMapper whereFieldRaw(string $field,$op,$condition = '',string $logic = 'AND')
 * @method static BaseQueryMapper whereRaw(string $where,array $bind = [],string $logic = 'AND')
 * @method static BaseQueryMapper whereOrRaw(string $where,array $bind = [])
 * @method static BaseQueryMapper removeWhereField(string $field,string $logic = 'AND')
 * @method static BaseQueryMapper when($condition,$query,$otherwise = '')

 * @orm-ide-end
 *
 */
class BaseModel extends Model
{
    protected $autoWriteTimestamp = false;
    
    
    private static $requestCache;
    public static function getRequestCache($data)
    {
        $key = md5(self::class.json_encode($data,256));
        if (!empty(self::$requestCache[$key])) {
            return self::$requestCache[$key];
        }

        self::$requestCache[$key] = self::get($data);

        return self::$requestCache[$key];
    }
}
