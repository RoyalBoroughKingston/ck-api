<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class UpdateRequestReceived implements Responsable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * UpdateRequestReceived constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response()->json([
            'message' => 'The update request has been received and needs to be reviewed',
            'data' => $this->data,
        ]);
    }
}
