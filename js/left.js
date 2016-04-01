/**
 * Created by gangdong-gyun on 2016. 3. 29..
 */
$(document).ready(function(){
    $('.accordion-toggle,.nameuser').on('click', function () {
        var mu;
        if($(this).hasClass('accordion-toggle')) {
            mu = ($(this).attr('href')).replace('#collapse', '')
        }else{
            mu=($(this).attr('href')).replace('/php/profile.php?id=', '')
        }
        console.log(mu)
        $.ajax({url:"php/data/subscribe.php", type: "GET", data: {mu:mu,action:"check"}, dataType: 'json'})
        $('.newcontent[data-substarget='+mu+']').remove();
    })
});