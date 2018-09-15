<?php

namespace App\Events;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Carbon;

class EndpointHit
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
     * @var \Illuminate\Support\Carbon
     */
    protected $createdAt;

    /**
     * @var \App\Models\Model|null
     */
    protected $model;

    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $action
     * @param string $description
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function __construct(Request $request, string $action, string $description, Model $model = null)
    {
        $user = $request->user();
        $this->user = $user;
        $this->action = $action;
        $this->description = $description;
        $this->ipAddress = $request->ip();
        $this->userAgent = $request->userAgent();
        $this->createdAt = now();
        $this->model = $model;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $message
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \App\Events\EndpointHit
     */
    public static function onCreate(Request $request, string $message, Model $model = null): self
    {
        return new static($request, Audit::ACTION_CREATE, $message, $model);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $message
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \App\Events\EndpointHit
     */
    public static function onRead(Request $request, string $message, Model $model = null): self
    {
        return new static($request, Audit::ACTION_READ, $message, $model);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $message
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \App\Events\EndpointHit
     */
    public static function onUpdate(Request $request, string $message, Model $model = null): self
    {
        return new static($request, Audit::ACTION_UPDATE, $message, $model);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $message
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \App\Events\EndpointHit
     */
    public static function onDelete(Request $request, string $message, Model $model = null): self
    {
        return new static($request, Audit::ACTION_DELETE, $message, $model);
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

    /**
     * @return \Illuminate\Support\Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @return \App\Models\Model|null
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @param string|null $action
     * @return bool
     */
    public function isFor(string $model, string $action = null): bool
    {
        return $action
            ? ($this->getModel() instanceof $model) && $this->getAction() === $action
            : $this->getModel() instanceof $model;
    }

    /**
     * @param string $model
     * @param string|null $action
     * @return bool
     */
    public function isntFor(string $model, string $action = null): bool
    {
        return !$this->isFor($model, $action);
    }
}
