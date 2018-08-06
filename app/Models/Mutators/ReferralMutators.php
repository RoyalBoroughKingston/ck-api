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
     * @param string $email
     */
    public function setEmailAttribute(string $email)
    {
        $this->attributes['email'] = encrypt($email);
    }

    /**
     * @param string $phone
     * @return null|string
     */
    public function getPhoneAttribute(?string $phone): ?string
    {
        return $phone ? decrypt($phone) : null;
    }

    /**
     * @param string $phone
     */
    public function setPhoneAttribute(string $phone)
    {
        $this->attributes['phone'] = encrypt($phone);
    }

    /**
     * @param string $otherContact
     * @return null|string
     */
    public function getOtherContactAttribute(?string $otherContact): ?string
    {
        return $otherContact ? decrypt($otherContact) : null;
    }

    /**
     * @param string $otherContact
     */
    public function setOtherContactAttribute(string $otherContact)
    {
        $this->attributes['other_contact'] = encrypt($otherContact);
    }

    /**
     * @param string $postcodeOutwardCode
     * @return null|string
     */
    public function getPostcodeOutwardCodeAttribute(?string $postcodeOutwardCode): ?string
    {
        return $postcodeOutwardCode ? decrypt($postcodeOutwardCode) : null;
    }

    /**
     * @param string $postcodeOutwardCode
     */
    public function setPostcodeOutwardCodeAttribute(string $postcodeOutwardCode)
    {
        $this->attributes['postcode_outward_code'] = encrypt($postcodeOutwardCode);
    }

    /**
     * @param string $comments
     * @return null|string
     */
    public function getCommentsAttribute(?string $comments): ?string
    {
        return $comments ? decrypt($comments) : null;
    }

    /**
     * @param string $comments
     */
    public function setCommentsAttribute(string $comments)
    {
        $this->attributes['comments'] = encrypt($comments);
    }

    /**
     * @param string $refereeName
     * @return null|string
     */
    public function getRefereeNameAttribute(?string $refereeName): ?string
    {
        return $refereeName ? decrypt($refereeName) : null;
    }

    /**
     * @param string $refereeName
     */
    public function setRefereeNameAttribute(string $refereeName)
    {
        $this->attributes['referee_name'] = encrypt($refereeName);
    }

    /**
     * @param string $refereeEmail
     * @return null|string
     */
    public function getRefereeEmailAttribute(string $refereeEmail): ?string
    {
        return $refereeEmail ? decrypt($refereeEmail) : $refereeEmail;
    }

    /**
     * @param string $refereeEmail
     */
    public function setRefereeEmailAttribute(string $refereeEmail)
    {
        $this->attributes['referee_email'] = encrypt($refereeEmail);
    }

    /**
     * @param string $refereePhone
     * @return null|string
     */
    public function getRefereePhoneAttribute(?string $refereePhone): ?string
    {
        return $refereePhone ? decrypt($refereePhone) : null;
    }

    /**
     * @param string $refereePhone
     */
    public function setRefereePhoneAttribute(string $refereePhone)
    {
        $this->attributes['referee_phone'] = encrypt($refereePhone);
    }
}
