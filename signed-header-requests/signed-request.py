import hmac
from base64 import b64encode
from hashlib import sha1


def make_signed_auth_header(endpoint, params, datetime, client_id, secret):
    kv_params = ['{}={}'.format(k, v) for k, v in params.items()]
    kv_params.sort()
    kv_string = '\n'.join(kv_params)
    str_to_sign = '{}\n{}\n{}\n'.format(endpoint, datetime, kv_string)
    hashed_str = b64encode(hmac.new(secret, str_to_sign, sha1).digest())
    return {'Authorization': 'Signature {}:{}'.format(client_id, hashed_str)}

if __name__ == "__main__":
    print(make_signed_auth_header(
        '/entity.find',
        { 'entity_type': 'user', 'filter': "lastUpdated >= '2016-01-01'" },
        'Fri, 26 Feb 2016 19:08:44 GMT',
        'apkrahlfumwse2e9nvrrotv6vchuptzw',
        'rylicq8ydkz0vmki3gqaoxbk4gyrr05t'
    ))
