<?php

namespace Kaoken\LaravelDBEmailLog\Model;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s.u';
    protected $dates = ['create_tm'];
    protected ?array $jsonDecodeData=null;


    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $config = app()['config']["logging.db_log"];
        $this->connection = $config['connection'];
        parent::__construct($attributes);
    }

    /**
     * Decode JSON of character string state of 'context' and obtain by array
     * @return array|null
     */
    public function getJsonDecodeData(): ?array
    {
        if($this->jsonDecodeData===null)
            $this->jsonDecodeData = json_decode($this->context,true);
        return $this->jsonDecodeData;
    }
}
