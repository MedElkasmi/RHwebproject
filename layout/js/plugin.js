$(document).ready(function () 
{
    // Navbar Animation and decoration
    $('.sidebar .fa-gear').on('click',function (){
        $(this).parent('.sidebar').toggleClass('is-visible');
        if($(this).parent('.sidebar').hasClass('is-visible'))
        {
            $(this).parent('.sidebar').animate({
                left : 0
            },500)

            $('body').animate({
                paddingLeft : '180px'
            },500)
        }
        else
        {
            $(this).parent('.sidebar').animate({
                left : '-180px'
            },500)

            $('body').animate({
                paddingLeft : 0
            },500)
        }
    });

    $('.confirm').click(function () {
        return confirm("Are you Sure you want to execute this action ?");
    })

});




