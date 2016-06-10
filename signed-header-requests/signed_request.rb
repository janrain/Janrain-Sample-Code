require 'base64'
require 'cgi'
require 'openssl'

def make_signed_auth_header(endpoint, params, datetime, client_id, secret)
  param_string = params.keys.sort.map { |key| "#{key}=#{params[key]}" }.join("\n")
  string_to_sign = "#{endpoint}\n#{datetime}\n#{param_string}\n"
  signature = Base64.encode64(OpenSSL::HMAC.digest('sha1', secret, string_to_sign)).chomp
  return { "Authorization": "Signature #{client_id}:#{signature}" }
end

puts make_signed_auth_header(
  '/entity.find',
  { 'entity_type': 'user', 'filter': "lastUpdated >= '2016-01-01'" },
  '2016-02-26 19:08:44',
  'apkrahlfumwse2e9nvrrotv6vchuptzw',
  'rylicq8ydkz0vmki3gqaoxbk4gyrr05t'
)
