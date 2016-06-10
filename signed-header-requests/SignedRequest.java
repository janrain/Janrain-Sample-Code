import java.security.InvalidKeyException;
import java.security.Key;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Base64;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.TreeSet;
import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;

public class SignedRequest {
  private static final String HMAC_SHA1_ALGORITHM = "HmacSHA1";

  public static Map<String, String> makeSignedAuthHeader(
    String endpoint,
    Map<String, String> params,
    String datetime,
    String clientId,
    String secret)
  throws NoSuchAlgorithmException, InvalidKeyException {
    Set<String> paramKeys = new TreeSet<String>(params.keySet());
    List<String> kvParams = new ArrayList<String>();
    for (String key : paramKeys) {
      kvParams.add(String.format("%s=%s", key, params.get(key)));
    }
    String paramString = String.join("\n", kvParams);
    String stringToSign = String.format("%s\n%s\n%s\n", endpoint, datetime, paramString);
    Key signingKey = new SecretKeySpec(secret.getBytes(), HMAC_SHA1_ALGORITHM);
    Mac hmacInstance = Mac.getInstance(HMAC_SHA1_ALGORITHM);
    hmacInstance.init(signingKey);
    byte[] rawHmac = hmacInstance.doFinal(stringToSign.getBytes());
    String signature = new String(Base64.getEncoder().encode(rawHmac));
    Map<String, String> result = new HashMap<String,String>();
    result.put("Authorization", String.format("Signature %s:%s", clientId, signature));
    return result;
  }

  public static void main(String[] args)
  throws NoSuchAlgorithmException, InvalidKeyException {
    Map<String, String> params = new HashMap<String, String>();
    params.put("entity_type", "user");
    params.put("filter", "lastUpdated >= '2016-01-01'");

    System.out.println(makeSignedAuthHeader(
      "/entity.find",
      params,
      "2016-02-26 19:08:44",
      "apkrahlfumwse2e9nvrrotv6vchuptzw",
      "rylicq8ydkz0vmki3gqaoxbk4gyrr05t"
    ));
  }
}
