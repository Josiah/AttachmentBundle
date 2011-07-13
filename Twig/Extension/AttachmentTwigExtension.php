<?php namespace WebDev\AttachmentBundle\Twig\Extension;

use Twig_Extension as Extension;
use Twig_Filter_Method as FilterMethod;
use WebDev\AttachmentBundle\Service\AttachmentService;

class AttachmentTwigExtension
    extends Extension
{
    /**
     * Compiles a list of filters exposed by this extension
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'relpath' => new FilterMethod($this,'relpath'),
        );
    }

    public function relpath($value)
    {
        if($value instanceof \SplFileInfo)
        {
            $path = $value->getRealPath();
        }
        else
        {
            $path = realpath($value);
        }
        $cwd = getcwd();

        if(!$path || $path == $cwd)
        {
            throw new \Exception("No relationship between path `$value` ($path) and $cwd");
        }

        $length = 0;
        while(($pos = strpos($path,DIRECTORY_SEPARATOR,$length)) !== false
            && substr($cwd,0,$length) === substr($path,0,$length))
        {
            $length = $pos+1;
        }

        return substr($path,$length);
    }

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