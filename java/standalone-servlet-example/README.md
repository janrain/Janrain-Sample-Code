Example usage of auth widget with call to auth_info api with resulting token from Java.

There's a minimal web page in [src/main/webapp/index.html](standalone-servlet-example/src/main/webapp/index.html) with the `<script>` tag and `<div>` for a embedded auth widget - you'll need to set the name of your application in the JavaScript - or you can copy the JavaScript tag into a page of your own.  Also, you'll need your API key, set in [src/main/webapp/WEB-INF/web.xml](standalone-servlet-example/src/main/webapp/WEB-INF/web.xml) here.

The Java Servlet example in [src/main/java/com/janrain/example/AuthInfo.java](standalone-servlet-example/src/main/java/com/janrain/example/AuthInfo.java) just serves as a destination for the user's browser to be redirected to after logging in, then it calls the `auth_info` API with that token and displays the results.

To compile and run, just run `mvn jetty:run` in the top level of the project, assuming you Maven installed.  This example uses the servlet-api and commons-io.
