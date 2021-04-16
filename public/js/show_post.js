console.log('debug');

function onClickUprateBtn(event) {
    console.log("uprate function");
    event.preventDefault();
    const url = this.href;
    this.disable = true;
    if(url != null){
        console.log("url !=NULL");
        const rateCount = document.getElementById(`ratecount${this.id}`);
        axios.get(url).then(function(response){
            console.log("response request:");
            console.log(response.data.rates);
            rateCount.textContent = response.data.rates;
        });
    }
    this.disable = false;

}

function onClickDownrateBtn(event) {
    console.log("downrate function");
    event.preventDefault();
    const url = this.href;
    if(url != null){
        console.log("url !=NULL");
        const rateCount = document.getElementById(`ratecount${this.id}`);
        axios.get(url).then(function(response){
            console.log("response request:");
            rateCount.textContent = response.data.rates;
        });
    }
}

function onclickSubmitButton(event){
    console.log("onclicksubmitbutton function");
    event.preventDefault();
    const form =  document.querySelector('#add_comment_form');
    const textarea = form.querySelector('textarea');
    const button = this;

    // Interrupting input

    button.disabled = true;
    textarea.disabled = true;
    button.innerHTML = "Publication...";

    // Extracting and packaging data
    const data = new FormData();
    const content = textarea.value;
    console.log(content);
    data.append('content',content);

    // Sending data
    const url = form.action;
    const requestAjax = new XMLHttpRequest();
    requestAjax.open('POST',url);
    requestAjax.send(data);
    requestAjax.onload = function () {
        console.log("response");
        console.log(requestAjax.responseText);
        const response = JSON.parse(requestAjax.responseText);
        console.log(response);
        var newDiv = document.createElement("div");
        newDiv.setAttribute('class',"card border-dark mb-3");
        newDiv.setAttribute('style',"max-width: 100%;");
        var reference;
        if(response.reference != null) {
            reference = `
                <div class="reference-div">
                    <div class="comment-header">
                        <p class="comment-author"> <a href="/user/show/${response.reference.authorId}">${response.reference.author}</a></p>
                        <p class="comment-date">
                        ${response.reference.date} 
                        </p>
                    </div>
                    <p class="comment-content">${response.reference.content}</p>
                </div>
            `;
        } else {
            reference = "";
        }
        newDiv.innerHTML = `
        <div class="comment-div" id="commentDiv${response.id}">
            <div class="comment-header">
                <p class="comment-author"><a href="/user/my_account">${userFirstName} ${userLastName}</a></p>
                <div class="comment-header-right">
                    <p class="comment-date">
                    ${response.creationDate}
                    </p>
                    <a class="delete-comment" id="${response.id}" href="/comment/delete/${response.id}"><img src="/img/garbage.png" alt="garbage.png"></a>
                </div>
            </div>
                ${reference}
            <div>
                <p class="comment-content">${content}</p>
                <div class="comment-rate-group">
                    <a href="/comment/uprate/${response.id}" class="js-uprate btn-up" id="${response.id}"><img src="/img/up.png" alt="up.png"></a>
                    <span id="ratecount${response.id}">0</span>
                    <a href="/comment/downrate/${response.id}" class="js-downrate btn-down" id="${response.id}"> <img src="/img/down.png" alt="up.png"></a>
                </div>
            </div>
        </div>`

        newDiv.querySelector('a.js-uprate').addEventListener('click',onClickUprateBtn);
        newDiv.querySelector('a.js-downrate').addEventListener('click',onClickDownrateBtn);
        newDiv.querySelector('a.delete-comment').addEventListener('click',onClickDeleteBtn);
        document.getElementById("comments_area").prepend(newDiv);
        textarea.value = "";
        button.disabled = false;
        textarea.disabled = false;
        button.innerHTML = "Ajouter";
    }
}

function onClickAnswerToBtn(event) {
    console.log("onClickAnswerToBtn function");
    event.preventDefault();
    location.href = "#textarea";
    const form = document.getElementById("add_comment_form");
    form.action = this.href;
    const span = document.getElementById("answer_to");
    span.innerHTML = `
        Répondre à ${this.id} <a href="" class="cancel-answer-to"><img src="/img/close.png" alt="close.png"></a>        
        `;
    span.classList.add("badge-warning");
    form.querySelector('.cancel-answer-to').addEventListener('click',onClickCancelAnswerTo);

}

function onClickDeleteBtn(event){
    console.log("onClickCloseBtn function");
    event.preventDefault();
    const url = this.href;
    const id = this.id;
    if(url != null){
        axios.get(url).then(function(response){
            console.log("response request message:");
            console.log(response.data);
            if(response.data.code == 200){
                document.getElementById(`commentDiv${id}`).remove()
            }else{
                this.innerHTML = "Error";
            }
        });
    }
}

function onClickCancelAnswerTo(event) {
    event.preventDefault();
    const span = document.getElementById("answer_to");
    span.innerHTML = "";
    span.classList.remove("badge-warning");    
    const id = document.getElementById("postId").value;
    document.getElementById("add_comment_form").action = `/comment/create/${id}`;
}

const userFirstName = document.getElementById("userFirstName").value;
const userLastName = document.getElementById("userLastName").value;


document.querySelectorAll('a.js-uprate').forEach( 
    link => link.addEventListener('click',onClickUprateBtn)
);

document.querySelectorAll('a.js-downrate').forEach( 
    link => link.addEventListener('click',onClickDownrateBtn)
);

document.querySelectorAll('a.answer-to').forEach( 
    link => link.addEventListener('click',onClickAnswerToBtn)
);

document.querySelector("#submit_button").addEventListener('click',onclickSubmitButton);


document.querySelectorAll('.delete-comment').forEach( 
    link => link.addEventListener('click',onClickDeleteBtn)
);