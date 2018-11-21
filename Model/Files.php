<?php

namespace Raj\LaravelFiles\Model;

use DB;

use Illuminate\Database\Eloquent\Model;

/**
 * Name of modal class is kept plural in order to remove confusion with Symphony File System class.
*/
class Files extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files';

    /**
     * Morph Relation used by owner of the files.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner() {
        return $this->morphTo();
    }

}
