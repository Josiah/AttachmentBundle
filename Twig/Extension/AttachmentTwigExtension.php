<?php namespace WebDev\AttachmentBundle\Twig\Extension;

use Twig_Extension as Extension;
use Twig_Filter_Method as FilterMethod;
use WebDev\AttachmentBundle\Service\AttachmentService;

class AttachmentTwigExtension
    extends Extension
{
    public function __construct( AttachmentService $attachments )
    {
        $this->attachments = $attachments;
    }
    
    /**
     * @var AttachmentService
     */
    protected $attachments;
    
    /**
     * Compiles a list of filters exposed by this extension
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'files' => new FilterMethod($this,'files'),
        );
    }
    
    /**
     * @return AttachmentCollection containing the attachemtns of the specified object that match the identifier name supplied
     * @param object $object to retrieve the attachments of
     * @param string $type of attachment to retrieve
     */
    public function files( $object, $type="" ){ return $this->attachments->files($object,$type); }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'attachment';
    }
}