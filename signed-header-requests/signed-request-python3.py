#/usr/bin/env python3
import hmac
from base64 import b64encode
from hashlib import sha1


def make_signed_auth_header(endpoint, params, datetime, client_id, secret):
    kv_params = ['{}={}'.format(k, v) for k, v in params.items()]
    kv_params.sort()
    kv_string = '\n'.join(kv_params)
    str_to_sign = '{}\n{}\n{}\n'.format(endpoint, datetime, kv_string)
    hashed_str = str(b64encode(hmac.new(secret.encode('UTF-8'), str_to_sign.encode('UTF-8'), sha1).digest()), 'UTF-8')
    return {'Authorization': 'Signature {}:{}'.format(client_id, hashed_str)}

if __name__ == "__main__":
    print(make_signed_auth_header(
        '/entity.find',
        { 'entity_type': 'user', 'filter': "lastUpdated >= '2016-01-01'" },
        '2016-02-26 19:08:44',
        'apkrahlfumwse2e9nvrrotv6vchuptzw',
        'rylicq8ydkz0vmki3gqaoxbk4gyrr05t'
    ))
