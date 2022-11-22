<?php

namespace App\Events\UserOnboarding;

use App\Models\UserOnboarding\UserOnboardingProcess;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmitFirmAccessRequest
{

    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $model;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserOnboardingProcess $process)
    {

        $this->model = $process;

    }

}
