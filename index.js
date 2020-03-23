function EveryHour() {
    axios({
        method: 'post',
        url: 'twitter.php',
    })
    .then(function(response){
        console.log(response.data);
        setTimeout(EveryHour, 1000*60*60*2)
    })
}

EveryHour();


