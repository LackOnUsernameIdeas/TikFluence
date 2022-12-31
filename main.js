function fetchProperData($song_name){
    fetch('fetchData.php').then((res) => res.json())
    .then(response => {
        // response.forEach(song => {
        //     for (let key in song) {
        //       console.log(`${key}: ${song[key]}`)
        //     }
        // })
        let newarr = response.filter(function(item){
            return item.song_name == 'Tokei no Uta'; //$song_name
        });
        console.log(newarr);
    }).catch(error => console.log(error))
}