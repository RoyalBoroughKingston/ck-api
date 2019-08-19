<?php

namespace App\Models\Mutators;

trait ReferralMutators
{
    /**
     * @param string $name
     * @return string
     */
    public function getNameAttribute(string $name): string
    {
        return decrypt($name);
    }

    /**
     * @param string $name
     */
    public function setNameAttribute(string $name)
    {
        $this->attributes['name'] = encrypt($name);
    }

    /**
     * @param string $email
     * @return string|null
     */
    public function getEmailAttribute(?string $email): ?string
    {
        return $email ? decrypt($email) : null;
    }

    /**
     * @param string|null $email
     */
    public function setEmailAttribute(?string $email)
    {
        $this->attributes['email'] = $email ? encrypt($email) : null;
    }

    /**
     * @param string|null $phone
     * @return string|null
     */
    public function getPhoneAttribute(?string $phone): ?string
    {
        return $phone ? decrypt($phone) : null;
    }

    /**
     * @param string|null $phone
     */
    public function setPhoneAttribute(?string $phone)
    {
        $this->attributes['phone'] = $phone ? encrypt($phone) : null;
    }

    /**
     * @param string|null $otherContact
     * @return string|null
     */
    public function getOtherContactAttribute(?string $otherContact): ?string
    {
        return $otherContact ? decrypt($otherContact) : null;
    }

    /**
     * @param string|null $otherContact
     */
    public function setOtherContactAttribute(?string $otherContact)
    {
        $this->attributes['other_contact'] = $otherContact ? encrypt($otherContact) : null;
    }

    /**
     * @param string|null $postcodeOutwardCode
     * @return string|null
     */
    public function getPostcodeOutwardCodeAttribute(?string $postcodeOutwardCode): ?string
    {
        return $postcodeOutwardCode ? decrypt($postcodeOutwardCode) : null;
    }

    /**
     * @param string|null $postcodeOutwardCode
     */
    public function setPostcodeOutwardCodeAttribute(?string $postcodeOutwardCode)
    {
        $this->attributes['postcode_outward_code'] = $postcodeOutwardCode ? encrypt($postcodeOutwardCode) : null;
    }

    /**
     * @param string|null $comments
     * @return string|null
     */
    public function getCommentsAttribute(?string $comments): ?string
    {
        return $comments ? decrypt($comments) : null;
    }

    /**
     * @param string|null $comments
     */
    public function setCommentsAttribute(?string $comments)
    {
        $this->attributes['comments'] = $comments ? encrypt($comments) : null;
    }

    /**
     * @param string|null $refereeName
     * @return string|null
     */
    public function getRefereeNameAttribute(?string $refereeName): ?string
    {
        return $refereeName ? decrypt($refereeName) : null;
    }

    /**
     * @param string|null $refereeName
     */
    public function setRefereeNameAttribute(?string $refereeName)
    {
        $this->attributes['referee_name'] = $refereeName ? encrypt($refereeName) : null;
    }

    /**
     * @param string|null $refereeEmail
     * @return string|null
     */
    public function getRefereeEmailAttribute(?string $refereeEmail): ?string
    {
        return $refereeEmail ? decrypt($refereeEmail) : $refereeEmail;
    }

    /**
     * @param string|null $refereeEmail
     */
    public function setRefereeEmailAttribute(?string $refereeEmail)
    {
        $this->attributes['referee_email'] = $refereeEmail ? encrypt($refereeEmail) : null;
    }

    /**
     * @param string|null $refereePhone
     * @return string|null
     */
    public function getRefereePhoneAttribute(?string $refereePhone): ?string
    {
        return $refereePhone ? decrypt($refereePhone) : null;
    }

    /**
     * @param string $refereePhone
     */
    public function setRefereePhoneAttribute(?string $refereePhone)
    {
        $this->attributes['referee_phone'] = $refereePhone ? encrypt($refereePhone) : null;
    }
}
