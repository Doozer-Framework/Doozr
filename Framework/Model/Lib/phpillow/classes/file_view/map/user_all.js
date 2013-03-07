function( doc )
{
    if ( doc.type == "user" )
    {
        emit( null, doc._id );
    }
}
