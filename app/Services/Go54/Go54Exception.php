<?php

namespace App\Services\Go54;

use RuntimeException;

class Go54Exception extends RuntimeException {}

class InsufficientWalletBalanceException extends Go54Exception {}

class Go54ApiException extends Go54Exception {}
