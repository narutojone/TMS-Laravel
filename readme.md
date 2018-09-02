## Task Management System
Task Management System (TMS) is an internal application used to track clients and all the tasks we have on each one of them.

### Enviroment information
Here is the following prerequisites with their version numbers:

| Prerequisite | Version |
| ------ | ------ |
| Laravel | 5.5 |
| PHP | 7.1 |
| MySQL | Latest |

---

#### PHP.ini changes needed:
From PHP `7.1` you will expire an exception thrown in the SAML integration. To solve this you will need to update your `php.ini` and reload you local webserver.

Add the following value to you `php.ini`:
```
assert.active = 0
```

---

#### Environment changes needed:
First implement the `.env.example` content into you local `.env`. Then alter the following values as they are required due to checks.

```  
SAML2_IDP_ENTITYID=https://idp.dev/local
SAML2_IDP_SSO_URL=https://idp.dev/local
SAML2_IDP_SLO_URL=https://idp.dev/local
SAML2_IDP_cFi=abcdefghijklmn
```
The values should look identical to these except if you are working on the SAML Authentication.

---

#### Other comments:

**Please note:** Migrations do as of today not have "down" methods. Please comment these out when pushing code to PR's.