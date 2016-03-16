<?php

/**
 * Class Database
 * Creates a PDO database connection. This connection will be passed into the models (so we use
 * the same connection for all models and prevent to open multiple connections at once)
 */

namespace App\Core;

use App\Core\Cache\Memcached;
use App\Libraries\Inflect;

/**
 * Class Database
 * @package App\Core
 */
class Database extends \PDO
{
    /**
     * Construct this Database object, extending the PDO object
     * By the way, the PDO object is built into PHP by default
     */
    public function __construct()
    {
        /**
         * set the (optional) options of the PDO connection. in this case, we set the fetch mode to
         * "objects".
         * @see http://www.php.net/manual/en/pdostatement.fetch.php
         */
        $errMode = getenv('ENV') === 'dev' ? '\PDO::ERRMODE_WARNING' : '\PDO::ERRMODE_EXCEPTION';
        $options = array(\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ, \PDO::ATTR_ERRMODE => $errMode);

        /**
         * Generate a database connection, using the PDO connector
         * @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
         * Also important: We include the charset, as leaving it out seems to be a security issue:
         * @see http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers#Connecting_to_MySQL says:
         * "Adding the charset to the DSN is very important for security reasons,
         * most examples you'll see around leave it out. MAKE SURE TO INCLUDE THE CHARSET!"
         */
        parent::__construct(getenv('DB_TYPE') . ':host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8', getenv('DB_USER'), getenv('DB_PASSWORD'), $options);
    }

    /**
     * returns database equivalent name for model.
     * @return string - plural table name
     */
    protected function getTableName()
    {
        $class = get_class($this);

        $mem = new Memcached();
        if ($tableName = $mem->get($class . '-table-name')) {
            return $tableName;
        }

        $break = explode('\\', $class);
        $ObjectName = end($break);
        $db_name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $ObjectName));
        $tableName = Inflect::pluralize($db_name );

        $mem->add($class . '-table-name', $tableName, 1440 );

        return $tableName;
    }
}