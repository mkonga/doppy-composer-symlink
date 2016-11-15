Composer Symlink
================

This composer plugin allows you to configure symlinks in your composer.json file.
It wil create missing symlinks and update existing (if the target has changed).

## Installation

### Add composer dependency 

````
    "require": {
        "doppy/composer-symlink": "^1.0.0",
    }
````

### Add script calls

````
    "scripts"          : {
        "post-install-cmd": [
            "Doppy\\ComposerSymlink\\ComposerSymlink::apply"
        ],
        "post-update-cmd" : [
            "Doppy\\ComposerSymlink\\ComposerSymlink::apply"
        ]
    },
````

### Configure symlinks

````
    "extra": {
        "doppy-composer-symlink": {
            "./link"     : "target",
            "./inroot"   : "vendor/acme/",
            "./web/fonts": "../vendor/acme/fonts/"
        }
    }
````

* You can configure symlinks in subdirectories, keep in mind that the target should be relative to the source.
* An error is thrown when the directory where the symlink should be created does not exist, is not writeable or is not a directory.
* If you change the target, next time you run composer it will update the target.
* If the target does not exist you get a warning, but this won't give a fatal error.

### Some recommendations

* Do not commit the symlinks in your repository.
* Add the symlinks to your .gitignore
* Do not create symlinks outside your application dir.


