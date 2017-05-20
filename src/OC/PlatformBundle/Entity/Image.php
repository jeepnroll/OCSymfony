<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    private $file;

    private $tempFileName;

    public function setFile(File $file){
        $this->file = $file;

        if(null !== $this->url){
            $this->tempFileName = $this->url;

            $this->url = null;
            $this->alt = null;
        }
    }

    public function getFile()
    {
        return $this->file;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if(null === $this->file){
            return;
        }

        $this->url = $this->file->guessExtension();
        $this->alt = $this->file->getClientOriginalName();

    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload(){
        if(null === $this->file){
            return;
        }

       if(null !== $this->tempFileName){
            $oldFile = $this->getUploadRootDir(). '/ ' . $this->id . '.' . $this->tempFileName;
            if(file_exists($oldFile)){
                unlink($oldFile);
            }
       }

       $this->file->move(
           $this->getUploadRootDir(),
           $this->id.'.'.$this->url
       );

    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFileName = $this->getUploadRootDir() . "/" . $this->id . "." . $this->url;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload(){
        if(file_exists($this->tempFileName)){
            unlink($this->tempFileName);
        }
    }


    public function getUploadDir(){
        return 'uploads/img';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * @return mixed
     */
    public function getTempFileName()
    {
        return $this->tempFileName;
    }

    /**
     * @param mixed $tempFileName
     */
    public function setTempFileName($tempFileName)
    {
        $this->tempFileName = $tempFileName;
    }


}
