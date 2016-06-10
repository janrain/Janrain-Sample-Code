'use strict';
const crypto = require('crypto');

function makeSignedAuthHeader(endpoint, params, datetime, client_id, secret) {
  const paramKeys = Object.keys(params).sort()
  const paramString = paramKeys.map((k) => `${k}=${params[k]}`).join('\n')
  const stringToSign = `${endpoint}\n${datetime}\n${paramString}\n`
  const hmac = crypto.createHmac('sha1', secret)
  hmac.update(stringToSign)
  return { Authorization: `Signature ${client_id}:${hmac.digest('base64')}` }
}

console.log(makeSignedAuthHeader(
  '/entity.find',
  { entity_type: 'user', filter: "lastUpdated >= '2016-01-01'" },
  '2016-02-26 19:08:44',
  'apkrahlfumwse2e9nvrrotv6vchuptzw',
  'rylicq8ydkz0vmki3gqaoxbk4gyrr05t'
))
