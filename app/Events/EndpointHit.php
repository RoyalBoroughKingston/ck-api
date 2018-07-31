<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

abstract class EndpointHit
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var null|\App\Models\User
     */
    protected $user;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * @var string
     */
    protected $ipAddress;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $user = $request->user();
        $this->user = $user;
        $this->ipAddress = $request->ip();
        $this->userAgent = $request->userAgent();
    }

    /**
     * @return \App\Models\User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
}
