<?php namespace WebDev\AttachmentBundle\Configuration;

use Exception;

/**
 * File Attachment Annotation
 *
 * Defines an the file attachments that can be made to an object
 *
 * @author Josiah Bond <josiah@web-dev.com.au>
 * @package WebDev Attachment Bundle
 * @Annotation
 */
class FileAttachment
{
    public function __construct( array $attributes )
    {
        extract($attributes);
        
        if(isset($value)) $name = $value;
        if(isset($name)) $this->name = $name;
        if(isset($pattern)) $this->pattern = $pattern;
    }
    
    protected $name;
    public function getName() { return $this->name; }
    
    protected $pattern;
    public function getPattern() { return $this->pattern; }
}