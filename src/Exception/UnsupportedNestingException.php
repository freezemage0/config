<?php


namespace Freezemage\Config\Exception;


use OutOfBoundsException;


class UnsupportedNestingException extends OutOfBoundsException implements ConfigException {}