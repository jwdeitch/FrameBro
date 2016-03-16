<?php
/**
 * Author: Jon Garcia.
 * Date: 2/28/16
 * Time: 12:04 PM
 */

namespace App\Models;


use App\Core\Model\Loupe;

class Post extends Loupe
{
    protected $primaryKey = 'pid';

    public function user() {

        return $this->belongsTo('\\App\\Models\\User', 'uid');

    }

}
