package com.janrain.example;

import java.net.URL;
import java.net.URLEncoder;
import java.net.HttpURLConnection;
import java.io.OutputStreamWriter;
import java.io.IOException;

import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.commons.io.IOUtils;

/*
 * A Servlet that implements the tokenUrl callback endpoint for Engage.
 * Takes the token returned from Engage, and makes an API call to retrieve the user's profile.
 * Displays that profile as a page of JSON in the browser.
 */

public class AuthInfo extends HttpServlet {

    public void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException {
        // The user's browser will POST a token to your "token_url" you specified to have them
        // redirected to after the auth process:
        String token = request.getParameter("token");
        // Do a request to the Janrain API with the token we just received.
        // see http://developers.janrain.com/documentation/api/auth_info/
        // You may wish to make this HTTP request with e.g. Apache HttpClient instead.
        URL url = new URL("https://rpxnow.com/api/v2/auth_info");
        String params = String.format("apiKey=%s&token=%s",
            URLEncoder.encode(getServletConfig().getInitParameter("apiKey"), "UTF-8"),
            URLEncoder.encode(token, "UTF-8")
        );
        HttpURLConnection connection = (HttpURLConnection) url.openConnection();
        connection.setRequestMethod("POST");
        connection.setDoOutput(true);
        connection.connect();
        OutputStreamWriter writer = new OutputStreamWriter( connection.getOutputStream(), "UTF-8" );
        writer.write(params);
        writer.close();
        // Here, we're just copying the response returned by the API to the page served to the browser.
        response.setCharacterEncoding("UTF-8");
        response.setContentType("text/javascript");
        IOUtils.copy(connection.getInputStream(), response.getOutputStream());
    }

}
