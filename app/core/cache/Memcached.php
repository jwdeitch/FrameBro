<?php
/**
 * Author Jon Garcia
 * Memcached class
 */
namespace App\Core\Cache;
use App\Core\MissingLib;

/**
 * Class Memcached
 * @package App\Core\Cache
 */
class Memcached
{
    private $memcachedAdapter;
    public $host;

    /**
     * Memcached constructor.
     */
    public function __construct()
    {
        if (class_exists('\Memcached')) {

            $this->memcachedAdapter = new \Memcached();
            $this->setServer( getenv('CACHE_HOST'), getenv( 'CACHE_PORT') );

        } else {

            $this->memcachedAdapter = new MissingLib();
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get( $key )
    {
        return $this->memcachedAdapter->get( $key );
    }

    /**
     * @param $key
     * @param $value
     * @param $expiration
     * @return bool
     */
    public function add( $key, $value, $expiration )
    {
        return $this->memcachedAdapter->set( $key, $value, $expiration );
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete( $key )
    {
        return $this->memcachedAdapter->delete( $key );
    }

    /**
     * @return bool
     */
    public function clear( $delay = 0 )
    {
        return $this->memcachedAdapter->flush( $delay );
    }

    /**
     * @param string $host
     * @param int $port
     * @return bool
     */
    public function setServer( $host = 'localhost', $port = 11211 )
    {
        $this->host = "$host:$port";
        return $this->memcachedAdapter->addServer( $host, $port );
    }

    /**
     * @return mixed
     */
    public function getServer()
    {
        return $this->host;
    }
}