<?php

namespace P4M\CoreBundle\Entity;

use JMS\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Groups({"json"})
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Groups({"json"})
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;
    
//    @Assert\NotBlank(message="Please choose a picture")
    /**
     *
     * @var type 
     * 
     * @Assert\Image(maxSize = "1024k",maxSizeMessage="Filesize Maximum 1Mo")
     */
    private $file;
    private $tempFilename ;

    private $set = false;

    private $forced = false;

    private $localPath;




    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /*
     * Should only be used for default pictures
     */
    public function forceId($forcedId)
    {
        $this->id = $forcedId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set alt
     *
     * @param string $alt
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
    
    
    public function getFile()
    {
        return $this->file;
    }
    // On modifie le setter de File, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
  
    public function setFile(UploadedFile $file)
    {
        
        $this->file = $file;
    
    
    // On vérifie si on avait déjà un fichier pour cette entité
        if (null !== $this->name) {
          // On sauvegarde l'extension du fichier pour le supprimer plus tard
            $this->tempFilename = $this->name;

            // On réinitialise les valeurs des attributs name et alt
            $this->name = null;
            $this->alt = null;
        }
        $this->set = true;
    }
    
    public function getSet()
    {
        return $this->set;
    }

  /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
  public function preUpload()
  {
      
        // Si jamais il n'y a pas de fichier (champ facultatif)
        if (null === $this->file) {
          return;
        }

        // Le nom du fichier est son id, on doit juste stocker également son extension
        // Pour faire propre, on devrait renommer cet attribut en « extension », plutôt que « url »
        $this->name = $this->file->guessExtension();

    //    die($this->name);
        // Et on génère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
        $this->alt = $this->file->getClientOriginalName();
  }

  /**
    * @ORM\PostPersist()
    * @ORM\PostUpdate()
    */
  public function upload()
  {
        // Si jamais il n'y a pas de fichier (champ facultatif)
        if (null === $this->file) {
          return;
        }

        // Si on avait un ancien fichier, on le supprime
        if (null !== $this->tempFilename) {
          $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
          if (file_exists($oldFile)) {
            unlink($oldFile);
          }
        }


        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
          $this->getUploadRootDir(), // Le répertoire de destination
          $this->id.'.'.$this->name   // Le nom du fichier à créer, ici « id.extension »
        );
        
        
        chmod( $this->getUploadRootDir().'/'.$this->id.'.'.$this->name, 0777);
  }

  /**
   * @ORM\PreRemove()
   */
  public function preRemoveUpload()
  {
    // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
    $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->name;
  }

  /**
   * @ORM\PostRemove()
   */
  public function removeUpload()
  {
    // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
    if (file_exists($this->tempFilename)) {
      // On supprime le fichier
      unlink($this->tempFilename);
    }
  }

  public function getUploadDir()
  {
    // On retourne le chemin relatif vers l'image pour un navigateur
    return 'images/uploads';
  }

  protected function getUploadRootDir()
  {
    // On retourne le chemin relatif vers l'image pour notre code PHP
    return __DIR__.'/../../../../web/'.$this->getUploadDir();
  }
  
  public function getWebPath()
  {
    return $this->getUploadDir().'/'.$this->getId().'.'.$this->getName();
  }

  
  public function forceLocalPicture($path)
  {
        $this->forced = true;
        $this->localPath = $path;
         $pathInfo = pathinfo($this->localPath);
        $this->name = $pathInfo['extension'];
        $this->alt = $pathInfo['basename'];
}
  
  /**
    * @ORM\PostPersist()
    * @ORM\PostUpdate()
    */
  
    public function copyFromLocal()
    {
        if ($this->forced == true)
        {   
            $dest = $this->getUploadRootDir().'/'.$this->id.'.'.$this->name;
            copy($this->localPath, $dest);
            chmod($dest, 0644);
        }
    }
}