<?php

// Path to doctrine common class-loader
$classLoader = realpath_ext(
    DOOZR_DOCUMENT_ROOT . '..' . '/vendor/doctrine/common/lib/Doctrine/Common/ClassLoader.php'
);

require_once $classLoader;


// Annotations loader
$loader = new \Doctrine\Common\ClassLoader(
    'Doctrine\Common\Annotations',
    realpath_ext(
        DOOZR_DOCUMENT_ROOT . '..' . '/vendor/doctrine/annotations/lib'
    )
);
$loader->register();


// Collections loader
$loader = new \Doctrine\Common\ClassLoader(
    'Doctrine\Common\Collections',
    realpath_ext(
        DOOZR_DOCUMENT_ROOT . '..' . '/vendor/doctrine/collections/lib'
    )
);
$loader->register();


// Lexer loader
$loader = new \Doctrine\Common\ClassLoader(
    'Doctrine\Common\Lexer',
    realpath_ext(
        DOOZR_DOCUMENT_ROOT . '..' . '/vendor/doctrine/lexer/lib'
    )
);
$loader->register();


// CouchDB loader
$loader = new \Doctrine\Common\ClassLoader(
    'Doctrine\CouchDB',
    realpath_ext(
        DOOZR_DOCUMENT_ROOT . '..' . '/vendor/doctrine/couchdb/lib'
    )
);
$loader->register();


// Common loader
$loader = new \Doctrine\Common\ClassLoader(
    'Doctrine\Common',
    realpath_ext(
        DOOZR_DOCUMENT_ROOT . '..' . '/vendor/doctrine/common/lib'
    )
);
$loader->register();


// Doctrine ODM loader
$loader = new \Doctrine\Common\ClassLoader(
    'Doctrine',
    realpath_ext(
        DOOZR_DOCUMENT_ROOT . '..' . '/vendor/doctrine/couchdb-odm/lib'
    ) . DIRECTORY_SEPARATOR
);
$loader->register();


//
$loader = new \Doctrine\Common\ClassLoader(
    'Symfony',
    realpath_ext(
        DOOZR_DOCUMENT_ROOT . '..' . '/vendor/symfony/console/'
    )
);
$loader->register();


// Register annotations namespace
Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
    'Doctrine\ODM\CouchDB\Mapping\Annotations',
    realpath_ext(
        DOOZR_DOCUMENT_ROOT . '..' . '/vendor/doctrine/couchdb-odm/lib'
    ) . DIRECTORY_SEPARATOR
);
