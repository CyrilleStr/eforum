const commentCapacity = 10;
var printedCommentsCount = commentCapacity;

function onSubmitOrderBySelect(event){
    event.preventDefault();
    console.log("onSubmitOrderByForm function");
    const id = document.getElementById("postId").value;
    const url = `/comment/list/${id}/0/${this.value}/`;
    console.log(url);
    const commentsDiv = document.getElementById("comments_area");
    commentsDiv.style.opacity = "30%";
    axios.get(url).then(function(response){
        commentsDiv.style.opacity = "100%";
        commentsDiv.innerHTML = response.data;
        printedCommentsCount = 10;
        document.getElementById("printed_comments").innerHTML = printedCommentsCount;
        if(document.getElementById("authentified").value == "true") {
            addAllEventListener();
        }
        showMore = document.getElementById("show_more");
        if(showMore != null){
        showMore.addEventListener('click',onClickShowMoreBtn);
        }
    });
}

function onClickShowMoreBtn(event){
    const btn = this;
    event.preventDefault();
    console.log('onClickShowMoreBtn');
    const url = this.href;
    btn.remove();
    axios.get(url).then(function(response){
      var newDiv = document.createElement("div");
      newDiv.innerHTML = response.data;
      document.getElementById("comments_area").appendChild(newDiv);
      showMore = document.getElementById("show_more");
      if(showMore != null){
        showMore.addEventListener('click',onClickShowMoreBtn);
      }
      printedCommentsCount += parseInt(document.getElementById('nb_comments_added').value);
      document.getElementById("printed_comments").innerHTML = printedCommentsCount; 
    });
  }

document.getElementById("sort_by").addEventListener('change',onSubmitOrderBySelect);

showMore = document.getElementById("show_more");
if(showMore != null){
showMore.addEventListener('click',onClickShowMoreBtn);
}
