<?php namespace WebDev\AttachmentBundle\DBAL;

use SplFileInfo;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class FileType
    extends StringType
{
    const FILETYPE = 'file';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new SplFileInfo($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if($value instanceof SplFileInfo)
        {
            return $value->getRealPath();
        }
        elseif(is_string($value))
        {
            return $value;
        }
        else
        {
            return (string) $value;
        }
    }

    public function getName(){ return self::FILETYPE; }
}