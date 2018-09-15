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
     * @return null|string
     */
    public function getEmailAttribute(?string $email): ?string
    {
        return $email ? decrypt($email) : null;
    }

    /**
     * @param null|string $email
     */
    public function setEmailAttribute(?string $email)
    {
        $this->attributes['email'] = $email ? encrypt($email) : null;
    }

    /**
     * @param null|string $phone
     * @return null|string
     */
    public function getPhoneAttribute(?string $phone): ?string
    {
        return $phone ? decrypt($phone) : null;
    }

    /**
     * @param null|string $phone
     */
    public function setPhoneAttribute(?string $phone)
    {
        $this->attributes['phone'] = $phone ? encrypt($phone) : null;
    }

    /**
     * @param null|string $otherContact
     * @return null|string
     */
    public function getOtherContactAttribute(?string $otherContact): ?string
    {
        return $otherContact ? decrypt($otherContact) : null;
    }

    /**
     * @param null|string $otherContact
     */
    public function setOtherContactAttribute(?string $otherContact)
    {
        $this->attributes['other_contact'] = $otherContact ? encrypt($otherContact) : null;
    }

    /**
     * @param null|string $postcodeOutwardCode
     * @return null|string
     */
    public function getPostcodeOutwardCodeAttribute(?string $postcodeOutwardCode): ?string
    {
        return $postcodeOutwardCode ? decrypt($postcodeOutwardCode) : null;
    }

    /**
     * @param null|string $postcodeOutwardCode
     */
    public function setPostcodeOutwardCodeAttribute(?string $postcodeOutwardCode)
    {
        $this->attributes['postcode_outward_code'] = $postcodeOutwardCode ? encrypt($postcodeOutwardCode) : null;
    }

    /**
     * @param null|string $comments
     * @return null|string
     */
    public function getCommentsAttribute(?string $comments): ?string
    {
        return $comments ? decrypt($comments) : null;
    }

    /**
     * @param null|string $comments
     */
    public function setCommentsAttribute(?string $comments)
    {
        $this->attributes['comments'] = $comments ? encrypt($comments) : null;
    }

    /**
     * @param null|string $refereeName
     * @return null|string
     */
    public function getRefereeNameAttribute(?string $refereeName): ?string
    {
        return $refereeName ? decrypt($refereeName) : null;
    }

    /**
     * @param null|string $refereeName
     */
    public function setRefereeNameAttribute(?string $refereeName)
    {
        $this->attributes['referee_name'] = $refereeName ? encrypt($refereeName) : null;
    }

    /**
     * @param null|string $refereeEmail
     * @return null|string
     */
    public function getRefereeEmailAttribute(?string $refereeEmail): ?string
    {
        return $refereeEmail ? decrypt($refereeEmail) : $refereeEmail;
    }

    /**
     * @param null|string $refereeEmail
     */
    public function setRefereeEmailAttribute(?string $refereeEmail)
    {
        $this->attributes['referee_email'] = $refereeEmail ? encrypt($refereeEmail) : null;
    }

    /**
     * @param null|string $refereePhone
     * @return null|string
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
