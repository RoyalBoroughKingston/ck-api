<?php

namespace App\Http\Responses;

use App\Models\UpdateRequest;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

class UpdateRequestReceived implements Responsable
{
    /**
     * @var \App\Models\UpdateRequest
     */
    protected $updateRequest;

    /**
     * @var int
     */
    protected $code;

    /**
     * UpdateRequestReceived constructor.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @param int $code
     */
    public function __construct(UpdateRequest $updateRequest, int $code = Response::HTTP_OK)
    {
        $this->updateRequest = $updateRequest;
        $this->code = $code;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response()->json([
            'message' => 'The update request has been received and needs to be reviewed',
            'id' => $this->updateRequest->id,
            'data' => $this->updateRequest->getUpdateable()->getData(
                $this->updateRequest->data
            ),
        ], $this->code);
    }
}
