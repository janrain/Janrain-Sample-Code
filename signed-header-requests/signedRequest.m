#include <CommonCrypto/CommonDigest.h>
#include <CommonCrypto/CommonHMAC.h>

NSDictionary *makeSignedAuthHeader(
    NSString *endpoint,
    NSDictionary *params,
    NSString *datetime,
    NSString *clientId,
    NSString *secret
) {
    NSArray *paramKeys = [params allKeys];
    NSArray *sortedParamKeys = [paramKeys sortedArrayUsingSelector:@selector(compare:)];
    NSMutableArray *kvParams = [NSMutableArray array];
    for (NSString *key in sortedParamKeys) {
        [kvParams addObject:[NSString stringWithFormat:@"%@=%@", key, params[key]]];
    }
    NSString *paramString = [kvParams componentsJoinedByString:@"\n"];
    NSString *stringToSign = [NSString stringWithFormat:@"%@\n%@\n%@\n", endpoint, datetime, paramString];
    const char *cKey = [secret cStringUsingEncoding:NSASCIIStringEncoding];
    const char *cStringToSign = [stringToSign cStringUsingEncoding:NSASCIIStringEncoding];
    unsigned char cHMAC[CC_SHA1_DIGEST_LENGTH];
    CCHmac(kCCHmacAlgSHA1, cKey, strlen(cKey), cStringToSign, strlen(cStringToSign), cHMAC);
    NSData *HMAC = [[NSData alloc] initWithBytes:cHMAC length:sizeof(cHMAC)];
    NSString *signature = [HMAC base64EncodedStringWithOptions:NSDataBase64Encoding64CharacterLineLength];
    NSArray *authHeaderPieces = [NSArray arrayWithObjects:clientId,signature, nil];
    NSDictionary *authHeader = @{
        @"Authorization" : [@"Signature " stringByAppendingString:[authHeaderPieces componentsJoinedByString:@":"]]
    };
    return authHeader;
}


NSDictionary *params = @{
    @"entity_type" : @"user",
    @"filter" : @"lastUpdated >= '2016-01-01'"
};
NSDictionary *authHeader = makeSignedAuthHeader(
    @"/entity.find",
    params,
    @"2016-02-26 19:08:44",
    @"apkrahlfumwse2e9nvrrotv6vchuptzw",
    @"rylicq8ydkz0vmki3gqaoxbk4gyrr05t"
);

NSLog(@"%@", authHeader);
