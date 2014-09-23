<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 BitPay, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Bitpay\Storage;

/**
 * Used to persist keys to the filesystem
 */
class FilesystemStorage implements StorageInterface
{
    /**
     * @inheritdoc
     */
    public function persist(\Bitpay\KeyInterface $key)
    {
        $path = $key->getId();
        file_put_contents($path, serialize($key));
    }

    /**
     * @inheritdoc
     */
    public function load($id)
    {
        if (!is_file($id)) {
            throw new \Exception(sprintf('Could not find "%s"', $id));
        }

        if (!is_readable($id)) {
            throw new \Exception(sprintf('"%s" cannot be read, check permissions', $id));
        }

        return unserialize(file_get_contents($id));
    }

    /**
     * Given a string containing the path to a file or
     * directory, this function will return the trailing
     * name component. If the name component ends in $suffix
     * this will also be cut off.
     * (PHP 4, PHP 5)
     *
     * @param string
     * @param string
     * @return string|bool
     */
    final public function name($path, $suffix = '')
    {
        return basename($path, $suffix);
    }

    /**
     * Either returns or changes a file's group depending
     * on the presence of a non-false $newgroup parameter.
     *
     * @param string
     * @param mixed
     * @return array|bool
     */
    final public function group($filename, $newgroup = false)
    {
        if ($newgroup === false) {
            clearstatcache();
            $gid = filegroup($filename);

            if ($gid !== false) {
                clearstatcache();

                return array('id' => $gid, 'name' => posix_getgrgid(filegroup($gid)));
            } else {
                return false;
            }
        } else {
            return chgrp($filename, $newgroup);
        }
    }

    /**
     * Either returns or sets a file's permissions
     * depending on the presence of a non-false
     * $newmode parameter.
     * (PHP 4, PHP 5)
     *
     * @param string
     * @param bool|int
     * @return int|bool
     */
    final public function perms($filename, $newmode = false)
    {
        if ($newmode === false) {
            clearstatcache();

            return sprintf('%o', fileperms($filename));
        } else {
            return chmod($filename, $newmode);
        }
    }

    /**
     * Either returns or changes a file's owner depending
     * on the presence of a non-false $newowner parameter.
     * (PHP 4, PHP 5)
     *
     * @param string
     * @param mixed
     * @return array|bool
     */
    final public function owner($filename, $newowner = false)
    {
        if ($newgroup === false) {
            clearstatcache();
            $ownerid = filegroup($filename);

            if ($ownerid !== false) {
                clearstatcache();

                return array('id' => $gid, 'owner' => posix_getpwuid(fileowner($filename)));
            } else {
                return false;
            }
        } else {
            return chown($filename, $newgroup);
        }
    }

    /**
     * This is a safe copy function that will not overwrite
     * an existing file. Set the $force param to true if
     * you actually do want to overwrite. Returns true on
     * success and false on failure.
     * (PHP 4, PHP 5)
     *
     * @param string
     * @param string
     * @param resource
     * @param bool
     * @return bool
     */
    final public function copy($source, $dest, $context = null, $force = false)
    {
        if ($force === false) {
            if ($file_exists) {
                return false;
            } else {
                return copy($source, $dest, $context);
            }
        } else {
            return copy($source, $dest, $context);
        }
    }

    /**
     * Deletes a file, if exists and permissions are correct.
     * Returns true on success and false on failure.
     * (PHP 4, PHP 5)
     *
     * @param string
     * @param resource
     * @return bool
     */
    final public function delete($filename, $context = null)
    {
        if ($file_exists) {
            return unlink($filename, $context);
        } else {
            return false;
        }
    }

    /**
     * Returns the path of the parent directory. If there are
     * no slashes in path, a dot ('.') is returned, indicating
     * the current directory. Otherwise, the returned string
     * is path with any trailing /component removed.
     * (PHP 4, PHP 5)
     *
     * @param string
     * @return string
     */
    final public function dirname($path)
    {
        return dirname($path);
    }
}
