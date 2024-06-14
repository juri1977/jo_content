<?php
namespace JO\JoContent\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 * Institut
 */
class Institut extends AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

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
    protected $zip = 0;

    /**
     * federalstate
     *
     * @var \JO\JoContent\Domain\Model\Federalstate
     */
    protected $federalstate = null;

    /**
     * street
     *
     * @var string
     */
    protected $street = '';

    /**
     * streetnumber
     *
     * @var int
     */
    protected $streetnumber = 0;

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
    protected $phone = '';

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
     * typ
     *
     * @var string
     */
    protected $typ = '';

    /**
     * fax
     *
     * @var string
     */
    protected $fax = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image = null;

    /**
     * employee
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoContent\Domain\Model\Person>
     */
    protected $employee = null;

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the typ
     *
     * @return string $typ
     */
    public function getTyp()
    {
        return $this->typ;
    }

    /**
     * Sets the typ
     *
     * @param string $typ
     * @return void
     */
    public function setTyp($typ)
    {
        $this->typ = $typ;
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

    /**
     * Returns the city
     *
     * @return string city
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
     * @return string zip
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the zip
     *
     * @param int $zip
     * @return void
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->employee = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * @return int $streetnumber
     */
    public function getStreetnumber()
    {
        return $this->streetnumber;
    }

    /**
     * Sets the streetnumber
     *
     * @param int $streetnumber
     * @return void
     */
    public function setStreetnumber($streetnumber)
    {
        $this->streetnumber = $streetnumber;
    }

    /**
     * Returns the email
     *
     * @return string $email
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
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets the phone
     *
     * @param string $phone
     * @return void
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
    public function setImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
    {
        $this->image = $image;
    }

    /**
     * Adds a Person
     *
     * @param \JO\JoContent\Domain\Model\Person $employee
     * @return void
     */
    public function addEmployee(\JO\JoContent\Domain\Model\Person $employee)
    {
        $this->employee->attach($employee);
    }

    /**
     * Removes a Person
     *
     * @param \JO\JoContent\Domain\Model\Person $employeeToRemove The Person to be removed
     * @return void
     */
    public function removeEmployee(\JO\JoContent\Domain\Model\Person $employeeToRemove)
    {
        $this->employee->detach($employeeToRemove);
    }

    /**
     * Returns the employee
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoContent\Domain\Model\Person> $employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Sets the employee
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoContent\Domain\Model\Person> $employee
     * @return void
     */
    public function setEmployee(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $employee)
    {
        $this->employee = $employee;
    }
}
