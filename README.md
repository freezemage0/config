# Configuration
`freezemage0/config` is a library that provides an easy way to read and create configuration files.
Supports `.json`, `.ini` and `.php` formats.

## Installation
You can install this package using composer:
`composer require freezemage0/config`

## Usage

### ConfigFactory
ConfigFactory automatically resolves the Importer and Exporter for the configuration.
The resolving is based on file extension.

#### Usage example:
##### Creation of configuration object
```php
<?php


use Freezemage\Config\ConfigFactory;


$factory = new ConfigFactory();
$config = $factory->create($_SERVER['DOCUMENT_ROOT'] . '/config.json');
```

The returned config file will be ready to decode and encode JSON data from `config.json` file.

##### Registering of custom Importers and Exporters
You may want to make your own importer or exporter.
In that case you can register them using `ConfigFactory::registerImporter()` and `ConfigFactory::registerExporter()` methods.
All custom importers and exporters MUST implement `ImporterInterface` and `ExporterInterface` respectively.

```php
<?php


use Freezemage\Config\ConfigFactory;
use Freezemage\Config\Importer\ImporterInterface;


class MyImporter implements ImporterInterface {
    private $filename;
    
    public function import(): array {
        //import operation here
    }
    
    public function setFilename(string $filename): void {
        $this->filename = $filename;
    }
    
    public function getFilename(): ?string {
        return $this->filename;
    }
}

$factory = new ConfigFactory();
$factory->registerImporter('my-file-extension', new MyImporter());
```

### ImmutableConfig
Basic configuration class, which provides methods to read, set and save configurations.
Instances of this class are immutable, meaning that any call to `set()` will return a new instance.

#### Basic usage:
Retrieving specific value by key can be done by calling `ImmutableConfig::get()` method.
Retrieving all configuration values can be done by calling `ImmutableConfig::getConfig()` method.
The configuration is loaded on-demand.
```php
<?php


use Freezemage\Config\ConfigFactory;


$factory = new ConfigFactory();
$config = $factory->create($_SERVER['DOCUMENT_ROOT'] . '/config.json');

$database = $config->get('database'); // Loads whole config and returns value
$allValues = $config->getConfig(); // Uses previously loaded values.
```

#### Key chaining
The get/set methods of ImmutableConfig support `key chaining`.
It means that you can use dot (.) to concatenate the nested keys of configuration in order to get the nested value.
Here is the configuration:
```php
<?php

array(
    'database' => array(
        'username' => 'user',
        'password' => 'passwd'
    )
);
```

You can retrieve `username` for `database` connection using the following call:
```php
<?php


use Freezemage\Config\ConfigFactory;


$factory = new ConfigFactory();
$config = $factory->create($_SERVER['DOCUMENT_ROOT'] . '/config.json');

$username = $config->get('database.username');
echo $username; // prints "user"
```

#### Feature management

##### Local management
To disable/enable `key chaining` for current instance of `ImmutableConfig`, call `ImmutableConfig->disableKeyChaining()`.

##### Global management
Example on how to disable/enable `key chaining` for all instances that will be created by `ConfigFactory` object:
```php
<?php

use Freezemage\Config\ConfigFactory;

$factory = new ConfigFactory();
$factory->getFeatureManager()->getKeyChaining()->disable();

$config = $factory->create('config.json'); // key chaining will be disabled for that instance.
```

#### Saving
You can also create/edit configuration using `ImmutableConfig::set()` method.
When you set value, you get a new instance of `ImmutableConfig` with a new `key => value` pair.
This is done in order to preserve immutability of the original configuration until you are ready to save it.
```php
<?php


use Freezemage\Config\Importer\JsonImporter;
use Freezemage\Config\Exporter\JsonExporter;
use Freezemage\Config\ImmutableConfig;

$importer = new JsonImporter();
$exporter = new JsonExporter();

$importer->setFilename($_SERVER['DOCUMENT_ROOT'] . '/connection.json'); // Implying that file exists. 

$config = new ImmutableConfig($importer, $exporter);
$config = $config->set('database', array('username' => 'user')); // New ImmutableConfig is created.
$config->set('database.password', 'passwd')->save(); // Key chaining is supported for setter as well.

```
If you create a new instance of `ImmutableConfig` with no file to import, then it will be created.
If the `Exporter` within `ImmutableConfig` doesn't have filename, then it will be generated based on configuration content.
The same configuration leads to the same filename.
The generated configuration name can be retrieved via `ImmutableConfig->getExporter()->getFilename()`

#### Section extraction
You can extract a section from the config and work with it as a separate instance of `ImmutableConfig`.
The extracted `ImmutableConfig` section will behave exactly as if it was root section.
Be wary that calling `ImmutableConfig->save()` will rewrite the contents of original config file,
unless you do something like the following:

```php
<?php


use \Freezemage\Config\ConfigFactory;


$factory = new ConfigFactory();
$config = $factory->create('config.json');
$database = $config->extractSection('database');
$database->getExporter()->setFilename('database.json');
$database->save(); // the content of database section will be saved into a separate 'database.json' file.
```


#### Configuration converting
You may want to convert configuration from `.json` to `.php` format.
The following example does exactly that:

```php
<?php


use Freezemage\Config\ConfigFactory;
use Freezemage\Config\Exporter\PhpExporter;


$factory = new ConfigFactory();

$configName = $_SERVER['DOCUMENT_ROOT'] . '/config.json';
$config = $factory->create($configName);

$exporter = new PhpExporter();
$exporter->setFilename($configName);

$config->setExporter($exporter);
$config->save();
```

## Precautions

Nested sections are not supported for `IniExporter` (as they are generally not support by anyone).\
Calling `IniExporter->export()` with nested section will throw `UnsupportedNestingException`.