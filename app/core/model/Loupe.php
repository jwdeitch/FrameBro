<?php
/**
 * Created By: Jon Garcia
 * Date: 1/13/16
 */

namespace App\Core\Model;

use App\Core\Api\ModelInterface;
use App\Core\Database;

/**
 * Class Loupe
 * @package App\Core\Model
 */
class Loupe extends Database implements ModelInterface
{
    public $attributes;
    public $count;
    public $lastId;

    protected $model;
    protected $primaryKey = 'id';
    protected $table;

    protected $timeCreated = 'created_at';
    protected $timeUpdated = 'updated_at';

    protected $created_at;
    protected $updated_at;

    protected $customTime = false;

    protected $findAll = false;

    private $query = array (
        'groupBy'       => null,
        'group_concat'  => array(),
        'concat'        => null,
        'queries'       => array(),
        'limit'         => null,
        'offset'        => null,
        'bindings'      => array()
    );

    private $distinctValues = false;

    private $foreignKey;

    private $onlySelect;

    private $relatedModel;

    private $localKey;
    private $pivotTable;

    protected $SQL;
    protected $SQLError;

    /**
     * Loupe constructor.
     */
    public function __construct()
    {
        if (is_null($this->table)) {
            $this->table = $this->getTableName();
        }

        $this->model = self::getSelfModelName();
        $this->attributes = new Attributes();

        parent::__construct();
    }

    /**
     * @return string
     */
    public function getSelfModelName()
    {
        $namespace = strtolower(get_class($this));
        $pos = strrpos($namespace, '\\');
        $name = $pos === false ? $namespace : substr($namespace, $pos + 1);
        return $name;
    }


    /**
     * @param null $id
     * @return $this
     */
    public function find( $id = NULL )
    {
        if (!is_null($id)) {
            $comparison = '=';
            if (is_array($id)) {
                $comparison = 'IN';
                $escapeValues = array_map(array( $this, 'quote' ), $id);
                $value = '('.join(',', $escapeValues).')';
                $bindings = NULL;
            } elseif (is_numeric($id)) {
                $value = ':id';
                $bindings = [ ':id' => $id ];
            }

            $this->query['queries'][]['primaryKey'] = "WHERE $this->primaryKey $comparison $value";
            $this->query['bindings'] = $bindings;
        } else {
            $this->findAll = true;
        }

        return $this;
    }

    /**
     * Sets a select distinct query
     * @return $this
     */
    public function distinct() {
        $this->distinctValues = true;
        return $this;
    }

    /**
     * @param $field
     * @param $binding
     * @param string $comparison
     * @return $this
     */
    public function where( $field, $binding, $comparison = "=" )
    {
        $append = range('a', 'z');
        $clause = (!isset($this->query['queries'][0])) ? 'WHERE' : 'AND';
        $this->query['queries'][]['where'] = "$clause $field $comparison ";
        $keys = array_keys($this->query['queries']);
        $this->query['queries'][end($keys)]['where'] .= ":bind_{$append[end($keys)]}";
        $this->query['bindings'][":bind_{$append[end($keys)]}"] = $binding;
        return $this;
    }

    /**
     * @param $field
     * @param $binding
     * @param string $comparison
     * @return $this
     */
    public function orWhere( $field, $binding, $comparison = "=" )
    {
        $append = range('a', 'z');

        $this->query['queries'][]['where'] = "OR $field $comparison ";
        $keys = array_keys($this->query['queries']);
        $this->query['queries'][end($keys)]['where'] .= ":bind_{$append[end($keys)]}";
        $this->query['bindings'][":bind_{$append[end($keys)]}"] = $binding;

        return $this;
    }

    /**
     * Performs join
     * @param $leftTable
     * @param $leftField
     * @param $rightField
     * @param string $joinType
     * @return $this
     */
    public function join($leftField, $rightField, $rightTable = null, $joinType = 'INNER JOIN' ) {

        $dottedLeftField = strpos($leftField, '.');
        $dottedRightField = strpos($rightField, '.');

        if ( !$dottedRightField && is_null($rightTable)) {
            throw new \Exception('You must specify a table for at least the right field when $rightTable is null. i.e. "rightTable.rightField"');
        }

        $leftColumn = $dottedLeftField ? $leftField : $this->table . '.' . $leftField;
        $rightColumn = is_null($rightTable) ? $rightField : $rightTable . '.' . $rightField;

        $rightTable = !is_null($rightTable) ? $rightTable : explode('.', $rightField)[0] ;

        $this->query['joins'][] = "$joinType $rightTable ON $leftColumn = $rightColumn";

        return $this;
    }

    /**
     * performs left join
     * @param $rightTable
     * @param $leftField
     * @param $rightField
     * @return $this|Loupe
     */
    public function leftJoin($leftField, $rightField, $rightTable = null ) {
        return $this->join( $leftField, $rightField, $rightTable, 'LEFT JOIN' );
    }

    /**
     * performs right join
     * @param $rightTable
     * @param $leftField
     * @param $rightField
     * @return $this|Loupe
     */
    public function rightJoin( $leftField, $rightField, $rightTable = null ) {
        return $this->join( $leftField, $rightField, $rightTable, 'RIGHT JOIN' );
    }

    /**
     * @param null $column
     * @return mixed
     */
    public function count($column = null )
    {
        if ( is_null($column)) {
            $column = $this->primaryKey;
        }
        $this->get( [ "COUNT($column)" ] );

        $count = reset( $this->attributes );
        $this->attributes = new Attributes();
        $this->count = $count;
        return intval($count);
    }

    /**
     * @param null $attributes
     * @return bool
     * @throws \Exception
     */
    public function save($attributes = NULL)
    {
        $date = new \DateTime();
        $this->created_at = $date->format("Y-m-d H:i:s");
        $this->updated_at = $date->format("Y-m-d H:i:s");

        if (is_null($attributes)) {
            $attributes = $this->attributes;
        }

        if (empty((array) $attributes)) {
            throw new \Exception('No data to save');
        }

        $append = range('a', 'z');

        if ( $this->customTime === false ) {
            $timeCreated = $this->timeCreated;
            $timeUpdated = $this->timeUpdated;

            $attributes->$timeCreated = $this->created_at;
            $attributes->$timeUpdated = $this->updated_at;
        }

        $primaryKeyName = $this->primaryKey;

        if (isset($attributes->$primaryKeyName)) {
            $primaryKey = $attributes->$primaryKeyName;
            unset($attributes->$primaryKeyName);
            unset($attributes->$timeCreated);
        }

        foreach($attributes as $property => $value ) {
            $propertyNames[] = $property;
            $propertyValues[] = $value;
        }

        foreach($propertyValues as $key => $value) {
            $binds['columns'][$propertyNames[$key]] = ":bind_{$append[$key]}";
            $binds['values'][":bind_{$append[$key]}"] = $value;
        }

        $columns = implode(', ', $propertyNames);
        $bindsInsert = implode(', ', $binds['columns']);

        if ( isset($primaryKey) ) {
            $bindsUpdate = '';
            foreach ($binds['columns'] as $col => $bind) {
                    $bindsUpdate .= $col . ' = ' . $bind;
                if (end($binds['columns']) !== $bind) {
                    $bindsUpdate .=  ', ';
                }
            }

            $action =  $this->prepare("UPDATE $this->table SET $bindsUpdate WHERE $this->primaryKey = $primaryKey");
            //adding primary id back to $this.
            $this->attributes->{$this->primaryKey} = $primaryKey;
        }
        else {
            $action =  $this->prepare("INSERT INTO $this->table ($columns) VALUES ( $bindsInsert )");
        }

        $this->SQL = $action;
        if ($action->execute($binds['values'])) {

            $this->count = $action->rowCount();
            $this->lastId = $this->lastInsertId();
            return true;
        } else {
            $this->SQLError = $action->errorInfo();
            return false;
        }
    }

    /**
     * @throws \Exception
     */
    public function delete()
    {
        if (!isset($this->attributes->{$this->primaryKey})) {
            Throw new \Exception('No object has been loaded');
        }
        $key = $this->attributes->{$this->primaryKey};
        $delete = $this->prepare("DELETE FROM $this->table WHERE $this->primaryKey = :bind");
        $this->SQL = $delete;

        if ( $delete->execute(array(':bind' => $key ))) {
            $this->count = $delete->rowCount();
            return true;
        } else {
            $this->SQLError = $delete->errorInfo();
            return false;
        }
    }


    /**
     * regardless of the chained position of this method,
     * it will always be appended to the last part of the statement unless there's an offset
     * @param $int Integer
     * @return $this Object
     */
    public function limit($int)
    {
        $this->query['limit'] = "LIMIT $int";
        return $this;
    }

    /**
     * regardless of the chained position of this method,
     * it will always be appended to the last part of the statement
     * @param $int Integer
     * @return $this Object
     */
    public function offset($int)
    {
        $this->query['offset'] = "OFFSET $int";
        return $this;
    }

    /**
     * @param $column Integer
     * @return $this Object
     */
    public function order($column)
    {
        $this->query['queries'][]['order'] = "ORDER BY $column";
        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function groupBy($field)
    {
        $this->query['groupBy'] = "GROUP BY $field";
        return $this;
    }


    /**
     * @param array $fields
     * @return $this
     */
    public function groupConcat(array $fields)
    {
        $i = 0;
        foreach( $fields as $field => $display_as ) {
            $this->query['group_concat'][$i] = "GROUP_CONCAT($field) $display_as";
            $this->query['group_concat_property'][] = $display_as;
            if ($display_as !== end($fields)) {
                $this->query['group_concat'][$i] .= ",";
            }
            $i++;
        }
        return $this;
    }

    /**
     * Very specific way to use this. See example below pay attention to quotes
     * $test = new User();
     * $test->contact("username, '-', fname, '-', created_at");
     * @param $concatenatedFields
     * @param null $displayAs
     * @return $this
     */
    public function concat($concatenatedFields, $displayAs = null )
    {
        $concat = !is_null($displayAs) ? "($concatenatedFields) $displayAs" : "($concatenatedFields)";

        $this->query['concat'] = "CONCAT $concat";

        return $this;
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (!isset($this->$property)) {
            if (isset($this->attributes->$property)) {
                return $this->attributes->$property;
            }
        }
        return NULL;
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        if (!isset($this->$property)) {
            $this->attributes->$property = $value;
        }
    }

    /**
     * Helper method. Breaks query into partially completed queries with a select and conditions.
     * @param $fields
     * @return array
     */
    private function partialQuery($fields )
    {
        $fields = implode(', ', $fields);

        $select = $this->distinctValues === true ? "SELECT DISTINCT $fields " :
            ( !is_null( $this->onlySelect ) ? $this->onlySelect : "SELECT $fields ") ;

        //reset distinct values
        $this->distinctValues = false;

        $conditions = '';
        $space = '';

        if (!empty($this->query['concat'])) {

            $stm = $this->query['concat'];
            $select = "SELECT $stm ";
        }

        if (!empty($this->query['group_concat'])) {
            $group_concat = ', ';
            foreach($this->query['group_concat'] as $concat) {
                $group_concat .= $space . $concat;
                $space = ' ';
            }
            $select .= "$group_concat ";
        }

        if (!empty($this->query['joins'])) {
            foreach($this->query['joins'] as $join) {
                $conditions .= $space . $join;
                $space = ' ';
            }
        }

        if ( !empty($this->query['queries']) ) {
            foreach ($this->query['queries'] as $clauses) {
                foreach ($clauses as $clause) {
                    $conditions .= $space . $clause;
                }
                $space = ' ';
            }
        }

        if (!empty($this->query['groupBy'])) {
            $conditions .= " {$this->query['groupBy']}";
        }

        if (!empty($this->query['limit'])) {
            $conditions .= " {$this->query['limit']}";
        }

        if (!empty($this->query['offset'])) {
            $conditions .= " {$this->query['offset']}";
        }

        $select .= "FROM";

        return ['select' => $select, 'conditions' => $conditions ];
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function get(array $fields = array('*'))
    {
        $partials = $this->partialQuery($fields);

        $statement = $this->prepare("{$partials['select']} $this->table {$partials['conditions']}");

        $this->SQL = $statement;

        $statement->execute($this->query['bindings']);

        $this->count = $statement->rowCount();

        if ( $this->count ) {
            if ($this->count === 1) {
                $properties = $statement->fetch();
                foreach ($properties as $property => $value) {
                    if ($property === $this->primaryKey && is_null($value)) {
                        $this->count = 0;
                        unset($this->properties);
                        return false;
                    }
                    if (isset($this->query['group_concat_property'])) {
                        foreach($this->query['group_concat_property'] as $val) {
                            if ($val === $property) {
                            $this->attributes->$property = explode(',', $value);
                            }
                        }
                    }
                    if ( !isset($this->attributes->$property )) {
                        $this->attributes->$property = $value;
                    }
                }
            } else {
                while ($attribute = $statement->fetchObject('\\App\\Core\\Model\\Attributes')) {
                    $attributes[] = $attribute;
                }
                $this->attributes = new \ArrayIterator( $attributes );
            }
            return $this;
        } else {
            $this->SQLError = $statement->errorInfo();
            return $this;
        }
    }

    /**
     * @return Attributes|array|\ArrayObject|bool
     */
    public function toArray() {

        if ( !$this->count ) {

            return false;

        } elseif ($this->count === 1 ) {

            $result = new \ArrayObject( $this->attributes );
            $this->attributes = $result;
        } else {

            $this->attributes = iterator_to_array($this->attributes);

            foreach ( $this->attributes as &$attribute ) {
                $attribute = new \ArrayObject( $attribute );
            }
        }

        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return reset($this->attributes);
    }

    /**
     * @param $relationship
     * @return $this
     */
    public function with( $relationship )
    {
        $this->$relationship();
        return $this;
    }

    /**
     * @param $relationship
     * @return $this - distinct values of the related model;
     */
    public function withOnly( $relationship )
    {
        $this->$relationship();

        $table = $this->relatedModel->table;

        $this->onlySelect = "SELECT DISTINCT $table.* ";

        return $this;

    }

    /**
     * returns instance of another object via relationships.
     * @param $relationship
     * @return mixed
     */
    public function morphTo($relationship)
    {
        $this->$relationship();

        $model = $this->relatedModel;

        /**
         * if there's a pivot table we will actually force to
         * become that pivot table object since most pivot tables don't have models;
         */
        if (!is_null($this->pivotTable)) {
            $model->table = $this->pivotTable;
            $model->customTime = true;
        }

        return $this->relatedModel;
    }

    /**
     * @param $relatedModel
     * @param null $foreignKey
     * @param null $localKey
     * @return $this
     */
    public function hasOne($relatedModel, $foreignKey = null, $localKey = null )
    {
        $this->relatedModel = new $relatedModel;
        $relatedModelTable = $this->relatedModel->table;
        $this->localKey = !is_null($localKey) ? $localKey : $this->primaryKey;
        $this->foreignKey = !is_null($foreignKey) ? $foreignKey : $this->model . '_id' ;

        $this->leftJoin( $this->localKey, $relatedModelTable . '.' . $this->foreignKey );

        $this->limit(1);

        return $this;
    }

    /**
     * @param $relationship
     * @param $foreignKey
     * @param null $localKey
     * @return $this
     * @throws \Exception
     */
    public function hasMany( $relatedModel, $foreignKey = null, $localKey = null )
    {

        $this->relatedModel = new $relatedModel;
        $relatedModelTable = $this->relatedModel->table;
        $this->localKey = !is_null($localKey) ? $localKey : $this->primaryKey;
        $this->foreignKey = !is_null($foreignKey) ? $foreignKey : $this->model . '_id' ;

        $this->join( $this->localKey, $relatedModelTable . '.' . $this->foreignKey );

        return $this;
    }

    /**
     * @param $relatedModel
     * @param null $foreignKey
     * @param null $relatedModelKey
     * @return $this
     * @throws \Exception
     */
    public function belongsTo($relatedModel, $foreignKey = null, $localKey = null )
    {

        $this->relatedModel = new $relatedModel;
        $relatedModelTable = $this->relatedModel->table;
        $this->localKey = !is_null($localKey) ? $localKey : $this->primaryKey;
        $this->foreignKey = !is_null($foreignKey) ? $foreignKey : $this->model . '_id' ;

        $this->rightJoin( $this->localKey, $relatedModelTable . '.' . $this->foreignKey );

        return $this;

    }

    /**
     * @param $relatedModel
     * @param null $pivotTable
     * @param null $localKey
     * @param null $thisModelKey
     * @return mixed
     * @throws \Exception
     */
    public function belongsToMany($relatedModel, $pivotTable = NULL, $pivotTableLeftKey = NULL, $pivotTableRightKey = NULL)
    {

        $this->relatedModel = new $relatedModel();
        $relatedTable = $this->relatedModel->table;
        $relatedTablePrimaryKey = $this->relatedModel->primaryKey;

        if ( is_null($pivotTable) ) {
            $arr_tables = [$this->table, $relatedTable];
            sort($arr_tables);
            $this->pivotTable = implode('_', $arr_tables);
        } else {
            $this->pivotTable = $pivotTable;
        }

        $pivotTableLeftKey = is_null($pivotTableLeftKey) ? $this->relatedModel->model . '_id' : $pivotTableLeftKey;
        $pivotTableRightKey = is_null($pivotTableRightKey) ? $this->model . '_id' : $pivotTableRightKey;

        $this->join( $this->primaryKey, $pivotTableRightKey, $this->pivotTable )
            ->join( $this->pivotTable . '.' . $pivotTableLeftKey, $relatedTable . '.' . $relatedTablePrimaryKey );
    }

    /**
     * Sets a relationship through a third table
     * @param $relatedModel - the data that we really want.
     * @param $throughModel - The table that we're using to get the data
     * @param $throughTableKey - The column on through table that contains the referenced key
     * @param $throughTableLocalKey - The key in the related model that references the through table.
     */
    public function hasManyThrough($relatedModel, $throughModel, $throughModelKey = 'id', $throughModelLocalKey = 'id')
    {
        $this->relatedModel = new $relatedModel;
        $table = $this->relatedModel->table;

        $throughModel = new $throughModel;

        $this->join( $throughModel->primaryKey, $throughModel->table . '.' . $throughModelLocalKey )
            ->join( $throughModel->table . '.' . $throughModelKey, $table . '.' . $this->relatedModel->primaryKey );

        return $this;
    }

    /**
     * @param $value
     * @return bool|string
     */
    public function encrypt($value)
    {
        $hash_cost_factor = defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null;
        $encryptedPass = password_hash($value, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));

        return $encryptedPass;
    }

    /**
     * @param null $index
     * @return array
     */
    private function getCallingFunction($index = null)
    {
        if (is_null($index)) {
            foreach(debug_backtrace() as $debug) {
                $result[] = $debug['function'];
            }
        }
        else {
            $result = debug_backtrace()[$index]['function'];
        }

        return $result;
    }
}