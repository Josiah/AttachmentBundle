<?php namespace WebDev\AttachmentBundle\Configuration;

use Exception;

/**
 * Attachment Annotation
 *
 * Defines an the file attachments that can be made to an object
 *
 * @author Josiah Bond <josiah@web-dev.com.au>
 * @package WebDev Attachment Bundle
 * @Annotation
 */
class Attachment
{
    public function __construct( array $attributes )
    {
        extract($attributes);
        
        if(isset($value)) $path = $value;
        if(isset($path)) $this->path = $path;
    }
    
    protected $path;
    public function getPath() { return $this->path; }
}