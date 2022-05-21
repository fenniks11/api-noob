<?php

class JWT
{
    static function base64url_encode($str)
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    static function sign(string $type, object $payload, string $issuer, int $expiryDateInSecond, string $secret)
    {
        // check if expiry date is valid
        if ($expiryDateInSecond < 0) throw new Exception("JWT::sign => Expiry Date cannot be less than 0 second");

        // create payload
        $payload->type = $type;
        $payload->iat = $payload->nbf = time();
        $expiryDateInSecond > 0 && ($payload->exp =  $payload->iat + $expiryDateInSecond);
        $payload->iss = $issuer;
        $payload = json_encode($payload);

        // create header
        $header = json_encode(['typ' => "JWT", 'alg' => 'HS256']);

        // encode header and payload to base64
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // create signature and encode it to base64
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // assemble and return
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        return $jwt;
    }

    static function is_jwt_valid(string $jwt, string $type, string $issuer, string $secret)
    {
        // split the jwt
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $payload_decoded = json_decode($payload);
        $jwt_type = $payload_decoded->type;
        $jwt_issuer = $payload_decoded->iss;
        $signature_provided = $tokenParts[2];

        // check the expiration time - note this will return false if there's no exp on payload
        if (property_exists($payload_decoded, 'exp'))  $expired = ($payload_decoded->exp - time()) < 0;
        else  $expired = false;

        // build a signature based on the header and payload using the secret
        $base64_url_header = self::base64url_encode($header);
        $base64_url_payload = self::base64url_encode($payload);
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
        $base64_url_signature = self::base64url_encode($signature);

        // verify it matches the signature provided in the jwt
        $invalid = !($base64_url_signature === $signature_provided);

        // make sure type is access_token or key
        $wrong_type = $type != $jwt_type;

        // and issuer is correct
        $unknown_issuer = $issuer != $jwt_issuer;

        if ($expired || $invalid || $wrong_type || $unknown_issuer) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    static function getPayload(string $jwt)
    {
        $tokenParts = explode('.', $jwt);
        $payload = base64_decode($tokenParts[1]);
        $payload_decoded = json_decode($payload);

        unset($payload_decoded->iss);
        unset($payload_decoded->exp);
        unset($payload_decoded->iat);
        unset($payload_decoded->type);
        unset($payload_decoded->nbf);

        return $payload_decoded;
    }
}
