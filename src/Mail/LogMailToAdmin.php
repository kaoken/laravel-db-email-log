<?php

namespace Kaoken\LaravelDBEmailLog\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Kaoken\LaravelDBEmailLog\Model\Log;

class LogMailToAdmin extends Mailable
{
    use SerializesModels;

    /**
     *
     * @var Log
     */
    protected $log;

    /**
     * Create a new message instance.
     *
     * @param Log $log Log model
     */
    public function __construct($log)
    {
        $this->log = $log;
    }

    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        $config = app()['config']["app.db_log"];

        return $this->view('vendor.db_email_log.log')
            ->subject(env('APP_NAME').' - Log ['.$this->log->level_name.']')
            ->to($config['to'], 'Admin')
            ->with(['log'=>$this->log, 'config'=>$config]);
    }
}
