function onClickFollowBtn(event) {
    console.log("debug");
    event.preventDefault();
    const url = this.id;
    const btn = this;
    this.disable = true;
    axios.get(url).then(function(response){
        console.log(response.data.message);
        if (response.data.code == 200) {
          if(response.data.state == 1){
          btn.innerHTML = "Ne plus suivre";
          }else{
            btn.innerHTML = "Suivre";
          }
        }else{
          btn.innerHTML == "error";
        }
    });
    btn.disable = false;
}

document.querySelector("button.follow").addEventListener('click',onClickFollowBtn);