# Module: Session
## (DoozR\_Session\_Module)
This is the information for the DoozR default module "Session". *Session* provides access to PHP sessions mechanisms in a more comfortable way than PHP does. Some features of *Session* like 

- automatic session regenerating 
- custom client specific session identifier to prevent session hijacking
- OOP-Interface for handling CRUD operations on session(s)
- dddd

## Automatic regenerating of session-id
*Session* implements many of the known mechanisms to secure a session to prevent session hijacking. One of those mechanisms is that *Session* takes each _Xth_ run to regenerate the session-id (where X can be a predefined, or a random integer-value).

## Unique session-identifier per client
*Session* uses a mechanism to prevent that PHP use the default identifier **SESSIONID**. The identfier that *Session*

