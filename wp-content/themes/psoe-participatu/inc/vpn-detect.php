<?php

function getUserIP(): string
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return trim($_SERVER['HTTP_CLIENT_IP']);
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function isValidIP(string $ip): bool
{
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

function isDatacenterIP(string $ip): bool
{
    $host = @gethostbyaddr($ip);

    if (!$host || $host === $ip) {
        return false;
    }

    $providers = [
        'amazonaws',
        'aws',
        'google',
        'googleusercontent',
        'digitalocean',
        'linode',
        'ovh',
        'microsoft',
        'azure',
        'vultr',
        'akamaitechnologies',
        'cloudflare',
        'oraclecloud',
        'scaleway',
        'hetzner',
        'contabo'
    ];

    foreach ($providers as $provider) {
        if (stripos($host, $provider) !== false) {
            return true;
        }
    }

    return false;
}

function queryProxyCheck(string $ip, string $apiKey, int $timeout = 5): array
{
    $url = "https://proxycheck.io/v2/{$ip}?key={$apiKey}&vpn=1&risk=1&asn=1";

    $context = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'timeout' => $timeout,
            'header'  => "User-Agent: PHP-VPN-Detector/1.0\r\n"
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return [
            'success' => false,
            'error'   => 'request_failed'
        ];
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        return [
            'success' => false,
            'error'   => 'invalid_json'
        ];
    }

    if (isset($data['status']) && strtolower($data['status']) === 'error') {
        $message = strtolower($data['message'] ?? 'unknown_error');

        $creditErrors = [
            'run out of api queries',
            'out of api queries',
            'not enough queries',
            'query limit exceeded'
        ];

        foreach ($creditErrors as $creditError) {
            if (strpos($message, $creditError) !== false) {
                return [
                    'success' => false,
                    'error'   => 'no_credit',
                    'message' => $data['message'] ?? ''
                ];
            }
        }

        return [
            'success' => false,
            'error'   => 'api_error',
            'message' => $data['message'] ?? ''
        ];
    }

    if (!isset($data[$ip]) || !is_array($data[$ip])) {
        return [
            'success' => false,
            'error'   => 'ip_not_found'
        ];
    }

    $ipData = $data[$ip];

    return [
        'success'   => true,
        'is_proxy'  => isset($ipData['proxy']) && strtolower($ipData['proxy']) === 'yes',
        'provider'  => $ipData['provider'] ?? null,
        'type'      => $ipData['type'] ?? null,
        'risk'      => $ipData['risk'] ?? null,
        'country'   => $ipData['country'] ?? null,
        'asn'       => $ipData['asn'] ?? null,
        'raw'       => $ipData
    ];
}

/**
 * Consulta el consumo real de una API key en proxycheck.io
 * (dashboard/export/usage → Queries Today / Daily Limit / Burst).
 * Se cachea 5 min en transient para no disparar una petición
 * por cada carga de portada.
 *
 * @return array{error?:string, plan?:string, limit?:int, today?:int, remaining?:int, total?:int, burst?:int}
 */
function psoe_get_proxycheck_usage(string $apiKey): array
{
    $cacheKey = 'psoe_pc_usage_' . md5($apiKey);
    $cached   = get_transient($cacheKey);

    if (false !== $cached) {
        return is_array($cached) ? $cached : [];
    }

    $url = 'https://proxycheck.io/dashboard/export/usage/?json=1&key=' . rawurlencode($apiKey);

    $context = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'timeout' => 6,
            'header'  => "User-Agent: PHP-VPN-Detector/1.0\r\n",
        ],
    ]);

    $raw  = @file_get_contents($url, false, $context);
    $json = (false !== $raw) ? json_decode($raw, true) : null;

    if (!is_array($json) || !isset($json['Daily Limit'])) {
        $data = [
            'error' => is_array($json)
                ? (string) ($json['error']['message'] ?? 'respuesta inesperada de proxycheck.io')
                : 'sin respuesta de proxycheck.io',
        ];
        set_transient($cacheKey, $data, MINUTE_IN_SECONDS);
        return $data;
    }

    $limit = (int) $json['Daily Limit'];
    $today = (int) ($json['Queries Today'] ?? 0);

    $data = [
        'plan'      => (string) ($json['Plan Tier'] ?? '-'),
        'limit'     => $limit,
        'today'     => $today,
        'remaining' => max(0, $limit - $today),
        'total'     => (int) ($json['Queries Total'] ?? 0),
        'burst'     => (int) ($json['Burst Tokens Available'] ?? 0),
    ];

    set_transient($cacheKey, $data, 5 * MINUTE_IN_SECONDS);

    return $data;
}

/**
 * Panel de diagnóstico: peticiones restantes y API key en uso.
 * Solo se muestra a administradores (manage_options).
 *
 * @param array    $apiKeys Key => email de la cuenta.
 * @param string   $ip      IP del visitante (informativo).
 * @param array|null $result Resultado de detectVPNOrProxy() si se ejecutó.
 */
function psoe_vpn_debug_panel(array $apiKeys, string $ip = '', ?array $result = null): void
{
    if (!function_exists('current_user_can') || !current_user_can('manage_options')) {
        return;
    }

    $usedKey   = $result['used_api_key'] ?? null;
    $rows      = [];
    $marked    = false;
    $activeKey = null;

    foreach ($apiKeys as $apiKey => $account) {
        $usage = psoe_get_proxycheck_usage($apiKey);

        $active = false;
        if (!$marked) {
            // Si ya se ejecutó la detección, marcamos la key que respondió;
            // si no, la que usaría detectVPNOrProxy() = la primera con crédito.
            $active = (null !== $usedKey)
                ? ($usedKey === $apiKey)
                : (empty($usage['error']) && (int) ($usage['remaining'] ?? 0) > 0);

            if ($active) {
                $marked    = true;
                $activeKey = $apiKey;
            }
        }

        $rows[] = [
            'key'     => (string) $apiKey,
            'account' => (string) $account,
            'usage'   => $usage,
            'active'  => $active,
        ];
    }

    // Estado de la detección en esta carga.
    if ($result) {
        if (empty($result['valid_ip'])) {
            $status = 'IP no vàlida';
        } elseif (!empty($result['vpn_detected'])) {
            $status = 'VPN / proxy detectat';
        } else {
            $status = 'Connexió neta';
        }
        $status .= ' · proxycheck: ' . ($result['proxycheck_status'] ?? '-');
    } else {
        $status = 'Detecció no executada en aquesta càrrega';
    }

    echo '<div class="psoe-debug">';
    echo '<div class="psoe-debug-head">';
    echo '<span class="psoe-debug-title">proxycheck.io · diagnòstic</span>';
    echo '<span class="psoe-debug-pill">' . esc_html($status) . '</span>';
    echo '</div>';

    if ($ip !== '') {
        echo '<p class="psoe-debug-ip">IP del visitant: <code>' . esc_html($ip) . '</code></p>';
    }

    echo '<table class="psoe-debug-table"><thead><tr>'
        . '<th>API key</th><th>Compte</th><th>Hui</th><th>Cup diari</th>'
        . '<th>Restants</th><th>Burst</th><th>Pla</th>'
        . '</tr></thead><tbody>';

    foreach ($rows as $row) {
        $u      = $row['usage'];
        $hasErr = !empty($u['error']);

        echo '<tr' . ($row['active'] ? ' class="is-active"' : '') . '>';
        echo '<td class="psoe-debug-key">' . esc_html($row['key'])
            . ($row['active'] ? ' <span class="psoe-debug-flag">en ús</span>' : '') . '</td>';
        echo '<td>' . esc_html($row['account']) . '</td>';

        if ($hasErr) {
            echo '<td colspan="5" class="psoe-debug-error">' . esc_html($u['error']) . '</td>';
        } else {
            echo '<td>' . esc_html((string) $u['today']) . '</td>';
            echo '<td>' . esc_html((string) $u['limit']) . '</td>';
            echo '<td><strong>' . esc_html((string) $u['remaining']) . '</strong></td>';
            echo '<td>' . esc_html((string) $u['burst']) . '</td>';
            echo '<td>' . esc_html($u['plan']) . '</td>';
        }

        echo '</tr>';
    }

    echo '</tbody></table>';

    echo '<p class="psoe-debug-foot">';
    if (null !== $usedKey) {
        echo 'S\'ha operat amb: <code>' . esc_html($usedKey) . '</code>';
    } elseif (null !== $activeKey) {
        echo 'S\'operarà amb: <code>' . esc_html($activeKey) . '</code>';
    } else {
        echo '<strong>Cap API key amb crèdit disponible.</strong>';
    }
    echo ' <span class="psoe-debug-note">(caché 5 min)</span></p>';

    echo '</div>';
}

function detectVPNOrProxy(string $ip, array $apiKeys): array
{
    if (!isValidIP($ip)) {
        return [
            'ip'                => $ip,
            'valid_ip'          => false,
            'vpn_detected'      => false,
            'datacenter'        => false,
            'source'            => null,
            'used_api_key'      => null,
            'proxycheck_status' => 'invalid_ip',
            'details'           => []
        ];
    }

    $datacenter = isDatacenterIP($ip);

    $proxycheckResult = null;
    $usedApiKey = null;

    foreach ($apiKeys as $apiKey => $email) {
        //pre("Api key de: " . $email . " para la IP: " . $ip);
        $result = queryProxyCheck($ip, $apiKey);

      /*      if($email == 'it@speedsixwheels.com'){
            $result['success'] = false;
        }
 */

        if ($result['success'] === true) {
             $apikeyaccount = $email;
            $proxycheckResult = $result;
            $usedApiKey = $apiKey;
            break;
        } 

     

 

        // Si no tiene crédito, prueba con la siguiente
        if (($result['error'] ?? '') === 'no_credit') {
            continue;
        }

        // Para otros errores también seguimos intentando con otra key
        continue;
    }

    $proxyDetected = false;
    $proxycheckStatus = 'not_available';
    $details = [];
   

    if ($proxycheckResult && $proxycheckResult['success'] === true) {
        $proxyDetected = $proxycheckResult['is_proxy'];
        $proxycheckStatus = 'ok';
        $details['proxycheck'] = $proxycheckResult;
    } else {
        $proxycheckStatus = 'all_keys_failed';
    }

    $vpnDetected = ($proxyDetected || $datacenter);

    return [
        'ip'                => $ip,
        'valid_ip'          => true,
        'apikey_account'    => $apikeyaccount ?? null,
        'vpn_detected'      => $vpnDetected,
        'proxy_detected'    => $proxyDetected,
        'datacenter'        => $datacenter,
        'source'            => $proxyDetected ? 'proxycheck' : ($datacenter ? 'reverse_dns_datacenter' : 'clean'),
        'used_api_key'      => $usedApiKey,
        'proxycheck_status' => $proxycheckStatus,
        'details'           => $details
    ];
}

