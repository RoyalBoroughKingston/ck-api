<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

class UpdateRequestReceived implements Responsable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var int
     */
    protected $code;

    /**
     * UpdateRequestReceived constructor.
     *
     * @param array $data
     * @param int $code
     */
    public function __construct(array $data = [], int $code = Response::HTTP_OK)
    {
        $this->data = $data;
        $this->code = $code;
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
        ], $this->code);
    }
}
