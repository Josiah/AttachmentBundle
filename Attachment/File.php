<?php namespace WebDev\AttachmentBundle\Attachment;

use WebDev\AttachmentBundle\Configuration\FileAttachment;
use WebDev\Conventional\StringTransformer;

/**
 * Attachment File for an Object
 *
 * @author Josiah <josiah@web-dev.com.au>
 */
class File
{
    public function __construct(FileAttachment $config, $object, AttachmentManager $manager)
    {
        $this->config = $config;
        $this->object = $object;
        $this->manager = $manager;
    }

    /**
     * @var WebDev\AttachmentBundle\Configuration\FileAttachment
     */
    protected $config;

    /**
     * @var mixed
     */
    protected $object;

    /**
     * @var WebDev\AttachmentBundle\Attachment\AttachmentManager
     */
    protected $manager;

    /**
     * Retrieves the name of the file attachment
     *
     * @return string
     */
    public function getName()
    {
        return $this->config->getName();
    }
    
    /**
     * Attempts to guess a sane pattern for the file storage context
     *
     * @return string pattern
     */
    protected function guessPattern()
    {
        $class = new ReflectionClass($this->object);

        $pattern = "data/";
        $pattern.= $class->getShortName();
        $pattern.= "-";
        $pattern.= md5($class->getName());
        if( $this->config->getName() )
        {
            $pattern.= "/";
            $pattern.= $this->config->getName();
        }
        return $pattern;
    }

    /**
     * Derives the absolute path to the attachment file
     *
     * @return string
     */
    public function path()
    {
        $pattern = $this->config->getPattern() ?: $this->guessPattern();
        $transform = new StringTransformer($pattern,$this->object);
        $transform->setThrowExceptions(true);

        $path = $transform();
        $base = $this->manager->getBasePath();
        return "{$base}/{$path}";
    }

    /**
     * Saves the specified file as this attachment
     *
     * @param string $file
     */
    public function saveUploadedFile($file)
    {
        $path = $this->path();

        // Ensure containing directory exists
        if(!is_dir($dir = dirname($path)))
        {
            mkdir($dir,0700,true);
        }

        die($path);

        return move_uploaded_file($file,$path);
    }
}