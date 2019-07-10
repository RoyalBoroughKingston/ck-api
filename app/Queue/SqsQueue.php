<?php

namespace App\Queue;

use Illuminate\Queue\SqsQueue as BaseSqsQueue;

class SqsQueue extends BaseSqsQueue
{
    /**
     * Get the queue or return the default.
     *
     * @param string|null $queue
     * @return string
     */
    public function getQueue($queue)
    {
        $queue = $queue ?: $this->default;

        return filter_var($queue, FILTER_VALIDATE_URL) === false
            ? $this->prefix . $queue : $queue;
    }
}
