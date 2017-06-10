<?php
namespace TeamNeusta\PushMe\DTO;

use JsonSerializable;
/**
 * Class Commit
 * @package TeamNeusta\PushMe\DTO
 */
class Commit implements JsonSerializable
{
    /**
     * @var string
     */
    private $commitHash = '';
    /**
     * @var null|string
     */
    private $branch = null;
    /**
     * @var string
     */
    private $message = '';
    /**
     * @var \DateTime
     */
    private $commitDate = 0;
    /**
     * @var Author
     */
    private $author;

    /**
     * @return string
     */
    public function getCommitHash(): string
    {
        return $this->commitHash;
    }

    /**
     * @param string $commitHash
     *
     * @return $this
     */
    public function setCommitHash(string $commitHash)
    {
        $this->commitHash = $commitHash;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param null|string $branch
     *
     * @return $this
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return int
     */
    public function getCommitDate(): \DateTime
    {
        return $this->commitDate;
    }

    /**
     * @param int $commitDate
     *
     * @return $this
     */
    public function setCommitDate(int $commitDate)
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($commitDate);
        $this->commitDate = $dateTime;

        return $this;
    }

    /**
     * @return Author
     */
    public function getAuthor(): Author
    {
        return $this->author;
    }

    /**
     * @param Author $author
     *
     * @return $this
     */
    public function setAuthor(Author $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}