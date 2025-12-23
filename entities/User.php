<?php
class User
{
    private $id;
    private $email;
    private $password;
    private $username;
    private $firstName;
    private $lastName;
    private $birthDate;
    private $balance;

    public function __construct($email, $password, $username, $firstName, $lastName, $birthDate, $balance, $id = null)
    {
        $this->id = $id;
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setUsername($username);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setBirthDate($birthDate);
        $this->setBalance($balance);
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getBirthDate()
    {
        return $this->birthDate;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    // Setters
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

}
