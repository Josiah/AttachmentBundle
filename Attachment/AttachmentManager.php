<?php namespace WebDev\AttachmentBundle\Attachment;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;
use ReflectionClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use WebDev\AttachmentBundle\Configuration\Attachment as AttachmentAnnotation;
use WebDev\Conventional\Resolver;

/**
 * Attachment Manager
 *
 * @author Josiah Bond <josiah@web-dev.com.au>
 */
class AttachmentManager
{
    public function __construct( Reader $reader )
    {
        $this->reader = $reader;
        $this->resolver = new Resolver();
    }
    
    /**
     * @var Reader
     */
    protected $reader;
    
    /**
     * Retrieves the attachments of the specified object
     *
     * @param mixed $object
     * @return WebDev\AttachmentBundle\Attachement\File[]
     */
    public function files($object)
    {
        if( !is_object($object) )
        {
            throw new Exception("Can't retrieve the attachments of a ".get_type($object));
        }
        
        $class = new ReflectionClass($object);
        $files = array();
        foreach( $this->reader->getClassAnnotations($class) as $annotation)
        {
            if(!($annotation instanceof FileAttachment)) continue;
            
            $file = new File($annotation,$object,$this);
            $files[$file->getName()] = $file;
        }
        
        return $files;
    }
    
    /**
     * Retrieves an attachment of the specified object
     *
     * @param mixed $object
     * @param strign $name of the file to return
     * @return WebDev\AttachmentBundle\Attachement\File
     */
    public function file($object, $name)
    {
        if( !is_object($object) )
        {
            throw new Exception("Can't retrieve the attachments of a ".get_type($object));
        }

        $class = new ReflectionClass($object);
        foreach( $this->reader->getClassAnnotations($class) as $annotation)
        {
            if(!($annotation instanceof FileAttachment)) continue;
            if(!($annotation->getName() == $name)) continue;
            
            return new File($annotation,$object,$this);
        }
    }

    const ATTACHMENT_ANNOTATION = "WebDev\\AttachmentBundle\\Configuration\\Attachment";
    
    /**
     * Base path for file contexts that ustilize this attachment service
     *
     * @var string
     */
    protected $basePath;
    public function getBasePath(){ return $this->basePath; }
    public function setBasePath( $path ){ $this->basePath = $path; }

    /**
     * Hydrates the entity attachments post-load
     *
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        $this->hydrateUploads($entity);
    }

    /**
     * Called on post persist
     * 
     * @param EventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        $this->processUploads($entity);
    }

    /**
     * Called on post update
     * 
     * @param EventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        $this->processUploads($entity);
    }

    /**
     * Processes any uploads that have occured on the entity
     *
     * @param mixed $entity
     */
    public function processUploads($entity)
    {
        if(!is_object($entity)) return;

        $class = new ReflectionClass($entity);
        foreach($class->getProperties() as $property)
        {
            $annotation = $this->reader->getPropertyAnnotation($property,self::ATTACHMENT_ANNOTATION);
            if($annotation instanceof AttachmentAnnotation)
            {
                $attachment = new Attachment($this,$entity,$annotation,$entity);
                $uploadedFile = $this->resolver->get($entity,$property->getName());

                // Process any uploaded files
                if($uploadedFile instanceof UploadedFile)
                {
                    $path = $attachment->path();
                    $uploadedFile->move(dirname($path),basename($path));
                    $savedFile = new File($path);
                    $this->resolver->set($entity,$property->getName(),$savedFile);
                }
            }
        }
    }

    /**
     * Hydrates the attachments into entities that have been retrieved from the database
     *
     * @param mixed $entity
     */
    public function hydrateUploads($entity)
    {
        if(!is_object($entity)) return;

        $class = new ReflectionClass($entity);
        foreach($class->getProperties() as $property)
        {
            $annotation = $this->reader->getPropertyAnnotation($property,self::ATTACHMENT_ANNOTATION);
            if($annotation instanceof AttachmentAnnotation)
            {
                $attachment = new Attachment($this,$entity,$annotation,$entity);
                $path = $attachment->path();
                if(is_file($path))
                {
                    $file = new File($path);
                    $this->resolver->set($entity,$property->getName(),$file);
                }
            }
        }
    }
}