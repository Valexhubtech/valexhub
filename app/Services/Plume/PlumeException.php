<?php

namespace App\Services\Plume;

use RuntimeException;

class PlumeException extends RuntimeException {}

class PlumeDomainExistsException extends PlumeException {}

class PlumeNotVerifiedException extends PlumeException {}

class PlumeRateLimitException extends PlumeException {}
