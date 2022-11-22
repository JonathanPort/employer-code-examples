<?php

namespace App\Events\UserOnboarding;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use App\Models\UserOnboarding\UserOnboardingProcess;
use App\Models\UserOnboarding\FirmRegisterRequest;
use App\Models\UserOnboarding\FirmAccessRequest;

class DeclineFirmRegisterRequest
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $model;
    public $request;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FirmRegisterRequest $request)
    {

        $this->request = $request;

        $this->model = $request->requester()->first();

    }

}
