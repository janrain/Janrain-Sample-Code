package com.janrain.example

// Plant and PlantController are intended to be an example
// of a Grails domain object and controller that you want to restrict
// access to signed-in users only.

class PlantController {
    def beforeInterceptor = [action:this.&checkUser]
    
    def scaffold = Plant
    
    def checkUser() {
        if(!session.user) {
            // i.e. user not logged in
            return false
        }
    }
}
