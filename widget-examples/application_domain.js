var applicationDomain = '_APPLICATION_DOMAIN_'

var appName = (function(domain) {
  matches = domain.match(/^https:\/\/(.+)\.rpxnow\.com\/$/);
  if (!matches) {
    alert(
      'There was an issue parsing your Application Domain. Please check that ' +
      applicationDomain + ' is correct'
    );
  }
  return matches[1];
}(applicationDomain));
