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
     * @return string
     */
    public function getEmailAttribute(string $email): string
    {
        return decrypt($email);
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
     * @return string
     */
    public function getPhoneAttribute(string $phone): string
    {
        return decrypt($phone);
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
     * @return string
     */
    public function getOtherContactAttribute(string $otherContact): string
    {
        return decrypt($otherContact);
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
     * @return string
     */
    public function getPostcodeOutwardCodeAttribute(string $postcodeOutwardCode): string
    {
        return decrypt($postcodeOutwardCode);
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
     * @return string
     */
    public function getCommentsAttribute(string $comments): string
    {
        return decrypt($comments);
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
     * @return string
     */
    public function getRefereeNameAttribute(string $refereeName): string
    {
        return decrypt($refereeName);
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
     * @return string
     */
    public function getRefereeEmailAttribute(string $refereeEmail): string
    {
        return decrypt($refereeEmail);
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
     * @return string
     */
    public function getRefereePhoneAttribute(string $refereePhone): string
    {
        return decrypt($refereePhone);
    }

    /**
     * @param string $refereePhone
     */
    public function setRefereePhoneAttribute(string $refereePhone)
    {
        $this->attributes['referee_phone'] = encrypt($refereePhone);
    }
}
