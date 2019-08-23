<?php

namespace App\Models;

use App\Contracts\Geocoder;
use App\Http\Requests\Location\UpdateRequest as Request;
use App\Models\Mutators\LocationMutators;
use App\Models\Relationships\LocationRelationships;
use App\Models\Scopes\LocationScopes;
use App\Rules\FileIsMimeType;
use App\Support\Address;
use App\Support\Coordinate;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
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
        'has_wheelchair_access' => 'boolean',
        'has_induction_loop' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return \App\Models\Location
     */
    public function updateCoordinate(): self
    {
        /**
         * @var \App\Contracts\Geocoder
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
        $rules = (new Request())->rules();

        // Remove the pending assignment rule since the file is now uploaded.
        $rules['image_file_id'] = [
            'nullable',
            'exists:files,id',
            new FileIsMimeType(File::MIME_TYPE_PNG),
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
        $data = $updateRequest->data;

        $this->update([
            'address_line_1' => $data['address_line_1'] ?? $this->address_line_1,
            'address_line_2' => $data['address_line_2'] ?? $this->address_line_2,
            'address_line_3' => $data['address_line_3'] ?? $this->address_line_3,
            'city' => $data['city'] ?? $this->city,
            'county' => $data['county'] ?? $this->county,
            'postcode' => $data['postcode'] ?? $this->postcode,
            'country' => $data['country'] ?? $this->country,
            'accessibility_info' => $data['accessibility_info'] ?? $this->accessibility_info,
            'has_wheelchair_access' => $data['has_wheelchair_access'] ?? $this->has_wheelchair_access,
            'has_induction_loop' => $data['has_induction_loop'] ?? $this->has_induction_loop,
            'image_file_id' => array_key_exists('image_file_id', $data)
                ? $data['image_file_id']
                : $this->image_file_id,
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

    /**
     * @return \App\Support\Coordinate
     */
    public function toCoordinate(): Coordinate
    {
        return new Coordinate($this->lat, $this->lon);
    }

    /**
     * @param int|null $maxDimension
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException|\InvalidArgumentException
     * @return \App\Models\File|\Illuminate\Http\Response|\Illuminate\Contracts\Support\Responsable
     */
    public static function placeholderImage(int $maxDimension = null)
    {
        if ($maxDimension !== null) {
            return File::resizedPlaceholder($maxDimension, File::META_PLACEHOLDER_FOR_LOCATION);
        }

        return response()->make(
            Storage::disk('local')->get('/placeholders/location.png'),
            Response::HTTP_OK,
            ['Content-Type' => File::MIME_TYPE_PNG]
        );
    }

    /**
     * @return bool
     */
    public function hasImage(): bool
    {
        return $this->image_file_id !== null;
    }
}
