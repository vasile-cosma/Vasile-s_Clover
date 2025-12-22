<?php
class Card
{
    private $id;
    private $name;
    private $value;
    private $suit;
    private $img;

    public function __construct($name, $value, $suit, $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->suit = $suit;
        $this->img = '../app/static/images/Cartas/' . $name . '_of_' . $suit . '.svg';
    }

    // Getters
    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getSuit()
    {
        return $this->suit;
    }
    public function getImg()
    {
        return $this->img;
    }

    // Setters
    public function setID($id)
    {
        $this->id = $id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function setSuit($suit)
    {
        $this->suit = $suit;
    }

    public function setImg($img)
    {
        $this->img = $img;
    }

}
