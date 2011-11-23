package com.janrain.example

import grails.converters.JSON
import org.codehaus.groovy.grails.web.json.*;

class UserController {
    def scaffold = User
    
    // This is the Janrain Engage "token URL" for the Android native
    // Engage sign-in library. It is the endpoint from which Engage user
    // sign-ins are spawned.
    def janrainMobileSignIn = {
        def apiKey = "ENTER YOUR API KEY HERE"
        def rpxnow = "https://rpxnow.com"
        def auth_info_url = new URL(
            rpxnow + "/api/v2/auth_info?apiKey="
            + apiKey + "&token=" + params.token)
            
        def connection = auth_info_url.openConnection()
        connection.connect()
        def auth_info_response = connection.content.text
        def auth_info = JSON.parse(auth_info_response)
        
        def identifier = auth_info.profile.identifier
        def user = User.findWhere(engageIdentifier:identifier)
        if (!user) user = createUserWithAuthInfo(auth_info)
        
        // You should specify a long session timeout for mobile
        // sign-ins, this would be ~10 years:
        session.maxInactiveInterval = 60*60*24*7*52*10
        session.user = user
        
        render "Sign-in successful"
    }
    
    def createUserWithAuthInfo(auth_info) {
        def identifier = auth_info.profile.identifier
        def displayName_ = auth_info.profile.displayName
        def user = new User(engageIdentifier:identifier,
            displayName:displayName_)
        user.save()
    }

}
