<?php
namespace JO\JoContent\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 * Person
 */
class Person extends AbstractEntity
{
    /**
     * gender
     *
     * @var int
     */
    protected $gender = '';

    /**
     * lastname
     *
     * @var string
     */
    protected $lastname = '';

    /**
     * firstname
     *
     * @var string
     */
    protected $firstname = '';

    /**
     * image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image = null;

    /**
     * email
     *
     * @var string
     */
    protected $email = '';

    /**
     * phone
     *
     * @var string
     */
    protected $phone = 0;

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * mobile
     *
     * @var string
     */
    protected $mobile = '';

    /**
     * website
     *
     * @var string
     */
    protected $website = '';

    /**
     * fax
     *
     * @var string
     */
    protected $fax = '';

    /**
     * city
     *
     * @var string
     */
    protected $city = '';

    /**
     * zip
     *
     * @var string
     */
    protected $zip = '';

    /**
     * street
     *
     * @var string
     */
    protected $street = '';

    /**
     * streetnumber
     *
     * @var string
     */
    protected $streetnumber = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * department
     *
     * @var string
     */
    protected $department = '';

    /**
     * departmenttext
     *
     * @var string
     */
    protected $departmenttext = '';

    /**
     * publikationen
     *
     * @var string
     */
    protected $publikationen = '';

    /**
     * federalstate
     *
     * @var \JO\JoContent\Domain\Model\Federalstate
     */
    protected $federalstate = null;

    /**
     * Returns the image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Returns the gender
     *
     * @return int gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Sets the gender
     *
     * @param string $gender
     * @return void
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Returns the lastname
     *
     * @return string lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Sets the lastname
     *
     * @param string $lastname
     * @return void
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Returns the firstname
     *
     * @return string firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Sets the firstname
     *
     * @param string $firstname
     * @return void
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Returns the email
     *
     * @return string email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the phone
     *
     * @return string phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets the phone
     *
     * @param int $phone
     * @return void
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Returns the title
     *
     * @return int title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the mobile
     *
     * @return string $mobile
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Sets the mobile
     *
     * @param string $mobile
     * @return void
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * Returns the website
     *
     * @return string $website
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Sets the website
     *
     * @param string $website
     * @return void
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * Returns the fax
     *
     * @return string $fax
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Sets the fax
     *
     * @param string $fax
     * @return void
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * Returns the city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the zip
     *
     * @return string $zip
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the zip
     *
     * @param string $zip
     * @return void
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Returns the street
     *
     * @return string $street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Sets the street
     *
     * @param string $street
     * @return void
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * Returns the streetnumber
     *
     * @return string $streetnumber
     */
    public function getStreetnumber()
    {
        return $this->streetnumber;
    }

    /**
     * Sets the streetnumber
     *
     * @param string $streetnumber
     * @return void
     */
    public function setStreetnumber($streetnumber)
    {
        $this->streetnumber = $streetnumber;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the department
     *
     * @return string $department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Sets the department
     *
     * @param string $department
     * @return void
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * Returns the departmenttext
     *
     * @return string $departmenttext
     */
    public function getDepartmenttext()
    {
        return $this->departmenttext;
    }

    /**
     * Sets the departmenttext
     *
     * @param string $departmenttext
     * @return void
     */
    public function setDepartmenttext($departmenttext)
    {
        $this->departmenttext = $departmenttext;
    }

    /**
     * Returns the publikationen
     *
     * @return string $publikationen
     */
    public function getPublikationen()
    {
        return $this->publikationen;
    }

    /**
     * Sets the publikationen
     *
     * @param string $publikationen
     * @return void
     */
    public function setPublikationen($publikationen)
    {
        $this->publikationen = $publikationen;
    }

    /**
     * Returns the federalstate
     *
     * @return \JO\JoContent\Domain\Model\Federalstate $federalstate
     */
    public function getFederalstate()
    {
        return $this->federalstate;
    }

    /**
     * Sets the federalstate
     *
     * @param \JO\JoContent\Domain\Model\Federalstate $federalstate
     * @return void
     */
    public function setFederalstate(\JO\JoContent\Domain\Model\Federalstate $federalstate)
    {
        $this->federalstate = $federalstate;
    }
}
