<?php namespace WebDev\AttachmentBundle\Attachment;

use WebDev\AttachmentBundle\Configuration\FileAttachment;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Exception;

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
    
    /**
     * Base path for file contexts that ustilize this attachment service
     *
     * @var string
     */
    protected $basePath;
    public function getBasePath(){ return $this->basePath; }
    public function setBasePath( $path ){ $this->basePath = $path; }
}