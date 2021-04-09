console.log('debug');

axios.get("/notif/get").then(function(response){
    console.log("response request:");
    console.log(response.data);
    const notifGroup = document.querySelector(".notif-group");
    if (response.data[0].length > 0) {
        for (let i = 0; i < response.data[0].length; i++) {
            var newEl = document.createElement("a");
            newEl.innerHTML = response.data[0][i];
            newEl.href = response.data[1][i];
            newEl.id = response.data[2][i];
            newEl.addEventListener('click',onClickLinkNotif);
            notifGroup.prepend(newEl);
        }
    } else {
            var newEl = document.createElement("p");
            newEl.innerHTML = "Aucunes nouvelles notifications";
            notifGroup.prepend(newEl);
    }
});

function onClickLinkNotif(event) {
    event.preventDefault();
    id = this.id;
    axios.get(`/notif/delete/${id}`);
    window.location.href = this.href;
}