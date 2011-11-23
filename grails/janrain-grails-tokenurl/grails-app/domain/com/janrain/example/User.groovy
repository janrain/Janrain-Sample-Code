package com.janrain.example

class User {
    String displayName
    String engageIdentifier
        
    static constraints = {
        engageIdentifier(unique:true)
    }
}
