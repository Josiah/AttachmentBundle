=================
Attachment Bundle
=================
------------------------------------
Bringing the File System to your ORM
------------------------------------

The attachment bundle automates the mapping between your entities and the file system.

Why use it?
===========

* Super Flexible - The attachment storage directory can take any form
* Fast - Instead of making another database call to get to each entity, files are accessed directly
  from the location they're stored
* CDN Friendly - *Coming Soon* when you store your files, place them in a CDN for fast access
* Secure - Everything is below the web-root and files are stored as umask `0700` to restrict access

Example
=======

    # AcmeBundle/Entity/File.php
    <?php namespace AcmeBundle\Entity;

    // Annotations
    use Doctrine\ORM\Mapping\Column;
    use Doctrine\ORM\Mapping\Entity;
    use Doctrine\ORM\Mapping\GeneratedValue;
    use Doctrine\ORM\Mapping\Id;
    use Doctrine\ORM\Mapping\Table;
    use WebDev\AttachmentBundle\Configuration\FileAttachment;

    /**
     * @Entity
     * @Table("file")
     * @FileAttachment("data", path="data/files/{id}/{filename}")
     */
    class File
    {
        /**
         * @Id @GeneratedValue
         * @Column(type="integer")
         */
        protected $id;
        public function getId(){ return $this->id; }

        /**
         * @Column
         */
        protected $filename;
        public function getFilename(){ return $this->filename; }
        public function setFilename($filename){ $this->filename = $filename; }
    }