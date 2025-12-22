<?php
class Score
{
    private $id;
    private $userId;
    private $score;

    public function __construct($userId, $score, $id = null)
    {
        $this->id = $id;
        $this->setUserId($userId);
        $this->setScore($score);
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getScore()
    {
        return $this->score;
    }

    // Setters
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setScore($score)
    {
        $this->score = $score;
    }

}
