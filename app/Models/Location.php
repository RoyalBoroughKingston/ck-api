<?php

namespace App\Models;

use App\Contracts\Geocoder;
use App\Models\Mutators\LocationMutators;
use App\Models\Relationships\LocationRelationships;
use App\Models\Scopes\LocationScopes;
use App\Support\Address;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class Location extends Model implements AppliesUpdateRequests
{
    use LocationMutators;
    use LocationRelationships;
    use LocationScopes;
    use UpdateRequests;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'lat' => 'float',
        'lon' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return \App\Models\Location
     */
    public function updateCoordinate(): self
    {
        /**
         * @var \App\Contracts\Geocoder $geocoder
         */
        $geocoder = resolve(Geocoder::class);
        $coordinate = $geocoder->geocode($this->toAddress());

        $this->lat = $coordinate->lat();
        $this->lon = $coordinate->lon();

        return $this;
    }

    /**
     * Check if the update request is valid.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateUpdateRequest(UpdateRequest $updateRequest): Validator
    {
        $rules = [
            'address_line_1' => ['required', 'string', 'min:1', 'max:255'],
            'address_line_2' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'address_line_3' => ['present', 'nullable', 'string', 'min:1', 'max:255'],
            'city' => ['required', 'string', 'min:1', 'max:255'],
            'county' => ['required', 'string', 'min:1', 'max:255'],
            'postcode' => ['required', 'string', 'min:1', 'max:255'],
            'country' => ['required', 'string', 'min:1', 'max:255'],
            'accessibility_info' => ['present', 'nullable', 'string', 'min:1', 'max:10000'],
        ];

        return ValidatorFacade::make($updateRequest->data, $rules);
    }

    /**
     * Apply the update request.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \App\Models\UpdateRequest
     */
    public function applyUpdateRequest(UpdateRequest $updateRequest): UpdateRequest
    {
        $this->update([
            'address_line_1' => $updateRequest->data['address_line_1'],
            'address_line_2' => $updateRequest->data['address_line_2'],
            'address_line_3' => $updateRequest->data['address_line_3'],
            'city' => $updateRequest->data['city'],
            'county' => $updateRequest->data['county'],
            'postcode' => $updateRequest->data['postcode'],
            'country' => $updateRequest->data['country'],
            'accessibility_info' => $updateRequest->data['accessibility_info'],
        ]);

        $this->updateCoordinate()->save();

        return $updateRequest;
    }

    /**
     * @return \App\Models\Location
     */
    public function touchServices(): Location
    {
        $this->services()->get()->each->save();

        return $this;
    }

    /**
     * @return \App\Support\Address
     */
    public function toAddress(): Address
    {
        return Address::create(
            [$this->address_line_1, $this->address_line_2, $this->address_line_3],
            $this->city,
            $this->county,
            $this->postcode,
            $this->country
        );
    }
}
