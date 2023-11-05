<?php

namespace App\Http\Interfaces;

use Illuminate\Http\Request;

class BotResponse
{
    private int $id;
    private string $title;
    private string $mediumPicture;
    private string $largePicure;

    public function __construct(array $responseData)
    {
        $this->setId(intval($responseData['node']['id']));
        $this->setTitle($responseData['node']['title']);
        $this->setMediumPicture($responseData['node']['main_picture']['medium']);
        $this->setLargePicure($responseData['node']['main_picture']['large']);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMediumPicture(): string
    {
        return $this->mediumPicture;
    }

    /**
     * @param string $mediumPicture
     */
    public function setMediumPicture(string $mediumPicture): void
    {
        $this->mediumPicture = $mediumPicture;
    }

    /**
     * @return string
     */
    public function getLargePicure(): string
    {
        return $this->largePicure;
    }

    /**
     * @param string $largePicure
     */
    public function setLargePicure(string $largePicure): void
    {
        $this->largePicure = $largePicure;
    }


}
