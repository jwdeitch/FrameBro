<?php
/**
 * Author: Jon Garcia
 * Date: 1/23/16
 * Time: 12:00 PM
 */

namespace App\Core\Model;

/**
 * Class Attributes
 * @package App\Core\Model
 */
class Attributes  implements \IteratorAggregate
{

    /**
     * @return \ArrayIterator
     */
    public function getIterator() {

        return new \ArrayIterator( $this );

    }

}