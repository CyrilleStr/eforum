const pageCapacity = 10;
const printedPosts = document.getElementById("printed_posts");
var printedPostsCount = pageCapacity;
const maxPrintedPostCount = document.getElementById('maxPrintedPostCount').value;

function onClickShowMoreBtn(event){
  const btn = this;
  event.preventDefault();
  console.log('onClickShowMoreBtn');
  const url = this.href;
  btn.remove();
  axios.get(url).then(function(response){
    var newDiv = document.createElement("div");
    newDiv.innerHTML = response.data;
    document.getElementById("posts_div").appendChild(newDiv);
    showMore = document.getElementById("show_more");
    if(showMore != null){
      showMore.addEventListener('click',onClickShowMoreBtn);
    }
    printedPostsCount +=10;
    if(printedPostsCount > maxPrintedPostCount){
      printedPostsCount = maxPrintedPostCount;
    }
    printedPosts.innerHTML = printedPostsCount; 
  });
}

function onSubmitOrderBySelect(event){
  event.preventDefault();
  console.log("onSubmitOrderByForm function");
  const catName = document.getElementById("catName").value;
  const subCatName = document.getElementById("subCatName").value;
  const url = `/post/list/0/${catName}/${subCatName}/${this.value}/true`;
  console.log(url);
  const postDiv = document.getElementById("posts_div");
  postDiv.style.opacity = "30%";
  axios.get(url).then(function(response){
    postDiv.style.opacity = "100%";
    postDiv.innerHTML = response.data;
    showMore = document.getElementById("show_more");
    if(showMore != null){
      showMore.addEventListener('click',onClickShowMoreBtn);
    }
    printedPosts.innerHTML = printedPostsCount; 
  });
}
showMore = document.getElementById("show_more");
if(showMore != null){
  showMore.addEventListener('click',onClickShowMoreBtn);
}
document.getElementById("sort_by").addEventListener('change',onSubmitOrderBySelect);