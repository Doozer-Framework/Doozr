function( doc )
{
    if ( doc.type == "user" )
    {
        emit( doc.login, doc._id );
    }
}
