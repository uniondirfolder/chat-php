<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Events\RemainingTimeChanged;
use App\Events\WinnerNumberGenerated;
use Illuminate\Console\Command;

class GameExecutor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:execute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start executing the game';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public const TIME = 7;
    public const MIN_NUMBER = 0;
    public const MAX_NUMBER = 36;


    /**
     * @var int|string
     */
    private $time = self::TIME;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $counter = 60*24;

        while ($counter!==0) {
            broadcast(new RemainingTimeChanged($this->time . 's'));

            $this->time--;
            sleep(1);

            if ($this->time === 0) {
                $this->time = 'Waiting to start';
                broadcast(new RemainingTimeChanged($this->time));
                broadcast(new WinnerNumberGenerated(mt_rand(self::MIN_NUMBER, self::MAX_NUMBER)));

                sleep(60);
                $this->time = self::TIME;

                $counter--;
            }
        }
    }
}
