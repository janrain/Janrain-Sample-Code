<%@ page language="java" contentType="text/html; charset=ISO-8859-1"
    pageEncoding="ISO-8859-1" import="java.net.URLEncoder"%>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>No Widget Test Page</title>

<!--
	This example illustrates an Engage setup without using Engage's 
	UI components or javascript.
	
	You will create links that the user clicks to login. (You could 
	alternatively use server-side redirects to these same same URLs 
	if you wanted to encapsulate the login flow entirely in a servlet.)
	
	Please see this document for more information on constructing URLs
	for kicking off the authentication flow:
	
	https://support.janrain.com/entries/508776-custom-user-interface
		
-->
</head>
<body>

<%!	
    // Your Application Domain (as displayed on the Engage dashboard)
	// e.g. https://my-app.rpxnow.com/
    String appDomain = "YOUR_APP_DOMAIN_HERE"; 

    // Your token URL where users will be redirected back to your
    // site after authentication
    String tokenUrl = "http://localhost:8080/callback_url";  
    
    // Method to simpify creating links for provider login flows
    // See: https://support.janrain.com/entries/508776-custom-user-interface
    String getLoginLink(String providerName) throws Exception {
    	
    	// format:
    	// https://{application domain}/{provider name}/start?token_url={token url}
    	return appDomain + providerName + "/start?token_url=" 
    		+ URLEncoder.encode(tokenUrl, "UTF-8");
    }

%>

	<p>Login Links</p>

	<ul>
		<li><a href="<%=getLoginLink("linkedin")%>">LinkedIn</a></li>
		<li><a href="<%=getLoginLink("twitter")%>">Twitter</a></li>
		<li><a href="<%=getLoginLink("facebook")%>">Facebook</a></li>
	</ul>
</body>
</html>