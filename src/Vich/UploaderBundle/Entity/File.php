<?php

namespace Vich\UploaderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Embeddable
 */
class File
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", precision=0, scale=0, nullable=true, unique=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="original_name", type="string", precision=0, scale=0, nullable=true, unique=false)
     */
    private $originalName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mime_type", type="string", precision=0, scale=0, nullable=true, unique=false)
     */
    private $mimeType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="size", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $size;


}
