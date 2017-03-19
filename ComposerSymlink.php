<?php

namespace Doppy\ComposerSymlink;

use Composer\IO\IOInterface;
use Composer\Script\Event;

class ComposerSymlink
{
    /**
     * @var IOInterface
     */
    static protected $io;

    /**
     * Applies configured symlinks in the composer file to disk
     *
     * @param Event $event
     */
    public static function apply(Event $event)
    {
        $extras   = $event->getComposer()->getPackage()->getExtra();
        self::$io = $event->getIO();

        if (!isset($extras['doppy-composer-symlink'])) {
            throw new \InvalidArgumentException('The Symlink handler needs to be configured through the extra.doppy-composer-symlink setting.');
        }

        $configuredSymlinks = $extras['doppy-composer-symlink'];

        if (!is_array($configuredSymlinks)) {
            throw new \InvalidArgumentException('The extra.doppy-composer-symlink setting must be an array.');
        }

        foreach ($configuredSymlinks as $link => $target) {
            // cleanup link
            $link = trim(trim($link), '/');

            // apply
            self::applySymlink($link, $target);
            self::checkSymlinkTarget($link, $target);
        }
    }

    /**
     * Applies a symlink
     *
     * @param string $link
     * @param string $target
     */
    private static function applySymlink($link, $target)
    {
        // check if link already exists
        if (is_link($link)) {
            // check if target matched
            $currentTarget = readlink($link);
            if ($currentTarget != $target) {
                // message
                self::$io->write(sprintf('<info>updating symlink `%s` to `%s` (was `%s`)</info>', $link, $target, $currentTarget));

                // remove existing symlink
                unlink($link);

                // create new
                self::createSymlink($link, $target);
            }

            // all done for this loop
            return;
        }

        // check if not already file or directory
        if (file_exists($link)) {
            throw new \RuntimeException(
                sprintf('Unable to create symlink `%s`, as there is already a file or directory present', $link)
            );
        }

        // message
        self::$io->write(sprintf('<info>creating symlink `%s` to `%s`</info>', $link, $target));

        // create new
        self::createSymlink($link, $target);
    }

    /**
     * Actually creates a symlink
     *
     * @param string $link
     * @param string $target
     */
    private static function createSymlink($link, $target)
    {
        // check basedir
        $baseName = dirname($link);
        if (!is_dir($baseName)) {
            throw new \RuntimeException(
                sprintf('Unable to create symlink `%s`, as the basedir does not exist.', $link)
            );
        }
        if (!is_writable($baseName)) {
            throw new \RuntimeException(
                sprintf('Unable to create symlink `%s`, as the basedir is not writeable.', $link)
            );
        }

        // create symlink
        symlink($target, $link);
    }

    /**
     * Does a simple check on a symlink and outputs a warning if the target does not exist
     *
     * @param string $link
     * @param string $target
     */
    private static function checkSymlinkTarget($link, $target)
    {
        if (!file_exists($link)) {
            // seems the target does not exist (yet), output a error for the user
            self::$io->writeError(sprintf('<warning>Warning: Symlink `%s` exists, but target `%s` appears not to exist.</warning>', $link, $target));
        }
    }
}
