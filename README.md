# BUT2-s4-Handipro-Auth
Projet universitaire sur la mise en place d'une API Rest - partie authentification 

# Usage 
POST https://handipromanager.alwaysdata.net/handiauth/auth

{
    "login": "login",
    "password": "password"
}

et 
GET https://handipromanager.alwaysdata.net/handiauth/auth
+ Authorization : Bearer [Token]
