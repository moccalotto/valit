<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Util;

use DateTime;
use SplFileInfo;

/**
 * Provide file information.
 */
class FileInfo
{
    /**
     * @var string
     */
    public $name = null;

    /**
     * @var bool
     */
    public $isCustom = false;

    /**
     * @var bool
     */
    public $exists = false;

    /**
     * @var int
     */
    public $permissions = null;

    /**
     * @var int
     */
    public $userId = null;

    /**
     * @var int
     */
    public $groupId = null;

    /**
     * @var int
     */
    public $size = null;

    /**
     * @var string
     */
    public $realpath = null;

    /**
     * @var string
     */
    public $dirname = null;

    /**
     * @var string
     */
    public $basename = null;

    /**
     * @var string
     */
    public $extension = null;

    /**
     * @var bool
     */
    public $isReadable = null;

    /**
     * @var bool
     */
    public $isWritable = null;

    /**
     * @var bool
     */
    public $isExecutable = null;

    /**
     * @var bool
     */
    public $isFile = null;

    /**
     * @var bool
     */
    public $isDir = null;

    /**
     * @var bool
     */
    public $isLink = null;

    /**
     * @var DateTimeInterface
     */
    public $createdAt = null;

    /**
     * @var DateTimeInterface
     */
    public $modifiedAt = null;

    /**
     * @var DateTimeInterface
     */
    public $accessedAt = null;

    /**
     * Constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->init($file);
    }

    /**
     * Create custum FileInfo instance.
     *
     * @param string $file
     * @param array  $info
     *
     * @return FileInfo
     */
    public static function custom($file, array $info)
    {
        $instance = new static(null);
        $instance->name = $file;
        $instance->exists = true;
        $instance->isCustom = true;

        foreach ($info as $key => $value) {
            $instance->$key = $value;
        }

        return $instance;
    }

    /**
     * Remove override of a given file.
     *
     * @param string $filename
     */
    public static function removeOverride($filename)
    {
        unset(static::$overrides[$filename]);
    }

    /**
     * Initialize instance variables for a given file.
     *
     * @param string $file
     */
    protected function init($file)
    {
        $this->name = $file;

        if (!file_exists($file)) {
            $this->exists = false;

            return;
        }

        $info = new SplFileInfo($file);

        $this->exists = true;
        $this->permissions = $info->getPerms() & 0777;

        $this->userId = $info->getOwner();
        $this->groupId = $info->getGroup();
        $this->size = $info->getSize();

        $this->realpath = $info->getRealPath();
        $this->dirname = $info->getPath();
        $this->basename = $info->getBasename();
        $this->extension = $info->getExtension();
        $this->isFile = $info->isFile();
        $this->isDir = $info->isDir();
        $this->isLink = $info->isLink();
        $this->isExecutable = $info->isExecutable();
        $this->isReadable = $info->isReadable();
        $this->isWritable = $info->isWritable();
        $this->createdAt = DateTime::createFromFormat('U', $info->getCTime());
        $this->modifiedAt = DateTime::createFromFormat('U', $info->getMTime());
        $this->accessedAt = DateTime::createFromFormat('U', $info->getATime());

        return true;
    }
}
