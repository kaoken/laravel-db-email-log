<?php
/**
 * Mail send limit exceeded
 */
namespace Kaoken\LaravelDBEmailLog\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendLimitMailToAdmin extends Mailable
{
    use SerializesModels;

    /**
     *
     * @var Log
     */
    protected Log $log;

    /**
     * Create a new message instance.
     *
     * @param Log $log Log model
     */
    public function __construct(Log $log)
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

        return $this->view('vendor.db_email_log.over_limit')
            ->subject(env('APP_NAME').' - Log mail send limit exceeded. ['.$this->log->level_name.']')
            ->to($config['to'], 'Admin')
            ->with(['log'=>$this->log, 'config'=>$config]);
    }
}
