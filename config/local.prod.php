<?php

// Production environment

return function (array $settings): array {
    $settings['error']['display_error_details'] = false;

    return $settings;
};
