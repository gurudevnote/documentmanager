/**
 * Created by thaiht on 10/6/15.
 */

function filterDocuments(keyword){
    if(keyword === 'all'){
        $('.document-row').each(function(){
            $(this).show();
        });
        return true;
    }

    $('.document-row').each(function(){
        var row = $(this);
        if(row.find('.document-username:first').html() === keyword || row.find('.document-folder:first').html() === keyword){
            row.show();
        }
        else{
            row.hide();
        }
    });
}
